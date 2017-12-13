<?php
/**
 * Header Builder: mkhb_logo shortcode.
 *
 * @since 6.0.0
 * @package Header_Builder
 */

/**
 * HB Logo element shortcode.
 *
 * @since 6.0.0
 *
 * @param  array $atts All parameter will be used in the shortcode.
 * @return string      Rendered HTML.
 */
function mkhb_logo_shortcode( $atts ) {
	$options = shortcode_atts(
		array(
			'id' => 'mkhb-logo-1',
			'alignment' => '',
			'display' => '',
			'margin' => '',
			'padding' => '',
			'width' => '',
			'home' => '1',
			'theme' => 'dark',
			'workspace' => 'normal',
			'device' => 'desktop',
			'visibility' => 'desktop, tablet, mobile',
		),
		$atts
	);

	// Check if logo is should be displayed in current device or not.
	if ( ! mkhb_is_shortcode_displayed( $options['device'], $options['visibility'] ) ) {
		return '';
	}

	// Logo image source and blog/site description.
	$logo_src = mkhb_logo_image_src( $options['theme'], $options['workspace'] );
	$description = get_bloginfo( 'description' );

	// Set Logo inline style.
	$style = mkhb_logo_style( $options, $logo_src );

	// Logo ID.
	$logo_id = $options['id'];

	// Logo link homepage.
	$link = '';
	if ( '1' === $options['home'] ) {
		$link = 'href="' . get_home_url() . '"';
	}

	// Logo additional class.
	$logo_class = mkhb_shortcode_display_class( $options );

	// @todo Temporary Solution - Data Attribute for inline container.
	$data_attr = mkhb_shortcode_display_attr( $options );

	$markup = sprintf( '
		<div id="%s" class="mkhb-logo-el %s" %s>
			<a %s class="mkhb-logo-el__link">
				<img class="mkhb-logo-el__image" title="%s" alt="%s" src="%s"/>
			</a>
		</div>',
		esc_attr( $logo_id ),
		esc_attr( $logo_class ),
		$data_attr,
		$link,
		esc_attr( $description ),
		esc_attr( $description ),
		esc_url( $logo_src )
	);

	// @todo: wp_add_inline_style can't be used for shortcode. Temporary fix.
	wp_register_style( 'mkhb', false, array( 'mkhb-grid' ) );
	wp_enqueue_style( 'mkhb' );
	wp_add_inline_style( 'mkhb', $style );

	return $markup;
}
add_shortcode( 'mkhb_logo', 'mkhb_logo_shortcode' );

/**
 * Generate inline style for HB Logo.
 *
 * @param  array $options  All options will be used in the shortcode.
 * @param  array $logo_src Logo image source.
 * @return string          Logo inline CSS.
 */
function mkhb_logo_style( $options, $logo_src ) {
	// Logo ID.
	$logo_id = $options['id'];

	$style = "#$logo_id {";

	// Logo Padding.
	if ( ! empty( $options['padding'] ) ) {
		$style .= "padding: {$options['padding']};";
	}

	// Logo Margin.
	if ( ! empty( $options['margin'] ) ) {
		$style .= "margin: {$options['margin']};";
	}

	// Logo Alignment.
	if ( ! empty( $options['alignment'] ) ) {
		$style .= "text-align: {$options['alignment']};";
	}

	$style .= '}';

	// If user not set the width and use for Jupiter logo from Jupiter theme itself.
	$logo_default = THEME_IMAGES . '/jupiter-logo.png';
	if ( '' === $options['width'] && $logo_default === $logo_src ) {
		$options['width'] = '200';
	}

	// Logo width.
	if ( ! empty( $options['width'] ) ) {
		$style .= "#$logo_id .mkhb-logo-el__image { width: {$options['width']}; }";
	}

	return $style;
}

/**
 * Get logo image based on Header Workplace and Device.
 *
 * @since 6.0.0
 *
 * @param  string $logo_theme Logo key used.
 * @param  string $workspace  Workspace used.
 * @return string Logo image source URL.
 */
function mkhb_logo_image_src( $logo_theme, $workspace ) {
	global $mk_options;

	$logo_default = THEME_IMAGES . '/jupiter-logo.png';
	$logo_src = $logo_default;

	// Normal logo list.
	$logo_types = array(
		'dark' => 'logo',
		'sticky' => 'sticky_header_logo',
		'light' => 'light_header_logo',
		'mobile' => 'responsive_logo',
	);

	$logo_types['dark'] = ( 'sticky' === $workspace ) ? 'sticky_header_logo' : 'logo' ;

	// Check current workspace and set correct logo.
	$logo_src = ( ! empty( $mk_options[ $logo_types[ $logo_theme ] ] ) ) ? $mk_options[ $logo_types[ $logo_theme ] ] : $logo_default;

	// Check if current page override HB logo source.
	if ( ! mkhb_is_override_by_styling() ) {
		return $logo_src;
	}

	$logo_types['light'] = 'light_logo';

	// Check overriden logo source.
	global $post;
	$logo_src_over = get_post_meta( $post->ID, $logo_types[ $logo_theme ], true );
	if ( ! empty( $logo_src_over ) ) {
		return $logo_src_over;
	}

	return $logo_src;
}
