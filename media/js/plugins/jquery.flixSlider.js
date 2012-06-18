/**
 * flixSlider Jquery Plugin
 * 
 * Netflix-esque slider for images. 
 * Best used on images of the same height and width.
 * Registers as an anonymous AMD module.
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


// registers as an AMD module if applicable
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery','plugins/jquery.easing'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
	
	// object to attach to global
	var Slider = {
		// initializing function
		init: function (options, el) {
			
			var self = this;
			
			// cache elements
			self.el = el;
			self.$el = $(el);
			self.$container = $("<div></div>",{'class':'flixSlider-container',}).appendTo(self.$el);
			self.handlers.resetWidths(self);
			self.$scroll = self.$el.find("div.flixSlider-scroll");
			self.$scroll.appendTo(self.$container);
			self.isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/);
			
			// override defaults with user-supplied options
			self.options = $.extend( {}, $.fn.flixSlider.options, options );
		 	
			// get scrolling div dimensions
			self.setScrollDims();
			// add arrows
			self.addPrevNextBtns();
			// position arrows
			self.setArrowPosition();
			// set event handlers for arrows
			self.addArrowEvents();
			// set scroll event handler for container
			self.setScrollWheel();
			// set window resize events
			self.setWindowResizeEvts();
			
			self.$container.animate({
				opacity:1
			});
		},
		
		setScrollDims: function() {
			
			
			var self = this,
				newScrollWidth = 0,
				newScrollHeight = 0;
			
			// loop through elements, get width of each, add to scroll width
			self.$scroll
				.children().each(function(index, el){
					var $el = $(el),
						curElHeight = $el.outerHeight(true);
					newScrollWidth += $el.outerWidth(true);
					newScrollHeight = (curElHeight > newScrollHeight) 
						? curElHeight 
						: newScrollHeight;
				})
			.end()
			.css({
				'width':newScrollWidth+"px",
				'height':newScrollHeight+"px"
			});
			
			self.scrollWidth = newScrollWidth;
			self.scrollHeight = newScrollHeight;
			
		},
		
		
		addPrevNextBtns: function() {
			console.log("addPrevNextBtns called");
			
			var self = this,
				// to store height of arrow later
				arrowsTop,
				// display them?
				display = (self.containerWidth < self.scrollWidth) ? 'block' : 'none',
				// create previous button
				$arrowP = $('<button></button>',{
					'class':'flixSlider-ButtonPrev'
				})
				// add border color according to options
				.css({
					borderColor:"transparent "+self.options.arrowColor+" transparent transparent"
				})
				// add to container
				.prependTo(self.$el),
				
				// create next button
				$arrowN = $('<button></button>',{
					'class':'flixSlider-ButtonNext'
				})
				.css({
					borderColor:"transparent transparent transparent "+self.options.arrowColor,
					display:display
				})
				.appendTo(self.$el);
				
			// add to object for reference later
			self.$arrowP = $arrowP;
			self.$arrowN = $arrowN;
			self.shouldScroll = display === 'block';
				
		},
		
		setArrowPosition: function(){
			// get calc height of arrow to center vertically
			var self = this,
				arrowsTop = self.scrollHeight/2 - self.$arrowP.outerHeight()/2;
			
			// half the container height - half the arrow height is the correct top value
			self.$arrowP.css({
				top: arrowsTop
			});
			
			self.$arrowN.css({
				top: arrowsTop
			});
			
		},
		
		addArrowEvents: function () {
			var self = this;
			
			// set mouseover and mouseout events
			
			if (self.options.hover && !self.isMobile) { // check if hover events desired
			   
			   self.$arrowP
   				.on("mouseenter",function(evt){
   					self.handlers.scrollBack(self);
   				})
   				.on("mouseleave",function(evt){
   					self.handlers.stopScroll(self);
   				});
   			self.$arrowN
   			   .on("mouseenter",function(evt){
   					self.handlers.scrollForward(self);
   				})
   				.on("mouseleave", function(evt){
   					self.handlers.stopScroll(self,true);
   				});
			   
			}
			
			self.$arrowP
			   .on("click",function(evt){
				   self.handlers.moveBack(self)
				});
			self.$arrowN
				.on("click",function(evt){
				   self.handlers.moveForward(self);
				});
		},
		
		setScrollWheel: function () {
		   var self = this;
		   
		   self.$container.on("scroll", function(evt){
		      self.handlers.scrollListener(self);
		   });
		   
		},
		
		setWindowResizeEvts: function() {
		   var self = this;
		   $(window).on("resize",function(evt){
		      self.handlers.resetWidths(self);
		   });
		},
		
		handlers: {
			scrollBack: function (self) {
				
				if ( ! self.shouldScroll ) return;
				// stop scrolling
				self.$container.stop();
				self.$arrowN.show();
				var curLeftOffset = this._getOffset(self),
					distance = self.scrollWidth - self.containerWidth,
					time = ( (curLeftOffset) / self.options.pixelsPerSecond ) * 1000;
					
					console.log("curLeftOffset: "+curLeftOffset);
					console.log("time:"+time);
				
				self.$container.animate({
					'scrollLeft':1
				},time,'linear');
				
			},
			stopScroll: function (self,forward) {
			   var scrollLeft = forward ? "+=" : "-=" ,
			      amt = 40,
			      time = ( amt/self.options.pixelsPerSecond ) * 2000;
			      
			   
				self.$container
				   .stop()
				   .animate(
				      {'scrollLeft':scrollLeft+amt},
				      600,
				      // "EaseOutExpo"
				      "easeOutExpo"
				   );
			},
			scrollForward: function(self) {
				
				if ( ! self.shouldScroll ) return;
				self.$container.stop();
				self.$arrowP.show();
				var curLeftOffset = this._getOffset(self),
					distance = self.scrollWidth - self.containerWidth,
					time = ( (distance - curLeftOffset) / self.options.pixelsPerSecond ) * 1000;
				
				self.$container.animate({
					'scrollLeft':distance
				},time,'linear');
				
				
			},
			moveBack: function (self) { // move the scroll back a half distance, or to the beginning (for mobile users mostly)
			   
			   if ( ! self.shouldScroll ) return;
			   
			   var curLeftOffset = this._getOffset(self);
			   
			   // stop scroll if any
			   self.$container.stop();
            
            
			   if ( curLeftOffset < self.containerWidth ) { // move to 0
               self.$container.animate(
                  {'scrollLeft':'0'},
                  600,
                  'easeOutExpo'
               );       // move back width of container
			   } else {                                     
			      self.$container.animate(
			         {'scrollLeft':'-='+self.containerWidth},
			         700,
			         'easeOutExpo'
		         );
			   }
			   
			},
			moveForward: function (self) {
			   if ( ! self.shouldScroll ) return;
			   
			   var curLeftOffset = this._getOffset(self),
			      furthestLeft = self.scrollWidth - self.containerWidth;
			   
			   // stop scroll if any
			   self.$container.stop();
            
            
			   if ( furthestLeft - curLeftOffset < self.containerWidth ) { // move to 0
               self.$container.animate(
                  {'scrollLeft':furthestLeft},
                  600,
                  'easeOutExpo'
               );       // move back width of container
			   } else {                                     
			      self.$container.animate(
			         {'scrollLeft':'+='+self.containerWidth},
			         700,
			         'easeOutExpo'
			      );
			   }
			},
			scrollListener: function (self) {
			   
			   var scrollLeft = self.$container[0].scrollLeft;
			   if ( scrollLeft === 0 ) self.$arrowP.hide();
			   else if (scrollLeft >= self.scrollWidth - self.containerWidth ) self.$arrowN.hide();
			   else self.$arrowN.show() && self.$arrowP.show();
			},
			_getOffset: function (self) {
            return Math.abs(parseInt(self.$container[0].scrollLeft));
			},
			resetWidths: function (self) {
			   self.containerWidth = self.$el.outerWidth();
			},
		}
		
	};
	
	// attach to global jquery object
	$.fn.flixSlider = function (options) {
		return this.each(function(){
			var slider = Object.create( Slider );
			slider.init(options, this);
		});
	};
	
	// set option defaults
	$.fn.flixSlider.options = {
		arrowColor:"black",
		pixelsPerSecond:300,
		slideGroupCount:4,
		hover:true
	};
	
}));