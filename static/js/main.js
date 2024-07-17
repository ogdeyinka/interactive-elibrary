 function popUp (file) {
swidth=screen.availWidth;
sheight=screen.availHeight;
win = window.open(file,'doPop',  'toolbar=0,  location=0, status=0,   menubar=0,   scrollbars=0,  resizable=0 , width=1000,  height=900');
win.window.focus();

}

	$(document).ready(function() {
				
		$("a.swfile").fancybox({
    fitToView: false,
    autoSize: false,
    afterLoad: function () {
        this.width = $(this.element).data("width");
        this.height = $(this.element).data("height");
    },
		type: 'swf',
		'swf'			: {
			   	 'wmode'		: 'opaque',
				'allowFullScreen'	: 'true',
				'quality': 'best',
				'allowScriptAccess' : 'always',
				'allowfullscreen'   : 'true',
			},
		wmode: 'transparent',
		scrolling   : 'no',
     	allowfullscreen   : 'true',
		padding    : 0,
		margin     : 2, 
		openEffect:  'elastic', 
		closeEffect: 'elastic', 
		openEasing: 'swing',
		allowscriptaccess : 'always',
		closeClick  : false, 
		helpers   : { 
		overlay : {
			closeClick: false,
			locked: true,
				} 
				},
		 autoCenter : false,
			afterLoad  : function () {
				$.extend(this, {
					aspectRatio : false,
					width   : '95%',
					height  : '100%',
				});
			},
	    afterShow: function() { 
        $('<div class="expander"></div>').appendTo(".fancybox-item").click(function() {           
		   toggleFullscreen();
           return false; 
        });
        $(".fancybox-inner").click(function(e) {
        return false; 
		})
        
    },
    afterClose: function() {
        $(document).fullScreen(false);
    }
		  });
	  
			$("a.various").fancybox({
    fitToView: false,
    autoSize: false,
    afterLoad: function () {
        this.width = $(this.element).data("width");
        this.height = $(this.element).data("height");
    },
		type: 'iframe',
		'swf'			: {
			   	 'wmode'		: 'opaque',
				'allowFullScreen'	: 'true',
				'quality': 'high',
				'allowScriptAccess' : 'always',
			},
		wmode: 'transparent',
		scrolling   : 'no',
     	allowfullscreen   : 'false',
		padding    : 0,
		margin     : 2, 
		openEffect:  'elastic', 
		closeEffect: 'elastic', 
		openEasing: 'swing',
		allowscriptaccess : 'always',
		closeClick  : false, 
		helpers   : { 
		overlay : {
			closeClick: false,
			locked: true,
				} 
				},
		 autoCenter : false,
			afterLoad  : function () {
				$.extend(this, {
					aspectRatio : false,
					width   : '95%',
					height  : '100%',
				});
			},
	    afterShow: function() { 
        $('<div class="expander"></div>').appendTo(".fancybox-item").click(function() {           
		   toggleFullscreen();
           return false; 
        });
        $(".fancybox-inner").click(function(e) {
        return false; 
		})
        
    },
    afterClose: function() {
        $(document).fullScreen(false);
    }
		  });	  
		  
	
				});	

	


function toggleFullscreen(elem) {
  var el = document.body;

				// Supports most browsers and their versions.
				var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen
				|| el.mozRequestFullScreen || el.msRequestFullScreen ; //|| el.fullscreenElement

				if (requestMethod) {

					// Native full screen.
					requestMethod.call(el);

				} else if (typeof window.ActiveXObject !== "undefined") {

					// Older IE.
					var wscript = new ActiveXObject("WScript.Shell");

					if (wscript !== null) {
						wscript.SendKeys("{F11}");
					}
				}
				}
  
