/*!
	Vfstyle v. 1.0
	jQuery plugin for ColorBox
	(c) 2014 Kirungi F. Fideri - http://www.fidsoft.com
*/

// Enable this plugin from the VideoFlow Settings panel under ColorBox Settings
// You must define your css styles in the file "/components/com_videoflow/views/videoflow/tmpl/colorbox/css/vfColorBox.css" to use an external stylesheet. 

(function ( $ ) {
	$.fn.vfstyle = function( options ) {
		var settings = $.extend({
			margin: "auto",
			textAlign:"center",
			tag: "body",
			display: "block",
                        vurl: "/components/com_videoflow/views/videoflow/tmpl/colorbox/css/vfColorBox.css",
                        applyStyle: false,
                        loadCss: false
		}, options );
		var $this = $(this);
                  $this.load(function() {
                    if (settings.applyStyle) {
                      $this.contents().find(settings.tag).css({"display": settings.display, "margin": settings.margin,"text-align": settings.textAlign});
                    };
                    if (settings.loadCss) {
                      var script = document.createElement("link");
                      script.type = "text/css";
                      script.rel = "stylesheet";
                      script.href = settings.vurl;
                      script.media = "screen";
                      $this.contents().find("head").append(script);
                    };
                  });
        return this;
	};
}( jQuery ));