define(function(){
	
	// Checks to make sure Object has method create()
	if ( typeof Object.create !== 'function' ) {
		Object.create = function( obj ) {
			function F(){};
			F.prototype = obj;
			return new F();
		}
	}
	
});
