jQuery( document ).ready( function( $ ) {

	var flexViewportContainer = '.woocommerce-page.single-product .images';
	var flexViewport = '.woocommerce-page.single-product .images .flex-viewport';

	// Height.
	wp.customize( 'mk_cz[sh_pp_sty_img_image_ratio]' , function( value ) {

		value.bind( function( to ) {
			mkPreviewSaveReload();
		} );

	} );

	// Border width.
	wp.customize( 'mk_cz[sh_pp_sty_img_border_width]' , function( value ) {

		var el = 'sh_pp_sty_img_border_width';
		var styles = {};

		styles[ flexViewport ] = 'border-width: ' + value() + 'px';
		mkPreviewInternalStyle( styles, el );

		setTimeout( function() {
			$( '.flex-active-slide' ).resize();
		}, 1000);

		value.bind( function( to ) {
			$( flexViewport ).css( 'border-width', to + 'px' );

			setTimeout( function() {
				$( '.flex-active-slide' ).resize();
			}, 1000);
		} );

	} );

	// Border color.
	wp.customize( 'mk_cz[sh_pp_sty_img_border_color]' , function( value ) {

		var el = 'sh_pp_sty_img_border_color';
		var styles = {};

		styles[ flexViewport ] = 'border-color: ' + value();
		mkPreviewInternalStyle( styles, el );

		value.bind( function( to ) {
			$( flexViewport ).css( 'border-color', to );
		} );

	} );

	// Method for Control's event handlers: sh_pp_sty_img_box_model.
	wp.customize('mk_cz[sh_pp_sty_img_box_model]', function (value) {
		var boxModel = mkPreviewBoxModel(value());
		// calculate container width.
		var newWidth = parseInt( boxModel['margin-left'], 10 ) + parseInt( boxModel['margin-right'], 10 );
		var containerWidth = mk_get_image_gallery_width('mk_cz[sh_pp_set_layout]');
		
		$( flexViewportContainer ).css({
			width: 'calc(' + containerWidth + '% - ' + newWidth + 'px)'
		});
		$( flexViewportContainer ).css(boxModel);

		value.bind(function (to) {
			var boxModel = mkPreviewBoxModel(to);
			// calculate container width.
			var newWidth = parseInt( boxModel['margin-left'], 10 ) + parseInt( boxModel['margin-right'], 10 );
			var containerWidth = mk_get_image_gallery_width('mk_cz[sh_pp_set_layout]');

			$( flexViewportContainer ).css({
				width: 'calc(' + containerWidth + '% - ' + newWidth + 'px)'
			});
			$( flexViewportContainer ).css(boxModel);
			$( '.flex-control-nav' ).resize()	
		});
	});

} );

