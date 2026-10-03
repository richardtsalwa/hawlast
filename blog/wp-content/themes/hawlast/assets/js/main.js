/**
 * HAWLAST theme scripts.
 *
 * Vanilla, deferred, no dependencies. Mobile navigation only: the desktop pill
 * menu needs no JavaScript at all.
 */
( function () {
	'use strict';

	var toggle = document.querySelector( '[data-hawlast-toggle]' );

	if ( ! toggle ) {
		return;
	}

	var menu = document.getElementById( toggle.getAttribute( 'aria-controls' ) );

	if ( ! menu ) {
		return;
	}

	function setOpen( open ) {
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		menu.classList.toggle( 'is-open', open );
	}

	toggle.addEventListener( 'click', function () {
		setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
	} );

	// Close when focus leaves the navigation, so keyboard users are not stranded.
	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key === 'Escape' && toggle.getAttribute( 'aria-expanded' ) === 'true' ) {
			setOpen( false );
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( toggle.getAttribute( 'aria-expanded' ) !== 'true' ) {
			return;
		}

		if ( ! menu.contains( event.target ) && ! toggle.contains( event.target ) ) {
			setOpen( false );
		}
	} );
}() );