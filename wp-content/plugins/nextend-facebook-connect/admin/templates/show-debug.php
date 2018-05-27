<div class="nsl-admin-content">
    <style>
        .nsl-admin-notices {
            display: none;
        }
    </style>
    <h1 class="title"><?php _e('Debug', 'nextend-facebook-connect'); ?></h1>
    <?php

    $proAddonState = NextendSocialLoginAdmin::getProState();
    $authorizedDomain = NextendSocialLogin::$settings->get('authorized_domain');
    $currentDomain = NextendSocialLogin::getDomain();

    $licenseKey = substr(NextendSocialLogin::$settings->get('license_key'),0,8);
    $isLicenseKeyOk = NextendSocialLogin::$settings->get('license_key_ok');

    $defaultRedirect = NextendSocialLogin::$settings->get('default_redirect');
    $defaultRedirectReg = NextendSocialLogin::$settings->get('default_redirect_reg');

    $fixRedirect = NextendSocialLogin::$settings->get('redirect');
    $fixRedirectReg = NextendSocialLogin::$settings->get('redirect_reg');

    echo "<p><b>Pro Addon State</b> : ".$proAddonState."</p>";
    echo "<p><b>Authorized Domain</b> : ".$authorizedDomain."</p>";
    echo "<p><b>Current Domain</b> : ".$currentDomain."</p><br>";

    echo "<p><b>License Key</b> : ".$licenseKey."...</p>";
    echo "<p><b>License Key OK</b> : ". (boolval($isLicenseKeyOk) ? 'true' : 'false') ."</p><br>";

    echo "<p><b>Default Redirect URL</b> : ".$defaultRedirect."</p>";
    echo "<p><b>Default Reg Redirect URL</b> : ".$defaultRedirectReg."</p><br>";

    echo "<p><b>Fix Redirect URL</b> : ".$fixRedirect."</p>";
    echo "<p><b>Fix Reg Redirect URL</b> : ".$fixRedirectReg."</p><br>";




    ?>
</div>