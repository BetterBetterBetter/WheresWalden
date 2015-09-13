EasySocial.module( 'site/toolbar/login' , function($){

	var module 				= this;

	EasySocial.require()
	.library( 'popbox' )
	.done(function($){

		EasySocial.Controller(
			'Toolbar.Login',
			{
				defaultOptions:
				{
					"{dropdown}"		: "[data-toolbar-login-dropdown]"
				}
			},
			function(self){ return{

				init: function()
				{


				},

				"{self} popboxActivate" : function( el , event , popbox )
				{
					$( popbox.tooltip ).find( 'label' ).on( 'click' , function( event )
					{
						// Prevent propagation
						event.stopPropagation();
					});
					// $( popbox.tooltip ).implement( EasySocial.Controller.Toolbar.Login.User );
				}
			}}
		);

		module.resolve();
	});

});
