<?php
/**
 * Header Builder: mkhb_textbox shortcode.
 *
 * @since 6.0.0
 * @package Header_Builder
 */

/**
 * HB Textbox element shortcode.
 *
 * @since 6.0.0
 *
 * @param  array  $atts All parameter will be used in the shortcode.
 * @param  string $content The enclosed content.
 * @return string $markup Rendered HTML.
 */
function mkhb_textbox_shortcode( $atts, $content = null ) {
	$options = shortcode_atts(
		array(
			'id' => 'mkhb-textbox-1',
			'alignment' => '',
			'color' => '',
			'display' => '',
			'font-family' => 'Open Sans',
			'font-type' => 'google',
			'font-size' => '',
			'font-style' => '',
			'font-weight' => '',
			'hover-color' => '',
			'href' => '',
			'line-height' => '',
			'margin' => '',
			'padding' => '',
			'target' => '_blank',
			'type' => 'plain',
			'device' => 'desktop',
			'visibility' => 'desktop, tablet, mobile',
		),
		$atts
	);

	// Check if textbox is should be displayed in current device or not.
	if ( ! mkhb_is_shortcode_displayed( $options['device'], $options['visibility'] ) ) {
		return '';
	}

	$markup = mkhb_textbox_get_markup( $options, $content );
	$style = mkhb_textbox_get_style( $options );

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
add_shortcode( 'mkhb_textbox', 'mkhb_textbox_shortcode' );

/**
 * Generate the element's markup for use on the front-end.
 *
 * @since 6.0.0
 *
 * @param  array  $options All options will be used in the shortcode.
 * @param  string $content The enclosed content.
 * @return string $markup Element HTML code.
 */
function mkhb_textbox_get_markup( $options, $content ) {
	$markup  = '';

	// Textbox input_text.
	$prefix = array(
		'plain' => '',
		'phone' => 'tel:',
		'email' => 'mailto:',
	);

	$link = '';
	$target = '';
	$smooth_scroll = '';
	if ( ! empty( $options['href'] ) ) {
		// Textbox input type.
		$text_type = ( ! empty( $options['type'] ) ) ? $options['type'] : 'plain';

		// Textbox URL target.
		if ( 'plain' === $text_type && ! empty( $options['target'] ) ) {
			$target = 'target = "' . $options['target'] . '"';
		}

		// Textbox class for smooth scroll, only on #link.
		if ( null !== wp_parse_url( $options['href'], PHP_URL_FRAGMENT ) ) {
			$smooth_scroll = 'js-smooth-scroll';
		}

		$url = $prefix[ $text_type ] . $options['href'];
		$link = 'href = "' . esc_url( $url ) . '"';
	}

	// Textbox font family.
	$font_family = '';
	if ( ! empty( $options['font-family'] ) ) {
		$font_family = 'font-family: ' . $options['font-family'] . ';';
	}

	// Textbox additional class.
	$textbox_class = mkhb_shortcode_display_class( $options );

	// @todo Temporary Solution - Data Attribute for inline container.
	$data_attr = mkhb_shortcode_display_attr( $options );

	/*
	 * Textbox text. According to http://www.json.org/, we cannot have the
	 * \n "character" in JSON data. We can only thus store new line
	 * characters as, well the \n "string", which makes it indistinguishable
	 * from any \n "string" directly entered by the user. For this reason,
	 * we need to encode our strings in base64 to circumvent this problem.
	 */
	$text = htmlentities( rawurldecode( base64_decode( strip_tags( $content ), true ) ), ENT_COMPAT, 'UTF-8' );

	$markup = sprintf( '
		<div id="%s" class="mkhb-textbox-el %s" %s>
			<a %s class="mkhb-textbox-el__link %s" %s>%s</a>
		</div>',
		esc_attr( $options['id'] ),
		esc_attr( $textbox_class ),
		$data_attr,
		$link,
		$smooth_scroll,
		$target,
		// We do not support HTML in the text area except for the break tag.
		// Supporting HTML here implies we need to render the CSS attached
		// to any "class" attribute. This means both CSS included in
		// Jupiter, any any custom CSS you may have in Theme Options. A of
		// this writing that is out of scope.
		preg_replace( '/&lt;br\W*?\&gt;/i', '<br>', esc_html( $text ) )
	);

	return $markup;
}

/**
 * Generate the element's style for use on the front-end.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string $style Element CSS code.
 */
function mkhb_textbox_get_style( $options ) {
	$style = '';
	$textbox_id = $options['id'];

	// Textbox inline, align, margin and padding.
	$style .= "#{$textbox_id} {";
	$display = '';
	$text_align = '';
	if ( ! empty( $options['display'] ) ) {
		if ( 'inline' === $options['display'] ) {
			$display .= 'display: inline-block; vertical-align: top;';
		}
	}
	if ( ! empty( $options['alignment'] ) ) {
		$text_align .= "text-align: {$options['alignment']};";
	}

	// Text Padding.
	if ( ! empty( $options['padding'] ) ) {
		$style .= "padding: {$options['padding']};";
	}

	// Text Margin.
	if ( ! empty( $options['margin'] ) ) {
		$style .= "margin: {$options['margin']};";
	}

	$style .= $display;
	$style .= $text_align;
	$style .= '}';
	$style .= mkhb_textbox_get_link_style( $options );

	return $style;
}

/**
 * Generate the element's style for textbox link on the front-end.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string $style Element CSS code.
 */
function mkhb_textbox_get_link_style( $options ) {
	$style = '';
	$textbox_id = $options['id'];

	// Textbox font size, font weight, font style, font family
	// color and line height.
	$style .= "#{$textbox_id} .mkhb-textbox-el__link {";
	$font_color = '';
	$font_family = '';
	$font_size = '';
	$font_style = '';
	$font_weight = '';
	$line_height = '';
	if ( ! empty( $options['color'] ) ) {
		$font_color .= "color: {$options['color']};";
	}
	if ( ! empty( $options['font-family'] ) ) {
		$font_family .= "font-family: {$options['font-family']};";
	}
	if ( ! empty( $options['font-size'] ) ) {
		$font_size .= "font-size: {$options['font-size']};";
	}
	if ( ! empty( $options['font-style'] ) ) {
		$font_style .= "font-style: {$options['font-style']};";
	}
	if ( ! empty( $options['font_weight'] ) ) {
		$font_weight .= "font-weight: {$options['font-weight']};";
	}
	if ( ! empty( $options['line-height'] ) ) {
		$line_height .= "line-height: {$options['line-height']};";
	}
	$style .= $font_color;
	$style .= $font_family;
	$style .= $font_size;
	$style .= $font_style;
	$style .= $font_weight;
	$style .= $line_height;
	$style .= '}';

	// Textbox color hover.
	$style .= "#{$textbox_id} .mkhb-textbox-el__link[href]:hover {";
	if ( ! empty( $options['hover-color'] ) ) {
		$style .= "color: {$options['hover-color']};";
	}
	$style .= '}';

	return $style;
}
