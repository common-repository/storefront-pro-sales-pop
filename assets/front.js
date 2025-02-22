
/*
 * Plugin front end scripts
 *
 * @package Storefront_Pro_Sales_Pop
 * @version 1.0.0
 */
jQuery( function ( $ ) {
	var $img, $loc, $pop, $tim, $ttl, interval, intervalCount, itemsCount;
	$pop = $( '#sfp-sales-pop' );
	$img = $( '#sfpsp-img' );
	$loc = $( '#sfpsp-location' );
	$ttl = $( '#sfpsp-title' );
	$tim = $( '#sfpsp-time' );
	itemsCount = sfpspOrderedItems.length;
	intervalCount = 0;
	if ( itemsCount ) {
		interval = setInterval( function () {
			var item, turnedOffAt;
			turnedOffAt = parseInt( localStorage.getItem( 'sfpspOff' ) );
			if ( parseInt( intervalCount ) === parseInt( itemsCount ) || Date.now() - turnedOffAt < 72000000 ) {
				return clearInterval( interval );
			}
			item = ! sfpspSettings.loopOrders && sfpspOrderedItems[intervalCount % itemsCount];
			$pop.data( 'permalink', item.link );
			$img.html( item.img );
			$loc.text( item.city + ', ' + item.country );
			$ttl.text( item.title );
			$tim.text( parseInt( item.time.replace( /[^0-9]/g, '' ) ) < 120 ? item.time : '' );
			$pop.fadeIn( 250 );
			if ( sfpspSettings.isPreview ) {
				return clearInterval( interval );
			}
			setTimeout( function () {
				return $pop.fadeOut( 250 );
			}, 3000 );
			return intervalCount ++;
		}, 5000 );
		return $pop.click( function ( e ) {
			console.log( e.target );
			if ( e.target.classList.contains( 'close' ) ) {
				$( this ).fadeOut( 250 );
				clearInterval( interval );
				if ( localStorage && localStorage.setItem ) {
					return localStorage.setItem( 'sfpspOff', Date.now() );
				}
			} else {
				return window.location.href = $pop.data( 'permalink' );
			}
		} );
	}
} );