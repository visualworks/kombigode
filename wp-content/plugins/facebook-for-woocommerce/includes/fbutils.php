<?php
/**
 * @package FacebookCommerce
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('WC_Facebookcommerce_Utils')) :

  /**
   * FB Graph API helper functions
   *
   */
  class WC_Facebookcommerce_Utils {

    const FB_RETAILER_ID_PREFIX = 'wc_post_id_';

    /**
     * WooCommerce 2.1 support for wc_enqueue_js
     *
     * @since 1.2.1
     *
     * @access public
     * @param string $code
     * @return void
     */
    public static function wc_enqueue_js($code) {
      if (function_exists('wc_enqueue_js')) {
        wc_enqueue_js($code);
      } else {
        global $woocommerce;
        $woocommerce->add_inline_js($code);
      }
    }

    /**
     * Validate URLs, make relative URLs absolute
     *
     * @access public
     * @param string $url
     * @return string
     */
    public static function make_url($url) {
      if (
        // The first check incorrectly fails for URLs with special chars.
        !filter_var($url , FILTER_VALIDATE_URL) &&
        substr($url, 0, 4) !== 'http'
      ) {
        return get_site_url() . $url ;
      } else {
        return $url;
      }
    }

    /**
     * Product ID for Dynamic Ads on Facebook can be SKU or wc_post_id_123
     * This function should be used to get retailer_id based on a WC_Product
     * from WooCommerce
     *
     * @access public
     * @param WC_Product $woo_product
     * @return string
     */
    public static function get_fb_retailer_id($woo_product) {
      if (!$woo_product instanceof WC_Product) {
        WC_Facebookcommerce_Integration::log(
          'WARNING: Facebook Pixel wont\'t track user behavior correctly.'.
          'expected WC_Product as parameter for get_fb_retailer_id but got '.
          'something else: ' . $woo_product);
        return '';
      }

      $woo_id = $woo_product->get_id();

      // Call $woo_product->get_id() instead of ->id to account for Variable
      // products, which have their own variant_ids.
      return $woo_product->get_sku() ? $woo_product->get_sku() . '_' .
         $woo_id : self::FB_RETAILER_ID_PREFIX . $woo_id;
    }


    /**
     * Compatibility method for legacy retailer IDs prior to 1.1
     * Returns a variety of IDs to match on for Pixel fires.
     *
     * @access public
     * @param WC_Product $woo_product
     * @return array
     */
    public static function get_fb_content_ids($woo_product) {
      return array(
        $woo_product->get_sku(),
        self::FB_RETAILER_ID_PREFIX . $woo_product->get_id(),
        self::get_fb_retailer_id($woo_product)
      );
    }

    /**
     * Clean up strings for FB Graph POSTing.
     * This function should will:
     * 1. Replace newlines chars/nbsp with a real space
     * 2. strip_tags()
     * 3. trim()
     *
     * @access public
     * @param String string
     * @return string
     */
    public static function clean_string($string) {
      $string = str_replace(array('&amp%3B', '&amp;'), '&', $string);
      $string = str_replace(array("\r", "\n", "&nbsp;", "\t"), ' ', $string);
      // Strip shortcodes via regex but keep inner content
      $string = preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $string);
      $string = wp_strip_all_tags($string, true); // true == remove line breaks
      return trim($string);
    }

    /**
     * Returns flat array of woo IDs for variable products, or
     * an array with a single woo ID for simple products.
     *
     * @access public
     * @param WC_Product $woo_product
     * @return array
     */
    public static function get_product_array($woo_product) {
      $result = array();
      if ($woo_product->get_type() === 'variable') {
        foreach ($woo_product->get_children() as $item_id) {
          array_push($result, $item_id);
        }
        return $result;
      } else {
        return array($woo_product->get_id());
      }
    }

  }
endif;
