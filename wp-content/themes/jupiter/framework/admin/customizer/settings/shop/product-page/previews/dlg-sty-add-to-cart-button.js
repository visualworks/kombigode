(function ($) {

	var add_to_cart = '.woocommerce-page.single-product div.product .single_add_to_cart_button';
	var add_to_cart_button = '.woocommerce-page.single-product div.product button.single_add_to_cart_button';

	// Method for Control's event handlers: sh_pp_sty_atc_btn_text.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_text]', function (value) {

		var icon = '';

		if ( $( add_to_cart_button ).length ) {
			icon = $( add_to_cart_button ).find( '.mk-button-icon' ).get(0).outerHTML;
		}

		$( add_to_cart_button ).html( icon + '' + value() );

		value.bind(function (to) {

			if ( 'true' != wp.customize( 'mk_cz[sh_pp_sty_atc_btn_show_icon]' ).get() ) {
				$( add_to_cart_button ).html( to );
			} else {
				$( add_to_cart_button ).html( icon + '' + to );
			}

		});

	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_show_icon.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_show_icon]', function (value) {
		if ('true' == value()) {
			$(add_to_cart_button).find('.mk-button-icon').show();
		} else {
			$(add_to_cart_button).find('.mk-button-icon').hide();
		}
		value.bind(function (to) {
			if ('true' == to) {
				$(add_to_cart_button).find('.mk-button-icon').show();
			} else {
				$(add_to_cart_button).find('.mk-button-icon').hide();
			}
		});
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_typography.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_typography]', function (value) {
		var typography = mkPreviewTypography(value(), true);
		$(add_to_cart).css(typography);

		// It's an exception.
		$( add_to_cart ).find( 'svg' ).css({
			width: value()['size'],
			height: value()['size'] 
		});

		value.bind(function (to) {
			var typography = mkPreviewTypography(to);
			$(add_to_cart).css(typography);

			// It's an exception.
			var typographySize = jQuery.parseJSON( to )['size'];

			$( add_to_cart ).find( 'svg' ).css({
				width: typographySize,
				height: typographySize 
			});
		});
	
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_background_color.
	wp.customize( 'mk_cz[sh_pp_sty_atc_btn_background_color]', function( value ) {

		var el = 'sh_pp_sty_atc_btn_background_color';
		var styles = {};

		styles[add_to_cart] = 'background-color: ' + value() + ' !important';
		mkPreviewInternalStyle( styles, el );
	
		value.bind( function( to ) {
			styles[add_to_cart] = 'background-color: ' + to + ' !important';
			mkPreviewInternalStyle( styles, el );
		} );
	
	} );

	// Method for Control's event handlers: sh_pp_sty_atc_btn_border_radius.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_border_radius]', function (value) {
		$(add_to_cart).css({
			'border-radius': value() + 'px'
		});
		value.bind(function (to) {
			$(add_to_cart).css({
				'border-radius': to + 'px'
			});
		});
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_border.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_border]', function (value) {
		$(add_to_cart).css({
			'border-width': value() + 'px'
		});
		value.bind(function (to) {
			$(add_to_cart).css({
				'border-width': to + 'px'
			});
		});
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_border_color.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_border_color]', function (value) {
		$(add_to_cart).css({
			'border-color': value()
		});
		value.bind(function (to) {
			$(add_to_cart).css({
				'border-color': to
			});
		});
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_icon_color.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_icon_color]', function (value) {
		$(add_to_cart).find('svg').css({
			'fill': value()
		});
		value.bind(function (to) {
			$(add_to_cart).find('svg').css({
				'fill': to
			});
		});
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_color_hover.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_color_hover]', function (value) {
		$(add_to_cart).on({
			mouseenter: function () {
				$(this).css({
					color: value()
				});
			},
			mouseleave: function () {
				$(this).css({
					color: mkPreviewTypography(wp.customize('mk_cz[sh_pp_sty_atc_btn_typography]')())['color']
				});
			}
		});
		value.bind(function (to) {
			$(add_to_cart).on({
				mouseenter: function () {
					$(this).css({
						color: to
					});
				},
				mouseleave: function () {
					$(this).css({
						color: mkPreviewTypography(wp.customize('mk_cz[sh_pp_sty_atc_btn_typography]')())['color']
					});
				}
			});
		});
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_background_color_hover.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_background_color_hover]', function (value) {

		var el = 'sh_pp_sty_atc_btn_background_color_hover';
		var styles = {};

		styles[add_to_cart + ':hover'] = 'background-color: ' + value() + ' !important';
		mkPreviewInternalStyle( styles, el );
	
		value.bind( function( to ) {
			styles[add_to_cart + ':hover'] = 'background-color: ' + to + ' !important';
			mkPreviewInternalStyle( styles, el );
		} );
		
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_border_color_hover.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_border_color_hover]', function (value) {
		$(add_to_cart).on({
			mouseenter: function () {
				$(this).css({
					'border-color': value()
				});
			},
			mouseleave: function () {
				$(this).css({
					'border-color': wp.customize('mk_cz[sh_pp_sty_atc_btn_border_color]')()
				});
			}
		});
		value.bind(function (to) {
			$(add_to_cart).on({
				mouseenter: function () {
					$(this).css({
						'border-color': to
					});
				},
				mouseleave: function () {
					$(this).css({
						'border-color': wp.customize('mk_cz[sh_pp_sty_atc_btn_border_color]')()
					});
				}
			});
		});
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_border_color_hover.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_icon_color_hover]', function (value) {
		$(add_to_cart).on({
			mouseenter: function () {
				$(this).find('svg').css({
					'fill': value()
				});
			},
			mouseleave: function () {
				$(this).find('svg').css({
					'fill': wp.customize('mk_cz[sh_pp_sty_atc_btn_icon_color]')()
				});
			}
		});
		value.bind(function (to) {
			$(add_to_cart).on({
				mouseenter: function () {
					$(this).find('svg').css({
						'fill': to
					});
				},
				mouseleave: function () {
					$(this).find('svg').css({
						'fill': wp.customize('mk_cz[sh_pp_sty_atc_btn_icon_color]')()
					});
				}
			});
		});
	});

	// Method for Control's event handlers: sh_pp_sty_atc_btn_box_model.
	wp.customize('mk_cz[sh_pp_sty_atc_btn_box_model]', function (value) {
		$(add_to_cart).css(mkPreviewBoxModel(value()));
		value.bind(function (to) {
			$(add_to_cart).css(mkPreviewBoxModel(to));
		});
	});

})(jQuery);