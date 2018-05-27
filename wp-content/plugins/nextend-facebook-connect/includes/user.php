<?php
require_once(NSL_PATH . '/includes/userData.php');

class NextendSocialUser {

    /** @var NextendSocialProvider */
    protected $provider;

    protected $access_token;

    private $userExtraData;

    /**
     * NextendSocialUser constructor.
     *
     * @param NextendSocialProvider $provider
     * @param                       $access_token
     */
    public function __construct($provider, $access_token) {
        $this->provider     = $provider;
        $this->access_token = $access_token;
    }

    /**
     * @param $key
     * $key is like id, email, name, first_name, last_name
     * Returns a single userdata of the current provider or empty sting if $key is invalid.
     *
     * @return string
     */
    public function getAuthUserData($key) {
        return $this->provider->getAuthUserData($key);
    }

    /**
     * Connect with a Provider
     * If user is not logged in
     * - and has no linked social data (in wp_social_users table), prepare them for register.
     * - but if has linked social data, log them in.
     * If the user is logged in, retrieve the user data,
     * - if the user has no linked social data with the selected provider and there is no other user who linked that id , link them and sync the access_token.
     */
    public function liveConnectGetUserProfile() {

        $user_id = $this->provider->getUserIDByProviderIdentifier($this->getAuthUserData('id'));
        if ($user_id !== null && !get_user_by('id', $user_id)) {
            $this->provider->removeConnectionByUserID($user_id);
            $user_id = null;
        }

        if (!is_user_logged_in()) {

            if ($user_id == null) {
                $this->prepareRegister();
            } else {
                $this->login($user_id);
            }
        } else {
            $current_user = wp_get_current_user();
            if ($user_id === null) {
                // Let's connect the account to the current user!

                if ($this->provider->linkUserToProviderIdentifier($current_user->ID, $this->getAuthUserData('id'))) {

                    $this->provider->syncProfile($current_user->ID, $this->provider, $this->access_token);

                    \NSL\Notices::addSuccess(sprintf(__('Your %1$s account is successfully linked with your account. Now you can sign in with %2$s easily.', 'nextend-facebook-connect'), $this->provider->getLabel(), $this->provider->getLabel()));
                } else {

                    \NSL\Notices::addError(sprintf(__('You have already linked a(n) %s account. Please unlink the current and then you can link other %s account.', 'nextend-facebook-connect'), $this->provider->getLabel(), $this->provider->getLabel()));
                }

            } else if ($current_user->ID != $user_id) {

                \NSL\Notices::addError(sprintf(__('This %s account is already linked to other user.', 'nextend-facebook-connect'), $this->provider->getLabel()));
            }
        }
    }

    /**
     * Prepares the registration and registers the user.
     * If the email is not registered yet, checks if register is enabled call register() function.
     * If the email is already registered, checks if autolink is enabled, if it is, log the user in.
     * Autolink enabled: links the current provider account with the existing social account and attempts to login.
     * Autolink disabled: Add error with already registered email message.
     */
    protected function prepareRegister() {

        $user_id = false;

        $providerUserID = $this->getAuthUserData('id');

        $email = '';
        if (NextendSocialLogin::$settings->get('store_email') == 1) {
            $email = $this->getAuthUserData('email');
        }

        if (empty($email)) {
            $email = '';
        } else {
            $user_id = email_exists($email);
        }
        if ($user_id === false) { // Real register
            if (apply_filters('nsl_is_register_allowed', true, $this->provider)) {
                $this->register($providerUserID, $email);
            } else {
                NextendSocialProvider::redirect(__('Authentication error', 'nextend-facebook-connect'), add_query_arg('registration', 'disabled', NextendSocialLogin::getLoginUrl()));
                exit;
            }

        } else if ($this->autoLink($user_id, $providerUserID)) {
            $this->login($user_id);
        }

        $this->provider->redirectToLoginForm();
    }

    /**
     * @param $username
     * Makes the username in an appropriate format. Removes white space and some special characters.
     * Also turns it into lowercase. And put a prefix before the username if user_prefix is set.
     * If this formated username is valid returns it, else return false.
     *
     * @return bool|string
     */
    protected function sanitizeUserName($username) {
        if (empty($username)) {
            return false;
        }

        $username = strtolower($username);

        $username = preg_replace('/\s+/', '', $username);

        $sanitized_user_login = sanitize_user($this->provider->settings->get('user_prefix') . $username, true);

        if (empty($sanitized_user_login)) {
            return false;
        }

        if (!validate_username($sanitized_user_login)) {
            return false;
        }

        return $sanitized_user_login;
    }

    /**
     * @param $providerID
     * @param $email
     * Registers the user.
     *
     * @return bool
     */
    protected function register($providerID, $email) {
        NextendSocialLogin::$WPLoginCurrentFlow = 'register';

        $sanitized_user_login = false;

        if (NextendSocialLogin::$settings->get('store_name') == 1) {
            /**
             * First checks provided first_name & last_name if it is not available checks name if it is neither available checks secondary_name.
             */
            $sanitized_user_login = $this->sanitizeUserName($this->getAuthUserData('first_name') . $this->getAuthUserData('last_name'));
            if ($sanitized_user_login === false) {
                $sanitized_user_login = $this->sanitizeUserName($this->getAuthUserData('username'));
                if ($sanitized_user_login === false) {
                    $sanitized_user_login = $this->sanitizeUserName($this->getAuthUserData('name'));
                }
            }
        }

        $email = '';
        if (NextendSocialLogin::$settings->get('store_email') == 1) {
            $email = $this->getAuthUserData('email');
        }
        $userData = array(
            'email'    => $email,
            'username' => $sanitized_user_login
        );

        do_action('nsl_before_register', $this->provider);

        do_action('nsl_' . $this->provider->getId() . '_before_register');

        if (NextendSocialLogin::$settings->get('terms_show') == '1') {

            add_filter('nsl_registration_require_extra_input', array(
                $this,
                'require_extra_input_terms'
            ));
        }


        /** @var array $userData Validated user data */
        $userData = $this->finalizeUserData($userData);

        /**
         * -If neither of the usernames ( first_name & last_name, secondary_name) are appropriate, the fallback username will be combined with and id that was sent by the provider.
         * -In this way we can generate an appropriate username.
         */
        if (empty($userData['username'])) {
            $userData['username'] = sanitize_user($this->provider->settings->get('user_fallback') . md5(uniqid(rand())), true);
        }

        /**
         * If the username is already in use, it will get a number suffix, that is not registered yet.
         */
        $default_user_name = $userData['username'];
        $i                 = 1;
        while (username_exists($userData['username'])) {
            $userData['username'] = $default_user_name . $i;
            $i++;
        }

        /**
         * Generates a random password. And set the default_password_nag to true. So the user get notify about randomly generated password.
         */
        if (empty($userData['password'])) {
            $userData['password'] = wp_generate_password(12, false);

            add_action('user_register', array(
                $this,
                'registerCompleteDefaultPasswordNag'
            ));
        }
        /**
         * Preregister, checks what roles shall be informed about the registration and sends a notification to them.
         */
        do_action('nsl_pre_register_new_user', $this);

        /**
         * Eduma theme user priority 1000 to auto log in users. We need to stay under that priority @see https://themeforest.net/item/education-wordpress-theme-education-wp/14058034
         * WooCommerce Follow-Up Emails use priority 10, so we need higher @see https://woocommerce.com/products/follow-up-emails/
         *
         * If there was no error during the registration process,
         * -links the user to the providerIdentifier ( wp_social_users table in database store this link ).
         * -set the roles for the user.
         * -login the user.
         */
        add_action('user_register', array(
            $this,
            'registerComplete'
        ), 11);

        $this->userExtraData = $userData;

        $ret = wp_create_user($userData['username'], $userData['password'], $userData['email']);
        if (is_wp_error($ret) || $ret === 0) {
            $this->registerError();
            exit;
        }

        //registerComplete will log in user and redirects. If we reach here, the user creation failed.
        return false;
    }

    /**
     * By setting the default_password_nag to true, will inform the user about random password usage.
     */
    public function registerCompleteDefaultPasswordNag($user_id) {
        update_user_option($user_id, 'default_password_nag', true, true);
    }


    /**
     * @param $user_id
     * Retrieves the name, first_name, last_name and update the user data.
     * Also set a reminder to change the generated password.
     * Links the user with the provider. Set their roles. Send notification about the registration to the selected roles.
     * Logs the user in.
     *
     * @return bool
     */
    public function registerComplete($user_id) {
        if (is_wp_error($user_id) || $user_id === 0) {
            /** Registration failed */
            $this->registerError();

            return false;
        }

        if (NextendSocialLogin::$settings->get('store_name') == 1) {
            $user_data = array();
            $name      = $this->getAuthUserData('name');
            if (!empty($name)) {
                $user_data['display_name'] = $name;
            }

            $first_name = $this->getAuthUserData('first_name');
            if (!empty($first_name)) {
                $user_data['first_name'] = $first_name;
                if (class_exists('WooCommerce', false)) {
                    add_user_meta($user_id, 'billing_first_name', $first_name);
                }
            }

            $last_name = $this->getAuthUserData('last_name');
            if (!empty($last_name)) {
                $user_data['last_name'] = $last_name;
                if (class_exists('WooCommerce', false)) {
                    add_user_meta($user_id, 'billing_last_name', $last_name);
                }
            }
        }

        if (!empty($user_data)) {
            $user_data['ID'] = $user_id;
            wp_update_user($user_data);
        }

        update_user_option($user_id, 'default_password_nag', true, true);

        $this->provider->linkUserToProviderIdentifier($user_id, $this->getAuthUserData('id'));

        do_action('nsl_registration_store_extra_input', $user_id, $this->userExtraData);

        do_action('nsl_register_new_user', $user_id, $this->provider);
        do_action('nsl_' . $this->provider->getId() . '_register_new_user', $user_id, $this->provider);

        $this->provider->deleteLoginPersistentData();

        do_action('register_new_user', $user_id);

        $this->login($user_id);

        return true;
    }


    private function registerError() {
        global $wpdb;

        $isDebug = NextendSocialLogin::$settings->get('debug') == 1;
        if ($isDebug) {
            if ($wpdb->last_error !== '') {
                echo "<div id='error'><p class='wpdberror'><strong>WordPress database error:</strong> [" . esc_html($wpdb->last_error) . "]<br /><code>" . esc_html($wpdb->last_query) . "</code></p></div>";
            }
        }

        $this->provider->deleteLoginPersistentData();

        if ($isDebug) {
            exit;
        }
    }

    protected function login($user_id) {

        add_action('nsl_' . $this->provider->getId() . '_login', array(
            $this->provider,
            'syncProfile'
        ), 10, 3);

        $isLoginAllowed = apply_filters('nsl_' . $this->provider->getId() . '_is_login_allowed', true, $this->provider, $user_id);

        if ($isLoginAllowed) {

            wp_set_current_user($user_id);

            $secure_cookie = is_ssl();
            $secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, array());
            global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie

            $auth_secure_cookie = $secure_cookie;
            wp_set_auth_cookie($user_id, true, $secure_cookie);
            $user_info = get_userdata($user_id);
            do_action('wp_login', $user_info->user_login, $user_info);

            do_action('nsl_login', $user_id, $this->provider);
            do_action('nsl_' . $this->provider->getId() . '_login', $user_id, $this->provider, $this->access_token);

            $this->redirectToLastLocationLogin();

        }

        $this->provider->redirectToLoginForm();
    }

    /**
     * Redirect the user to
     * -the Fixed redirect url if it is set
     * -where the login has happend if redirect is specified in the url
     * -the Default redirect url if it is set, and if redirect was not specified in the url
     */
    public function redirectToLastLocationLogin() {

        add_filter('nsl_' . $this->provider->getId() . 'last_location_redirect', array(
            $this,
            'loginLastLocationRedirect'
        ), 9, 2);

        $this->provider->redirectToLastLocation();
    }

    /**
     * @param $redirect_to
     * @param $requested_redirect_to
     * Modifies where the user shall be redirected, after successful login.
     *
     * @return mixed|void
     */
    public function loginLastLocationRedirect($redirect_to, $requested_redirect_to) {
        return apply_filters('login_redirect', $redirect_to, $requested_redirect_to, wp_get_current_user());
    }

    /**
     * @param $user_id
     * @param $providerUserID
     * If autoLink is enabled, it links the current account with the provider.
     *
     * @return bool
     */
    public function autoLink($user_id, $providerUserID) {

        $isAutoLinkAllowed = true;
        $isAutoLinkAllowed = apply_filters('nsl_' . $this->provider->getId() . '_auto_link_allowed', $isAutoLinkAllowed, $this->provider, $user_id);
        if ($isAutoLinkAllowed) {
            return $this->provider->linkUserToProviderIdentifier($user_id, $providerUserID);
        }

        return false;
    }

    /**
     * @return NextendSocialProvider
     */
    public function getProvider() {
        return $this->provider;
    }

    /**
     * @param $userData
     *
     * @return array
     * @throws NSLContinuePageRenderException
     */
    public function finalizeUserData($userData) {

        $data = new NextendSocialUserData($userData, $this, $this->provider);

        return $data->toArray();
    }

    public function require_extra_input_terms($askExtraData) {

        add_action('nsl_registration_form_end', array(
            $this,
            'registration_form_terms'
        ), 10000);

        return true;
    }

    public function registration_form_terms($userData) {
        ?>
        <p>
            <?php
            $terms = $this->provider->settings->get('terms');

            if (empty($terms)) {
                $terms = NextendSocialLogin::$settings->get('terms');
            }

            if (function_exists('get_privacy_policy_url')) {
                $terms = str_replace('#privacy_policy_url', get_privacy_policy_url(), $terms);
            }

            echo __($terms, 'nextend-facebook-connect');

            ?>
        </p>
        <?php
    }
}