jQuery(document).ready(function( $ ) {

	//Flexslider
	function flexslider() {
		$( ".flexslider" ).flexslider( {
			animation: 		"fade",
			animationSpeed: 250,
			slideshow: false
		});

		$( ".flex-next" ).html( '<i class="fa fa-chevron-right"></i>' );
		$( ".flex-prev" ).html( '<i class="fa fa-chevron-left"></i>' );
	}
	flexslider();

	//Fitvid
	function fitvids() {
		$( ".post-content iframe ").not( ".fitvid iframe" ).wrap( "<div class='fitvid'/>" );
		$( ".fitvid" ).fitVids();
	}
	fitvids();

	//View Lightbox
    function lightbox() {
    	$( ".single .slides li a" ).addClass( "view" );
    	$( ".single .slides li a" ).attr( "rel", "lightbox" );
    }
    lightbox();

	//Infinite Scroll
	if ( ( custom_js_vars.infinite_scroll ) == 'disabled' ) {} else {
		$( ".posts" ).infinitescroll( {
		      loading: {
		          msgText: 		"...Loading more posts...",
		          finishedMsg: 	"- All posts loaded -"
		      },
		      nextSelector: 	'.post-nav-right a',
		      navSelector: 		'.post-nav',
		      itemSelector: 	'article',
		      contentSelector: 	'.posts',
		      appendCallback: 	true
		},function () {
			//Run fitvid and flexslider on infinite posts
			fitvids();
			flexslider();
		});
	}

    //Custom BG
    if ( $( "body" ).hasClass( "custom-background" ) ) {
    	$( "body" ).removeClass( "fixed-bg" );
    }

    //Detect Android
    var ua = navigator.userAgent.toLowerCase();
	var isAndroid = ua.indexOf( "android" ) > -1; //&& ua.indexOf("mobile");
	if( isAndroid ) {
		$( "body.fixed-bg" ).addClass( "android-bg" );
	}

	// Author profile toggle
	$( ".navatar-badge" ).click( function() {
		$( ".author-profile" ).slideToggle( 200 );
		$( ".mobile-toggle.header-nav, .mobile-toggle.header" ).slideUp( 200 );
		return false;
	});

	// Mobile menu toggle
	$( ".menu-toggle" ).click( function(e) {
		e.preventDefault();
		$( ".header-nav, .widgets" ).slideToggle( 200 );
		$( ".author-profile" ).slideUp( 200 );
	});

	// Move logo to header on mobile
	$( window ).on( "resize load", function () {
		var current_width = $( window ).width();

		// If screen width is below iPad size
		if( current_width < 769 ) {

			// Move logo to the header on mobile
			$( ".logo-wrap" ).insertBefore( "#wrapper" );
		}

		//If screen width is above iPad size
		if( current_width > 768 ) {

			// Move logo back to sidebar
			$( ".logo-wrap" ).prependTo( ".header" );
		}
	});

	// Drop menu toggle
	$( window ).on( "load", function () {
		var current_width = $( window ).width();

		//If screen width is above iPad size
		if( current_width > 768 ) {

			// Drop navigation
			$( ".header-nav:not(.mobile-toggle) ul li" ).hoverIntent( {
				over : navover,
				out : navout,
				timeout : 200
			});

		}
	});

	function navover() {
		$( this ).children( "ul" )
		.stop( true,true )
		.css( "display", "none" )
		.slideDown( 200 );
	}

	function navout() {
		$( this ).children( "ul" )
		.stop( true, true )
		.slideUp( 200 );
	}

	// Mobile header toggle
	$( window ).on( "resize", function () {
		var current_width = $( window ).width();

		// If screen width is below iPad size
		if( current_width < 769 ) {
			$( ".header-nav, .header" ).addClass( "mobile-toggle" );
		}

		//If screen width is above iPad size
		if( current_width > 768 ) {
			$( ".header-nav, .header" ).show();
			$( ".header-nav" ).removeClass( "mobile-toggle" );
		}
	});

});