/**
* blandUpload Jquery Plugin
* 
* A bland little uploader
*
* Note: Uses the Object.create() method, which is not available in older browsers.
* I personally put this code in an util file:
*
*    if ( typeof Object.create !== 'function' ) {
*       Object.create = function( obj ) {
*          function F(){};
*          F.prototype = obj;
*          return new F();
*       }
*    }
* 
* Thanks to Jeffrey Way of Nettuts.com and Tutsplus.com
* for this util.
*/
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery','libs/mustache','plugins/jquery.form','jqueryui/sortable','plugins/jquery.colorbox','plugins/jquery.Jcrop'], factory);
    } else {
        // Browser globals
        factory(jQuery, Mustache);
    }
}(function ($,Mustache) {

    // object to attach to global
    var Bland = {

        init: function (options, el) {

            // self references this object (Bland)
            var self = this;

            // cache container
            self.container = el;
            self.$container = $(el);

            // override defaults with user-supplied options
            self.options = $.extend( {}, $.fn.blandUpload.options, options, self.$container.data() );

            // set fields for upload table
            self.setFields(self);
            
            // start count for rows in the upload table
            self.rowCount = 0;

            // add bland class to container
            self.$container.addClass("blandUpload-container");
            
            // set fileRows key
            self.fileRows = [];

            // check for inputs already in the container
            
            // store in variable and remove
            
            // create top part with button to upload
            self.createTop(self);

            // create middle part containing table to show uploads and downloads
            self.createTable(self);

            // create bottom part that shows upload progress
            self.createBottom(self);

            // add handler for clicking on upload button
            self.$button.on("click",function(evt){
                self.handlers.onUploadButtonClick(evt,self);
            });
            
            // add collapse link handlers
            self.$collapse_link.on("click",function(evt){
                self.toggleMiddle(self);
                evt.preventDefault();
            });
            
            // add listener for table resize
            self.$table.on("resize",function(evt){
                self.handlers.resizeTable(self);
            });
            // toggle middle
            self.toggleMiddle(self);
        },

        setFields: function(self) {
            // init fields
            var fields = [];
            // check if max file count
            if (self.options.maxfiles) fields.push("selected");
            // check for thumb
            if (self.options.makethumbs) fields.push("thumb");
            // add filename (required field)
            fields.push("filename");
            // check for caption
            if (self.options.makecaptions) fields.push(self.options.captionlabel);
            // check for crop
            if (self.options.allowcrop) fields.push("crop");
            // add remove button
            fields.push("remove");

            // store fields array
            self.fields = fields;
        },

        createTop: function(self) {
            // append rendered top template
            self.$container.append(
                Mustache.to_html(self.templates.top(self), {
                    "uploadtext":self.options.uploadtext,
                    "maxfiles":self.options.maxfiles
                })
            );
            // store top elem
            self.$top = $("div.blandUpload-top",self.$container);
            // store upload button
            self.$button = $("button.blandUpload-button",self.$top);
            // store status message
            self.$statusMsg = $(".blandUpload-status",self.$top);

            // store filesUploaded, filesSelected, filesAllowed
            self.$filesUploaded = $("span.blandUpload-filesUploaded",self.$statusMsg);
            self.$filesSelected = $("span.blandUpload-filesSelected",self.$statusMsg);
            self.$filesAllowed =  $("span.blandUpload-filesAllowed",self.$statusMsg);
            
        },

        createTable: function(self) {
            // append table to container
            self.$container.append(
                Mustache.to_html(self.templates.middle, {
                    fields:self.fields
                })
            );
            // store table container
            self.$middle = $("div.blandUpload-middle", self.$container);
            // store table
            self.$table = $("table.blandUpload-table", self.$middle);
            // store tbody
            self.$tbody = $("tbody",self.$table);
        },

        createBottom: function(self) {
            // append bottom to container
            self.$container.append(
                Mustache.to_html(self.templates.bottom, {})
            );
            // store bottom
            self.$bottom = $("div.blandUpload-bottom",self.$container);
            // store progress
            self.$progress = $("progress",self.$bottom);
            // store collapse link
            self.$collapse_link = $("a.blandUpload-collapse", self.$bottom);
            // store collapse span
            self.$collapse_span = $("span", self.$collapse_link);
        },

        buildUploadRows: function(files, self) {
            $.each(files,function(index, file){
                // create data obj for row template
                var data = $.extend( file,
                                {
                                    'index':self.rowCount++,
                                    'thumbwidth':self.options.thumbwidth,
                                    'makethumbs':self.options.makethumbs,
                                    'filename':file.name,
                                    'fileSizeKb':Math.round(file.size / 1024),
                                    'fileid':null,
                                    'makecaptions':self.options.makecaptions,
                                    'allowcrop':self.options.allowcrop,
                                    'hasFileMax':!!self.options.maxfiles,
                                    'isChecked':$("input.blandUpload-selected-cb:checked",self.$tbody).length < self.options.maxfiles
                                }
                            )
                    ,
                    // get template
                    template = self.templates.row(self),
                    // get raw html
                    rowHtml = Mustache.to_html( template , data ),
                    // jquery-ize it, append tbody
                    $row = $(rowHtml).appendTo(self.$tbody),
                    // grab crop button, set init behavior
                    $cropBtn = $("button.blandUpload-crop",$row)
                        .on("click",function(evt){
                            evt.preventDefault();
                        }),
                    // set remove btn behavior
                    $removeBtn = $("button.blandUpload-remove",$row)
                        .on("click",function(evt){
                            evt.preventDefault();
                            self.disableRow($row.data("chosen",true), "This file will be removed.", true, self);
                            
                        }),
                    $selectBox = $("input.blandUpload-selected-cb",$row)
                        .on("click",function(evt){
                            // compare checked values with max files
                            self.updateStats(self);
                        })
                ;
                // push to rows property
                self.fileRows.push($row);
                self.updateStats(self);
            });
            self.$tbody.sortable({
                helper: function(e, tr) {
                    var $originals = tr.children();
                    var $helper = tr.clone();
                    $helper.children().each(function(index)
                    {
                        // Set helper cell sizes to match the original sizes
                        $(this).width($originals.eq(index).width())
                    });
                    return $helper;
                },
                placeholder: "blandUpload-sortrow-helper",
                axis:"y"
            });
        },
        
        updateStats: function(self){
            var filesUploaded = self.fileRows.length,
                $cbs = $("input.blandUpload-selected-cb",self.$tbody),
                $selected_cbs = $cbs.filter(":checked"),
                filesSelected = $selected_cbs.length,
                isLimit = (filesSelected >= self.options.maxfiles);    
            // set stats
            self.$filesUploaded.text(filesUploaded);
            self.$filesSelected.text(filesSelected);
            self.$filesAllowed.text(self.options.maxfiles);

            // disable all unchecked boxes
            $cbs.not(":checked").each(function(i, el){
                var $el = $(this),
                    $row = $el.parent().parent();
                    
                if (isLimit) self.disableRow($row, "You have chosen the maximum number of files. Deselect others to choose this one.", false, self);
                else if ( !$row.data("chosen") ) self.enableRow($row);
                
            });
        },
        
        buildDownloadRows: function(files, self) {
            $.each(files,function(index, file){
                // cache upload row
                var $row = $("tr.blandUpload-row-"+file.index, self.$tbody )
                    .removeClass("blandUpload-uploadingrow")
                    .data({
                        "imgWidth":file.width,
                        "imgHeight":file.height
                    }),
                    $thumb_img = $("img.blandUpload-thumb-"+file.index, $row),
                    $thumb_inp = $("input.blandUpload-thumb-"+file.index, $row),
                    $src_inp = $("input."+self.options.prefix+"filename-"+file.index, $row);
                if (file.errorMessage) {
                    // disable that row
                    self.disableRow($row.data("chosen",true), file.errorMessage, false, self);
                    setTimeout(function(){
                        $row.fadeOut();
                    },4000);
                } else {
                    
                    // NOTE: do NOT use index, use file.index
                    // set filename
                    $(".blandUpload-filename-"+file.index, $row).text(file.name);
                    // set filename input
                    $src_inp.val(file.name);
                    // change src of img 
                    $thumb_img.attr('src',self.options.uploaddir + file.thumb);
                    // set thumb input
                    $thumb_inp.val(file.thumb);
                    // set behavior for thumb adjustment
                    $("td div.blandUpload-thumb-div button",$row)
                        .on("click",function(evt){
                            self.doCrop(
                                $row,
                                self.options.uploaddir+file.name, 
                                self.options.uploaddir+file.thumb,
                                {
                                    "width":self.options.thumbwidth, 
                                    "minHeight":(1/self.options.thumbratio) * self.options.thumbwidth,
                                    "ratio":self.options.thumbratio
                                },
                                $thumb_img,
                                $thumb_inp
                            );
                            evt.preventDefault();
                        })
                    ;
                    // set crop behavior
                    $("button.blandUpload-crop",$row)
                        .on("click",function(evt){
                            self.doCrop(
                                $row,
                                self.options.uploaddir+file.name,
                                self.options.uploaddir+file.name, 
                                {
                                    'maxWidth':self.options.maximgwidth, 
                                    'maxHeight':self.options.maximgheight, 
                                    'ratio':self.options.imgratio
                                },
                                null,
                                $src_inp,
                                function(res){
                                    $row.data({
                                        "imgWidth":res.width,
                                        "imgHeight":res.height
                                    })
                                }
                            );
                            evt.preventDefault();
                        });
                }
            });
        },
        
        doCrop: function($row, src, dest, options, $img_target, $inp_target, callBack) {
            // create crop form
            var self = this,
                defaults = {
                    'width':0,
                    'height':0,
                    'maxWidth':0,
                    'maxHeight':0,
                    'minWidth':0,
                    'minHeight':0,
                    'ratio':0,
                    'src':src,
                    'dest':dest
                },
                croptions = $.extend({},defaults, options);
                winWidth = $(window).width() - 50,
                imgWidth = winWidth > $row.data("imgWidth") ? $row.data("imgWidth") : winWidth,
                imgHeight = Math.floor(imgWidth * ($row.data("imgHeight")/$row.data("imgWidth")) ),
                tpl = self.templates.cropForm(self),
                $form = $( Mustache.to_html( tpl ,{ 
                    "src":src,                      
                    "imgWidth":$row.data("imgWidth"),            
                    "imgHeight":$row.data("imgHeight")
                } ) ).ajaxForm({
                    beforeSubmit:function(data){
                        if (data[0].value === 0 || data[0].value === "") {
                            alert("Please select a crop area");
                            return false;
                        }
                    },
                    data:croptions,
                    success:function(data){
                        // check for success
                        if (data.success === "1") {
                            $.colorbox.close();
                            if ($img_target instanceof jQuery) {
                                $img_target.attr({
                                    "src":data.dir+data.src+data.queryString,
                                    "width":data.width,
                                    "height":data.height
                                });
                            }
                            if ($inp_target instanceof jQuery) {
                                $inp_target.val(data.src);
                            }
                            // if callBack
                            if (typeof callBack === "function") callBack(data);
                        } else {
                            alert("An error occurred!");
                        }
                    }
                }),                      
                $img = $("img",$form),
                $finishBtn = $("button:eq(0)",$form),
                $cancelBtn = $("button:eq(1)",$form),
                $x = $("input.x",$form),
                $y = $("input.y",$form),
                $w = $("input.w",$form),
                $h = $("input.h",$form),
                $coords = $("input",$form).filter(":hidden");
                updateCoords = function(c){
                    $x.val(c.x);
                    $y.val(c.y);
                    $w.val(c.w);
                    $h.val(c.h);
                },
                clearCoords = function(){
                    $coords.val("");
                };
            
            // wrap image in container
            $img.wrap(function(i){
               return $('<div></div>',{
                   css:{
                       'width':imgWidth,
                       'height':imgHeight
                   }
               });
            });
            
            // set behavior for buttons
            $cancelBtn.on("click",function(evt){
                $.colorbox.close();
                evt.preventDefault();
            });
            
            // do modal window for crop
            $.colorbox({
                inline:true,
                href:$form,
                scrolling:false,
                onComplete:function(){
                    $img.Jcrop({
                        onChange:   updateCoords,
                        onSelect:   updateCoords,
                        onRelease:  clearCoords,
                        boxWidth: imgWidth,
                        boxHeight: imgHeight,
                        aspectRatio:croptions.ratio
                    });
                }
            });
            
        },
        
        disableRow: function($row, message, undo, self) {
            // check if not already disabled
            if ( $row.find(".blandUpload-disable-div").length ) return;
            
            // if maxfiles is set and checkbox is checked, uncheck it
            if (self.options.maxfiles > 0) {
                var $cb = $row.find("input.blandUpload-selected-cb:checked");
                $cb.prop("checked",false).attr("checked",false).trigger("click");
                $cb.prop("checked",false);
            }
            
            // set row
            var disableDivHtml = Mustache.to_html( self.templates.disableMsg , {
                    'message':message,
                    'undo':undo
                }),
                $disable = $(disableDivHtml),
                $undoBtn = $("button",$disable)
                    .on("click",function(evt){
                        self.enableRow($row.data("chosen",false));
                        self.updateStats(self);
                        evt.preventDefault();
                    }),
                rowHeight = $row.height();
            ;
            // add disable message to row
            $row.append($disable.height(rowHeight));

            
            
            // disable all inputs
            $("input, textarea", $row)
                .prop("disabled",true)
            ;
        },
        
        enableRow: function($row, $disable) {
            // enable inputs
            $("input, textarea, button", $row).prop("disabled",false);
            // check that disable was provided
            if (!$disable) $disable = $row.find("div.blandUpload-disable-div");
            
            // delete disable message
            $disable.remove();
        },
        
        toggleMiddle: function(self){
            if (self.$middle.css('display') === "none") {
                self.$middle.slideDown();
                self.$collapse_span.addClass("up").removeClass("down");
            } else {
                // change collapse span
                // self.$collapse_span.removeClass("up");
                
                // slide up
                self.$middle.slideUp();
                self.$collapse_span.addClass("down").removeClass("up");
            }
        },

        handlers: {
            onUploadButtonClick: function(evt, self) {
                // append form to body
                var formHtml = self.templates.uploadForm(self),
                form = $(formHtml).css('display','none').appendTo("body"),
                fileInp = $('input[type="file"]',form);

                // set change handler on input
                self.handlers.setFileChangeHandler(self,fileInp);

                // trigger click on fileInp
                fileInp.trigger("click");
                evt.preventDefault();
            },
            setFileChangeHandler:function(self,input) {
                // store form variable
                var form = input.parent(),
                // set handler function for ie and normal
                handler = function(){
                    if( input.val() !== "" ) {
                        // get files to be uploaded
                        var files = input[0].files;
                        
                        // get current number of rows (before new additions, set in options)
                        self.options['nextindex'] = self.rowCount;
                        
                        // build uploading rows
                        self.buildUploadRows(files, self);
                        
                        // get rows
                        var $rows = $("tr.blandUpload-uploadingrow",self.$tbody);
                        
                        // fire onuploadstart
                        if (typeof self.options.onuploadstart === "function") 
                            self.options.onuploadstart(files);
                        
                        // submit form with ajaxSubmit
                        form.ajaxSubmit({
                            url:self.options.uploadaction,
                            dataType:"json",
                            data:self.options,
                            success:function(data){
                                self.buildDownloadRows(data, self);
                            },
                            uploadProgress: function(event, pos, total, percentTotal){
                                // console.log(percentTotal);
                                self.$progress
                                .animate({"value":percentTotal})
                                .text(percentTotal);
                            },
                            error:function(){
                                // disable each row
                                $rows.each(function(index,obj){
                                    var $row = $(this);
                                    self.disableRow($row,"An error occurred on this upload!",false,self);
                                });
                            },
                            complete:function(){
                                form.remove();
                                self.$progress.stop().val(0).text(0);
                            }
                        });
                    }
                };

                if ($.browser.msie && $.browser.version < 9) {
                    setTimeout(function(){
                        handler();
                        },0);
                } else {
                    input.on("change",function(evt){
                        handler();
                    });
                }
            },
            resizeTable:function(self){
                
                return;
                
                if (self.$middle.css('display') === "none") return;
                
                self.$middle.stop();
                
                var newHeight = self.$table.height();
                self.$middle.animate({
                    'height':newHeight,
                    'maxHeight':newHeight
                });
            }
        },
        templates: {
                top:function(self){
                    return '<div class="blandUpload-top">'
                        + '<button class="blandUpload-button">{{uploadtext}}</button>'
                        + '<div class="blandUpload-status">'
                            + '<span class="blandUpload-filesUploaded">0</span> files uploaded'
                            + '{{#maxfiles}}'
                            + ', <span class="blandUpload-filesSelected">0</span> files selected, <span class="blandUpload-filesAllowed">'+self.options.maxfiles+'</span> files allowed.'
                            + '{{/maxfiles}}'
                        +'</div>'
                    + '</div>';
                },
                middle:'<div class="blandUpload-middle"><table class="blandUpload-table"><thead><tr>{{#fields}}<th scope="col">{{.}}</th>{{/fields}}</tr></thead><tbody></tbody></table></div>',
                'bottom':'<div class="blandUpload-bottom"><a class="blandUpload-collapse" href="#"><span class="down"></span></a><div class="blandUpload-progress"><progress max="100" value="0">0</progress></div></div>',
                uploadForm:function(self){
                    return '<form method="post" enctype="multipart/form-data" action="' + self.options.uploadaction + '">' +
                    '<input type="file" name="'+self.options.uploadinputname+'[]" multiple />' +
                    '<input type="hidden" name="blandUpload" value="true" />' +
                    '</form>';
                },
                row:function(self){
                    return '<tr class="blandUpload-row-{{index}} blandUpload-uploadingrow" data-index="{{index}}">'
                        + '{{#hasFileMax}}'
                        +'<td>'
                            + '<input type="checkbox" {{#isChecked}}checked="checked" {{/isChecked}}{{^isChecked}}disabled="disabled" {{/isChecked}}class="blandUpload-selected-cb" name="'+self.options.prefix+'selected" value="{{index}}" />'
                            + '<input type="hidden" name="'+self.options.prefix+'indeces[]" value="{{index}}" />'
                        +'</td>'
                        +'{{/hasFileMax}}'
                        + '{{#makethumbs}}'
                        + '<td>'
                            + '<div class="blandUpload-thumb-div">'
                                +'<img width="{{thumbwidth}}" src="/media/images/ajax-loader.gif" class="blandUpload-thumb-{{index}}" />'
                                +'<input type="hidden" name="'+self.options.prefix+'thumbs[]" class="blandUpload-thumb-{{index}}" value="{{thumb}}" />'
                                +'<button>'+self.options.thumbadjusttext+'</button>'
                            +'</div>'
                        + '</td>'
                        + '{{/makethumbs}}'
                        + '<td>'
                            + '<span><strong class="blandUpload-filename-{{index}} blandUpload-filename">{{filename}}</strong> <span class="blandUpload-size-span">({{fileSizeKb}}KB)</span></span>'
                            + '<input type="hidden" name="'+self.options.prefix+'filenames[]" class="'+self.options.prefix+'filename-{{index}}" value="{{filename}}" />'
                            + '<input type="hidden" name="'+self.options.prefix+'ids[]" value="{{fileid}}" />'
                            + '{{^hasFileMax}}<input type="hidden" name="'+self.options.prefix+'selected" value="1" />{{/hasFileMax}}'
                            + '</td>'
                        + '{{#makecaptions}}'
                        + '<td>'
                            + '<textarea name="'+self.options.prefix+'caption[]">{{caption}}</textarea>'
                        + '</td>'
                        + '{{/makecaptions}}'
                        + '{{#allowcrop}}'
                        + '<td>'
                            + '<button class="blandUpload-crop">crop</button>'
                        + '</td>'
                        + '{{/allowcrop}}'
                        + '<td>'
                            + '<button class="blandUpload-remove">remove</button>'
                        + '</td>'
                    + '</tr>';
                },
                disableMsg:'<div class="blandUpload-disable-div"><p>{{message}}</p>{{#undo}}<button>Undo</button>{{/undo}}</div>',
                cropForm:function(self){
                    return '<form method="post" action="'+self.options.cropaction+'">'+
                        '<input type="hidden" name="x" class="x" />'+
                        '<input type="hidden" name="y" class="y" />'+
                        '<input type="hidden" name="w" class="w" />'+
                        '<input type="hidden" name="h" class="h" />'+
                        '<img src="{{src}}" width="{{imgWidth}}" height="{{imgHeight}}" />'+
                        '<button>finish cropping</button>'+
                        '<button>cancel</button>'+
                    '</form>';
                }
                
        },

    };

    // attach to global jquery object
    $.fn.blandUpload = function (options) {
        return this.each(function(){
            var bland = Object.create( Bland );
            bland.init(options, this);
        });
    };

    // set option defaults
    $.fn.blandUpload.options = {
        // general
        prefix:"img-",                               // general prefix used for input names[]
        uploaddir:"/media/uploads/",                  // directory to (ultimately) upload images to
        uploadinputname:"files",                     // the name of the input[type="file"] when uploading
        uploadtext:"click to upload",                // text of the main "upload button"
        uploadaction:"/upload",                      // action of the upload form
        maxfiles:0,                                  // maximum number of files to be selected
        allowimagesonly:false,                       // whether or not to allow other images
        
        // title
        allowtitle:true,
        
        // thumbnails
        makethumbs:true,                             // making thumbs?
        thumbext:"-thb",                             // extension for thumbnail files
        thumbwidth:60,                               // width of thumbs
        thumbratio:1,                                // ratio of thumb dimensions => width/height
        thumbadjusttext:"adjust thumb",              // text of the button to adjust thumbnail
        
        // captions
        makecaptions:true,                           // making captions?
        captionlabel:"caption",
        
        // cropping
        allowcrop:false,                              // allowing crop?
        maximgwidth:null,                            // max image width (if maximgheight not set, taken as width)
        maximgheight:null,                           // max image height (if maximgwidth not set, taken as height)
        imgratio:0,                                  // ratio to crop image at
        cropaction:'/crop',                           // action for cropping form (same for thumbnail and images)
        
        // callbacks
        onuploadstart:null,
        onuploadcomplete:null
    };

}));