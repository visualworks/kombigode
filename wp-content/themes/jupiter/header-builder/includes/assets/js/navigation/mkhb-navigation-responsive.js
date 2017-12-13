/*
 * This library is written by menu-sidebar.js as referrence with some modification for the selector name.
 * And also some lines are removed because the functionallities are not needed yet.
 *
 * Source: components/layout/header/menu-sidebar.js
 * Version: 6.0.0
 */
(function($) {
    'use strict';

    window.MK = window.MK || {};
    MK.HB = MK.HB || {};

    $('.mkhb-navigation-resp__arrow').stop(true).on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        if ($this.hasClass('mkhb-navigation-resp__sub-closed')) {
            $this.siblings('ul').slideDown(450).end().removeClass('mkhb-navigation-resp__sub-closed').addClass('mkhb-navigation-resp__sub-opened');
        } else {
            $this.siblings('ul').slideUp(450).end().removeClass('mkhb-navigation-resp__sub-opened').addClass('mkhb-navigation-resp__sub-closed');
        }
    });

    var $window = $(window);
    var $body = $('body');
    var $resMenuWrap = $('.mkhb-navigation-resp__wrap');
    var $resMenuLink = $('.mkhb-navigation-resp');

    // Flags.
    var hasResMenu = ($resMenuWrap.length > 0);

    // Initial window and screen height.
    var windowHeight = $window.height();
    var screenHeight = screen.height;

    // This library run on Tablet or Mobile whatever the nav style selected here. (4 cases).
    if (!hasResMenu) return;

    function toggleResMenu(e) {
        e.preventDefault();
        var $this = $(this);
        var $parentID = $this.parent().attr('id');
        var $headerInner = $this.parents('header');
        var $resMenu = $headerInner.find('#' + $parentID + '-wrap.mkhb-navigation-resp__wrap');
        var searchBox = $('.mkhb-navigation-resp__searchform .text-input');
        var adminBarHeight = $('#wpadminbar').height(); /* Fix AM-1918 */

        if ($body.hasClass('mkhb-navigation-resp--opened-' + $parentID)) {
            $this.removeClass('is-active').find('.mkhb-navigation-resp__container').removeClass('fullscreen-active');
            $body.removeClass('mkhb-navigation-resp--opened-' + $parentID).addClass('mkhb-navigation-resp--closed-' + $parentID).trigger('mkhb-navigation-resp--closed-' + $parentID);
            $resMenu.hide();
        } else {
            $this.addClass('is-active').find('.mkhb-navigation-resp__container').addClass('fullscreen-active');
            $body.removeClass('mkhb-navigation-resp--closed-' + $parentID).addClass('mkhb-navigation-resp--opened-' + $parentID).trigger('mkhb-navigation-resp--opened-' + $parentID);
            $resMenu.show();
        }

        // For iPhone 5 focus bug , remove search box focused class.
        if(searchBox.hasClass('input-focused')){
            searchBox.removeClass('input-focused');
        }

    }

    $resMenuLink.each(function() {
        $(this).on('click', toggleResMenu);
    });


    var setResMenuHeight = function() {
        var height = $window.height() - MK.HB.val.offsetHeaderHeight(0);
        $resMenuWrap.css('max-height', height);
    };

    // Check if device virtual keyboard is active.
    var isVirtualKeyboard = function() {
        var currentWindowHeight = $window.height();
        var currentScreenHeight = screen.height;
        var searchBox = $('.mkhb-navigation-resp__searchform .text-input');
        var searchBoxIsFocused = false;

        // For iPhone 5 focus bug , add class for detect focus state.
        searchBox.on('touchstart touchend', function(e) {
            searchBox.addClass('input-focused');
        });

        searchBoxIsFocused = (searchBox.is(':focus') || searchBox.hasClass("input-focused"));

        if ($body.hasClass('[class^="mkhb-navigation-resp--opened"]') && searchBoxIsFocused && currentScreenHeight == screenHeight && currentWindowHeight != windowHeight) {
            return true;
        } else {
            return false;
        }
    };

    var hideResMenu = function hideResMenu() {
        if (MK.HB.utils.isResponsiveMenuState()) {

            /*
             * When search box in responsive menu is focused , window resize fired but at this time
             * responsive menu should be open.
             */
            if (!isVirtualKeyboard()) {
                // Hide toggled menu and its states.
                if ($body.hasClass('[class^="mkhb-navigation-resp--opened"]')) {
                    $resMenuLink.filter('.is-active').trigger('click');
                }
                // Hide menu wrapper.
                $resMenuWrap.hide();

            }
        }
    };

    $resMenuWrap.on('click', 'a', hideResMenu);

    /**
     * Handle responsive navigation reposition on overlapping and sticky header.
     * - Overlapping: Set nav container position into absolute.
     * - Sticky: Set nav container position into fixed.
     *
     * @since 6.0.0
     */
    var headerOverlap = $( '.mkhb-overlap' );
    var headerFixed = $( '.mkhb-fixed' );
    var headerSticky = $( '.mkhb-sticky' );
    var hasOverlap = ( headerOverlap.length > 0 );
    var hasFixed = ( headerFixed.length > 0 );
    var hasSticky = ( headerSticky.length > 0 );
    var renderedStyle = []; // Used to save repositioned nav responsive container.

    // Only run this code if the header is overlapping the content or sticky header is active.
    if ( ! hasOverlap && ! hasSticky && ! hasFixed ) {
        return;
    }

    /**
     * Reposition responsive navigation process.
     * Device, workspace, and top position is important to set container position.
     */
    var repositionRespNav = function repositionRespNav() {
        var parent = $( '.hb-devices' );
        var container = $( this );
        var device = container.data( 'device' );
        var workspace = container.data( 'workspace' );
        var fixed = container.hasClass( 'mkhb-fixed--filled' );
        var combine = workspace + '-' + device;
        var top = $( '#wpadminbar' ).height();
        top = ( top == null ) ? 0 : top;

        // Only run the code on tablet or mobile.
        if ( device === 'desktop' || renderedStyle.indexOf( combine ) > -1 ) {
            return;
        }

        // Don't run the code if the height is less than 0.
        var height = container.find( '.mkhb-device-container' ).height();
        if ( height <= 0 ) {
            return;
        }

        // Set up custom style.
        var style = {};
        var target = '';
        if ( workspace === 'normal' && ! fixed ) {
            style['position'] = 'absolute';
            style['height'] = 'auto';
            style['top'] = height + 'px';
            target = '.mkhb-navigation-resp__' + workspace + '.mkhb-navigation-resp__' + device;
        } else {
            style['position'] = 'fixed';
            style['height'] = 'auto';
            style['top'] = (top + height) + 'px';
            target = '.mkhb-navigation-resp__' + workspace + '.mkhb-navigation-resp__' + device;
        }

        // Apply custom style.
        if ( ! style || ! target ) {
            return;
        }

        parent.find( target ).css( style );
        renderedStyle.push( combine );
    }

    /**
     * Decide should we reposition the responsive nav container or not.
     */
    var setPositionRespNav = function setPositionRespNav() {
        if ( renderedStyle.length < 4 ) {
            var headerContainer = '';

            if ( hasOverlap ) {
                headerContainer = $( '.mkhb-overlap' );
            }

            if ( hasFixed ) {
                headerContainer = $( '.mkhb-fixed' );
            }

            if ( hasSticky ) {
                headerContainer = $( '.mkhb-sticky' );
            }

            headerContainer.each( repositionRespNav );
        }
    }

    $window.on( 'load', setPositionRespNav );
    $window.on( 'resize', setPositionRespNav );
    if ( hasSticky ) {
        $window.on( 'scroll', setPositionRespNav );
    }
}( jQuery ));
