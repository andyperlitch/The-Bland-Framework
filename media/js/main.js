/*
* Heatshield Online Store
* www.heatshieldstore.com
* Copyright 2012, Bruce's Custom Covers
*/

(function($){

    var uid = 'ar' + +new Date,

    defaults = autoResize.defaults = {
        onResize: function(){},
        onBeforeResize: function(){return 123},
        onAfterResize: function(){return 555},
        animate: {
            duration: 200,
            complete: function(){}
        },
        extraSpace: 50,
        minHeight: 'original',
        maxHeight: 500,
        minWidth: 'original',
        maxWidth: 500
    };

    autoResize.cloneCSSProperties = [
    'lineHeight', 'textDecoration', 'letterSpacing',
    'fontSize', 'fontFamily', 'fontStyle', 'fontWeight',
    'textTransform', 'textAlign', 'direction', 'wordSpacing', 'fontSizeAdjust',
    'paddingTop', 'paddingLeft', 'paddingBottom', 'paddingRight', 'width'
    ];

    autoResize.cloneCSSValues = {
        position: 'absolute',
        top: -9999,
        left: -9999,
        opacity: 0,
        overflow: 'hidden'
    };

    autoResize.resizableFilterSelector = [
    'textarea:not(textarea.' + uid + ')',
    'input:not(input[type])',
    'input[type=text]',
    'input[type=password]',
    'input[type=email]',
    'input[type=url]'
    ].join(',');

    autoResize.AutoResizer = AutoResizer;

    $.fn.autoResize = autoResize;

    function autoResize(config) {
        this.filter(autoResize.resizableFilterSelector).each(function(){
            new AutoResizer( $(this), config );
        });
        return this;
    }

    function AutoResizer(el, config) {

        if (el.data('AutoResizer')) {
            el.data('AutoResizer').destroy();
        }

        config = this.config = $.extend({}, autoResize.defaults, config);
        this.el = el;

        this.nodeName = el[0].nodeName.toLowerCase();

        this.originalHeight = el.height();
        this.previousScrollTop = null;

        this.value = el.val();

        if (config.maxWidth === 'original') config.maxWidth = el.width();
        if (config.minWidth === 'original') config.minWidth = el.width();
        if (config.maxHeight === 'original') config.maxHeight = el.height();
        if (config.minHeight === 'original') config.minHeight = el.height();

        if (this.nodeName === 'textarea') {
            el.css({
                resize: 'none',
                overflowY: 'hidden'
            });
        }

        el.data('AutoResizer', this);

        // Make sure onAfterResize is called upon animation completion
        config.animate.complete = (function(f){
            return function() {
                config.onAfterResize.call(el);
                return f.apply(this, arguments);
            };
            }(config.animate.complete));

            this.bind();

    }

    AutoResizer.prototype = {

        bind: function() {

            var check = $.proxy(function(){
                this.check();
                return true;
            }, this);

            this.unbind();

            this.el
            .bind('keyup.autoResize', check)
             //.bind('keydown.autoResize', check)
             .bind('change.autoResize', check)
            .bind('paste.autoResize', function() {
                 setTimeout(function() { check(); }, 0);
            });

            if (!this.el.is(':hidden')) {
                this.check(null, true);
            }

        },

        unbind: function() {
            this.el.unbind('.autoResize');
        },

        createClone: function() {

            var el = this.el,
            clone = this.nodeName === 'textarea' ? el.clone() : $('<span/>');

            this.clone = clone;

            $.each(autoResize.cloneCSSProperties, function(i, p){
                clone[0].style[p] = el.css(p);
            });

            clone
            .removeAttr('name')
            .removeAttr('id')
            .addClass(uid)
            .attr('tabIndex', -1)
            .css(autoResize.cloneCSSValues);

            if (this.nodeName === 'textarea') {
                clone.height('auto');
            } else {
                clone.width('auto').css({
                    whiteSpace: 'nowrap'
                });
            }

        },

        check: function(e, immediate) {

            if (!this.clone) {
                this.createClone();
                this.injectClone();
            }

            var config = this.config,
            clone = this.clone,
            el = this.el,
            value = el.val();

            // Do nothing if value hasn't changed
            if (value === this.prevValue) { return true; }
            this.prevValue = value;

            if (this.nodeName === 'input') {

                clone.text(value);

                // Calculate new width + whether to change
                var cloneWidth = clone.width(),
                newWidth = (cloneWidth + config.extraSpace) >= config.minWidth ?
                cloneWidth + config.extraSpace : config.minWidth,
                currentWidth = el.width();

                newWidth = Math.min(newWidth, config.maxWidth);

                if (
                    (newWidth < currentWidth && newWidth >= config.minWidth) ||
                    (newWidth >= config.minWidth && newWidth <= config.maxWidth)
                ) {

                    config.onBeforeResize.call(el);
                    config.onResize.call(el);

                    el.scrollLeft(0);

                    if (config.animate && !immediate) {
                        el.stop(1,1).animate({
                            width: newWidth
                            }, config.animate);
                        } else {
                            el.width(newWidth);
                            config.onAfterResize.call(el);
                        }

                }

                return;

            }

            // TEXTAREA

            clone.width(el.width()).height(0).val(value).scrollTop(10000);

            var scrollTop = clone[0].scrollTop;

            // Don't do anything if scrollTop hasen't changed:
            if (this.previousScrollTop === scrollTop) {
                return;
            }

            this.previousScrollTop = scrollTop;

            if (scrollTop + config.extraSpace >= config.maxHeight) {
                el.css('overflowY', '');
                scrollTop = config.maxHeight;
                immediate = true;
            } else if (scrollTop <= config.minHeight) {
                scrollTop = config.minHeight;
            } else {
                el.css('overflowY', 'hidden');
                scrollTop += config.extraSpace;
            }

            config.onBeforeResize.call(el);
            config.onResize.call(el);

            // Either animate or directly apply height:
            if (config.animate && !immediate) {
                el.stop(1,1).animate({
                    height: scrollTop
                }, config.animate);
            } else {
                el.height(scrollTop);
            config.onAfterResize.call(el);
            }

        },

        destroy: function() {
            this.unbind();
            this.el.removeData('AutoResizer');
            this.clone.remove();
            delete this.el;
            delete this.clone;
        },

        injectClone: function() {
            (
                autoResize.cloneContainer ||
                (autoResize.cloneContainer = $('<arclones/>').appendTo('body'))
            ).append(this.clone);
        }

    };

})(jQuery);

var syspath = '/ajax/';
function msgWin(width,on,content,timeout,noclose){
	if(on){
	    var closeBtn = ( noclose == true ) ? '' : '<a class="xbox" href="Javascript:msgWin(null,false)"></a>';
		if(width == null) width = '30%';
		if($("#grayout").length > 0){
			if($("#grayout").css('display') == 'none') {
				$("#grayout").fadeIn('fast');
				$(".msg_wrap").fadeIn('fast');
			}else{
			    $("#grayout").unbind();
			}
			$(".msg").html('loading...');
			$(".msg").animate({
				width:width
			});
			
			if(content != null) $(".msg").html(closeBtn + '<div>'+content+'</div>');
		} else{
			var str = '<div id="grayout"></div><div class="msg_wrap"><div class="msg" style="width:';
			str += width;
			str += ';"><div class="tctr">loading...</div></div></div>';
			$("body").append(str);
			if(content != null) $(".msg").html(closeBtn+'<div>'+content+'</div>');
			$("#grayout").fadeIn('fast');
			$(".msg_wrap").fadeIn('fast');
		}
		var newY = window.pageYOffset + 100;
    	$(".msg_wrap").css('top',newY+'px');
		if(timeout != null) setTimeout('msgWin(null,false)',timeout);
		if(!noclose) $("#grayout").click(function(){msgWin(null,false);$(this).unbind('click');});
	} else {
		if($("#grayout").css('display') == 'block' && $("#grayout").length > 0) {
			$("#grayout").fadeOut('fast');
			$(".msg_wrap").fadeOut('fast',function(){
			    $(".msg").html('');
			});
		}
	}
}
function fillMsg(res){
	$(".msg").html('<a class="xbox" href="Javascript:msgWin(null,false)"></a>'+res);
}
var acctMsgShow = false;
var acctMsgKey = 0;
function acctMsg(msg,dur){
    if( $('.acctMsg').length == 0 ) $('body').append('<div class="acctMsg"></div>');
	if( acctMsgShow == true ){
		setTimeout(function(){
		    acctMsg(msg,dur)
		},1000,'30','30');
	} else {
	    var newY = getScrollXY()[1] + 50;
    	$(".acctMsg").css('top',newY+'px');
		acctMsgShow = true;
		acctMsgKey = Math.round(Math.random()*10000) + 1;
		$('.acctMsg').html('<a class="xbox" href="Javascript:clsAcctMsg('+acctMsgKey+')"></a>' + msg );
		if(dur == null) dur = 8000;
		$(".acctMsg").fadeIn('fast');
		setTimeout('clsAcctMsg('+acctMsgKey+')',dur);
	}
}
function getScrollXY() {
  var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  return [ scrOfX, scrOfY ];
}
function clsAcctMsg(key){
	if(key == acctMsgKey){
		$(".acctMsg").fadeOut('fast');
		acctMsgShow = false;
	}
}
function setDefaultVal (reference,def) {
    var elem = $(reference);
	if(elem.val() != def) elem.removeClass('gray');
	elem.live('focus',function(){
		if( $(this).val() == def ) {
			$(this).val('');
			$(this).removeClass('gray');
		}
	});
	elem.live('blur',function(){
		if( $(this).val() == '') {
			$(this).val(def);
			$(this).addClass('gray');
		}
	});
}
function setAutoResize(elem){
    var $elem = $(elem);
    $elem.autoResize();
}
function cLog(msg){
    if(window.console){
        console.log(msg);
    }
}
function initHsTabs(ajax){
  $(".hs-tabs").each(function(){
    // set $this var
    var $this = $(this);
    
    // get scrolling items
    var $items = $(".hs-tab-scroll-item",$this);
    
    // check if items greater than 1
    if($items.length <= 1) return;
    
    // vars
    var targetWidth = $items.width(),                    // get target width for items
      outerWidth = $items.outerWidth(),                  // outer width of items (includes padding)
      targetPadding = ( outerWidth - targetWidth) / 2,   // calculate target padding for items
      itemCount = $items.length,                         // number of items
      $initLI = $(".hs-tab-ul li.sel"),                  // initially selected list element
      initIndex = $initLI.index(),                       // index of above
      initNext = $initLI.next().find('a').attr('href'), // href of item after initially selected link
      initPrev = $initLI.prev().find('a').attr('href'); // href of item before initially selected link
    
    // add forward/back button
    if ( (initIndex + 1) < itemCount) $this.append('<a class="next-btn" href="'+initNext+'"></a>');
    if ( initIndex > 0 ) $this.append('<a class="prev-btn" href="'+initPrev+'"></a>');
    
    // set css of items
    $items
      .css('width',targetWidth+'px')
      .css('paddingRight',targetPadding+'px')
      .css('paddingLeft',targetPadding+'px');
      
    // set scroll element
    var $scroll = $(".hs-tab-scroll",$this);
    $scroll.css({
      width: ($items.length * outerWidth + 20)+'px',
      left: outerWidth*initIndex*-1
    });
    
    // loop through tabs at the top
    $(".hs-tab-ul li a,.next-btn,.prev-btn",$this).live('click',function(e){
      
      // check if ie and ajax
      if(isIE() && ajax === true) return;
      
      // prevent default action
      e.preventDefault();
      
      // get id and index
      var 
        $elem = $(this),
        trgHref = $elem.attr('href'),
        $trg = $('#'+trgHref.replace(/[^-_\w]+/,'')),
        $next = $trg.next(),
        $prev = $trg.prev(),
        trgIndex = $items.index($trg);
        
      
      
      // blur link
      $elem.blur();
      
      // move to selected section
      $scroll.animate({
        left:outerWidth*trgIndex*-1
      });
      
      // load page and push history state if ajax
      if(ajax === true){
        if($trg.html() == '') $trg.html('<div class="ajax-loading">loading...</div>');
        pushHistory('title for '+trgHref,trgHref);
        
      }
      
      // remove/add .sel from tabs
      var 
        $li_sel = $this
          .find('.hs-tab-ul')
          .children()
          .removeClass('sel')
          .find('a[href="'+trgHref+'"]')
          .parent()
          .addClass('sel'),
        $nextLink = $li_sel.next().find('a'),
        $prevLink = $li_sel.prev().find('a');
        
      // rewrite prev/next btns
      $('.next-btn,.prev-btn',$this).remove();
      if ( (trgIndex + 1) < itemCount) $this.append('<a class="next-btn" href="'+$nextLink.attr('href')+'"></a>');
      if ( trgIndex > 0 ) $this.append('<a class="prev-btn" href="'+$prevLink.attr('href')+'"></a>');
      
    });
  });
}
function isIE()
{
  return (navigator.appName == 'Microsoft Internet Explorer');
}
/*!
ICanHaz.js version 0.10 -- by @HenrikJoreteg
More info at: http://icanhazjs.com
*/
;(function () {
/*
  mustache.js — Logic-less templates in JavaScript

  See http://mustache.github.com/ for more info.
*/

var Mustache = function () {
  var _toString = Object.prototype.toString;

  Array.isArray = Array.isArray || function (obj) {
    return _toString.call(obj) == "[object Array]";
  }

  var _trim = String.prototype.trim, trim;

  if (_trim) {
    trim = function (text) {
      return text == null ? "" : _trim.call(text);
    }
  } else {
    var trimLeft, trimRight;

    // IE doesn't match non-breaking spaces with \s.
    if ((/\S/).test("\xA0")) {
      trimLeft = /^[\s\xA0]+/;
      trimRight = /[\s\xA0]+$/;
    } else {
      trimLeft = /^\s+/;
      trimRight = /\s+$/;
    }

    trim = function (text) {
      return text == null ? "" :
        text.toString().replace(trimLeft, "").replace(trimRight, "");
    }
  }

  var escapeMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;'
  };

  function escapeHTML(string) {
    return String(string).replace(/&(?!\w+;)|[<>"']/g, function (s) {
      return escapeMap[s] || s;
    });
  }

  var regexCache = {};
  var Renderer = function () {};

  Renderer.prototype = {
    otag: "{{",
    ctag: "}}",
    pragmas: {},
    buffer: [],
    pragmas_implemented: {
      "IMPLICIT-ITERATOR": true
    },
    context: {},

    render: function (template, context, partials, in_recursion) {
      // reset buffer & set context
      if (!in_recursion) {
        this.context = context;
        this.buffer = []; // TODO: make this non-lazy
      }

      // fail fast
      if (!this.includes("", template)) {
        if (in_recursion) {
          return template;
        } else {
          this.send(template);
          return;
        }
      }

      // get the pragmas together
      template = this.render_pragmas(template);

      // render the template
      var html = this.render_section(template, context, partials);

      // render_section did not find any sections, we still need to render the tags
      if (html === false) {
        html = this.render_tags(template, context, partials, in_recursion);
      }

      if (in_recursion) {
        return html;
      } else {
        this.sendLines(html);
      }
    },

    /*
      Sends parsed lines
    */
    send: function (line) {
      if (line !== "") {
        this.buffer.push(line);
      }
    },

    sendLines: function (text) {
      if (text) {
        var lines = text.split("\n");
        for (var i = 0; i < lines.length; i++) {
          this.send(lines[i]);
        }
      }
    },

    /*
      Looks for %PRAGMAS
    */
    render_pragmas: function (template) {
      // no pragmas
      if (!this.includes("%", template)) {
        return template;
      }

      var that = this;
      var regex = this.getCachedRegex("render_pragmas", function (otag, ctag) {
        return new RegExp(otag + "%([\\w-]+) ?([\\w]+=[\\w]+)?" + ctag, "g");
      });

      return template.replace(regex, function (match, pragma, options) {
        if (!that.pragmas_implemented[pragma]) {
          throw({message:
            "This implementation of mustache doesn't understand the '" +
            pragma + "' pragma"});
        }
        that.pragmas[pragma] = {};
        if (options) {
          var opts = options.split("=");
          that.pragmas[pragma][opts[0]] = opts[1];
        }
        return "";
        // ignore unknown pragmas silently
      });
    },

    /*
      Tries to find a partial in the curent scope and render it
    */
    render_partial: function (name, context, partials) {
      name = trim(name);
      if (!partials || partials[name] === undefined) {
        throw({message: "unknown_partial '" + name + "'"});
      }
      if (!context || typeof context[name] != "object") {
        return this.render(partials[name], context, partials, true);
      }
      return this.render(partials[name], context[name], partials, true);
    },

    /*
      Renders inverted (^) and normal (#) sections
    */
    render_section: function (template, context, partials) {
      if (!this.includes("#", template) && !this.includes("^", template)) {
        // did not render anything, there were no sections
        return false;
      }

      var that = this;

      var regex = this.getCachedRegex("render_section", function (otag, ctag) {
        // This regex matches _the first_ section ({{#foo}}{{/foo}}), and captures the remainder
        return new RegExp(
          "^([\\s\\S]*?)" +         // all the crap at the beginning that is not {{*}} ($1)

          otag +                    // {{
          "(\\^|\\#)\\s*(.+)\\s*" + //  #foo (# == $2, foo == $3)
          ctag +                    // }}

          "\n*([\\s\\S]*?)" +       // between the tag ($2). leading newlines are dropped

          otag +                    // {{
          "\\/\\s*\\3\\s*" +        //  /foo (backreference to the opening tag).
          ctag +                    // }}

          "\\s*([\\s\\S]*)$",       // everything else in the string ($4). leading whitespace is dropped.

        "g");
      });


      // for each {{#foo}}{{/foo}} section do...
      return template.replace(regex, function (match, before, type, name, content, after) {
        // before contains only tags, no sections
        var renderedBefore = before ? that.render_tags(before, context, partials, true) : "",

        // after may contain both sections and tags, so use full rendering function
            renderedAfter = after ? that.render(after, context, partials, true) : "",

        // will be computed below
            renderedContent,

            value = that.find(name, context);

        if (type === "^") { // inverted section
          if (!value || Array.isArray(value) && value.length === 0) {
            // false or empty list, render it
            renderedContent = that.render(content, context, partials, true);
          } else {
            renderedContent = "";
          }
        } else if (type === "#") { // normal section
          if (Array.isArray(value)) { // Enumerable, Let's loop!
            renderedContent = that.map(value, function (row) {
              return that.render(content, that.create_context(row), partials, true);
            }).join("");
          } else if (that.is_object(value)) { // Object, Use it as subcontext!
            renderedContent = that.render(content, that.create_context(value),
              partials, true);
          } else if (typeof value == "function") {
            // higher order section
            renderedContent = value.call(context, content, function (text) {
              return that.render(text, context, partials, true);
            });
          } else if (value) { // boolean section
            renderedContent = that.render(content, context, partials, true);
          } else {
            renderedContent = "";
          }
        }

        return renderedBefore + renderedContent + renderedAfter;
      });
    },

    /*
      Replace {{foo}} and friends with values from our view
    */
    render_tags: function (template, context, partials, in_recursion) {
      // tit for tat
      var that = this;

      var new_regex = function () {
        return that.getCachedRegex("render_tags", function (otag, ctag) {
          return new RegExp(otag + "(=|!|>|&|\\{|%)?([^#\\^]+?)\\1?" + ctag + "+", "g");
        });
      };

      var regex = new_regex();
      var tag_replace_callback = function (match, operator, name) {
        switch(operator) {
        case "!": // ignore comments
          return "";
        case "=": // set new delimiters, rebuild the replace regexp
          that.set_delimiters(name);
          regex = new_regex();
          return "";
        case ">": // render partial
          return that.render_partial(name, context, partials);
        case "{": // the triple mustache is unescaped
        case "&": // & operator is an alternative unescape method
          return that.find(name, context);
        default: // escape the value
          return escapeHTML(that.find(name, context));
        }
      };
      var lines = template.split("\n");
      for(var i = 0; i < lines.length; i++) {
        lines[i] = lines[i].replace(regex, tag_replace_callback, this);
        if (!in_recursion) {
          this.send(lines[i]);
        }
      }

      if (in_recursion) {
        return lines.join("\n");
      }
    },

    set_delimiters: function (delimiters) {
      var dels = delimiters.split(" ");
      this.otag = this.escape_regex(dels[0]);
      this.ctag = this.escape_regex(dels[1]);
    },

    escape_regex: function (text) {
      // thank you Simon Willison
      if (!arguments.callee.sRE) {
        var specials = [
          '/', '.', '*', '+', '?', '|',
          '(', ')', '[', ']', '{', '}', '\\'
        ];
        arguments.callee.sRE = new RegExp(
          '(\\' + specials.join('|\\') + ')', 'g'
        );
      }
      return text.replace(arguments.callee.sRE, '\\$1');
    },

    /*
      find `name` in current `context`. That is find me a value
      from the view object
    */
    find: function (name, context) {
      name = trim(name);

      // Checks whether a value is thruthy or false or 0
      function is_kinda_truthy(bool) {
        return bool === false || bool === 0 || bool;
      }

      var value;

      // check for dot notation eg. foo.bar
      if (name.match(/([a-z_]+)\./ig)) {
        var childValue = this.walk_context(name, context);
        if (is_kinda_truthy(childValue)) {
          value = childValue;
        }
      } else {
        if (is_kinda_truthy(context[name])) {
          value = context[name];
        } else if (is_kinda_truthy(this.context[name])) {
          value = this.context[name];
        }
      }

      if (typeof value == "function") {
        return value.apply(context);
      }
      if (value !== undefined) {
        return value;
      }
      // silently ignore unkown variables
      return "";
    },

    walk_context: function (name, context) {
      var path = name.split('.');
      // if the var doesn't exist in current context, check the top level context
      var value_context = (context[path[0]] != undefined) ? context : this.context;
      var value = value_context[path.shift()];
      while (value != undefined && path.length > 0) {
        value_context = value;
        value = value[path.shift()];
      }
      // if the value is a function, call it, binding the correct context
      if (typeof value == "function") {
        return value.apply(value_context);
      }
      return value;
    },

    // Utility methods

    /* includes tag */
    includes: function (needle, haystack) {
      return haystack.indexOf(this.otag + needle) != -1;
    },

    // by @langalex, support for arrays of strings
    create_context: function (_context) {
      if (this.is_object(_context)) {
        return _context;
      } else {
        var iterator = ".";
        if (this.pragmas["IMPLICIT-ITERATOR"]) {
          iterator = this.pragmas["IMPLICIT-ITERATOR"].iterator;
        }
        var ctx = {};
        ctx[iterator] = _context;
        return ctx;
      }
    },

    is_object: function (a) {
      return a && typeof a == "object";
    },

    /*
      Why, why, why? Because IE. Cry, cry cry.
    */
    map: function (array, fn) {
      if (typeof array.map == "function") {
        return array.map(fn);
      } else {
        var r = [];
        var l = array.length;
        for(var i = 0; i < l; i++) {
          r.push(fn(array[i]));
        }
        return r;
      }
    },

    getCachedRegex: function (name, generator) {
      var byOtag = regexCache[this.otag];
      if (!byOtag) {
        byOtag = regexCache[this.otag] = {};
      }

      var byCtag = byOtag[this.ctag];
      if (!byCtag) {
        byCtag = byOtag[this.ctag] = {};
      }

      var regex = byCtag[name];
      if (!regex) {
        regex = byCtag[name] = generator(this.otag, this.ctag);
      }

      return regex;
    }
  };

  return({
    name: "mustache.js",
    version: "0.4.0",

    /*
      Turns a template and view into HTML
    */
    to_html: function (template, view, partials, send_fun) {
      var renderer = new Renderer();
      if (send_fun) {
        renderer.send = send_fun;
      }
      renderer.render(template, view || {}, partials);
      if (!send_fun) {
        return renderer.buffer.join("\n");
      }
    }
  });
}();
/*!
  ICanHaz.js -- by @HenrikJoreteg
*/
/*global  */
(function () {
    function trim(stuff) {
        if (''.trim) return stuff.trim();
        else return stuff.replace(/^\s+/, '').replace(/\s+$/, '');
    }
    var ich = {
        VERSION: "0.10",
        templates: {},
        
        // grab jquery or zepto if it's there
        $: (typeof window !== 'undefined') ? window.jQuery || window.Zepto || null : null,
        
        // public function for adding templates
        // can take a name and template string arguments
        // or can take an object with name/template pairs
        // We're enforcing uniqueness to avoid accidental template overwrites.
        // If you want a different template, it should have a different name.
        addTemplate: function (name, templateString) {
            if (typeof name === 'object') {
                for (var template in name) {
                    this.addTemplate(template, name[template]);
                }
                return;
            }
            if (ich[name]) {
                console.error("Invalid name: " + name + "."); 
            } else if (ich.templates[name]) {
                console.error("Template \"" + name + "  \" exists");
            } else {
                ich.templates[name] = templateString;
                ich[name] = function (data, raw) {
                    data = data || {};
                    var result = Mustache.to_html(ich.templates[name], data, ich.templates);
                    return (ich.$ && !raw) ? ich.$(result) : result;
                };
            }
        },
        
        // clears all retrieval functions and empties cache
        clearAll: function () {
            for (var key in ich.templates) {
                delete ich[key];
            }
            ich.templates = {};
        },
        
        // clears/grabs
        refresh: function () {
            ich.clearAll();
            ich.grabTemplates();
        },
        
        // grabs templates from the DOM and caches them.
        // Loop through and add templates.
        // Whitespace at beginning and end of all templates inside <script> tags will 
        // be trimmed. If you want whitespace around a partial, add it in the parent, 
        // not the partial. Or do it explicitly using <br/> or &nbsp;
        grabTemplates: function () {        
            var i, 
                scripts = document.getElementsByTagName('script'), 
                script,
                trash = [];
            for (i = 0, l = scripts.length; i < l; i++) {
                script = scripts[i];
                if (script && script.innerHTML && script.id && (script.type === "text/html" || script.type === "text/x-icanhaz")) {
                    ich.addTemplate(script.id, trim(script.innerHTML));
                    trash.unshift(script);
                }
            }
            for (i = 0, l = trash.length; i < l; i++) {
                trash[i].parentNode.removeChild(trash[i]);
            }
        }
    };
    
    // Use CommonJS if applicable
    if (typeof require !== 'undefined') {
        module.exports = ich;
    } else {
        // else attach it to the window
        window.ich = ich;
    }
    
    if (typeof document !== 'undefined') {
        if (ich.$) {
            ich.$(function () {
                ich.grabTemplates();
            });
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                ich.grabTemplates();
            }, true);
        }
    }
        
})();
})();

/*!
Numeric Entry plugin
*/
;(function($){
  $.fn.numericEntry = function(options){
    var opts = $.extend({}, $.fn.numericEntry.defaults, options);
    
    return this.each(function(index,elem){
      $(elem).bind('focus',function(){
        // grab the field and value
        var $this = $(this),
          val = $this.val();

        // take off .gray class
        $this.removeClass('gray');

        // if zero, make nothing, else highlight
        if (val <= 0) $this.val('');
        else $this.select();

        // clear any previous blur and keydown events
        $this
          .unbind('blur keydown')
          .bind('keydown',function(event){
            return isNumericEntry(event.which);
          })
          .bind('blur',function(){
            var computedVal = cleanNumericValue($this.val());
            if (computedVal === '') $this.addClass('gray').val('0');
            else $this.val(computedVal);
          });
      });
    });
  }
  
  // private functions
  /**
   * Checks if keyCode provided is OK for numeric entry
   * 
   * @var Int keyCode       : code of key stroke
   * @return Bool
  */
  function isNumericEntry(keyCode)
  {
    // log code for debug
    cLog('keyCode: '+keyCode);
    
    // standard number keys
    var isStandard = (keyCode > 47 && keyCode < 58);

    // extended keyboard numbers (keypad)
    var isExtended = (keyCode > 95 && keyCode < 106);

    // backspace, forward delete, arrows
    var isOther = ( ',8,9,37,38,39,40,46,'.indexOf(','+keyCode+',') > -1);

    // check for vals
    if( isStandard || isExtended || isOther ) return true
    else return false;
  }

  /**
   * Cleans a value of non-numeric chars
   * 
   * @param String value     : value to be eval'd and cleaned
   * @return String newValue
  */
  function cleanNumericValue(value)
  {
    var pattern = new RegExp('[^0-9]+','g');
    return value.replace(pattern,'');
  }
  
  
  // defaults
  $.fn.numericEntry.defaults = {
    defaultVal:'0'
  }
})(jQuery);
