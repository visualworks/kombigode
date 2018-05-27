<?php
/**
 * Header Builder: mkhb_icon shortcode.
 *
 * @since 6.0.0
 * @package Header_Builder
 */

/**
 * HB Icon element shortcode.
 *
 * @since 6.0.0
 *
 * @param  array $atts All parameter will be used in the shortcode.
 * @return string $markup Rendered HTML.
 */
function mkhb_icon_shortcode( $atts ) {
	$options = shortcode_atts(
		array(
			'id' => 'mkhb-icon-1',
			'icon' => '',
			'url' => '',
			'alignment' => '',
			'display' => '',
			'size' => '16px',
			'color' => '',
			'box-background-color' => '',
			'box-border-radius' => '',
			'box-border-width' => '',
			'box-border-color' => '',
			'hover-color' => '',
			'hover-box-background-color' => '',
			'hover-box-border-color' => '',
			'padding' => '',
			'margin' => '',
			'alt' => 'Welcome on board',
			'target' => '_blank',
			'device' => 'desktop',
			'visibility' => 'desktop, tablet, mobile',
		),
		$atts
	);

	// Check if icon is should be displayed in current device or not.
	if ( ! mkhb_is_shortcode_displayed( $options['device'], $options['visibility'] ) ) {
		return '';
	}

	$markup = mkhb_icon_get_markup( $options );
	$style = mkhb_icon_get_style( $options );

	wp_register_style( 'mkhb', false, array( 'mkhb-grid' ) );
	wp_enqueue_style( 'mkhb' );
	wp_add_inline_style( 'mkhb', $style );

	return $markup;
}
add_shortcode( 'mkhb_icon', 'mkhb_icon_shortcode' );

/**
 * Generate the element's markup for use on the front-end.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string $markup Element HTML code.
 */
function mkhb_icon_get_markup( $options ) {
	$markup = sprintf(
		'<div id="%s" class="mkhb-icon-el"></div>',
		esc_attr( $options['id'] )
	);

	// Icon type and size.
	$icon = mkhb_get_icon_svg( $options['icon'], intval( $options['size'] ) );

	// Render only the container, if the icon is empty.
	if ( empty( $icon ) ) {
		return $markup;
	}

	// Icon anchor URL attribute.
	$url_attr = ( ! empty( $options['url'] ) ) ? 'href="' . esc_url( $options['url'] ) . '"' : '';
	$target = ( ! empty( $options['url'] ) && ! empty( $options['target'] ) ) ? 'target="' . esc_attr( $options['target'] ) . '"' : '';

	// Icon additional class.
	$icon_class = mkhb_shortcode_display_class( $options );

	// @todo Temporary Solution - Data Attribute for inline container.
	$data_attr = mkhb_shortcode_display_attr( $options );

	$markup = sprintf(
		'<div id="%s" class="mkhb-icon-el %s" %s>
			<a class="mkhb-icon-el__link" %s %s alt="%s">
				%s
			</a>
		</div>',
		esc_attr( $options['id'] ),
		esc_attr( $icon_class ),
		$data_attr,
		$target,
		$url_attr,
		esc_attr( $options['alt'] ),
		$icon
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
function mkhb_icon_get_style( $options ) {
	$style = '';

	// Icon display.
	$style = "#{$options['id']} {";
	if ( ! empty( $options['alignment'] ) ) {
		$style .= "text-align: {$options['alignment']};";
	}
	if ( ! empty( $options['display'] ) ) {
		if ( 'inline' === $options['display'] ) {
			$style .= 'display: inline-block; vertical-align: top;';
		}
	}
	$style .= '}';

	// Icon type and size.
	$icon = mkhb_get_icon_svg( $options['icon'], intval( $options['size'] ) );

	// Render only the container, if the icon is empty.
	if ( empty( $icon ) ) {
		return $style;
	}

	$style .= mkhb_get_icon_style( $options );
	$style .= mkhb_get_hover_style( $options );

	return $style;
}

/**
 * Generate the element's style for use on the front-end.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string $style Element CSS code.
 */
function mkhb_get_icon_style( $options ) {
	$style = '';

	// Icon colors.
	$icon_color = '';
	$box_bg_color = '';
	if ( ! empty( $options['color'] ) ) {
		$icon_color = "color: {$options['color']};";
	}
	if ( ! empty( $options['box-background-color'] ) ) {
		$box_bg_color = "background: {$options['box-background-color']};";
	}

	// Icon border.
	$box_border = mkhb_icon_border( $options );

	// Icon layout.
	$padding = '';
	$margin  = '';
	$border_radius = '';

	// Icon Padding.
	if ( ! empty( $options['padding'] ) ) {
		$padding .= "padding: {$options['padding']};";
	}

	// Icon Margin.
	if ( ! empty( $options['margin'] ) ) {
		$margin .= "margin: {$options['margin']};";
	}

	if ( ! empty( $options['box-border-radius'] ) ) {
		$border_radius .= "border-radius: {$options['box-border-radius']};";
	}

	// Icon Width and Height.
	$icon_size = mkhb_icon_get_size( $options );
	$style .= "
		#{$options['id']} .mkhb-icon-el__link {
			{$icon_size}
			{$box_bg_color}
			{$icon_color}
			{$padding}
			{$margin}
			{$border_radius}
			{$box_border}
		}
	";

	return $style;
}

/**
 * Generate the element's style for use on the front-end.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string $style Element CSS code.
 */
function mkhb_get_hover_style( $options ) {
	$style = '';

	$icon_hover_color = '';
	$box_hover_bg_color = '';
	$box_hover_border_col = '';
	if ( ! empty( $options['hover-color'] ) ) {
		$icon_hover_color = "color: {$options['hover-color']};";
	}
	if ( ! empty( $options['hover-box-background-color'] ) ) {
		$box_hover_bg_color = "background: {$options['hover-box-background-color']};";
	}
	if ( ! empty( $options['hover-box-border-color'] ) ) {
		$box_hover_border_col = "border-color: {$options['hover-box-border-color']};";
	}

	// Icon anchor URL attribute.
	$url_attr = ( ! empty( $options['url'] ) ) ? 'href="' . esc_url( $options['url'] ) . '"' : '';

	// Enable hover effect only if the URL is not empty.
	if ( ! empty( $url_attr ) ) {
		$style .= "
			#{$options['id']} .mkhb-icon-el__link:hover {
				{$icon_hover_color}
				{$box_hover_bg_color}
				{$box_hover_border_col}
			}
		";
	}

	return $style;
}

/**
 * Generate the element's style for use on the front-end.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string          Icon internal CSS width & height.
 */
function mkhb_icon_get_size( $options ) {
	// Icon box layout.
	$box_height = ( ! empty( $options['size'] ) ) ? intval( $options['size'] ) : 0;
	$box_width = ( ! empty( $options['size'] ) ) ? intval( $options['size'] ) : 0;
	if ( ! empty( $options['box-border-width'] ) && ! empty( $options['padding'] ) ) {
		$border_widths = explode( ' ', $options['box-border-width'] );
		$padding_widths = explode( ' ', $options['padding'] );

		$border_offset['height'] = intval( $border_widths[0] ) + intval( $border_widths[2] );
		$border_offset['width']  = intval( $border_widths[1] ) + intval( $border_widths[3] );
		$padding_offset['height'] = intval( $padding_widths[0] ) + intval( $padding_widths[2] );
		$padding_offset['width']  = intval( $padding_widths[1] ) + intval( $padding_widths[3] );
		$box_height = intval( $options['size'] ) + $border_offset['height'] + $padding_offset['height'];
		$box_width = intval( $options['size'] ) + $border_offset['width'] + $padding_offset['width'];
	}

	$icon_size = "height: {$box_height}px;width: {$box_width}px;";

	return $icon_size;
}


/**
 * Return icon SVG.
 *
 * @since 6.0.0
 *
 * @param string $icon_name Icon name.
 * @param string $icon_size Icon size.
 * @return string           Icon SVG.
 */
function mkhb_get_icon_svg( $icon_name, $icon_size ) {
	// If the icon type is empty or not array, return null and don't render the element.
	if ( empty( $icon_name ) ) {
		return '';
	}

	$icon_class = ( ! empty( $icon_name ) ) ? $icon_name : '';

	/*
	 * Mk_SVG_Icons is a class from Jupiter package. HB - Icon will use it to load the SVG
	 * icon based on the class name. Make sure this class is exist.
	 */
	if ( ! class_exists( 'Mk_SVG_Icons' ) ) {
		require_once THEME_HELPERS . '/svg-icons.php';
	}

	$mk_svg = new Mk_SVG_Icons();
	$icon = $mk_svg::get_svg_icon_by_class_name( false, $icon_class, (int) $icon_size );

	return $icon;
}

/**
 * Generate internal style for HB Row Border.
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return string          Row internal CSS border.
 */
function mkhb_icon_border( $options ) {
	$style = '';

	// Border Width, Style, and Color.
	if ( ! empty( $options['box-border-width'] ) && ! empty( $options['box-border-color'] ) ) {
		$border_widths = explode( ' ', $options['box-border-width'] );
		$border_colors = explode( ' ', $options['box-border-color'] );

		$style .= "
			border-top: {$border_widths[0]} solid {$border_colors[0]};
			border-right: {$border_widths[1]} solid {$border_colors[1]};
			border-bottom: {$border_widths[2]} solid {$border_colors[2]};
			border-left: {$border_widths[3]} solid {$border_colors[3]};
		";
	}

	return $style;
}
