/**
 * errors Jquery Plugin
 * 
 * Takes an object of errors and displays them for their respective form inputs.
 *
 * Note: Uses the Object.create() method, which is not available in older browsers.
 * I personally put this code in an util file:
 *
 *		if ( typeof Object.create !== 'function' ) {
 *			Object.create = function( obj ) {
 *				function F(){};
 *				F.prototype = obj;
 *				return new F();
 *			}
 *		}
 * 
 * Thanks to Jeffrey Way of Nettuts.com and Tutsplus.com
 * for this util.
*/
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery',], factory);
	} else {
		// Browser globals
		factory(jQuery, window, document);
	}
}(function ($,window,document,undefined) {
	
	// object to attach to global
	var Errors = {
		
		init: function (options, el) {
		   
		   // self references this object
		   var self = this;

			// cache elements
			self.el = el;
			self.$el = $(el);
			
			// override defaults with user-supplied options
			self.options = $.extend( {}, $.fn.errors.options, options );
   			
			// remove current errors if options allow
			if ( self.options.removeCurrent ) {
				$(".input-error",self.$el).removeClass("input-error");
				$("label.err",self.$el).remove();
			}

			// if errors not empty, do action
			if ( ! $.isEmptyObject(self.options.errors) ) {
				for ( var name in self.options.errors ) {
					self.displayError(name,self.options.errors[name],self);
				}
			}
			
		},
		
		displayError: function(name, message, self) {
		    // check if general form error
		    if ( name === "form-error" ) {
		        return $.pnotify({
		            'title':message.title,
		            'text':message.text,
		            'type':message.type,
		            'hide':message.hide
		        })
		    }
		    
			// get object
			var inp = $("input[name='"+name+"'],select[name='"+name+"'],textarea[name='"+name+"'], div#"+name+"upload",self.$el),
			    label;
			
			if ( inp.length ) {
				// create label
				label = $("<label></label>",{
					"text":message,
					"for":name,
					"class":"err"
				});
				inp.before(label);
				
				// add error class to parent p
				inp.parent().addClass("input-error");
			}
		},
		
		handlers: {
		   
		}
		
	};
	
	// attach to global jquery object
	$.fn.errors = function (options) {
		return this.each(function(){
			var errors = Object.create( Errors );
			errors.init(options, this);
		});
	};
	
	// set option defaults
	$.fn.errors.options = {
		errors:{},
		removeCurrent:true
	};
	
}));