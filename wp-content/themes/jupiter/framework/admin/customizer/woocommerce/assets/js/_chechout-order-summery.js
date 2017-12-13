/**
 * Floating order summery table in checkout page.
 *
 * @since 5.9.4
 */

jQuery( document ).ready(function( $ ) {

	if ( ! $( 'body' ).hasClass( 'woocommerce-checkout' ) ||
		 ! $( '.woocommerce-checkout .mk-grid' ).hasClass( 'full-layout' ) ) {
		return;
	}

	var mkFloatCheckoutSummeryTable = function( scroll ) {
		var table = $( '#customer_details .col-2' );
		var steps = $( '.mk-checkout-steps' );
		var tableTop = 53 + steps.outerHeight( true );
		var tableRight = $( '.theme-content  > .woocommerce' ).offset().left;

		if ( scroll == true ) {
			tableTop += window.pageYOffset;
		}

		table.css( {
			top: tableTop,
			right: tableRight,
			visibility: 'visible'
		} );
	}

	var resizeTimer;

	$( window ).on( 'resize', function() {
		clearTimeout( resizeTimer );
		resizeTimer = setTimeout( function() {
			mkFloatCheckoutSummeryTable();
		}, 50 );
	});

	$( 'body' ).on( 'mk-position-order-summery', function() {
		mkFloatCheckoutSummeryTable();
	});

	// $( window ).on( 'scroll', function() {
	// 	mkFloatCheckoutSummeryTable( true );
	// });

	mkFloatCheckoutSummeryTable();

});