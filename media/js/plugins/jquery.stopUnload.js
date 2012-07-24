(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    
    $.stopUnload = function(options){
        // store this
        var self = this;
        
        // plugin defaults
        self.defaults = {
            override:false,
            message:"Are you sure you want to leave this page?",
            cancel:false
        };
        
        // extend options
        self.options = $.extend( {}, self.defaults, options );
        
        // check if cancelling
        if ( self.options.cancel ) {
            window.onbeforeunload = null;
            return;
        }
        
        // check if already set
        if ( typeof window.onbeforeunload === "function" ) {
            if ( !self.options.override ) return;
            else window.onbeforeunload = null;
        }
        
        // set unload event
        window.onbeforeunload = function(evt){
            var retVal = self.options.message;
            evt.returnValue = retVal;
            return retVal;
        };
    }
    
}));