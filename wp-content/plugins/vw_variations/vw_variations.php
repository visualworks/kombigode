<?php
/*
Plugin Name: VW Variations
Plugin URI:  https://www.visualworks.com.br
Description: Bulk add WooCommerce variable product variations
Version:     1.0.0
Author:      Visual Works
Author URI:  https://www.visualworks.com.br
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: vw_variations
Domain Path: /languages
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class VW_Variations {
  
  private $default_meta = array(
        '_publicize_pending' => '1',
        '_vc_post_settings' => 'a:1:{s:10:\"vc_grid_id\";a:0:{}}',
        '_variation_description' => '',
        '_sku' => '',
        '_regular_price' => '27.99',
        '_sale_price' => '',
        '_sale_price_dates_from' => '',
        '_sale_price_dates_to' => '',
        'total_sales' => '0',
        '_tax_status' => 'taxable',
        '_tax_class' => '',
        '_manage_stock' => 'no',
        '_backorders' => 'no',
        '_sold_individually' => 'no',
        '_weight' => '',
        '_length' => '',
        '_width' => '',
        '_height' => '',
        '_upsell_ids' => 'a:0:{}',
        '_crosssell_ids' => 'a:0:{}',
        '_purchase_note' => '',
        '_default_attributes' => 'a:3:{s:15:"pa_tamanho-capa";s:5:"40x40";s:13:"pa_enchimento";s:14:"sem-enchimento";s:16:"pa_verso-da-capa";s:5:"preto";}',
        '_product_attributes' => 'a:3:{s:13:"pa_enchimento";a:6:{s:4:"name";s:13:"pa_enchimento";s:5:"value";s:0:"";s:8:"position";i:0;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}s:16:"pa_verso-da-capa";a:6:{s:4:"name";s:16:"pa_verso-da-capa";s:5:"value";s:0:"";s:8:"position";i:1;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}s:18:"pa_tamanho-da-capa";a:6:{s:4:"name";s:18:"pa_tamanho-da-capa";s:5:"value";s:0:"";s:8:"position";i:2;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}}',
        '_virtual' => 'no',
        '_downloadable' => 'no',
        '_product_image_gallery' => '',
        '_download_limit' => '-1',
        '_download_expiry' => '-1',
        '_stock' => NULL,
        '_stock_status' => 'instock',
        '_wc_average_rating' => '0',
        '_wc_rating_count' => 'a:0:{}',
        '_wc_review_count' => '0',
        '_downloadable_files' => 'a:0:{}',
        '_price' => '27.99',
        '_product_version' => '3.1.1',
        'attribute_pa_tamanho-da-capa' => 'tamanho-40x40',
        'attribute_pa_enchimento' => 'sem-enchimento',
        'attribute_pa_verso-da-capa' => 'amarelo'
    );

  public function __construct(){
    add_action('admin_menu', array($this, 'vw_settings_menu'));
    add_action('init', array($this, 'vw_enqueue_scripts'));
    add_action('admin_action_vw_settings_menu', array($this, 'vw_settings_menu'));
    add_action('wp_ajax_vw_create_variation', array($this, 'vw_create_variation'));
    add_action('wp_ajax_nopriv_vw_create_variation', array($this, 'vw_create_variation'));
  }
  
  public function vw_enqueue_scripts(){
    wp_enqueue_script(
      'vw_variations-js',
      plugin_dir_url(__FILE__) . 'js/vw_variations.js',
      array('jquery', 'jquery-ui-autocomplete', 'jquery-ui-position', 'jquery-ui-widget'),
      '2.0.0',
      true
    );
    wp_localize_script('vw_variations-js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
  }

  public function vw_settings_menu(){
    add_submenu_page('options-general.php', 'VW Variations', 'VW Variations', 'manage_options', 'vw_variations', array($this, 'vw_list_variations'));
  }

  public function vw_list_variations(){    
    global $wpdb;

    $products = get_posts(array(
      'post_parent' => 0,
      'numberposts' => 722,
      'offset'      => 0,
//       'include'     => array(21095),
      'exclude'     => array(21449, 21192, 21426, 21381, 21331, 21288, 21166, 21142, 21095),
      'post_type'   => 'product',
      'post_status' => 'publish',
      'order'       => 'ASC',
      'orderby'     => 'ID'
    ));
    echo '<h2>Create Product Variations</h2>';
    echo '<div><input type="button" class="button button-primary button-large" id="vw-create-variation" value="Começar">';
    echo '<table class="wp-list-table fixed striped posts">';
    echo '<tfoot></tfoot>';
    echo '<thead><tr><th>#</th><th>ID</th><th>Product Title</th></tr></thead>';
    echo '<tbody class="the-list">';
    $i = 1;
    foreach($products as $product){
      echo '<tr id="row-' . $product->ID . '" class="vw-product-list"><td style="padding: 3px; text-align: center;">' . $i . '</td><td style="padding: 3px; text-align: center;" class="vw-product-id">' . $product->ID . '</td><td style="padding: 3px;">' . $product->post_title . '</td></tr>';
      $i++;
    }
    echo '</tbody>';
    echo '</table>';
    $ajax_url = admin_url('admin-ajax.php');
//     echo '<script type="text/javascript">jQuery(document).ready(function() {
//             jQuery("#vw-create-variation").on("click", function() {
//               var rows = jQuery("tbody.the-list tr.vw-product-list");
//               var product_list = [];
//               var i = 0;
//               rows.each(function(index, element) {
//                 product_list.push(jQuery(element).find(".vw-product-id").html());
//               });
//               setInterval(function() {
//                 var data = {
//                   "action": "vw_create_variation",
//                   "vw_variation_post_id": product_list[i]
//                 };
//                 console.log(i, product_list[i]);
//                 jQuery.ajax({
//                   url: "' . $ajax_url . '",
//                   data: data,
//                   cache: false,
//                   context: document.body,
//                   dataType: "json",
//                   type: "POST",
//                   success: function(data, textStatus, jqXHR) {
//                     console.log("Success: Product ID", product_list[i]);
//                     jQuery("#row-" + product_list[i]).remove();
//                   },
//                   complete: function(data) {
//                     console.log("Complete: Verify Product ID", product_list[i]);
//                   },
//                   error: function(jqXHR, textStatus, errorThrown) {
//                     jQuery("#row-" + product_list[i]).css({
//                       fontSize: "18px",
//                       color: "#f00"
//                     });
//                     console.error("Error: Product ID", product_list[i], textStatus, errorThrown);
//                   }
//                 });

//                 i++;
//               }, 10000);
//             });
//           });</script>';
  }
  
  public function vw_create_variation(){
    global $wpdb;

    if(!isset($_POST['vw_variation_post_id'])){
      print json_encode('error');
      wp_die();
    }
    
    $products = get_posts(array(
      'post_parent' => 0,
      'numberposts' => 722,
      'offset'      => 0,
      'include'     => array($_POST['vw_variation_post_id']),
      'exclude'     => array(21449, 21192, 21426, 21381, 21331, 21288, 21166, 21142, 21095),
      'post_type'   => 'product',
      'post_status' => 'publish',
      'order'       => 'ASC',
      'orderby'     => 'ID'
    ));
    
    $terms_fillings = get_terms(
      array('taxonomy' => 'pa_enchimento', 'hide_empty' => false)
    );
    
    $terms_colors = get_terms(
      array('taxonomy' => 'pa_verso-da-capa', 'hide_empty' => false)
    );
    
    $terms_sizes = get_terms(
      array('taxonomy' => 'pa_tamanho-da-capa', 'hide_empty' => false)
    );
    
    $i = 0;
    foreach($products as $product){
      foreach($terms_fillings as $filling){
        foreach($terms_colors as $color){
          foreach($terms_sizes as $size){
            $post_id = $this->vw_insert_post($product);
            $i++;
            $this->vw_insert_meta($post_id, $size->slug, $filling->slug, $color->slug);
            $this->vw_insert_terms($post_id,$filling->term_id, $filling->taxonomy);
            $this->vw_insert_terms($post_id,$color->term_id, $color->taxonomy);
            $this->vw_insert_terms($post_id,$size->term_id, $size->taxonomy);
            
            // wp_update_post(array('ID' => $product->ID));
            wp_publish_post($product->ID);
          }
        }
      }
    }
    print json_encode($product->ID);
    wp_die();
  }
  public function vw_insert_terms($post_id, $term, $taxonomy){
    wp_set_object_terms($post_id, $term, $taxonomy);
  }
  public function vw_insert_post($product){
    global $wpdb;
    return wp_insert_post(array(
            'post_author' => '1',
            'post_content' => '',
            'post_content_filtered' => '',
            'post_title' => $product->title,
            'post_excerpt' => '',
            'post_status' => 'publish',
            'post_type' => 'product_variation',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_password' => '',
            'to_ping' =>  '',
            'pinged' => '',
            'post_parent' => $product->ID,
            'menu_order' => 0,
            'guid' => $product->guid
        ));
  }
  public function vw_insert_meta($post_id, $size, $filling, $color){
    $default_meta = $this->default_meta;
    foreach($default_meta as $meta_key => $meta_value){
      if($meta_key == '_regular_price' || $meta_key == '_price'){
        $meta_value = $this->vw_get_price($size, $filling, $color);
      }
      if($meta_key == 'attribute_pa_tamanho-da-capa'){
        $meta_value = $size;
      }
      if($meta_key == 'attribute_pa_enchimento'){
        $meta_value = $filling;
      }
      if($meta_key == 'attribute_pa_verso-da-capa'){
        $meta_value = $color;
      }
      add_post_meta($post_id, $meta_key, $meta_value);
    }
  }
  public function vw_get_price($size, $fill, $color){
        $price = '37.99';
        switch(str_replace('tamanho-', '', $size)){
            case '40x40':
                if($color == 'estampa'){
                    if($fill == 'sem-enchimento'){
                        $price = '37.99';
                    } else {
                        $price = '47.99';
                    }
                } else {
                    if($fill == 'sem-enchimento'){
                        $price = '27.99';
                    } else {
                        $price = '37.99';
                    }
                }
                break;
            case '45x45':
                if($color == 'estampa'){
                    if($fill == 'sem-enchimento'){
                        $price = '44.90';
                    } else {
                        $price = '55.00';
                    }
                } else {
                    if($fill == 'sem-enchimento'){
                        $price = '35.00';
                    } else {
                        $price = '46.00';
                    }
                }
                break;
            case '50x30':
                if($color == 'estampa'){
                    if($fill == 'sem-enchimento'){
                        $price = '32.00';
                    } else {
                        $price = '42.00';
                    }
                } else {
                    if($fill == 'sem-enchimento'){
                        $price = '26.00';
                    } else {
                        $price = '34.00';
                    }
                }
                break;
            case '50x50':
                if($color == 'estampa'){
                    if($fill == 'sem-enchimento'){
                        $price = '51.90';
                    } else {
                        $price = '69.00';
                    }
                } else {
                    if($fill == 'sem-enchimento'){
                        $price = '41.00';
                    } else {
                        $price = '49.00';
                    }
                }
                break;
            case '50x70':
                if($color == 'estampa'){
                    if($fill == 'sem-enchimento'){
                        $price = '68.00';
                    } else {
                        $price = '83.00';
                    }
                } else {
                    if($fill == 'sem-enchimento'){
                        $price = '49.00';
                    } else {
                        $price = '69.00';
                    }
                }
                break;
            case '60x60':
                if($color == 'estampa'){
                    if($fill == 'sem-enchimento'){
                        $price = '66.00';
                    } else {
                        $price = '86.00';
                    }
                } else {
                    if($fill == 'sem-enchimento'){
                        $price = '51.00';
                    } else {
                        $price = '68.00';
                    }
                }
                break;
        }
        return $price;
    }
}

new VW_Variations();
// $product->create_variation();
