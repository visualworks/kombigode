<?php
/*
Plugin Name: Pixel de Facebook para Woocommerce
Plugin URI: https://www.labschool.es
Description: Agrega f&aacute;cilmente el pixel de Facebook, adem&aacute;s de eventos est&aacute;ndar y personalizados a tu Woocommerce (ViewShop, ViewCategory, ViewTag, ViewContent, AddToCart, InitiateCheckout y Purchase).
Version: 1.1.2
Requires at least: 3.0
Tested up to: 4.9
WC requires at least: 3.0
WC tested up to: 3.3
Author: Lab School
Author URI: https://www.labschool.es
License: GPLv2
*/

if(!defined('ABSPATH')) exit;
require_once('fb-admin.php');

// ACTIVAR PLUGIN
register_activation_hook( __FILE__, 'pfb_woo_install' );

function pfb_woo_install() {
   $pfb_woo_options = array(
      'id' 				=> '',
      'include_snippet' 	=> '0',
      'product_ref' 		=> '1',
      'currency'			=> 'EUR',
      'taxes'				=> '0',
      'view_content' 		=> '0',
      'view_shop'			=> '0',
      'view_category'		=> '0',
      'view_tag'			=> '0',
      'add_to_cart' 		=> '0',
      'initiate_checkout' 	=> '0',
      'purchase' 			=> '0'
   );
   if (!get_option('pfb_woo_options')) {
      update_option( 'pfb_woo_options', $pfb_woo_options );
   }
}

// INICIAR PLUGIN
add_action('plugins_loaded', 'pfb_woo_setup');
function pfb_woo_setup() {
	add_action( 'wp_head', 'pfb_woo_header', 102 );
}

function pfb_woo_header() {
    $options 	= get_option('pfb_woo_options');
    $id 		= $options['id'];
    if (!isset($options['include_snippet']) != '1' || $options['include_snippet'] ) { 
?> 
<!-- Facebook Pixel -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
    
fbq('init', '<?php echo "$id";?>');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo "$id";?>&ev=PageView&noscript=1"/></noscript>
<!-- Facebook Pixel -->
	<?php
    }
	//VIEWCONTENT
	if (!isset($options['view_content']) != '1' || $options['view_content']) { 
		add_action('wp_footer', 'fb_viewcontent');
		function fb_viewcontent() {
			global $product;
			if(is_product()){
				$options 				= get_option('pfb_woo_options');
				$content_sku 			= $product->get_sku();
				$content_id 			= $product->get_id();
				$name 					= $product->get_title();
				$price 					= $product->get_price();
				$price_ex				= wc_get_price_excluding_tax($product);
				$product_cats 			= get_the_terms($product->get_id(), 'product_cat');
				if ($product_cats) { 
					$cat 				= $product_cats[0];
					$product_category 	= $cat->name;
				} else {
					$product_category 	= 'Ninguna';
				}		
				?>
<!-- Facebook ViewContent event -->
<script>
fbq('track', 'ViewContent', {
	content_type: 'product', 
	content_name: '<?php echo $name; ?>',
	content_category: '<?php echo $product_category ?>', 
	content_ids: 	['<?php echo (isset($options['product_ref']) && $options['product_ref']) ? $content_id : $content_sku; ?>'],
	value: <?php echo (isset($options['taxes']) && $options['taxes']) ? $price : $price_ex; ?>, 
	currency:'<?php echo $options['currency'];?>'
});
</script>
			<?php
			}
		}
	}
	
	//VIEWSHOP
	if (!isset($options['view_shop']) != '1' || $options['view_shop']) { 
		add_action('wp_footer', 'fb_viewshop');
		function fb_viewshop() {			
			global $wp_query;
			if(is_shop()){
				$options 				= get_option('pfb_woo_options');
				$products 				= array_values(array_map(function($item) {return wc_get_product($item->ID);}, $wp_query->get_posts()));
				foreach ($products as $product) {		
					$content_sku[] 		= "'".$product->get_sku()."'";
					$content_id[] 		= "'".$product->get_id()."'";
				}	
				?>
<!-- Facebook ViewShop custom event -->
<script>
fbq('track', 'ViewShop', {
	content_type: 'product', 
	content_ids: [<?php echo (isset($options['product_ref']) && $options['product_ref']) ? implode( ', ',$content_id) : implode( ', ',$content_sku); ?>]
});
</script>
			<?php
			}
		}
	}
	//VIEWCATEGORY
	if (!isset($options['view_category']) != '1' || $options['view_category']) { 
		add_action('wp_footer', 'fb_viewcategory');
		function fb_viewcategory() {			
			global $wp_query;
			if( is_product_category() ){
				$options 				= get_option('pfb_woo_options');
				$products 				= array_values(array_map(function($item) {return wc_get_product($item->ID);}, $wp_query->get_posts()));
				foreach ($products as $product) {		
					$content_sku[] 		= "'".$product->get_sku()."'";
					$content_id[] 		= "'".$product->get_id()."'";
				}	
				?>
<!-- Facebook ViewCategory custom event -->
<script>
fbq('track', 'ViewCategory', {
	content_type: 'product', 
	content_category: '<?php echo single_cat_title(); ?>',
	content_ids: [<?php echo (isset($options['product_ref']) && $options['product_ref']) ? implode( ', ',$content_id) : implode( ', ',$content_sku); ?>]
});
</script>
			<?php
			}
		}
	}
	//VIEWTAG
	if (!isset($options['view_tag']) != '1' || $options['view_tag']) { 
		add_action('wp_footer', 'fb_viewtag');
		function fb_viewtag() {			
			global $wp_query;
			if( is_product_tag() ){
				$options 				= get_option('pfb_woo_options');
				$products 				= array_values(array_map(function($item) {return wc_get_product($item->ID);}, $wp_query->get_posts()));
				foreach ($products as $product) {		
					$content_sku[] 		= "'".$product->get_sku()."'";
					$content_id[] 		= "'".$product->get_id()."'";
				}	
				?>
<!-- Facebook ViewTag custom event -->
<script>
fbq('track', 'ViewTag', {
	content_type: 'product', 
	content_category: '<?php echo single_tag_title(); ?>',
	content_ids: [<?php echo (isset($options['product_ref']) && $options['product_ref']) ? implode( ', ',$content_id) : implode( ', ',$content_sku); ?>]
});
</script>
			<?php
			}
		}
	}
	//ADDTOCART
	if (!isset($options['add_to_cart']) != '1' || $options['add_to_cart']) { 
		add_action('wp_footer', 'fb_addtocart');
		function fb_addtocart( ) {
			if(is_cart() && WC()->cart->get_cart_contents_count() > 0){		
				$options 			= get_option('pfb_woo_options');
				$cart_items 		= WC()->cart->cart_contents;
				$cart_total_ex 		= WC()->cart->subtotal_ex_tax;
				$cart_total 		= WC()->cart->subtotal;
				$cart_prods 		= WC()->cart->get_cart();
				if($cart_items) foreach ($cart_items as $cart_item){
					$content_id[] 	= "'".$cart_item['product_id']."'";
					$content_sku[] 	= "'".$cart_item['data']->get_sku()."'";
				}				
				?>
<!-- Facebook AddToCart event -->
<script>
fbq('track', 'AddToCart', {
	content_type: 'product', 
	content_ids: [<?php echo (isset($options['product_ref']) && $options['product_ref']) ? implode( ', ',$content_id) : implode( ', ',$content_sku); ?>], 
	value: <?php echo (isset($options['taxes']) && $options['taxes']) ? $cart_total : $cart_total_ex; ?>, 
	currency:'<?php echo $options['currency'];?>'
});
</script>
			<?php
			}
		}
	}
	//INITIATECHECKOUT
	if (!isset($options['initiate_checkout']) != '1' || $options['initiate_checkout']) { 
		add_action('woocommerce_after_checkout_form', 'fb_initiatecheckout');
		function fb_initiatecheckout() {
			if( is_checkout() ){
				$options 			= get_option('pfb_woo_options');
				$cart_items			= WC()->cart->cart_contents;
				$cart_total_ex 		= WC()->cart->subtotal_ex_tax;
				$cart_total 		= WC()->cart->subtotal;
				$cart_prods 		= WC()->cart->get_cart();
				if($cart_items) foreach ($cart_items as $cart_item){
					$content_id[] 	= "'".$cart_item['product_id']."'";
					$content_sku[] 	= "'".$cart_item['data']->get_sku()."'";
				}	
				?>
<!-- Facebook InitiateCheckout event -->
<script>
fbq('track', 'InitiateCheckout', {
	content_type: 'product', 
	content_ids: [<?php echo (isset($options['product_ref']) && $options['product_ref']) ? implode( ', ',$content_id) : implode( ', ',$content_sku); ?>], 
	value: <?php echo (isset($options['taxes']) && $options['taxes']) ? $cart_total : $cart_total_ex; ?>,  
	currency:'<?php echo $options['currency'];?>'
});
</script>
			<?php
			}
		}
	}
	//PURCHASE
	if (!isset($options['purchase']) != '1' || $options['purchase']) { 
		add_action('woocommerce_thankyou', 'fb_purchase');
		function fb_purchase( $order_id ) {
			$options 				= get_option('pfb_woo_options');
			$order 					= new WC_Order( $order_id );
			$order_total 			= $order->get_total();
			$order_total_ex			= $order_total - $order->get_total_tax();
			$order_items 			= $order->get_items();			
			if($order_items) foreach ($order_items as $order_item => $item){ 
				$content_id[] 		= "'".$item['product_id']."'";
				$product_id			= wc_get_product($item['product_id']);
				$variation_id		= wc_get_product($item['variation_id']);
				if ($variation_id) {
					$content_sku[] 	= "'".$variation_id->get_sku()."'";
				} else {
					$content_sku[] 	= "'".$product_id->get_sku()."'";
				}
			}	
			?>
<!-- Facebook Purchase event -->
<script>
fbq('track', 'Purchase', {
	content_type: 'product', 
	content_ids: [<?php echo (isset($options['product_ref']) && $options['product_ref']) ? implode( ', ',$content_id) : implode( ', ',$content_sku); ?>], 
	value: <?php echo (isset($options['taxes']) && $options['taxes']) ? $order_total : $order_total_ex; ?>, 
	currency:'<?php echo $options['currency'];?>'
});
</script> 
		<?php
		}
	}
}
?>