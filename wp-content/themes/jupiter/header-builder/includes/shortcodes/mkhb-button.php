<?php
/**
 * Header Builder: mkhb_button shortcode.
 *
 * @since 6.0.0
 * @package Header_Builder
 */

/**
 * HB Button element shortcode.
 *
 * @since 6.0.0
 *
 * @param  array  $atts All parameter will be used in the shortcode.
 * @param  string $content The enclosed content.
 * @return string      Rendered HTML.
 */
function mkhb_button_shortcode( $atts, $content ) {
	$options = shortcode_atts(
		array(
			'id' => 'mkhb-button-1',
			'url' => '',
			'alignment' => '',
			'display' => '',
			'width' => '',
			'font-weight' => '',
			'font-size' => '',
			'font-style' => '',
			'color' => '',
			'background-color' => '',
			'hover-color' => '',
			'hover-background-color' => '',
			'border-radius' => '',
			'border-width' => '',
			'border-color' => '',
			'margin' => '',
			'padding' => '',
			'target' => '_blank',
			'font-type' => 'google',
			'font-family' => 'Roboto',
			'device' => 'desktop',
			'visibility' => 'desktop, tablet, mobile',
		),
		$atts
	);

	// Check if button is should be displayed in current device or not.
	if ( ! mkhb_is_shortcode_displayed( $options['device'], $options['visibility'] ) ) {
		return '';
	}

	// Set Button inline style.
	$style = mkhb_button_style( $options );

	// Button ID.
	$button_id = $options['id'];

	// Button URL.
	$link = '';
	if ( ! empty( $options['url'] ) ) {
		$link = 'href="' . esc_url( $options['url'] ) . '"';
	}

	// Button additional class.
	$button_class = mkhb_shortcode_display_class( $options );

	// @todo Temporary Solution - Data Attribute for inline container.
	$data_attr = mkhb_shortcode_display_attr( $options );

	$markup = sprintf( '
		<div id="%s" class="mkhb-button-el %s" %s>
			<a %s class="mkhb-button-el__link" target="%s" role="button">%s</a>
		</div>',
		esc_attr( $button_id ),
		esc_attr( $button_class ),
		$data_attr,
		$link,
		esc_attr( $options['target'] ),
		esc_html( $content )
	);

	// @todo: wp_add_inline_style can't be used for shortcode. Temporary fix.
	wp_register_style( 'mkhb', false, array( 'mkhb-grid' ) );
	wp_enqueue_style( 'mkhb' );
	wp_add_inline_style( 'mkhb', $style );

	// Enqueue current font.
	$data = array(
		'font-family' => $options['font-family'],
		'font-type' => $options['font-type'],
		'font-weight' => $options['font-weight'],
	);

	$hooks = mkhb_hooks();
	$hooks::set_hook( 'fonts', $data );

	return $markup;
}
add_shortcode( 'mkhb_button', 'mkhb_button_shortcode' );

/**
 * Generate inline style for HB Button.
 *
 * @since 6.0.0
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return string          Button inline CSS.
 */
function mkhb_button_style( $options ) {
	// Button ID.
	$button_id = $options['id'];

	$style = "#$button_id {";

	// Button Alignment.
	if ( ! empty( $options['alignment'] ) ) {
		$style .= "text-align: {$options['alignment']};";
	}

	$style .= '}';

	$style .= "#$button_id .mkhb-button-el__link {";

	// Button Width.
	if ( ! empty( $options['width'] ) ) {
		$style .= "width: {$options['width']};";
	}

	// Button Color.
	if ( ! empty( $options['color'] ) ) {
		$style .= "color: {$options['color']};";
	}

	// Button Background Color.
	if ( ! empty( $options['background-color'] ) ) {
		$style .= "background-color: {$options['background-color']};";
	}

	// Button Border Radius.
	if ( ! empty( $options['border-radius'] ) ) {
		$style .= "border-radius: {$options['border-radius']};";
	}

	// Button Margin and Padding Style.
	$style .= mkhb_button_layout( $options );

	// Button Link Style.
	$style .= mkhb_button_font_style( $options );

	// Button Border Style.
	$style .= mkhb_button_border( $options );

	$style .= '}';

	$style .= mkhb_button_hover( $options );

	return $style;
}

/**
 * Generate internal style for HB Button Layout.
 *
 * @since 6.0.0
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return string          Button internal CSS margin and padding.
 */
function mkhb_button_layout( $options ) {
	$style = '';

	// Button Padding.
	if ( ! empty( $options['padding'] ) ) {
		$style .= "padding: {$options['padding']};";
	}

	// Button Margin.
	if ( ! empty( $options['margin'] ) ) {
		$style .= "margin: {$options['margin']};";
	}

	return $style;
}

/**
 * Generate internal style for HB Button Border.
 *
 * @since 6.0.0
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return string          Button internal CSS border.
 */
function mkhb_button_border( $options ) {
	$style = '';

	// Border Width, Style, and Color.
	if ( ! empty( $options['border-width'] ) && ! empty( $options['border-color'] ) ) {
		$border_widths = explode( ' ', $options['border-width'] );
		$border_colors = explode( ' ', $options['border-color'] );

		$style .= "
			border-top: {$border_widths[0]} solid {$border_colors[0]};
			border-right: {$border_widths[1]} solid {$border_colors[1]};
			border-bottom: {$border_widths[2]} solid {$border_colors[2]};
			border-left: {$border_widths[3]} solid {$border_colors[3]};
		";
	}

	return $style;
}

/**
 * Generate internal style for HB Button Link.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string         Button Link internal CSS.
 */
function mkhb_button_font_style( $options ) {
	$style = '';

	// Button Font Weight.
	if ( ! empty( $options['font-weight'] ) ) {
		$style .= "font-weight: {$options['font-weight']};";
	}

	// Button Font Size.
	if ( ! empty( $options['font-size'] ) ) {
		$style .= "font-size: {$options['font-size']};";
	}

	// Button Font Style.
	if ( ! empty( $options['font-style'] ) ) {
		$style .= "font-style: {$options['font-style']};";
	}

	// Button Font Family.
	if ( ! empty( $options['font-family'] ) ) {
		$style .= "font-family: {$options['font-family']};";
	}

	return $style;
}

/**
 * Generate internal style for HB Button Link Hover.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string         Button Link Hover internal CSS.
 */
function mkhb_button_hover( $options ) {
	// Button ID.
	$button_id = $options['id'];

	$style = "#$button_id .mkhb-button-el__link:hover {";

	// Hover Button Color.
	if ( ! empty( $options['hover-color'] ) ) {
		$style .= "color: {$options['hover-color']};";
	}

	// Hover Button Background Color.
	if ( ! empty( $options['hover-background-color'] ) ) {
		$style .= "background-color: {$options['hover-background-color']};";
	}

	$style .= '}';

	return $style;
}
