<?php
/**
 * Header Builder: mkhb_social shortcode.
 *
 * @since 6.0.0
 * @package Header_Builder
 */

/**
 * HB Social element shortcode.
 *
 * @since 6.0.0
 *
 * @param  array $atts All parameter will be used in the shortcode.
 * @return string $markup Rendered HTML.
 */
function mkhb_social_shortcode( $atts ) {
	$options = shortcode_atts(
		array(
			'id' => 'mkhb-social-1',
			'icon-value' => '',
			'icon-link' => '',
			'target' => '_self',
			'alignment' => '',
			'display' => '',
			'size' => '16px',
			'color' => '',
			'background-color' => '',
			'border-radius' => '',
			'hover-color' => '',
			'hover-background-color' => '',
			'hover-border-color' => '',
			'border-width' => '',
			'border-color' => '',
			'space-between-icons' => '',
			'padding' => '5 5 5 5',
			'margin' => '',
			'device' => 'desktop',
			'visibility' => 'desktop, tablet, mobile',
		),
		$atts
	);

	// Check if social icon is should be displayed in current device or not.
	if ( ! mkhb_is_shortcode_displayed( $options['device'], $options['visibility'] ) ) {
		return '';
	}

	$markup = mkhb_social_get_markup( $options );
	$style = mkhb_social_get_style( $options );

	wp_register_style( 'mkhb', false, array( 'mkhb-grid' ) );
	wp_enqueue_style( 'mkhb' );
	wp_add_inline_style( 'mkhb', $style );

	return $markup;
}
add_shortcode( 'mkhb_social_media', 'mkhb_social_shortcode' );

/**
 * Generate the element's markup for use on the front-end.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string $markup Element HTML code.
 */
function mkhb_social_get_markup( $options ) {
	$markup = sprintf(
		'<div id="%s" class="mkhb-social-media-el"></div>',
		esc_attr( $options['id'] )
	);

	// Render icons.
	$icons = mkhb_render_icons( $options );

	// Render only the container, if the icons are empty.
	if ( empty( $icons ) ) {
		return $markup;
	}

	// Social Network additional class.
	$social_class = mkhb_shortcode_display_class( $options );

	// @todo Temporary Solution - Data Attribute for inline container.
	$data_attr = mkhb_shortcode_display_attr( $options );

	$markup = sprintf(
		'<div id="%s" class="mkhb-social-media-el %s" %s>%s</div>',
		esc_attr( $options['id'] ),
		esc_attr( $social_class ),
		$data_attr,
		$icons
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
function mkhb_social_get_style( $options ) {
	$style = '';

	// Social Media layout.
	$style = "#{$options['id']} {";
	if ( ! empty( $options['alignment'] ) ) {
		$style .= "text-align: {$options['alignment']};";
	}

	// Social Media Margin.
	if ( ! empty( $options['margin'] ) ) {
		$style .= "margin: {$options['margin']};";
	}

	if ( ! empty( $options['display'] ) ) {
		if ( 'inline' === $options['display'] ) {
			$style .= 'display: inline-block; vertical-align: top;';
		}
	}
	$style .= '}';

	// Render icons.
	$icons = mkhb_render_icons( $options );

	// Render only the container, if the icons are empty.
	if ( empty( $icons ) ) {
		return $style;
	}

	$style .= mkhb_get_social_style( $options );
	$style .= mkhb_social_hover_style( $options );

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
function mkhb_get_social_style( $options ) {
	$style = '';

	// Social media margin.
	$space = '';
	if ( ! empty( $options['space-between-icons'] ) ) {
		$space = 'margin-right: ' . $options['space-between-icons'] . ';';
	}

	// Social media colors.
	$icon_color = '';
	$box_bg_color = '';
	if ( ! empty( $options['color'] ) ) {
		$icon_color = "color: {$options['color']};";
	}
	if ( ! empty( $options['background-color'] ) ) {
		$box_bg_color = "background: {$options['background-color']};";
	}

	// Social media layout.
	$padding = '';
	$margin  = '';
	$border_radius = '';

	// Social media Padding.
	if ( ! empty( $options['padding'] ) ) {
		$padding = "padding: {$options['padding']};";
	}

	// Social media Margin.
	if ( ! empty( $options['margin'] ) ) {
		$margin = "margin: {$options['margin']};";
	}

	if ( ! empty( $options['border-radius'] ) ) {
		$border_radius .= "border-radius: {$options['border-radius']};";
	}

	// Social media border.
	$box_border = mkhb_social_border( $options );

	// Social media Width and Height.
	$icon_size = mkhb_social_get_size( $options );

	$style .= "
		#{$options['id']} .mkhb-icon-el__link {
			{$icon_color}
			{$icon_size}
			{$box_bg_color}
			{$padding}
			{$border_radius};
			{$box_border}
			{$space}
		}
		#{$options['id']} .mkhb-icon-el {
			display: inline-block;
		}
		#{$options['id']} .mkhb-icon-el:last-child .mkhb-icon-el__link {
			margin-right: 0px;
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
function mkhb_social_hover_style( $options ) {
	$style = '';

	$icon_hover_color = '';
	$box_hover_bg_color = '';
	$box_hover_border_col = '';
	if ( ! empty( $options['hover-color'] ) ) {
		$icon_hover_color = "color: {$options['hover-color']};";
	}
	if ( ! empty( $options['hover-background-color'] ) ) {
		$box_hover_bg_color = "background: {$options['hover-background-color']};";
	}
	if ( ! empty( $options['hover-border-color'] ) ) {
		$box_hover_border_col = "border-color: {$options['hover-border-color']};";
	}

	$style .= "
		#{$options['id']} .mkhb-icon-el__link--hoverable:hover {
			{$icon_hover_color}
			{$box_hover_bg_color}
			{$box_hover_border_col}
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
 * @return string          Icon internal CSS width & height.
 */
function mkhb_social_get_size( $options ) {
	// Icon box layout.
	$box_height = ( ! empty( $options['size'] ) ) ? intval( $options['size'] ) : 0;
	$box_width = ( ! empty( $options['size'] ) ) ? intval( $options['size'] ) : 0;
	if ( ! empty( $options['border-width'] ) && ! empty( $options['margin'] ) && ! empty( $options['padding'] ) ) {
		$border_widths = explode( ' ', $options['border-width'] );
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
 * Generate internal style for HB Row Border.
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return string          Row internal CSS border.
 */
function mkhb_social_border( $options ) {
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
 * Render list of elements including target, icons, and the URL.
 *
 * @since 6.0.0 Change the access to private, add parameters to pass icon_names and icon_size.
 *
 * @param array $options  All options will be used in the shortcode.
 * @return string            Generated icons in HTML format.
 */
function mkhb_render_icons( $options ) {
	// Render all social media icons.
	$icon_markup = '';
	if ( ! empty( $options['icon-link'] ) && ! empty( $options['icon-value'] ) ) {
		/*
		 * Mk_SVG_Icons is a class from Jupiter package. HB - Social Media will use it to load the SVG
		 * icon based on the class name. Make sure this class is exist.
		 */
		if ( ! class_exists( 'Mk_SVG_Icons' ) ) {
			require_once THEME_HELPERS . '/svg-icons.php';
		}

		$mk_svg = new Mk_SVG_Icons();

		$icon_links = explode( ',', $options['icon-link'] );
		$icon_values = explode( ',', $options['icon-value'] );
		$icon_size = intval( $options['size'] );

		foreach ( $icon_links as $index => $link ) {
			$icon = '';

			/*
			 * In the old data structures, the $property only have single value with string contain the
			 * class name. Right now, it's an array with 'link' and 'value' keys inside. So, we need to
			 * check it's exist and empty or not because $prop_link and $prop_value are required for the
			 * next step.
			 */
			$prop_value = ( ! empty( $icon_values[ $index ] ) ) ? $icon_values[ $index ] : '';

			// The correct class name is required. We can't call empty SVG icon here.
			$class_name = get_icon_class( $prop_value );
			$icon = $mk_svg::get_svg_icon_by_class_name( false, $class_name, (int) $icon_size );

			// $icon should not be empty. Don't render the icon if it's empty.
			if ( empty( $icon ) ) {
				continue;
			}

			$icon_markup .= mkhb_render_icon( $options, $icon, $link );

		} // End foreach().
	} // End if().

	return $icon_markup;
}

/**
 * Render individual element including target, icons, and the URL.
 *
 * @since 6.0.0 Change the access to private, add parameters to pass icon_names and icon_size.
 *
 * @param array  $options  All options will be used in the shortcode.
 * @param string $icon Icon name.
 * @param string $link Icon url.
 * @return string            Generated icons in HTML format.
 */
function mkhb_render_icon( $options, $icon, $link ) {
	$icon_markup = '';

	// Icon anchor URL attribute.
	$prop_link = '';
	$prop_target = '';
	$prop_hover_class = '';
	if ( ! empty( $link ) && ( '' !== $link ) ) {
		$prop_link = 'href="' . esc_url( $link ) . '"';
		$prop_target = ( ! empty( $options['target'] ) ) ? 'target="' . esc_attr( $options['target'] ) . '"' : '';
		$prop_hover_class = 'mkhb-icon-el__link--hoverable';
	}

	$icon_markup .= sprintf(
		'<div class="mkhb-icon-el">
			<a class="mkhb-icon-el__link %s" %s %s>
				%s
			</a>
		</div>',
		esc_attr( $prop_hover_class ),
		$prop_target,
		$prop_link,
		$icon
	);

	return $icon_markup;
}

/**
 * Return icon class name.
 *
 * @since 6.0.0 Change the access to private, add conditional logic to prevent empty key.
 *
 * @param string $key Icon name.
 * @return string Icon class name.
 */
function get_icon_class( $key ) {
	// If key is empty, return ''.
	if ( empty( $key ) ) {
		return '';
	}

	$icons = array(
		'px' => 'mk-jupiter-icon-px',
		'aim' => 'mk-jupiter-icon-aim',
		'amazon' => 'mk-jupiter-icon-amazon',
		'apple' => 'mk-icon-apple',
		'bebo' => 'mk-jupiter-icon-bebo',
		'behance' => 'mk-icon-behance',
		'blogger' => 'mk-moon-blogger',
		'delicious' => 'mk-icon-delicious',
		'deviantart' => 'mk-icon-deviantart',
		'digg' => 'mk-icon-digg',
		'dribbble' => 'mk-icon-dribbble',
		'dropbox' => 'mk-icon-dropbox',
		'envato' => 'mk-jupiter-icon-envato',
		'facebook' => 'mk-icon-facebook',
		'flickr' => 'mk-icon-flickr',
		'github' => 'mk-icon-github',
		'google' => 'mk-icon-google',
		'googleplus' => 'mk-icon-google-plus',
		'lastfm' => 'mk-icon-lastfm',
		'linkedin' => 'mk-icon-linkedin',
		'instagram' => 'mk-icon-instagram',
		'myspace' => 'mk-jupiter-icon-myspace',
		'path' => 'mk-icon-meanpath',
		'pinterest' => 'mk-icon-pinterest',
		'reddit' => 'mk-icon-reddit',
		'rss' => 'mk-icon-rss',
		'skype' => 'mk-icon-skype',
		'stumbleupon' => 'mk-icon-stumbleupon',
		'tumblr' => 'mk-icon-tumblr',
		'twitter' => 'mk-icon-twitter',
		'vimeo' => 'mk-moon-vimeo',
		'wordpress' => 'mk-icon-wordpress',
		'yahoo' => 'mk-icon-yahoo',
		'yelp' => 'mk-icon-yelp',
		'youtube' => 'mk-icon-youtube',
		'xing' => 'mk-icon-xing',
		'imdb' => 'mk-jupiter-icon-imdb',
		'qzone' => 'mk-jupiter-icon-qzone',
		'renren' => 'mk-icon-renren',
		'vk' => 'mk-icon-vk',
		'wechat' => 'mk-icon-wechat',
		'weibo' => 'mk-icon-weibo',
		'whatsapp' => 'mk-jupiter-icon-whatsapp',
		'soundcloud' => 'mk-icon-soundcloud',
		'creativemarket' => 'mk-jupiter-icon-creative-market',
		'ebay' => 'mk-jupiter-icon-ebay',
		'etsy' => 'mk-jupiter-icon-etsy',
		'slack' => 'mk-jupiter-icon-slack',
		'snapchat' => 'mk-jupiter-icon-snapchat',
		'spotify' => 'mk-jupiter-icon-spotify',
		'strava' => 'mk-jupiter-icon-strava',
		'telegram' => 'mk-jupiter-icon-telegram',
		'tripadvisor' => 'mk-jupiter-icon-tripadvisor',
		'zillow' => 'mk-jupiter-icon-zillow',
		'zomato' => 'mk-jupiter-icon-zomato',
	);

	// Make sure the $key is exist in icon class name list. The default is empty string.
	$icon_class_name = '';
	if ( array_key_exists( $key, $icons ) ) {
		$icon_class_name = $icons[ $key ];
	}

	return $icon_class_name;
}
