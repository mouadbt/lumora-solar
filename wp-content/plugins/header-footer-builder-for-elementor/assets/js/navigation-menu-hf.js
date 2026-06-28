( function( $, elementor ) {

	"use strict";

	var TahefobuTurbo = {

		init: function() {

			var widgets = {
				'tahefobu-navigation-menu.default' : TahefobuTurbo.widgetNavMenu,
			};
			// console.log('Turbo', widgets);
			$.each( widgets, function( widget, callback ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			});
		},

		widgetNavMenu: function( $scope ) {

			var $navMenu = $scope.find( '.tahefobu-nav-menu-container' ),
				$mobileNavMenu = $scope.find( '.tahefobu-mobile-nav-menu-container' );

			// Menu
			var subMenuFirst = $navMenu.find( '.tahefobu-nav-menu > li.menu-item-has-children' ),
				subMenuDeep = $navMenu.find( '.tahefobu-sub-menu li.menu-item-has-children' );

			if ( $scope.find('.tahefobu-mobile-toggle').length ) {
				$scope.find('a').on('click', function() {
					if (this.pathname == window.location.pathname && !($(this).parent('li').children().length > 1)) {
						$scope.find('.tahefobu-mobile-toggle').trigger('click');
					}
				});
			}

			if ( $navMenu.attr('data-trigger') === 'click' ) {
				// First Sub
				subMenuFirst.children('a').on( 'click', function(e) {
					var currentItem = $(this).parent(),
						childrenSub = currentItem.children('.tahefobu-sub-menu');

					// Reset
					subMenuFirst.not(currentItem).removeClass('tahefobu-sub-open');
					if ( $navMenu.hasClass('tahefobu-nav-menu-horizontal') || ( $navMenu.hasClass('tahefobu-nav-menu-vertical') && $scope.hasClass('tahefobu-sub-menu-position-absolute') ) ) {
						subMenuAnimation( subMenuFirst.children('.tahefobu-sub-menu'), false );
					}

					if ( ! currentItem.hasClass( 'tahefobu-sub-open' ) ) {
						e.preventDefault();
						currentItem.addClass('tahefobu-sub-open');
						subMenuAnimation( childrenSub, true );
					} else {
						currentItem.removeClass('tahefobu-sub-open');
						subMenuAnimation( childrenSub, false );
					}
				});

				// Deep Subs
				subMenuDeep.on( 'click', function(e) {
					var currentItem = $(this),
						childrenSub = currentItem.children('.tahefobu-sub-menu');

					// Reset
					if ( $navMenu.hasClass('tahefobu-nav-menu-horizontal') ) {
						subMenuAnimation( subMenuDeep.find('.tahefobu-sub-menu'), false );
					}

					if ( ! currentItem.hasClass( 'tahefobu-sub-open' ) ) {
						e.preventDefault();
						currentItem.addClass('tahefobu-sub-open');
						subMenuAnimation( childrenSub, true );

					} else {
						currentItem.removeClass('tahefobu-sub-open');
						subMenuAnimation( childrenSub, false );
					}
				});

				// Reset Subs on Document click
				$( document ).mouseup(function (e) {
					if ( ! subMenuFirst.is(e.target) && subMenuFirst.has(e.target).length === 0 ) {
						subMenuFirst.not().removeClass('tahefobu-sub-open');
						subMenuAnimation( subMenuFirst.children('.tahefobu-sub-menu'), false );
					}
					if ( ! subMenuDeep.is(e.target) && subMenuDeep.has(e.target).length === 0 ) {
						subMenuDeep.removeClass('tahefobu-sub-open');
						subMenuAnimation( subMenuDeep.children('.tahefobu-sub-menu'), false );
					}
				});
			} else {
				// Mouse Over
				subMenuFirst.on( 'mouseenter', function() {
					if ( $navMenu.hasClass('tahefobu-nav-menu-vertical') && $scope.hasClass('tahefobu-sub-menu-position-absolute') ) {
						$navMenu.find('li').not(this).children('.tahefobu-sub-menu').hide();
						// BUGFIX: when menu is vertical and absolute positioned, lvl2 depth sub menus wont show properly on hover
					}

					subMenuAnimation( $(this).children('.tahefobu-sub-menu'), true );
				});

				// Deep Subs
				subMenuDeep.on( 'mouseenter', function() {
					subMenuAnimation( $(this).children('.tahefobu-sub-menu'), true );
				});


				// Mouse Leave
				if ( $navMenu.hasClass('tahefobu-nav-menu-horizontal') ) {
					subMenuFirst.on( 'mouseleave', function() {
						subMenuAnimation( $(this).children('.tahefobu-sub-menu'), false );
					});

					subMenuDeep.on( 'mouseleave', function() {
						subMenuAnimation( $(this).children('.tahefobu-sub-menu'), false );
					});	
				} else {

					$navMenu.on( 'mouseleave', function() {
						subMenuAnimation( $(this).find('.tahefobu-sub-menu'), false );
					});
				}
			}


			// Mobile Menu
			var mobileMenu = $mobileNavMenu.find( '.tahefobu-mobile-nav-menu' );

			// Toggle Button
			$mobileNavMenu.find( '.tahefobu-mobile-toggle' ).on( 'click', function() {
				$(this).toggleClass('tahefobu-mobile-toggle-fx');

				if ( ! $(this).hasClass('tahefobu-mobile-toggle-open') ) {
					$(this).addClass('tahefobu-mobile-toggle-open');

					if ( $(this).find('.tahefobu-mobile-toggle-text').length ) {
						$(this).children().eq(0).hide();
						$(this).children().eq(1).show();
					}
				} else {
					$(this).removeClass('tahefobu-mobile-toggle-open');
					$(this).trigger('focusout');

					if ( $(this).find('.tahefobu-mobile-toggle-text').length ) {
						$(this).children().eq(1).hide();
						$(this).children().eq(0).show();
					}
				}

				// Show Menu
					$(this).parent().next().stop().slideToggle();		

				// Fix Width
				fullWidthMobileDropdown();
			});

			// Sub Menu Class
			mobileMenu.find('.sub-menu').removeClass('tahefobu-sub-menu').addClass('tahefobu-mobile-sub-menu');

			// Sub Menu Dropdown
			mobileMenu.find('.menu-item-has-children').children('a').on( 'click', function(e) {
				var parentItem = $(this).closest('li');

				// Toggle
				if ( ! parentItem.hasClass('tahefobu-mobile-sub-open') ) {
					e.preventDefault();
					parentItem.addClass('tahefobu-mobile-sub-open');
					parentItem.children('.tahefobu-mobile-sub-menu').first().stop().slideDown();
				} else {
					parentItem.removeClass('tahefobu-mobile-sub-open');
					parentItem.children('.tahefobu-mobile-sub-menu').first().stop().slideUp();
				}
			});

			// Run Functions
			fullWidthMobileDropdown();

			// Run Functions on Resize
			$(window).smartresize(function() {
				fullWidthMobileDropdown();
			});

			// Full Width Dropdown
			function fullWidthMobileDropdown() {
				if ( ! $scope.hasClass( 'tahefobu-mobile-menu-full-width' ) || (! $scope.closest('.elementor-column').length && ! $scope.closest('.e-con').length) ) {
					return;
				}

                // GOGA: maybe in some cases elementor-element instead of e-con
                var topSection = $scope.closest('.elementor-top-section');

				var eColumn   = $scope.closest('.elementor-column').length ? $scope.closest('.elementor-column') : $scope.closest('.elementor-element'),
					mWidth 	  = topSection.length ? (topSection.outerWidth() - 2 * mobileMenu.offset().left) : ($(window).outerWidth() - 2 * mobileMenu.offset().left),
					mPosition = eColumn.offset().left + parseInt(eColumn.css('padding-left'), 10);

                // GOGA: don't need to calculate mWidth since it has tu be full
				mobileMenu.css({
					'width' : mWidth +'px',
					'left' : - mPosition +'px'
				});
			}

			// Sub Menu Animation
			function subMenuAnimation( selector, show ) {
				if ( show === true ) {
					if ( $scope.hasClass('tahefobu-sub-menu-fx-slide') ) {
						selector.stop().slideDown();
					} else {
						selector.stop().fadeIn();
					}
				} else {
					if ( $scope.hasClass('tahefobu-sub-menu-fx-slide') ) {
						selector.stop().slideUp();
					} else {
						selector.stop().fadeOut();
					}
				}
			}

		}, // End widgetNavMenu
	
	} // End tahefobuTurbo







	$( window ).on( 'elementor/frontend/init', TahefobuTurbo.init );



}( jQuery, window.elementorFrontend ) );

// Resize Function - Debounce
(function($,sr){

  var debounce = function (func, threshold, execAsap) {
      var timeout;

      return function debounced () {
          var obj = this, args = arguments;
          function delayed () {
              if (!execAsap)
                  func.apply(obj, args);
              timeout = null;
          };

          if (timeout)
              clearTimeout(timeout);
          else if (execAsap)
              func.apply(obj, args);

          timeout = setTimeout(delayed, threshold || 100);
      };
  }
  // smartresize 
  jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');