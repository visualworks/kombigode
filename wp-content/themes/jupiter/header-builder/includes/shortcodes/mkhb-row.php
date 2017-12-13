<?php
/**
 * Header Builder: mkhb_row shortcode.
 *
 * @since 6.0.0
 * @package Header_Builder
 */

/**
 * HB Row element shortcode.
 *
 * @since 6.0.0
 *
 * @param  array  $atts    All parameter will be used in the shortcode.
 * @param  string $content Content inside Row shortcode.
 * @return string          Rendered HTML.
 */
function mkhb_row_shortcode( $atts, $content ) {
	$options = shortcode_atts(
		array(
			'id' => 'mkhb-row-1',
			'width' => 'full',
			'margin' => '',
			'padding' => '',
			'border-width' => '',
			'border-color' => '',
			'background-color' => '#ffffff',
			'background-image' => '',
			'background-repeat' => '',
			'background-position' => '',
			'background-attachment' => '',
			'sequence' => '0,2,1',
			'device' => 'desktop',
			'visibility' => 'desktop, tablet, mobile',
		),
		$atts
	);

	// Check if the row is empty or it should be displayed in current device or not.
	if ( ! mkhb_is_shortcode_displayed( $options['device'], $options['visibility'] ) || empty( $content ) ) {
		return '';
	}

	// Row ID.
	$row_id = $options['id'];

	$style = '';

	// Row Container Width.
	$fixed_container = '';
	if ( 'fixed' === $options['width'] ) {
		// Because we don't have any default value for fixed. We use mk_options grid_width.
		global $mk_options;
		$grid_width = 1140;
		if ( ! empty( $mk_options['grid_width'] ) ) {
			$grid_width = $mk_options['grid_width'] + 60;
		}
		$style .= "#$row_id > .mkhb-row__container { max-width: {$grid_width}px; }";
		$fixed_container = '.mkhb-row__container';
	}

	$style .= "#$row_id {$fixed_container} {";

	// Row Margin and Padding Style.
	$style .= mkhb_row_layout( $options );

	// Row Border Style.
	$style .= mkhb_row_border( $options );

	$style .= '}';

	$style .= "#$row_id {";
	// Row Background Style.
	$style .= mkhb_row_background( $options );
	$style .= '}';

	// Additional Class.
	$row_class = '';
	if ( ! empty( $options['background-image'] ) ) {
		$row_class = 'mkhb-row--bg-image';
	}

	$markup = sprintf(
		'<div id="%s" class="mkhb-row mkhb-equal-height-columns %s">
			<div class="%s">
				%s
			</div>
			<div class="clearfix"></div>
		</div>',
		$row_id,
		esc_attr( $row_class ),
		esc_attr( 'mkhb-row__container' ),
		do_shortcode( $content )
	);

	// @todo: wp_add_inline_style can't be used for shortcode. Temporary fix.
	wp_register_style( 'mkhb', false, array() );
	wp_enqueue_style( 'mkhb' );
	wp_add_inline_style( 'mkhb', $style );

	return $markup;
}
add_shortcode( 'mkhb_row', 'mkhb_row_shortcode' );

/**
 * Generate internal style for HB Row Background.
 *
 * @since 6.0.0
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string         Row internal CSS background.
 */
function mkhb_row_background( $options ) {
	$style = '';

	// Row Background Color.
	if ( ! empty( $options['background-color'] ) && mkhb_row_sequence( $options ) ) {
		$style .= "background-color: {$options['background-color']};";
	}

	// Row Background Image.
	if ( ! empty( $options['background-image'] ) ) {
		$bg_images = explode( ';', $options['background-image'] );
		foreach ( $bg_images as $bg_image ) {
			if ( ! empty( $bg_image ) ) {
				$style .= "background-image: {$bg_image};";
			}
		}
	}

	// Row Background Repeat.
	if ( ! empty( $options['background-repeat'] ) ) {
		$style .= "background-repeat: {$options['background-repeat']};";
	}

	// Row Background Position.
	if ( ! empty( $options['background-position'] ) ) {
		$style .= "background-position: {$options['background-position']};";
	}

	// Row Background Attachment.
	if ( ! empty( $options['background-attachment'] ) ) {
		$style .= "background-attachment: {$options['background-attachment']};";
	}

	return $style;
}

/**
 * Generate internal style for HB Row Border.
 *
 * @since 6.0.0
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return string          Row internal CSS border.
 */
function mkhb_row_border( $options ) {
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
 * Get current row sequence status.
 *
 * @since 6.0.0
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return boolean         Current sequence status.
 */
function mkhb_row_sequence( $options ) {
	// Row sequence status.
	$sequence = 0;
	if ( ! empty( $options['sequence'] ) ) {
		$sequence_raw = explode( ',', $options['sequence'] );
		$sequence = absint( end( $sequence_raw ) );
	}

	return $sequence;
}

/**
 * Generate internal style for HB Row Layout.
 *
 * @since 6.0.0
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return string          Button internal CSS margin and padding.
 */
function mkhb_row_layout( $options ) {
	$style = '';

	// Row Padding.
	if ( ! empty( $options['padding'] ) ) {
		$style .= "padding: {$options['padding']};";
	}

	// Row Margin.
	if ( ! empty( $options['margin'] ) ) {
		$style .= "margin: {$options['margin']};";

		if ( 'fixed' === $options['width'] ) {
			$style .= 'margin-left: auto;';
			$style .= 'margin-right: auto;';
		}
	}

	return $style;
}
