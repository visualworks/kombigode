/*
 * Common script for HB. For example: Fixed and Sticky Header animation.
 */
( function( $ ) {
	// Set common variables.
	var windowSel = $( window );
	var bodySel = $( 'body' );
	var fixedSel = $( '.mkhb-fixed' );
	var stickySel = $( '.mkhb-sticky' );
	// Used to check if responsive burger menu is active on sticky header or not.
	var stickyRespNav = $( '.mkhb-navigation-resp__sticky' );

	var windowHeight = windowSel.height();

	/**
	 * HELPERS LIST
	 */

	// FH - Get Header offset.
	function mkhbGetOffset( offset, device ) {
		var deviceEl = $( '.mkhb-' + device );
		var $offset = 0;
		if ( typeof offset === 'string' && offset !== 'header' ) {
			$offset = Math.round( ( parseInt( offset ) / 100 ) * windowHeight );
		} else if ( typeof offset === 'number' ) {
			$offset = parseInt( offset );
		}

		// Check if it's NaN or undefined.
		if ( 0 == $offset || isNaN( $offset ) ) {
			$offset = deviceEl.height();
			if ( deviceEl.hasClass( 'mkhb-overlap' ) ) {
				$offset = deviceEl.children( '.mkhb-device-container' ).height();
			}
		}

		return $offset;
	}

	/**
	 * FUNCTIONS LIST
	 */

	// FH - Set height of HB container.
	function mkhbSetFixedHeight( selector ) {
		selector.each( function( e ) {
			var thisSel = $( this );
			if ( ! thisSel.hasClass( 'mkhb-overlap' ) ) {
				var childHeight = thisSel.children( '.mkhb-device-container' ).height();
				thisSel.height( childHeight );
			}
		});
	}

	// SH - 1. Slide Down.
	function mkhbSlideDown( current, offset, device, top, curHeight ) {
		var onScroll = function onScroll() {
			if ( windowSel.scrollTop() > offset ) {
			    current.css({ 'top': top });
			    current.addClass( 'mkhb-sticky--active' );
			    mkhbHideRespBurger( false );
			} else {
				current.css({ 'top': -curHeight });
			    current.removeClass( 'mkhb-sticky--active' );
			    mkhbHideRespBurger( true );
			}
		};

		onScroll();
		windowSel.on( 'scroll', onScroll );
	}

	// SH - 2. Lazy.
	function mkhbLazy( current, offset, device, top, curHeight ) {
		var lastScrollPos = 0;
		var onScroll = function onScroll() {
			var scrollPos = windowSel.scrollTop();
			if ( scrollPos > offset && scrollPos < lastScrollPos ) {
			    current.css({ 'top': top });
			    current.addClass( 'mkhb-sticky--active' );
			    mkhbHideRespBurger( false );
			} else {
				current.css({ 'top': -curHeight });
			    current.removeClass( 'mkhb-sticky--active' );
			    mkhbHideRespBurger( true );
			}
			lastScrollPos = scrollPos;
		};

		onScroll();
		windowSel.on( 'scroll', onScroll );
	}

	// SH - Hide/show responsive burger menu.
	function mkhbHideRespBurger( hide_status ) {
		if ( stickyRespNav.length > 0 ) {
			stickyRespNav.each( function(){
				var current = $( this );
				var id = current.attr( 'id' ).replace( '-wrap', '' );

				// Check if current responsive burger menu is opened.
				var current_nav_opened = false;
				if ( bodySel.hasClass( 'mkhb-navigation-resp--opened-' + id ) ) {
					current_nav_opened = true;
				}

				// Hide/show only if current responsive burger menu is opened.
				if ( ! current_nav_opened ) {
					return;
				}

				if ( hide_status ) {
					current.hide();
				} else {
					current.show();
				}
			} );
		}
	}

	/**
	 * ACTIONS LIST
	 */

	// Action - FH - Handle all resize action.
	var resizeHeader = function resizeHeader() {
		var fixedRsz = $( '.mkhb-fixed' );
		if ( fixedRsz.length > 0 ) {
			mkhbSetFixedHeight( fixedRsz );
		}
	}

	// Action - FH - Set the height of each devices.
	if ( fixedSel.length > 0 ) {
		mkhbSetFixedHeight( fixedSel );
		windowSel.on( 'resize', resizeHeader );
	}

	// Actiion - SH - Play the effect for Sticky Header.
	var initialAction = function initialAction() {
		if ( stickySel.length > 0 ) {
			stickySel.each( function( e ) {
				var current = $( this );

				// Set the offset when the sticky will be displayed.
				var offset = current.data( 'offset' );
				var device = current.data( 'device' );
				offset = mkhbGetOffset( offset, device );

				// Set the initial position of the sticky menu.
				var top = $( '#wpadminbar' ).height();
				top = ( top == null ) ? 0 : top;
				var curHeight = current.height();
				current.css({ 'top': -curHeight });

				var effect = current.data( 'effect' );
				if ( effect == 'slide-down' ) {
					mkhbSlideDown( current, offset, device, top, curHeight );
				} else if ( effect == 'lazy' ) {
					mkhbLazy( current, offset, device, top, curHeight );
				}
			});
		}
	}

	windowSel.on( 'load', initialAction );

})( jQuery );