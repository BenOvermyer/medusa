var ManticoreAuth = require( './ManticoreAuth.js' );
var ManticoreUser = require( './ManticoreUser.js' );
var ManticoreChapter = require( './ManticoreChapter.js' );
var ManticoreRegister = require( './ManticoreRegister.js');

jQuery( document ).ready( function ( $ ) {

    var authController = new ManticoreAuth();
    var userController = new ManticoreUser();
    var chapterController = new ManticoreChapter();
	var registerController = new ManticoreRegister();

    if ( $( '.userform' ).length > 0 ) {
        userController.initMemberForm();
    }

	if ($('.registerform').length > 0) {
		registerController.initRegisterForm();
	}

    var MTIProjectId = '5c059f73-3466-4691-8b9a-27e7d9c1a9c7';
    (function () {
        var mtiTracking = document.createElement('script');
        mtiTracking.type = 'text/javascript';
        mtiTracking.async = 'true';
        mtiTracking.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//fast.fonts.net/t/trackingCode.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(mtiTracking);
    })();
} );
