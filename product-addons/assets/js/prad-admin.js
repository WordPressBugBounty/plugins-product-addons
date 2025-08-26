( function ( $ ) {
	'use strict';

	// *************************************
	// Add target blank for upgrade button
	// *************************************
	const pradMenuHref = $( '#toplevel_page_prad-dashboard > a' ).attr(
		'href'
	);
	if ( pradMenuHref?.indexOf( '?page=prad-dashboard' ) > 0 ) {
		$( '#toplevel_page_prad-dashboard > a' ).attr(
			'href',
			pradMenuHref + '#dashboard'
		);
	}

	if ( '?page=prad-dashboard' == window.location.search ) {
		const hash = window.location.hash;
		if ( hash ) {
			if ( hash.indexOf( 'demoid' ) < 0 ) {
				$( '#toplevel_page_prad-dashboard ul li' ).removeClass(
					'current'
				);

				if ( hash.includes( '#lists' ) ) {
					$(
						'#toplevel_page_prad-dashboard ul li a[href$=' +
							'#lists' +
							']'
					)
						.closest( 'li' )
						.addClass( 'current' );
				} else if ( hash === '#dashboard' ) {
					$(
						'#toplevel_page_prad-dashboard ul li.wp-first-item'
					).addClass( 'current' );
				} else {
					$(
						'#toplevel_page_prad-dashboard ul li a[href$=' +
							hash +
							']'
					).addClass( 'current' );
				}
			}
		}
	}
	$( document ).on(
		'click',
		'#toplevel_page_prad-dashboard ul li a',
		function () {
			let value = $( this ).attr( 'href' );
			if ( value ) {
				value = value.split( '#' );
				if (
					typeof value[ 1 ] != 'undefined' &&
					value[ 1 ].indexOf( 'demoid' ) < 0 &&
					value[ 1 ]
				) {
					$( '#toplevel_page_prad-dashboard ul li a' )
						.closest( 'ul' )
						.find( 'li' )
						.removeClass( 'current' );
					$( this ).closest( 'li' ).addClass( 'current' ); // Submenu click
					$(
						'#toplevel_page_prad-dashboard ul li a[href$=' +
							value[ 1 ] +
							']'
					)
						.closest( 'li' )
						.addClass( 'current' ); // Dash Nav Menu click
					if ( value[ 1 ] == 'dashboard' ) {
						$(
							'#toplevel_page_prad-dashboard ul li.wp-first-item'
						).addClass( 'current' );
					}
				}
			}
		}
	);

	$( '#toplevel_page_prad-dashboard ul > li > a' ).each( function () {
		if (
			$( this ).attr( 'href' ) &&
			$( this ).attr( 'href' ).indexOf( '?page=prad-dashboard' ) > 0
		) {
			if ( $( this ).hasClass( 'wp-first-item' ) !== false ) {
				$( this ).attr(
					'href',
					$( this ).attr( 'href' ) + '#dashboard'
				);
			}
		}
	} );

	$( '.wp-submenu > li > a' ).each( function () {
		if (
			$( this )
				.attr( 'href' )
				.indexOf( '?post_type=product&page=wowaddons-page' ) > 0
		) {
			$( this ).attr(
				'href',
				$( this )
					.attr( 'href' )
					.replace(
						'edit.php?post_type=product&page=wowaddons-page',
						'admin.php?page=prad-dashboard#dashboard'
					)
			);
		}
	} );
	// eslint-disable-next-line no-undef

	// Plugin Homepage URL Change
	if ( $( '#plugin-information-content' ).length > 0 ) {
		$(
			'a[href$="https://account.wpxpo.com/downloads/wowaddons-pro/"]'
		).attr(
			'href',
			'https://www.wpxpo.com/product-addons-for-woocommerce/'
		);
	}
} )( jQuery );
