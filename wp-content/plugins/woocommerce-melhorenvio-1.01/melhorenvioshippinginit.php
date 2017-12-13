<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    function melhorEnvioShippingInit(){
        if ( ! class_exists( 'WC_MelhorEnvioShippingMethod' ) ) {

            /**
             * Class WC_MelhorEnvioShippingMethod
             */
            class WC_MelhorEnvioShipping_Method extends WC_Shipping_Method{

                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct() {
                    $this->id                 = 'melhorenvio';
                    $this->method_title       = __( 'Melhor Envio', 'melhorenvio' );
                    $this->method_description = __( 'Metodo de envio utilzando a Melhor Envio.', 'melhorenvio' );

                    $this->init();

                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Melhor Envio', 'melhorenvio' );
                }

                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init() {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    // Save settings in admin if you have any defined
                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                }

                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 * @param array $package
                 * @return void
                 */
                public function calculate_shipping($package = array()) {
                    include_once "data/getters.php";
                    //var_dump($package);
                    $rates = getCotacaoFront($package);
                    $chosen = (array) json_decode(get_option('me_transportadoras'));

                    foreach ($rates  as  $raten){
                        foreach ($raten->services as $service){
                            $days = $service->delivery_time +$chosen['profit_days'] ;

                            //Logica para diferenciar dias de dia
                            if($days == 1){
                                $days_label = '( '.$days.' Dia)';
                            }if($days > 1){
                                $days_label = '( '.$days.' Dias)';
                            }


                            if($service->status == 1){
                                $rate = array(
                                    'id' => $raten->name.' '.$service->name,
                                    'label' => $raten->name.' '.$service->name.$days_label,
                                    'cost' => $service->price + ($service->price *($chosen['profit_value'] /100)),
                                    'calc_tax' => 'per_order'
                                );
                            }
                            $verifier = false;
                            foreach ($chosen as $transportadora){
                                if($transportadora == $raten->name){
                                    $verifier = true;
                                    break;
                                }
                            }

                            // Register the rate
                            if($service->status == 1 && $verifier && $chosen['show_in_front'] == 1){
                                $this->add_rate($rate);
                            }
                        }

                    }
                }




            }

        }

    }

    add_action( 'woocommerce_shipping_init', 'melhorEnvioShippingInit' );

    function add_your_shipping_method( $methods ) {
        $methods['WC_MelhorEnvio_Shipping_Method'] = 'WC_MelhorEnvioShipping_Method';
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'add_your_shipping_method');

}