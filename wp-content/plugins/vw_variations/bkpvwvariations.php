<?php

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
        add_action('admin_action_freez_recipes_settings', array($this, 'vw_create_variation'));
    }

    public function vw_settings_menu(){
        add_submenu_page('options-general.php', 'VW Variations', 'VW Variations', 'manage_options', 'vw_variations', array($this, 'vw_create_variation'));
    }

    public function vw_insert_post($product){
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

    public function vw_create_variation(){
        global $wpdb;
        echo '<h1>VW Variations</h1>';

        // get all product post_type from ko_posts
        // $query_products = $wpdb->prepare("SELECT 'ID' FROM {$wpdb->prefix}posts WHERE post_type='product' AND post_status='publish' AND post_parent=0 AND ID != 21381");
        $products = get_posts(array(
            'post_parent' => 0,
            'numberposts' => 722,
            'offset'      => 0,
            'include'     => array(18468),
//             'exclude'     => array(21449),
            'post_type'   => 'product',
            'post_status' => 'publish',
            'order'       => 'ASC',
            'orderby'     => 'ID'
        ));

        // get all available attributes
        $query_attributes = $wpdb->prepare("SELECT term.term_id, meta.meta_id, term.name, term.slug, tax.taxonomy, meta.meta_key
            FROM {$wpdb->prefix}terms term
            INNER JOIN {$wpdb->prefix}term_taxonomy tax ON
            tax.term_id = term.term_id
            INNER JOIN {$wpdb->prefix}termmeta meta ON
            meta.term_id = term.term_id
            WHERE taxonomy = %s OR taxonomy = %s OR taxonomy = %s", array('pa_tamanho-da-capa', 'pa_verso-da-capa', 'pa_enchimento'));
        $attributes = $wpdb->get_results($query_attributes);

        $product_variations = array();
        $colors = array();
        $sizes = array();
        $filling = array();
        foreach($attributes as $attribute){
            if($attribute->taxonomy == 'pa_verso-da-capa'){
                array_push($colors, array('term_id'=>$attribute->term_id, 'slug'=>$attribute->slug, 'taxonomy'=>$attribute->taxonomy));
            }
            if($attribute->taxonomy == 'pa_tamanho-da-capa'){
                array_push($sizes, array('term_id'=>$attribute->term_id, 'slug'=>$attribute->slug, 'taxonomy'=>$attribute->taxonomy));
            }
            if($attribute->taxonomy == 'pa_enchimento'){
                array_push($filling, array('term_id'=>$attribute->term_id, 'slug'=>$attribute->slug, 'taxonomy'=>$attribute->taxonomy));
            }
        }
        echo '<code>';
        $start = mktime();
        echo date('d/m/Y H:i:s', $start);
        // create all variations
        foreach($products as $product){
            echo '<h3>' . $product->post_title . '</h3>';
            foreach($sizes as $size){
                foreach($colors as $color){
                    foreach($filling as $fill){

                        $price = $this->vw_get_price($color['slug'], $size['slug'], $fill['slug']);

                        $variation_id = $this->vw_insert_post($product);

                        wp_set_object_terms($variation_id, $fill['slug'], $fill['taxonomy']);
                        wp_set_object_terms($variation_id, $color['slug'], $color['taxonomy']);
                        wp_set_object_terms($variation_id, $size['slug'], $size['taxonomy']);

                        foreach($this->default_meta as $meta => $value){
                            if($meta == '_regular_price'){
                                $value = $price;
                            }
                            if($meta == '_price'){
                                $meta = $price;
                            }
                            if($meta == 'attribute_pa_tamanho-da-capa'){
                                $value = $size['slug'];
                            }
                            if($meta == 'attribute_pa_enchimento'){
                                $value = $fill['slug'];
                            }
                            if($meta == 'attribute_pa_verso-da-capa'){
                                $value = $color['slug'];
                            }
                            add_post_meta($variation_id, $meta, $value);
                        }
                    }
                }
            }
        }
        echo '</code>';
    }

    public function vw_set_attributes(){
        $this->default_meta['_default_attributes'] = 'a:3:{s:15:"pa_tamanho-capa";s:5:"40x40";s:13:"pa_enchimento";s:14:"sem-enchimento";s:16:"pa_verso-da-capa";s:5:"preto";}';
        $this->default_meta['_product_attributes'] = 'a:3:{s:13:"pa_enchimento";a:6:{s:4:"name";s:13:"pa_enchimento";s:5:"value";s:0:"";s:8:"position";i:0;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}s:16:"pa_verso-da-capa";a:6:{s:4:"name";s:16:"pa_verso-da-capa";s:5:"value";s:0:"";s:8:"position";i:1;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}s:18:"pa_tamanho-da-capa";a:6:{s:4:"name";s:18:"pa_tamanho-da-capa";s:5:"value";s:0:"";s:8:"position";i:2;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}}';
    }

    public function vw_get_price($color, $size, $fill){
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
