( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		function runCartStyles() {
			// Handle both <dl> and <ul> cases
			const selectors = [
				'dl.variation',
				'ul.wc-block-components-product-details',
				'ul.wc-item-meta',
			];

			$( selectors.join( ',' ) ).each( function () {
				const $container = $( this );

				// Prevent double processing
				if ( $container.hasClass( 'prad-processed' ) ) {
					return;
				}
				$container.addClass( 'prad-processed' );

				let $items = [];

				if ( $container.is( 'dl' ) ) {
					// Group dt/dd pairs
					const $children = $container.children();
					for ( let i = 0; i < $children.length; i += 2 ) {
						const $pair = $children.slice( i, i + 2 );
						$items.push( $pair );
					}
				} else if ( $container.is( 'ul' ) ) {
					// Each <li> is a separate item
					$items = $container
						.children()
						.map( function () {
							return $( this );
						} )
						.get();
				}

				if ( $items.length > 3 ) {
					// Wrap the container if not already wrapped
					if (
						! $container
							.parent()
							.hasClass( 'prad-variation-container' )
					) {
						$container.wrap(
							'<div class="prad-variation-container"></div>'
						);
					}

					const expandedHeight = $container.outerHeight();

					// Hide extra items
					for ( let i = 3; i < $items.length; i++ ) {
						$items[ i ].addClass( 'prad-collapsed-hidden' );
					}

					// Measure heights
					$container.css( 'max-height', 'none' );
					const collapsedHeight = $container.outerHeight();

					$container.css( {
						'max-height': collapsedHeight,
						transition: 'max-height 0.4s ease',
					} );

					// Create toggle button
					const $toggle = $(
						'<div class="prad-show-more-btn">Show More</div>'
					);
					$toggle.css(
						'padding-left',
						$container.css( 'padding-left' )
					);
					$container.parent().append( $toggle );

					$toggle.on( 'click', function ( e ) {
						e.stopPropagation();
						const isExpanded = $( this ).data( 'expanded' );

						if ( ! isExpanded ) {
							// Expand
							$items.forEach( function ( $item ) {
								$item.removeClass( 'prad-collapsed-hidden' );
							} );
							$container.css( 'max-height', expandedHeight );
							$( this )
								.text( 'Show Less' )
								.data( 'expanded', true );
						} else {
							// Collapse
							$container.css( 'max-height', collapsedHeight );
							$( this )
								.text( 'Show More' )
								.data( 'expanded', false );

							// After transition, hide again
							$container.one( 'transitionend', function () {
								if ( ! $toggle.data( 'expanded' ) ) {
									for ( let i = 3; i < $items.length; i++ ) {
										$items[ i ].addClass(
											'prad-collapsed-hidden'
										);
									}
								}
							} );
						}
					} );
				}
			} );
		}

		// Set initial opacity low
		$( '.variation, .wc-block-components-product-details' ).css(
			'opacity',
			0.3
		);

		// Run collapsing behavior
		runCartStyles();

		setTimeout( function () {
			$( '.variation, .wc-block-components-product-details' ).css(
				'opacity',
				1
			);
		}, 300 );

		// General MutationObserver for dynamic WooCommerce changes
		const observerTargets = [
			'.woocommerce-checkout-review-order',
			'.woocommerce-cart-form',
			'.woocommerce',
			'body', // <- last resort
		];

		observerTargets.forEach( function ( selector ) {
			const $target = $( selector );

			if ( $target.length > 0 ) {
				const observer = new MutationObserver( function ( mutations ) {
					let shouldRun = false;

					mutations.forEach( function ( mutation ) {
						if (
							mutation.addedNodes.length > 0 ||
							mutation.type === 'childList'
						) {
							shouldRun = true;
						}
					} );

					if ( shouldRun ) {
						setTimeout( runCartStyles, 50 );
					}
				} );

				const config = { childList: true, subtree: true };
				observer.observe( $target[ 0 ], config );
			}
		} );
	} );
} )( jQuery );
