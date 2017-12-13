<?php
/*
Plugin Name: Melhor Envio - Cotação
Plugin URI:  http://www.melhorenvio.com.br/
Description: Plugin que permite a cotação de fretes utilizando a API do Melhor Envio. Ainda é possível disponibilizar as informações da cotação de frete diretamente para o consumidor final.
Version:     1.0.0
Author:      Vítor Soares
Author URI:  https://vhsoares.github.io/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/

/**
 * Created by PhpStorm.
 * User: pikachu
 * Date: 01/06/17
 * Time: 15:36
 */


if ( ! class_exists( 'MelhorEnvio' ) ) :

class MelhorEnvio
{

    public function __construct()
    {
        add_action('plugins_loaded', array($this,'init'));

    }

    public function init()
    {

        if (class_exists('WC_Integration')){
            //Incluindo a classe da integração
            include_once 'includes/melhorenviointegration.php';

            //Registrando a integração
            add_filter('woocommerce_integrations',array($this, 'add_integration'));

            //Criando os links no Menu
            add_action("admin_menu", "addMenu");


            function addMenu(){
                add_menu_page("Melhor Envio", "Melhor Envio", "administrator", "melhor-envio","pedidos", plugin_dir_url( __FILE__ )."data/mo.png");

                add_submenu_page("melhor-envio","Melhor Envio - Pedidos", "Pedidos", "administrator", "melhor-envio-requests", "pedidos");
                add_submenu_page("pedidos","Melhor Envio - Pedidos", "Pedidos", "administrator", "melhor-envio-request", "pedido");
                add_submenu_page("pedidos","Melhor Envio - Relatório", "Relatório", "administrator", "melhor-envio-relato", "relatorio");
                add_submenu_page("melhor-envio","Melhor Envio - Configurações do Plugin", "Configurações", "administrator", "melhor-envio-config", "configuracoes");
                add_submenu_page("melhor-envio","Melhor Envio - Configurações da Conta", "Sua Conta Melhor Envio", "administrator", "melhor-envio-subscription", "cadastro");
            }

            function pedidos(){
                if(get_option("me_api_key") == null || get_option("me_token") == null){
                    echo '<div class="wrap"> 
                            <h1></h1>
                              <div class="notice notice-error is-dismissible"> 
                                  <h1>Atenção</h1> 
                                  Erro nas chaves de API, favor completar seu cadastro no link <a href="'.get_admin_url(get_current_blog_id(),"admin.php?page=melhor-envio-subscription").'">Cadastro</a>
                               </div> 
                           </div>';
                }else{
                    include_once ABSPATH.'/wp-content/plugins/woocommerce/includes/wc-order-functions.php';
                    include_once 'views/pedidos.php';
                }
            }

            function cadastro(){
                include_once 'data/getters.php';
                include_once 'views/cadastro.php';
            }

            function configuracoes(){
                if(get_option("me_api_key") == null || get_option("me_token") == null){
                    echo '<div class="wrap"> 
                            <h1></h1>
                              <div class="notice notice-error is-dismissible"> 
                                  <h1>Atenção</h1> 
                                  Erro nas chaves de API, favor completar seu cadastro no link <a href="'.get_admin_url(get_current_blog_id(),"admin.php?page=melhor-envio-subscription").'">Cadastro</a>
                               </div> 
                           </div>';
                }else {
                    include_once 'data/getters.php';
                    include_once 'views/config.php';
                }
            }

            function pedido(){
                if(get_option("me_api_key") == null || get_option("me_token") == null){
                    echo '<div class="wrap"> 
                            <h1></h1>
                              <div class="notice notice-error is-dismissible"> 
                                  <h1>Atenção</h1> 
                                  Erro nas chaves de API, favor completar seu cadastro no link <a href="'.get_admin_url(get_current_blog_id(),"admin.php?page=melhor-envio-subscription").'">Cadastro</a>
                               </div> 
                           </div>';
                }else {
                    include_once 'data/getters.php';
                    include_once 'views/pedido.php';
                }
            }

            function relatorio(){
                if(get_option("me_api_key") == null || get_option("me_token") == null){
                    echo '<div class="wrap"> 
                            <h1></h1>
                              <div class="notice notice-error is-dismissible"> 
                                  <h1>Atenção</h1> 
                                  Erro nas chaves de API, favor completar seu cadastro no link <a href="'.get_admin_url(get_current_blog_id(),"admin.php?page=melhor-envio-subscription").'">Cadastro</a>
                               </div> 
                           </div>';
                }else {
                    include_once 'data/getters.php';
                    include_once 'views/relatoEnvio.php';
                }
            }

            include_once 'melhorenvioshippinginit.php';

        }
    }


    /**
     * Adiciona uma nova integração ao WooCommerce
     */
    public function add_integration(){

        $integrations[] = 'MelhorEnvioIntegration';
        return $integrations;
    }

}


$MelhorEnvioIntegration = new MelhorEnvio(__FILE__);


endif;