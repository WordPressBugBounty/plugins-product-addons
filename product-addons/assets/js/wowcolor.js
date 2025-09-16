( function () {
	'use strict';

	function ColorPicker(
		color = { h: 120, s: 100, v: 50, a: 1 },
		savedColors = [
			{ r: 239, g: 68, b: 68, a: 1 },
			{ r: 249, g: 115, b: 22, a: 1 },
			{ r: 250, g: 204, b: 21, a: 1 },
			{ r: 74, g: 222, b: 128, a: 1 },
			{ r: 45, g: 212, b: 191, a: 1 },
			{ r: 59, g: 103, b: 246, a: 1 },
			{ r: 0, g: 164, b: 100, a: 1 },
			{ r: 134, g: 166, b: 44, a: 1 },
		]
	) {
		let isDragging = false;
		let activeControl = null;
		let selectedSavedColor = null;

		// Setup Canvas elements
		const colorPanel = document.getElementById( 'colorPanel' );
		const colorCtx = colorPanel.getContext( '2d' );
		const hueSlider = document.getElementById( 'hueSlider' );
		const hueCtx = hueSlider.getContext( '2d' );
		const opacitySlider = document.getElementById( 'opacitySlider' );
		const opacityCtx = opacitySlider.getContext( '2d' );
		const colorSelector = document.getElementById( 'colorSelector' );
		const hueSelector = document.getElementById( 'hueSelector' );
		const opacitySelector = document.getElementById( 'opacitySelector' );
		const colorPreview = document.getElementById( 'colorPreview' );
		const colorValue = document.getElementById( 'colorValue' );
		const savedColorsContainer = document.getElementById( 'savedColors' );
		const addColorBtn = document.getElementById( 'addColor' );

		function setupEventListeners() {
			colorPanel.addEventListener( 'mousedown', ( e ) => {
				isDragging = true;
				activeControl = 'color';
				handleColorPanelInput( e );
			} );

			hueSlider.addEventListener( 'mousedown', ( e ) => {
				isDragging = true;
				activeControl = 'hue';
				handleHueInput( e );
			} );

			opacitySlider.addEventListener( 'mousedown', ( e ) => {
				isDragging = true;
				activeControl = 'opacity';
				handleOpacityInput( e );
			} );

			document.addEventListener( 'mousemove', ( e ) => {
				if ( ! isDragging ) {
					return;
				}

				switch ( activeControl ) {
					case 'color':
						handleColorPanelInput( e );
						break;
					case 'hue':
						handleHueInput( e );
						break;
					case 'opacity':
						handleOpacityInput( e );
						break;
				}
			} );

			document.addEventListener( 'mouseup', () => {
				isDragging = false;
				activeControl = null;
			} );

			addColorBtn.addEventListener( 'click', () => {
				addSavedColor();
			} );
		}

		function handleColorPanelInput( e ) {
			const rect = colorPanel.getBoundingClientRect();
			const x = Math.max(
				0,
				Math.min( e.clientX - rect.left, colorPanel.width )
			);
			const y = Math.max(
				0,
				Math.min( e.clientY - rect.top, colorPanel.height )
			);

			color.s = ( x / colorPanel.width ) * 100;
			color.v = 100 - ( y / colorPanel.height ) * 100;

			updateColorSelector( x, y );
			render();
		}

		function handleHueInput( e ) {
			const rect = hueSlider.getBoundingClientRect();
			const y = Math.max(
				0,
				Math.min( e.clientY - rect.top, hueSlider.height )
			);

			color.h = ( y / hueSlider.height ) * 360;
			updateHueSelector( y );
			render();
		}

		function handleOpacityInput( e ) {
			const rect = opacitySlider.getBoundingClientRect();
			const y = Math.max(
				0,
				Math.min( e.clientY - rect.top, opacitySlider.height )
			);

			color.a = 1 - y / opacitySlider.height;
			updateOpacitySelector( y );
			render();
		}

		function updateColorSelector( x, y ) {
			colorSelector.style.left = `${ x }px`;
			colorSelector.style.top = `${ y }px`;
		}

		function updateHueSelector( y ) {
			hueSelector.style.top = `${ y }px`;
		}

		function updateOpacitySelector( y ) {
			opacitySelector.style.top = `${ y }px`;
		}

		function drawColorPanel() {
			const hueColor = `hsl(${ color.h }, 100%, 50%)`;
			const whiteGrad = colorCtx.createLinearGradient(
				0,
				0,
				colorPanel.width,
				0
			);
			whiteGrad.addColorStop( 0, '#fff' );
			whiteGrad.addColorStop( 1, hueColor );

			colorCtx.fillStyle = whiteGrad;
			colorCtx.fillRect( 0, 0, colorPanel.width, colorPanel.height );

			const blackGrad = colorCtx.createLinearGradient(
				0,
				0,
				0,
				colorPanel.height
			);
			blackGrad.addColorStop( 0, 'transparent' );
			blackGrad.addColorStop( 1, '#000' );

			colorCtx.fillStyle = blackGrad;
			colorCtx.fillRect( 0, 0, colorPanel.width, colorPanel.height );
		}

		function drawHueSlider() {
			const gradient = hueCtx.createLinearGradient(
				0,
				0,
				0,
				hueSlider.height
			);
			for ( let i = 0; i <= 360; i += 60 ) {
				gradient.addColorStop( i / 360, `hsl(${ i }, 100%, 50%)` );
			}
			hueCtx.fillStyle = gradient;
			hueCtx.fillRect( 0, 0, hueSlider.width, hueSlider.height );
		}

		function drawOpacitySlider() {
			const size = 5;
			for ( let i = 0; i < opacitySlider.width; i += size ) {
				for ( let j = 0; j < opacitySlider.height; j += size ) {
					opacityCtx.fillStyle =
						( i + j ) % ( size * 2 ) === 0 ? '#fff' : '#ccc';
					opacityCtx.fillRect( i, j, size, size );
				}
			}

			const gradient = opacityCtx.createLinearGradient(
				0,
				0,
				0,
				opacitySlider.height
			);
			const currentColor = hsvToRgb( color.h, color.s, color.v, color.a );
			gradient.addColorStop(
				0,
				`rgba(${ currentColor.r }, ${ currentColor.g }, ${ currentColor.b }, 1)`
			);
			gradient.addColorStop(
				1,
				`rgba(${ currentColor.r }, ${ currentColor.g }, ${ currentColor.b }, 0)`
			);

			opacityCtx.fillStyle = gradient;
			opacityCtx.fillRect(
				0,
				0,
				opacitySlider.width,
				opacitySlider.height
			);
		}

		function render() {
			drawColorPanel();
			drawHueSlider();
			drawOpacitySlider();
			updateColorDisplay();
			renderSavedColors();
		}

		function updateColorDisplay() {
			const rgb = hsvToRgb( color.h, color.s, color.v, color.a );
			const hex = rgbaToHex( rgb.r, rgb.g, rgb.b, color.a );

			colorPreview.style.backgroundColor = `rgba(${ rgb.r }, ${ rgb.g }, ${ rgb.b }, ${ color.a })`;
			colorValue.textContent = hex;
		}

		function addSavedColor() {
			const rgba = hsvToRgb( color.h, color.s, color.v, color.a );
			savedColors.push( rgba );
			renderSavedColors();
		}

		function renderSavedColors() {
			savedColorsContainer.innerHTML = '';
			savedColors.forEach( ( savedColor, index ) => {
				const swatch = document.createElement( 'div' );
				swatch.className =
					'prad-cursor-pointer prad-border-secondary prad-br-round';
				swatch.style.backgroundColor = `rgba(${ savedColor.r }, ${ savedColor.g }, ${ savedColor.b }, ${ savedColor.a })`;
				swatch.style.width = '1.5rem';
				swatch.style.height = '1.5rem';
				if ( index === selectedSavedColor ) {
					swatch.classList.add( 'selected' );
				}
				swatch.addEventListener( 'click', () =>
					selectSavedColor( index )
				);
				savedColorsContainer.appendChild( swatch );
			} );
		}

		function selectSavedColor( index ) {
			selectedSavedColor = index;
			const selectedColor = savedColors[ index ];
			const hsv = rgbToHSV(
				selectedColor.r,
				selectedColor.g,
				selectedColor.b,
				selectedColor.a
			);
			color = { ...hsv, a: selectedColor.a };
			updateSelectors();
			render();
		}

		function updateSelectors() {
			const x = ( color.s / 100 ) * colorPanel.width;
			const y = ( 1 - color.v / 100 ) * colorPanel.height;
			updateColorSelector( x, y );
			updateHueSelector( ( color.h / 360 ) * hueSlider.height );
			updateOpacitySelector( ( 1 - color.a ) * opacitySlider.height );
		}

		function hsvToRgb( h, s, v, a = 1 ) {
			h = h / 360;
			s = s / 100;
			v = v / 100;

			let r, g, b;
			const i = Math.floor( h * 6 );
			const f = h * 6 - i;
			const p = v * ( 1 - s );
			const q = v * ( 1 - f * s );
			const t = v * ( 1 - ( 1 - f ) * s );

			switch ( i % 6 ) {
				case 0:
					r = v;
					g = t;
					b = p;
					break;
				case 1:
					r = q;
					g = v;
					b = p;
					break;
				case 2:
					r = p;
					g = v;
					b = t;
					break;
				case 3:
					r = p;
					g = q;
					b = v;
					break;
				case 4:
					r = t;
					g = p;
					b = v;
					break;
				case 5:
					r = v;
					g = p;
					b = q;
					break;
			}

			return {
				r: Math.round( r * 255 ),
				g: Math.round( g * 255 ),
				b: Math.round( b * 255 ),
				a: Math.round( a * 100 ) / 100,
			};
		}

		function rgbToHSV( r, g, b, a = 1 ) {
			r /= 255;
			g /= 255;
			b /= 255;

			const max = Math.max( r, g, b );
			const min = Math.min( r, g, b );
			const delta = max - min;

			let h = 0;
			const s = max === 0 ? 0 : delta / max;
			const v = max;

			if ( delta !== 0 ) {
				if ( max === r ) {
					h = ( g - b ) / delta;
				} else if ( max === g ) {
					h = ( b - r ) / delta + 2;
				} else {
					h = ( r - g ) / delta + 4;
				}
				h *= 60;
				if ( h < 0 ) {
					h += 360;
				}
			}

			return { h, s: s * 100, v: v * 100, a };
		}

		function rgbaToHex( r, g, b, a = 1 ) {
			const toHex = ( n ) => {
				const hex = Math.round( n ).toString( 16 );
				return hex.length === 1 ? '0' + hex : hex;
			};

			if ( a === 1 ) {
				return `#${ toHex( r ) }${ toHex( g ) }${ toHex( b ) }`;
			}

			return `#${ toHex( r ) }${ toHex( g ) }${ toHex( b ) }${ toHex(
				Math.round( a * 255 )
			) }`;
		}

		render();
		setupEventListeners();
	}

	// Initialize color picker
	ColorPicker();

	// eslint-disable-next-line no-undef
} )( jQuery );
