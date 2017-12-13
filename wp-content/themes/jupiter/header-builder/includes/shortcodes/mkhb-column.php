<?php
/**
 * Header Builder: mkhb_column shortcode.
 *
 * @since 6.0.0
 * @package Header_Builder
 */

/**
 * HB Column element shortcode.
 *
 * @since 6.0.0
 *
 * @param  array  $atts    All parameter will be used in the shortcode.
 * @param  string $content Content inside Column shortcode.
 * @return string          Rendered HTML.
 */
function mkhb_column_shortcode( $atts, $content ) {
	$options = shortcode_atts(
		array(
			'id' => 'mkhb-col-1',
			'vertical-alignment' => '',
			'margin' => '',
			'padding' => '',
			'border-width' => '',
			'border-color' => '',
			'background-color' => '',
			'background-image' => '',
			'background-repeat' => '',
			'background-position' => '',
			'background-attachment' => '',
			'width' => '12',
			'device' => 'desktop',
			'sequence' => '0,2,0',
		),
		$atts
	);

	// If the column widht is 0, don't render it.
	if ( 0 === $options['width'] ) {
		return '';
	}

	// If the content is not empty, run do_shortcode.
	$content_rendered = '';
	if ( ! empty( $content ) ) {
		$content_rendered = do_shortcode( $content );
	}

	// Column ID.
	$column_id = $options['id'];

	$style = "#$column_id {";

	// Column Margin and Padding Style.
	$style .= mkhb_column_layout( $options );

	// Column Border Style.
	$style .= mkhb_column_border( $options );

	// Column Background Style.
	$style .= mkhb_column_background( $options );

	// Additional Class.
	$column_class = '';

	if ( ! empty( $options['vertical-alignment'] ) ) {
		$style .= "vertical-align: {$options['vertical-alignment']};";
		$column_class .= 'mkhb-col--align-' . $options['vertical-alignment'];
	}

	$style .= '}';

	// Column Width Offset.
	$style .= mkhb_column_offset( $options );

	if ( ! empty( $options['background-image'] ) ) {
		$column_class .= ' mkhb-col--bg-image';
	}

	// Devices class name prefix.
	$prefix = array(
		'mobile' => 'xs',
		'tablet' => 'sm',
		'desktop' => 'md',
	);

	// Suffix class for container display.
	$suffix_class = '';
	if ( strpos( $content, 'display="inline"' ) || strpos( $content, "display='inline'" ) ) {
		$suffix_class = '-inline';
	}

	$markup = sprintf( '
		<div id="%s" class="mkhb-col mkhb-col-%s %s">
			<div class="mkhb-col__container%s">%s</div>
		</div>',
		esc_attr( $column_id ),
		esc_attr( $prefix[ $options['device'] ] . '-' . $options['width'] ),
		esc_attr( $column_class ),
		esc_attr( $suffix_class ),
		$content_rendered
	);

	// @todo: wp_add_inline_style can't be used for shortcode. Temporary fix.
	wp_register_style( 'mkhb', false, array( 'mkhb-grid' ) );
	wp_enqueue_style( 'mkhb' );
	wp_add_inline_style( 'mkhb', $style );

	return $markup;
}
add_shortcode( 'mkhb_col', 'mkhb_column_shortcode' );

/**
 * Generate internal style for HB Column Background.
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string         Column internal CSS background.
 */
function mkhb_column_background( $options ) {
	$style = '';

	// Column Background Color.
	if ( ! empty( $options['background-color'] ) && mkhb_column_sequence( $options ) ) {
		$style .= "background-color: {$options['background-color']};";
	}

	// Column Background Image.
	if ( ! empty( $options['background-image'] ) ) {
		$bg_images = explode( ';', $options['background-image'] );
		foreach ( $bg_images as $bg_image ) {
			if ( ! empty( $bg_image ) ) {
				$style .= "background-image: {$bg_image};";
			}
		}
	}

	// Column Background Repeat.
	if ( ! empty( $options['background-repeat'] ) ) {
		$style .= "background-repeat: {$options['background-repeat']};";
	}

	// Column Background Position.
	if ( ! empty( $options['background-position'] ) ) {
		$style .= "background-position: {$options['background-position']};";
	}

	// Column Background Attachment.
	if ( ! empty( $options['background-attachment'] ) ) {
		$style .= "background-attachment: {$options['background-attachment']};";
	}

	return $style;
}

/**
 * Generate internal style for HB Column Layout.
 *
 * @since 6.0.0
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return string          Column internal CSS margin and padding.
 */
function mkhb_column_layout( $options ) {
	$style = '';

	// Column Padding.
	if ( ! empty( $options['padding'] ) ) {
		$style .= "padding: {$options['padding']};";
	}

	// Column Margin.
	if ( ! empty( $options['margin'] ) ) {
		$style .= "margin: {$options['margin']};";
	}

	return $style;
}

/**
 * Generate internal style for HB Column Border.
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return string          Column internal CSS border.
 */
function mkhb_column_border( $options ) {
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
 * Generate internal style for HB Column Width Offset.
 *
 * @param  array $options All options will be used in the shortcode.
 * @return string         Column internal CSS width offset.
 */
function mkhb_column_offset( $options ) {
	$style = '';

	if ( empty( $options['margin'] ) ) {
		return $style;
	}

	// Column ID.
	$column_id = $options['id'];

	// Column Margin Raw.
	$column_margins = explode( ' ', $options['margin'] );

	// Column Offset.
	$column_offset = $column_margins[1] + $column_margins[3];

	if ( 0 < $column_offset ) {
		$column_offset .= 'px';
		$style = "
			#{$column_id}div[class^=mkhb-col-xs-],
			#{$column_id}.mkhb-col-sm-12,
			#{$column_id}.mkhb-col-md-12 {
				width: calc(100% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-11,
			#{$column_id}.mkhb-col-md-11 {
				width: calc(91.66666667% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-10,
			#{$column_id}.mkhb-col-md-10 {
				width: calc(83.33333333% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-9,
			#{$column_id}.mkhb-col-md-9 {
				width: calc(75% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-8,
			#{$column_id}.mkhb-col-md-8 {
				width: calc(66.66666667% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-7,
			#{$column_id}.mkhb-col-md-7 {
				width: calc(58.33333333% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-6,
			#{$column_id}.mkhb-col-md-6 {
				width: calc(50% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-5,
			#{$column_id}.mkhb-col-md-5 {
				width: calc(41.66666667% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-4,
			#{$column_id}.mkhb-col-md-4 {
				width: calc(33.33333333% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-3,
			#{$column_id}.mkhb-col-md-3 {
			 	width: calc(25% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-2,
			#{$column_id}.mkhb-col-md-2 {
			 	width: calc(16.66666667% - {$column_offset});
			}
			#{$column_id}.mkhb-col-sm-1,
			#{$column_id}.mkhb-col-md-1 {
				width: calc(8.33333333% - {$column_offset});
			}
		";
	} // End if().

	return $style;
}

/**
 * Get current column sequence status.
 *
 * @since 6.0.0
 *
 * @param  array $options  All options will be used in the shortcode.
 * @return boolean         Current sequence status.
 */
function mkhb_column_sequence( $options ) {
	// Column sequence status.
	$sequence = 0;
	if ( ! empty( $options['sequence'] ) ) {
		$sequence_raw = explode( ',', $options['sequence'] );
		$sequence = absint( end( $sequence_raw ) );
	}

	return $sequence;
}

