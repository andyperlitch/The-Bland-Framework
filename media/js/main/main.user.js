/**
 * Thanks to mdezem (Github), devundef (StackOverflow)
 * for this requirejs setup
 *
 * https://github.com/mdezem/MultiPageAppBoilerplate
 * http://stackoverflow.com/questions/11674824/how-to-use-requirejs-build-profile-r-js-in-a-mult-page-project
*/
/*jslint browser: true, devel: true */

(function () {
    'use strict';

    requirejs.config({
        baseUrl: "media/js/",
        paths: {
            // css paths
            'css_path':'../css',
            'css_plugins':'../css/plugins',
            
            // libraries paths
            // 'jquery' : 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min',
            'jquery':'./libs/jquery',
            'mustache' : './libs/mustache',

            // require plugins
            "css": "./plugins/require.css",
            "text": "./plugins/require.text",
            
            // utils
            'objects' : './utils/objects',
            'notifications' : './utils/notifications'
        }
    });

    //libs
    require([
        // require plugins
        "css",
        "text",
        
        // 3rd party libraries
        "jquery",
        "mustache",
        
        // utils
        "objects",
        "notifications",
        
        // other
        "plugins/hss.slideTabs"
    ],
    function () {
        var $ = require("jquery"),
            startModuleName = $("script[data-main][data-start]").attr("data-start");

        if (startModuleName) {
            require([startModuleName], function (startModule) {
                var fn = $.isFunction(startModule) ? startModule : startModule.init;
                if (fn) { fn(); }
            });
        }
    });
}());