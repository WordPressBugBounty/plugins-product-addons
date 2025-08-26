/* eslint-disable no-nested-ternary */
( function ( $ ) {
	'use strict';
	/*	Initialize data  */
	$.fn.dateTimePicker = function ( options ) {
		const defaults = {
			disabledWeekdays: [], // 0 = Sunday, 6 = Saturday
			disabledDates: [], // 0 = Sunday, 6 = Saturday
			format: 'YYYY-MM-DD',
			minDate: null,
			maxDate: null,
		};

		const settings = { ...defaults, ...options };

		// Helper functions
		function startOfDay( date ) {
			const newDate = new Date( date );
			newDate.setHours( 0, 0, 0, 0 );
			return newDate;
		}

		function endOfDay( date ) {
			const newDate = new Date( date );
			newDate.setHours( 23, 59, 59, 999 );
			return newDate;
		}

		// Process min/max dates
		if ( settings.minDate ) {
			settings.minDate = startOfDay( settings.minDate );
		}
		if ( settings.maxDate ) {
			settings.maxDate = endOfDay( settings.maxDate );
		}

		function isDateDisabled( date ) {
			// Check weekday restrictions
			if ( settings.disabledWeekdays.includes( date.getDay() ) ) {
				return true;
			}
			// Check day restrictions
			if ( settings.disabledDates.includes( date.getDate() ) ) {
				return true;
			}

			// Check min date
			const endOfDayDate = endOfDay( date );
			if ( settings.minDate && endOfDayDate < settings.minDate ) {
				return true;
			}

			// Check max date
			const startOfDayDate = startOfDay( date );
			if ( settings.maxDate && startOfDayDate > settings.maxDate ) {
				return true;
			}

			return false;
		}

		function formatDate( date ) {
			const year = date.getFullYear();
			const month = ( date.getMonth() + 1 ).toString().padStart( 2, '0' );
			const day = date.getDate().toString().padStart( 2, '0' );
			const hour = date.getHours().toString().padStart( 2, '0' );
			const minute = date.getMinutes().toString().padStart( 2, '0' );
			const shortMonths = [
				'Jan',
				'Feb',
				'Mar',
				'Apr',
				'May',
				'Jun',
				'Jul',
				'Aug',
				'Sep',
				'Oct',
				'Nov',
				'Dec',
			];

			let formattedDate = settings.format;

			const replacements = {
				YYYY: year,
				YY: year.toString().slice( -2 ),
				MMM: '#######',
				MM: month,
				M: parseInt( month ),
				DD: day,
				D: parseInt( day ),
				HH: hour,
				H: parseInt( hour ),
				mm: minute,
				m: parseInt( minute ),
			};

			Object.entries( replacements ).forEach( ( [ token, value ] ) => {
				formattedDate = formattedDate.replace( token, value );
			} );
			formattedDate = formattedDate.replace(
				'#######',
				shortMonths[ parseInt( month ) - 1 ]
			);

			return formattedDate;
		}

		return this.each( function () {
			const $input = $( this );
			let defDate = new Date();

			if ( settings.defValue ) {
				defDate = new Date( settings.defValue );
			}
			let currentDate = new Date( defDate );
			let selectedDate = new Date( currentDate );

			// Create picker HTML structure
			// <span class="prad-custom-current-month"></span>
			const $picker = $( '<div>', {
				class: 'prad-custom-date-picker',
			} ).html( `
            <div class="prad-custom-calendar-header">
              <div class="prad-custom-prev-month">
			  	<svg
					xmlns="http://www.w3.org/2000/svg"
					width="6"
					height="12"
					fill="none"
					viewBox="0 0 9 14"
				>
					<path
						stroke="#0B0E04"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="1.5"
						d="m7.5 13-6-6 6-6"
					/>
				</svg>
			</div>
			  <select class="prad-select prad-custom-month-dropdown prad-scrollbar">
			  </select>
			  <select class="prad-select prad-custom-year-dropdown prad-scrollbar">
			  </select>
              <div class="prad-custom-next-month">
			  	<svg
					xmlns="http://www.w3.org/2000/svg"
					width="6"
					height="12"
					fill="none"
					viewBox="0 0 9 14"
				>
					<path
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="1.5"
						d="m1.5 13 6-6-6-6"
					/>
				</svg>
			</div>
            </div>
            <div class="prad-custom-calendar-body">
              <div class="prad-custom-date-container prad-custom-weekdays">
                <span>Sun</span>
                <span>Mon</span>
                <span>Tue</span>
                <span>Wed</span>
                <span>Thu</span>
                <span>Fri</span>
                <span>Sat</span>
              </div>
              <div class="prad-custom-date-container prad-custom-days"></div>
            </div>
          ` );

			$input.after( $picker );
			$input.attr( 'readonly', true );

			function renderCalendar() {
				const $yearSelect = $picker.find(
					'.prad-custom-year-dropdown'
				);
				const currentYear = currentDate.getFullYear();

				$yearSelect.empty();
				for ( let i = currentYear - 10; i <= currentYear + 10; i++ ) {
					const isSelected = currentYear === i ? 'selected' : '';
					$yearSelect.append(
						`<option value="${ i }" ${ isSelected }>${ i }</option>`
					);
				}

				const $monthSelect = $picker.find(
					'.prad-custom-month-dropdown'
				);
				$monthSelect.empty();
				const currentMonth = currentDate.getMonth();
				const monthNames = [
					'January',
					'February',
					'March',
					'April',
					'May',
					'June',
					'July',
					'August',
					'September',
					'October',
					'November',
					'December',
				];
				monthNames.forEach( ( month, index ) => {
					const selected = currentMonth === index ? 'selected' : '';
					$monthSelect.append(
						`<option value="${ index }" ${ selected }>${ month }</option>`
					);
				} );

				const year = currentDate.getFullYear();
				const month = currentDate.getMonth();

				// Update header
				$picker.find( '.prad-custom-current-month' ).text(
					new Date( year, month ).toLocaleString( 'default', {
						month: 'long',
						year: 'numeric',
					} )
				);

				// Clear and populate days
				const $days = $picker.find( '.prad-custom-days' ).empty();
				const firstDay = new Date( year, month, 1 ).getDay();
				const daysInMonth = new Date( year, month + 1, 0 ).getDate();

				// Add empty cells for days before first day of month
				for ( let i = 0; i < firstDay; i++ ) {
					$days.append( '<div></div>' );
				}

				// Add days
				for ( let day = 1; day <= daysInMonth; day++ ) {
					const date = new Date( year, month, day );
					const isDisabled = isDateDisabled( date );
					const isSelected =
						selectedDate.getFullYear() === date.getFullYear() &&
						selectedDate.getDate() === date.getDate() &&
						selectedDate.getMonth() === date.getMonth();

					const $dayElement = $( '<div>', {
						class: `prad-custom-day${
							isDisabled ? ' prad-disabled' : ''
						}${ isSelected ? ' prad-selected' : '' }`,
						text: day,
					} );

					if ( ! isDisabled ) {
						$dayElement.on( 'click', () => {
							selectedDate = new Date( date );
							$days
								.find( '.prad-custom-day' )
								.removeClass( 'prad-selected' );
							$dayElement.addClass( 'prad-selected' );
							const newDate = new Date( selectedDate );

							// Validating the final datetime against min/max
							if (
								( settings.minDate &&
									newDate < settings.minDate ) ||
								( settings.maxDate &&
									newDate > settings.maxDate )
							) {
								return;
							}

							selectedDate = newDate;
							$input
								.val( formatDate( selectedDate ) )
								.trigger( 'change' );

							$input
								.get( 0 )
								.dispatchEvent( new Event( 'change' ) );

							$picker.hide();
						} );
					}

					$days.append( $dayElement );
				}

				// Update navigation buttons
				const firstOfMonth = new Date( year, month, 1 );
				const lastOfMonth = new Date( year, month + 1, 0 );

				// Disable previous month navigation if minDate exists and is earlier
				$picker
					.find( '.prad-custom-prev-month' )
					.prop(
						'disabled',
						settings.minDate &&
							endOfDay( firstOfMonth ) < settings.minDate
					);

				// Disable next month navigation if maxDate exists and is later
				$picker
					.find( '.prad-custom-next-month' )
					.prop(
						'disabled',
						settings.maxDate &&
							startOfDay( lastOfMonth ) > settings.maxDate
					);
			}

			// Event handlers
			$input
				.add( $input.siblings( '.prad-input-date-icon' ) )
				.on( 'click', () => {
					const inputElement = $( this ).hasClass(
						'prad-input-date-icon'
					)
						? $input
						: $( this );
					const rect = inputElement[ 0 ].getBoundingClientRect();
					const inputHeight = $( this ).outerHeight();
					const pickerHeight = $picker.outerHeight();
					const spaceBelow =
						$( window ).height() - ( rect.top + inputHeight );
					const spaceAbove = rect.top;

					let topPosition;

					if (
						spaceBelow < pickerHeight &&
						spaceAbove > pickerHeight
					) {
						// Not enough space below, but enough space above
						topPosition = rect.top - pickerHeight - 8;
					} else {
						// Default: show below
						topPosition = rect.top + inputHeight + 1;
					}

					// $( '.prad-custom-date-picker' ).not( $picker ).hide();
					// $picker.show();

					if ( $picker.is( ':visible' ) ) {
						$picker.hide();
					} else {
						$( '.prad-custom-date-picker' ).hide(); // Hide other open pickers
						$picker.show();
						currentDate = new Date( selectedDate );
					}

					// $picker.css( {
					// 	top: topPosition,
					// 	left: rect.left,
					// } );
				} );

			$picker
				.find( '.prad-custom-prev-month' )
				.on( 'click', ( event ) => {
					event.preventDefault(); // Prevent default action of the button
					currentDate.setMonth( currentDate.getMonth() - 1 );
					renderCalendar();
				} );

			$picker
				.find( '.prad-custom-next-month' )
				.on( 'click', ( event ) => {
					event.preventDefault(); // Prevent default action of the button
					currentDate.setMonth( currentDate.getMonth() + 1 );
					renderCalendar();
				} );

			$picker
				.find( '.prad-custom-month-dropdown' )
				.on( 'change', function () {
					const selectedMonth = parseInt( $( this ).val(), 10 ); // Get selected month (0-11)
					currentDate.setMonth( selectedMonth );
					renderCalendar();
				} );

			// Handle year selection from dropdown
			$picker
				.find( '.prad-custom-year-dropdown' )
				.on( 'change', function () {
					const selectedYear = parseInt( $( this ).val(), 10 );
					currentDate.setFullYear( selectedYear );
					renderCalendar();
				} );

			// Close pops
			$( document ).on( 'click', ( e ) => {
				const $container = $input.closest(
					'.prad-custom-datetime-picker-container'
				);
				if (
					! $container.is( e.target ) &&
					$container.has( e.target ).length === 0
				) {
					$picker.hide();
				}
			} );

			// Initial render
			renderCalendar();
		} );
	};

	$.fn.timePicker = function () {
		return this.each( function () {
			const parseTimeString = ( str ) => {
				if ( ! str ) {
					return null;
				}
				const match = str.match( /^(\d{1,2}):(\d{2})\s*(AM|PM)$/i );
				if ( ! match ) {
					return null;
				}
				let [ _, hour, minute, ampm ] = match;
				hour = parseInt( hour );
				minute = parseInt( minute );
				if ( ampm.toUpperCase() === 'PM' && hour !== 12 ) {
					hour += 12;
				}
				if ( ampm.toUpperCase() === 'AM' && hour === 12 ) {
					hour = 0;
				}
				return hour * 60 + minute;
			};

			const updateDropdownStates = ( $parentPicker ) => {
				const min = parseTimeString(
					$input.attr( 'data-min-time' )?.trim()
				);
				const max = parseTimeString(
					$input.attr( 'data-max-time' )?.trim()
				);

				// AM/PM dropdown
				[ 'AM', 'PM' ].forEach( ( ampm ) => {
					let valid = false;
					for ( let h = 1; h <= 12; h++ ) {
						for ( let m = 0; m < 60; m++ ) {
							const time =
								( ampm === 'PM' && h !== 12
									? h + 12
									: ampm === 'AM' && h === 12
									? 0
									: h ) *
									60 +
								m;
							if (
								min === null ||
								max === null ||
								( min <= max
									? time >= min && time <= max
									: time >= min || time <= max )
							) {
								// if (
								// 	( min === null || time >= min ) &&
								// 	( max === null || time <= max )
								// ) {
								valid = true;
								break;
							}
						}
					}
					$parentPicker
						.find(
							`#prad-custom-ampmPicker .prad-custom-option:contains("${ ampm }")`
						)
						.toggleClass( 'prad-custom-disabled', ! valid );
				} );

				// Hour dropdown
				const ampm = $parentPicker
					.find( '#prad-custom-ampmPicker .prad-custom-selected' )
					.text();
				for ( let h = 1; h <= 12; h++ ) {
					let valid = false;
					for ( let m = 0; m < 60; m++ ) {
						const time =
							( ampm === 'PM' && h !== 12
								? h + 12
								: ampm === 'AM' && h === 12
								? 0
								: h ) *
								60 +
							m;
						if (
							min === null ||
							max === null ||
							( min <= max
								? time >= min && time <= max
								: time >= min || time <= max )
						) {
							// if (
							// 	( min === null || time >= min ) &&
							// 	( max === null || time <= max )
							// ) {
							valid = true;
							break;
						}
					}
					$parentPicker
						.find(
							`#prad-custom-hourPicker .prad-custom-option:contains("${ h }")`
						)
						.toggleClass( 'prad-custom-disabled', ! valid );
				}

				// Minute dropdown
				const hour = parseInt(
					$parentPicker
						.find( '#prad-custom-hourPicker .prad-custom-selected' )
						.text()
				);
				for ( let m = 0; m < 60; m++ ) {
					const time =
						( ampm === 'PM' && hour !== 12
							? hour + 12
							: ampm === 'AM' && hour === 12
							? 0
							: hour ) *
							60 +
						m;
					const valid =
						min === null ||
						max === null ||
						( min <= max
							? time >= min && time <= max
							: time >= min || time <= max );
					// const valid =
					// 	( min === null || time >= min ) &&
					// 	( max === null || time <= max );
					$parentPicker
						.find(
							`#prad-custom-minutePicker .prad-custom-option:contains("${ m
								.toString()
								.padStart( 2, '0' ) }")`
						)
						.toggleClass( 'prad-custom-disabled', ! valid );
				}
			};
			const initDropdown = ( selector ) => {
				$( selector ).on( 'click', function () {
					const $this = $( this );
					$this.toggleClass( 'prad-custom-active' );
					$( '.prad-custom-dropdown' )
						.not( $this )
						.removeClass( 'prad-custom-active' )
						.find( '.prad-custom-options' )
						.hide();
					$this.find( '.prad-custom-options' ).toggle();
				} );

				$( selector ).on(
					'click',
					'.prad-custom-option:not(.prad-custom-disabled)',
					function ( e ) {
						e.stopPropagation();
						const text = $( this ).text();
						$( this )
							.closest( '.prad-custom-dropdown' )
							.find( '.prad-custom-selected' )
							.text( text )
							.trigger( 'change' );
						$( this )
							.closest( '.prad-custom-dropdown' )
							.find( '.prad-custom-options' )
							.hide();
						updateDropdownStates(
							$( this ).closest( '.prad-custom-time-picker' )
						);
					}
				);
			};

			const $input = $( this );
			const defTime = $input.attr( 'data-deftime' ) || '';
			const [ _time, _ampm ] = defTime.split( ' ' );
			const [ _hours, _minutes ] = _time.split( ':' );

			const $picker = $( '<div>', {
				class: 'prad-custom-time-field-picker',
			} ).html( `
				<div class="prad-custom-time-picker">
					<div class="prad-custom-dropdown" id="prad-custom-hourPicker">
						<div class="prad-custom-selected">${ _hours || 'HH' }</div>
						<div class="prad-custom-options prad-scrollbar"></div>
						<div class="prad-custom-selected-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="8" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 1 6 6 6-6"></path></svg></div>
					</div>
					<div class="prad-custom-dropdown" id="prad-custom-minutePicker">
						<div class="prad-custom-selected">${ _minutes || 'MM' }</div>
						<div class="prad-custom-selected-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="8" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 1 6 6 6-6"></path></svg></div>
						<div class="prad-custom-options prad-scrollbar"></div>
					</div>
					<div class="prad-custom-dropdown" id="prad-custom-ampmPicker">
						<div class="prad-custom-selected">${ _ampm || 'AM' }</div>
						<div class="prad-custom-selected-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="8" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 1 6 6 6-6"></path></svg></div>
						<div class="prad-custom-options prad-scrollbar"></div>
					</div>
				</div>
			` );

			$input.after( $picker );
			$input.attr( 'readonly', true );

			// Initialize time selects
			const $hourSelect = $picker.find( '#prad-custom-hourPicker' );
			const $minuteSelect = $picker.find( '#prad-custom-minutePicker' );
			const $ampmSelect = $picker.find( '#prad-custom-ampmPicker' );

			const hoursOptions = $hourSelect.find( '.prad-custom-options' );
			hoursOptions.empty();
			Array.from( { length: 12 }, ( _, i ) => i + 1 ).forEach(
				( val ) => {
					const display = val.toString().padStart( 2, '0' );
					hoursOptions.append(
						`<div class="prad-custom-option">${ display }</div>`
					);
				}
			);

			// Minute options
			const minutesOptions = $minuteSelect.find( '.prad-custom-options' );
			minutesOptions.empty();
			Array.from( { length: 60 }, ( _, i ) => i ).forEach( ( val ) => {
				const display = val.toString().padStart( 2, '0' );
				minutesOptions.append(
					`<div class="prad-custom-option">${ display }</div>`
				);
			} );

			// AM PM options
			const ampmOptions = $ampmSelect.find( '.prad-custom-options' );
			ampmOptions.empty();
			[ 'AM', 'PM' ].forEach( ( val ) => {
				const display = val.toString().padStart( 2, '0' );
				ampmOptions.append(
					`<div class="prad-custom-option">${ display }</div>`
				);
			} );

			initDropdown( $hourSelect );
			initDropdown( $minuteSelect );
			initDropdown( $ampmSelect );
			updateDropdownStates( $picker.find( '.prad-custom-time-picker' ) );

			$picker.find( '.prad-custom-selected' ).on( 'change', function () {
				const hours = $hourSelect
					.find( '.prad-custom-selected' )
					.text();
				const minutes = $minuteSelect
					.find( '.prad-custom-selected' )
					.text();
				const ampm = $ampmSelect.find( '.prad-custom-selected' ).text();
				$input
					.val( hours + ':' + minutes + ' ' + ampm )
					.trigger( 'change' );

				$input.get( 0 ).dispatchEvent( new Event( 'change' ) );
			} );

			// Event handlers
			$input
				.add( $input.siblings( '.prad-input-time-icon' ) )
				.on( 'click', () => {
					const inputElement = $( this ).hasClass(
						'prad-input-time-icon'
					)
						? $input
						: $( this );
					const rect = inputElement[ 0 ].getBoundingClientRect();
					const inputHeight = $( this ).outerHeight();
					const pickerHeight = $picker.outerHeight();
					const spaceBelow =
						$( window ).height() - ( rect.top + inputHeight );
					const spaceAbove = rect.top;

					let topPosition;

					if (
						spaceBelow < pickerHeight &&
						spaceAbove > pickerHeight
					) {
						// Not enough space below, but enough space above
						topPosition = rect.top - pickerHeight - 8;
					} else {
						// Default: show below
						topPosition = rect.top + inputHeight + 1;
					}

					if ( $picker.is( ':visible' ) ) {
						$picker.hide();
						$picker.find( '.prad-custom-options' ).hide();
					} else {
						$(
							'.prad-custom-time-field-picker .prad-custom-options'
						).hide();
						$( '.prad-custom-time-field-picker' ).hide(); // Hide other open pickers
						$picker.show();
					}
				} );

			$( document ).on( 'click', ( e ) => {
				const $container = $input.closest(
					'.prad-custom-datetime-picker-container'
				);
				if (
					! $container.is( e.target ) &&
					$container.has( e.target ).length === 0
				) {
					$picker.hide();
					$picker.find( '.prad-custom-options' ).hide();
				}
			} );
		} );
	};

	// Wait for DOM to be ready
	$( document ).ready( function () {
		// Initialize all date pickers
		$( '.prad-custom-date-input' ).each( function () {
			const $input = $( this );
			const options = $input.getDateTimeData();
			$input.dateTimePicker( options );
			$input.attr( 'data-initdate', 'yes' );
		} );

		// Picker Added after dom init compatible
		$( document ).on( 'focus', '.prad-custom-date-input', function () {
			const $input = $( this );

			if ( $input.attr( 'data-initdate' ) !== 'yes' ) {
				const options = $input.getDateTimeData();
				$input.siblings( '.prad-custom-date-picker' ).remove();
				$input.dateTimePicker( options );
				$input.attr( 'data-initdate', 'yes' );
			}
		} );

		// Initialize all time pickers
		$( '.prad-custom-time-input' ).each( function () {
			const $input = $( this );
			$input.timePicker();
			$input.attr( 'data-inittime', 'yes' );
		} );

		// Time Picker Added after dom init compatible
		$( document ).on( 'focus', '.prad-custom-time-input', function () {
			const $input = $( this );

			if ( $input.attr( 'data-inittime' ) !== 'yes' ) {
				$input.siblings( '.prad-custom-time-field-picker' ).remove();
				$input.timePicker();
				$input.attr( 'data-inittime', 'yes' );
			}
		} );
	} );

	$.fn.getDateTimeData = function () {
		const $input = $( this );

		let disabledWeekdays = [];
		let disabledDates = [];
		if ( $input.attr( 'data-disabled-weekdays' ) ) {
			if ( typeof $input.attr( 'data-disabled-weekdays' ) === 'string' ) {
				disabledWeekdays = JSON.parse(
					$input.attr( 'data-disabled-weekdays' )
				);
			} else {
				disabledWeekdays = $input.attr( 'data-disabled-weekdays' );
			}
		}
		if ( $input.attr( 'data-disabled-date' ) ) {
			if ( typeof $input.attr( 'data-disabled-date' ) === 'string' ) {
				disabledDates = JSON.parse(
					$input.attr( 'data-disabled-date' )
				);
			} else {
				disabledDates = $input.attr( 'data-disabled-date' );
			}
		}
		return {
			format: $input.attr( 'data-format' ) || 'YYYY-MM-DD',
			minDate: $input.attr( 'data-min-date' )
				? new Date( $input.attr( 'data-min-date' ) )
				: null,
			maxDate: $input.attr( 'data-max-date' )
				? new Date( $input.attr( 'data-max-date' ) )
				: null,
			disabledWeekdays: disabledWeekdays.map( Number ),
			disabledDates: disabledDates.map( Number ),
			defValue: $input.attr( 'data-defval' )
				? new Date( $input.attr( 'data-defval' ) )
				: null,
		};
	};

	// eslint-disable-next-line no-undef
} )( jQuery );
