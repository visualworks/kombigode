<?php
/**
 * Dynamic styles for Out Of Stock Badge Style section in Product Page > Styles.
 *
 * Prefix: pp -> product page, s -> styles.
 *
 * @package Jupiter
 * @subpackage MK_Customizer
 * @since 5.9.4
 */

$css .= '.single-product .images .flex-viewport {';

$border_width = mk_cz_get_option( 'sh_pp_sty_img_border_width' );
if ( $border_width ) {
	$css .= "border-width: {$border_width}px;";
}

$border_color = mk_cz_get_option( 'sh_pp_sty_img_border_color' );
if ( $border_color ) {
	$css .= "border-color: {$border_color};";
}

$css .= '}';

$css .= '.single-product div.images.woocommerce-product-gallery {';
$css .= mk_cs_box_model( 'sh_pp_sty_img_box_model' );

$box_model = mk_maybe_json_decode( mk_cz_get_option( 'sh_pp_sty_img_box_model' ) );
if ( $box_model ) {
	$margin_left = isset( $box_model->margin_left ) ? $box_model->margin_left : 0;
	$margin_right = isset( $box_model->margin_right ) ? $box_model->margin_right : 0;
	$new_width = (int) $margin_left + (int) $margin_right;
	$css .= 'width: calc(' . mk_get_image_gallery_width( 'sh_pp_set_layout' ) . '% - ' . $new_width . 'px );';
}
$css .= '}';

return $css;
