/************************************************************************************************
* Floatbox v3.24
* December 01, 2008
*
* Copyright (C) 2008 Byron McGregor
* Website: http://randomous.com/tools/floatbox/
* License: Creative Commons Attribution 3.0 License (http://creativecommons.org/licenses/by/3.0/)
* This comment block must be retained in all deployments and distributions
*************************************************************************************************/

function Floatbox() {
this.defaultOptions = {

/***** BEGIN OPTIONS CONFIGURATION *****/
// see docs/options.html for detailed descriptions

/*** <General Options> ***/
theme:          'auto'    ,// 'auto'|'black'|'white'|'blue'|'yellow'|'red'|'custom'
padding:         12       ,// pixels
panelPadding:    8        ,// pixels
outerBorder:     4        ,// pixels
innerBorder:     1        ,// pixels
overlayOpacity:  55       ,// 0-100
controlOpacity:  60       ,// 0-100
autoSizeImages:  true     ,// true|false
autoSizeOther:   true    ,// true|false
resizeImages:    true     ,// true|false
resizeOther:     true    ,// true|false
resizeTool:     'cursor'  ,// 'cursor'|'topleft'|'both'
infoPos:        'bl'      ,// 'tl'|'tc'|'tr'|'bl'|'bc'|'br'
controlPos:     'br'      ,// 'tl'|'tr'|'bl'|'br'
boxLeft:        'auto'    ,// 'auto'|pixels|'[-]xx%'
boxTop:         'auto'    ,// 'auto'|pixels|'[-]xx%'
shadowType:     'drop'    ,// 'drop'|'halo'|'none'
shadowSize:      12       ,// 8|12|16|24
enableDrag:      true     ,// true|false
showCaption:     true     ,// true|false
showItemNumber:  true     ,// true|false
showClose:       true     ,// true|false
hideFlash:       true     ,// true|false
hideJava:        true     ,// true|false
disableScroll:   false    ,// true|false
autoGallery:     false    ,// true|false
preloadAll:      true     ,// true|false
enableCookies:   false    ,// true|false
cookieScope:    'site'    ,// 'site'|'folder'
language:       'auto'    ,// 'auto'|'en'|... (see the languages folder)
graphicsType:   'auto'    ,// 'auto'|'international'|'english'
urlGraphics:    '/floatbox/graphics/'   ,// change this if you install in another folder
urlLanguages:   '/floatbox/languages/'  ,// change this if you install in another folder
/*** </General Options> ***/

/*** <Navigation Options> ***/
navType:           'both'    ,// 'overlay'|'button'|'both'|'none'
navOverlayWidth:    50       ,// 0-50
navOverlayPos:      30       ,// 0-100
showNavOverlay:    'never'   ,// 'always'|'once'|'never'
showHints:         'always'    ,// 'always'|'once'|'never'
enableWrap:         true     ,// true|false
enableKeyboardNav:  true     ,// true|false
outsideClickCloses: true     ,// true|false
numIndexLinks:      0        ,// number, -1 = no limit
indexLinksPanel:   'control' ,// 'info'|'control'
showIndexThumbs:    true     ,// true|false
/*** </Navigation Options> ***/

/*** <Animation Options> ***/
doAnimations:         true   ,// true|false
resizeDuration:       3.5    ,// 0-10
imageFadeDuration:    3.5    ,// 0-10
overlayFadeDuration:  4      ,// 0-10
splitResize:         'no'    ,// 'no'|'auto'|'wh'|'hw'
startAtClick:         true   ,// true|false
zoomImageStart:       true   ,// true|false
liveImageResize:      false  ,// true|false
/*** </Animation Options> ***/

/*** <Slideshow Options> ***/
slideInterval:  4.5    ,// seconds
endTask:       'exit'  ,// 'stop'|'exit'|'loop'
showPlayPause:  true   ,// true|false
startPaused:    false  ,// true|false
pauseOnResize:  true   ,// true|false
pauseOnPrev:    true   ,// true|false
pauseOnNext:    false   // true|false
/*** </Slideshow Options> ***/
};

/*** <New Child Window Options> ***/
// Will inherit from the primary floatbox options unless overridden here
// Add any you like
this.childOptions = {
overlayOpacity:      45,
resizeDuration:       3,
imageFadeDuration:    3,
overlayFadeDuration:  0
};
/*** </New Child Window Options> ***/

/***** END OPTIONS CONFIGURATION *****/
this.init();
}
Floatbox.prototype = {
	panelGap: 22,
	infoLinkGap: 16,
	showHintsTime: 1600,
	zoomPopBorder: 1,
	controlSpacing: 8,
	minInfoWidth: 80,
	minIndexWidth: 120,
	ctrlJump: 5,
	slowLoadDelay: 750,
	loaderDelay: 200,
	autoSizeSpace: 4,
	initialSize: 120,
	defaultWidth: '85%',
	defaultHeight: '82%',
init: function() {
	this.setOptions(this.defaultOptions);
	if (typeof fbPageOptions === 'object') this.setOptions(fbPageOptions);
	this.setOptions(this.parseOptionString(location.search.substring(1)));
	this.items = [];
	this.nodeNames = [];
	this.hiddenEls = [];
	this.timeouts = {};
	this.pos = {};
	var path = this.urlGraphics;
	this.slowZoomImg = path + 'loading_white.gif';
	this.slowLoadImg = path + 'loading_black.gif';
	this.iframeSrc = path + 'loading_iframe.html';
	this.resizeUpCursor = path + 'magnify_plus.cur';
	this.resizeDownCursor = path + 'magnify_minus.cur';
	this.notFoundImg = path + '404.jpg';
	var agent = navigator.userAgent,
		version = navigator.appVersion;
	this.mac = version.indexOf('Macintosh') !== -1;
	if (window.opera) {
		this.opera = true;
		this.operaOld = parseFloat(version) < 9.5;
		this.operaMac = this.mac;
	} else if (document.all) {
		this.ie = true;
		this.ieOld = parseInt(version.substr(version.indexOf('MSIE') + 5), 10) < 7;
		this.ie8b2 = version.indexOf('MSIE 8.0') !== -1 && navigator.appMinorVersion === 'beta 2';
		this.ieXP = parseInt(version.substr(version.indexOf('Windows NT') + 11), 10) < 6;
	} else if (agent.indexOf('Firefox') !== -1) {
		this.ff = true;
		this.ffOld = parseInt(agent.substr(agent.indexOf('Firefox') + 8), 10) < 3;
		this.ffNew = !this.ffOld;
		this.ffMac = this.mac;
	} else if (version.indexOf('WebKit') !== -1) {
		this.webkit = true;
		this.webkitNew = parseInt(version.substr(version.indexOf('WebKit') + 7), 10) >= 500;
		this.webkitOld = !this.webkitNew;
		this.webkitMac = this.mac;
	}
	this.isChild = !!(self.fb && self.fb.fbBox);
	if (!this.isChild) {
		this.fbParent = this.lastChild = this;
		this.anchors = [];
		this.children = [];
		this.preloads = {};
		this.preloads.count = 0;
		this.html = document.documentElement;
		this.bod = document.body || document.getElementsByTagName('body')[0];
		this.rtl = this.getStyle(this.bod, 'direction') === 'rtl' || this.getStyle(this.html, 'direction') === 'rtl';
		this.xhr = this.getXMLHttpRequest();
		this.strings = {
			hintClose: 'Exit (key: Esc)',
			hintPrev: 'Previous (key: <--)',
			hintNext: 'Next (key: -->)',
			hintPlay: 'Play (key: spacebar)',
			hintPause: 'Pause (key: spacebar)',
			hintResize: 'Resize (key: Tab)',
			imgCount: 'Image %1 of %2',
			nonImgCount: 'Page %1 of %2',
			mixedCount: '(%1 of %2)',
			infoText: 'Info...',
			printText: 'Print...'
		};
  	} else {
		this.fbParent = fb.lastChild;
		fb.lastChild = this;
		fb.children.push(this);
		if (this.fbParent.isSlideshow) this.fbParent.setPause(true);
		this.anchors = fb.anchors;
		this.children = fb.children;
		this.html = fb.html;
		this.bod = fb.bod;
		this.rtl = fb.rtl;
		this.xhr = fb.xhr;
		this.strings = fb.strings;
	}
	this.browserLanguage = (navigator.language || navigator.userLanguage || navigator.systemLanguage || navigator.browserLanguage || 'en').substring(0, 2);
	if (!this.isChild) {
		var lang = this.language === 'auto' ? this.browserLanguage : this.language;
		if (this.xhr) {
			var that = this;
			this.xhr.getResponse(this.urlLanguages + lang + '.json', function(xhr) {
				if ((xhr.status === 200 || xhr.status === 203 || xhr.status === 304) && xhr.responseText) {
					var ltArrow = String.fromCharCode(8592),
						rtArrow = String.fromCharCode(8594),
						text = xhr.responseText;
					if (that.ieXP) {
						text = text.replace(ltArrow, '<--').replace(rtArrow, '-->');
					}
					try {
						var obj = eval('(' + text + ')');
						if (obj && obj.hintClose) that.strings = obj;
					} catch(e) {}
				}
				if (that.rtl) {
					if (!/^(ar|he)$/.test(that.language)) {
						that.strings.infoText = that.strings.infoText.replace('...', '');
						that.strings.printText = that.strings.printText.replace('...', '');
					}
					that.strings.hintPrev = that.strings.hintPrev.replace(ltArrow, rtArrow).replace('-->', '<--');
					that.strings.hintNext = that.strings.hintNext.replace(rtArrow, ltArrow).replace('<--', '-->');
					var t = that.strings.hintPrev;
					that.strings.hintPrev = that.strings.hintNext;
					that.strings.hintNext = t;
				}
			});
		}
	}
	if (!this.rtl && (this.graphicsType.toLowerCase() === 'english' || (this.graphicsType === 'auto' && this.browserLanguage === 'en'))) {
		this.offPos = 'top left';
		this.onPos = 'bottom left';
	} else {
		this.offPos = 'top right';
		this.onPos = 'bottom right';
		this.controlSpacing = 0;
	}
	this.zIndex = {
		base: 90000 + 10*this.children.length,
		fbOverlay: 1,
		fbBox: 2,
		fbCanvas: 3,
		fbMainDiv: 4,
		fbLeftNav: 5,
		fbRightNav: 5,
		fbOverlayPrev: 6,
		fbOverlayNext: 6,
		fbResizer: 7,
		fbZoomDiv: 8,
		fbInfoPanel: 8,
		fbControlPanel: 8
	};
	var match = /\bautoStart=(.+?)(?:&|$)/i.exec(location.search);
	this.autoHref = match ? match[1] : false;
},
tagAnchors: function(baseEl) {
	var that = fb.lastChild,
		doOutline = this.ieOld && /^fb/.test(baseEl.id);
	function tag(tagName) {
		var elements = baseEl.getElementsByTagName(tagName);
		for (var i = 0, len = elements.length; i < len; i++) {
			var el = elements[i],
				revOptions = that.parseOptionString(el.getAttribute('rev')),
				href = revOptions.href || el.getAttribute('href');
			if (that.autoGallery && that.fileType(href) === 'img' && el.getAttribute('rel') !== 'nofloatbox') {
				el.setAttribute('rel', 'floatbox.autoGallery');
				if (that.autoTitle && !el.getAttribute('title')) el.setAttribute('title', that.autoTitle);
			}
			if (doOutline) el.setAttribute('hideFocus', 'true');
			that.tagOneAnchor(el, revOptions);
		}
	}
	tag('a');
	tag('area');
},
tagOneAnchor: function(anchor, revOptions) {
	var that = this,
		isAnchor = !!anchor.getAttribute;
	if (isAnchor) {
		var a = {
			rel: anchor.getAttribute('rel'),
			rev: anchor.getAttribute('rev'),
			title: anchor.getAttribute('title'),
			anchor: anchor,
			thumb: this.getThumb(anchor)
		};
		var match;
		if (a.thumb && (match = /(?:^|\s)fbPop(up|down)(?:\s|$)/i.exec(anchor.className))) {
			var up = (match[1] === 'up');
			a.popup = true;
			a.thumb.style.borderWidth = this.zoomPopBorder + 'px';
			anchor.onmouseover = function () {
				a.thumb.style.display = 'none';
				var aPos = that.getLeftTop(this, true),
					aLeft = aPos.left,
					aTop = aPos.top;
				aPos = that.getLayout(this);
				a.thumb.style.display = '';
				var relLeft = (aPos.width - a.thumb.offsetWidth)/2,
					relTop = up ? 2 - a.thumb.offsetHeight : aPos.height,
					scroll = that.getScroll(),
					screenRight = scroll.left + that.getDisplayWidth();
				var spill = aPos.left + relLeft + a.thumb.offsetWidth - screenRight;
				if (spill > 0) relLeft -= spill;
				var spill = aPos.left + relLeft - scroll.left;
				if (spill < 0) relLeft -= spill;
				if (up) {
					if (aPos.top + relTop < scroll.top) relTop = aPos.height;
				} else {
					if (aPos.top + relTop + a.thumb.offsetHeight > scroll.top + that.getDisplayHeight()) relTop = 2 - a.thumb.offsetHeight;
				}
				a.thumb.style.left = (aLeft + relLeft) + 'px';
				a.thumb.style.top = (aTop + relTop) + 'px';
			};
			anchor.onmouseout = function () {
				a.thumb.style.left = '0';
				a.thumb.style.top = '-9999px';
			};
			if (!anchor.onclick) anchor.onclick = anchor.onmouseout;
		}
	} else {
		var a = anchor;
	}
	if (/^(floatbox|gallery|iframe|slideshow|lytebox|lyteshow|lyteframe|lightbox)/i.test(a.rel)) {
		a.revOptions = revOptions || this.parseOptionString(a.rev);
		a.href = a.revOptions.href || anchor.href || anchor.getAttribute('href');
		a.level = this.children.length + (fb.lastChild.fbBox && !a.revOptions.sameBox ? 1 : 0);
		var a_i, i = this.anchors.length;
		while (i--) {
			a_i = this.anchors[i];
			if (a_i.href === a.href && a_i.rel === a.rel && a_i.rev === a.rev && a_i.title === a.title && a_i.level === a.level) {
				a_i.anchor = anchor;
				break;
			}
		}
		if (i === -1) {
			a.type = a.revOptions.type || this.fileType(a.href);
			if (a.type === 'html') {
				a.type = 'iframe';
				var match = /#(\w+)/.exec(a.href);
				if (match) {
					var doc = document;
					if (a.anchor) {
						doc = a.anchor.ownerDocument || a.anchor.document || doc;
					}
					if (doc === document && this.currentItem && this.currentItem.anchor) {
						doc = this.currentItem.anchor.ownerDocument || this.currentItem.anchor.document || doc;
					}
					var el = doc.getElementById(match[1]);
					if (el) {
						a.type = 'inline';
						a.sourceEl = el;
					}
				}
			}
			this.anchors.push(a);
			if (this.autoHref) {
				if (a.revOptions.showThis !== false && this.autoHref === a.href.substr(a.href.length - this.autoHref.length)) this.autoStart = a;
			} else if (a.revOptions.autoStart === true) {
				this.autoStart = a;
			} else if (a.revOptions.autoStart === 'once') {
				var match = /fbAutoShown=(.+?)(?:;|$)/.exec(document.cookie),
					val = match ? match[1] : '',
					href = escape(a.href);
				if (val.indexOf(href) === -1) {
					this.autoStart = a;
					document.cookie = 'fbAutoShown=' + val + href + '; path=/';
				}
			}
		}
		if (isAnchor) {
			anchor.onclick = function(e) {
				e = e || window.event;
				if (this.ie && !e) {
					var iframes = self.frames, i = iframes.length;
					while (i-- && !e) {
						try {
							if (typeof iframes[i].window === 'object') e = iframes[i].window.event;
						} catch(err) {}
					}
				}
				if (!(e && (e.ctrlKey || e.metaKey || e.shiftKey)) || a.revOptions.showThis === false || !/img|iframe/.test(a.type)) {
					fb.start(this);
					if (this.ie && e) e.returnValue = false;
					return false;
				}
			};
		}
	}
	return a;
},
fileType: function(href) {
	var s = href.toLowerCase(),
		i = s.indexOf('?');
	if (i !== -1) s = s.substr(0, i);
	s = s.substr(s.lastIndexOf('.') + 1);
	if (/^(jpe?g|png|gif|bmp)$/.test(s)) return 'img';
	if (s === 'swf' || /^(http:)?\/\/(www.)?youtube.com\/v\//i.test(href)) return 'flash';
	if (/^(mov|mpe?g|movie)$/.test(s)) return 'quicktime';
	return 'html';
},
preloadImages: function(href, chain) {
	if (this !== fb) return fb.preloadImages(href, chain);
	if (typeof chain !== 'undefined') arguments.callee.chain = chain;
	if (!href && arguments.callee.chain && (this.preloadAll || !this.preloads.count)) {
		for (var i = 0, len = this.anchors.length; i < len; i++) {
			var a = this.anchors[i];
			if (a.type === 'img' && !this.preloads[a.href]) {
				href = a.href;
				break;
			}
		}
	}
	if (href) {
		if (this.preloads[href]) {
			this.preloadImages();
		} else {
			var img = this.preloads[href] = new Image();
			img.onerror = function() {
				setTimeout(function() { fb.preloadImages(); }, 50);
				fb.preloads[href] = true;
			};
			img.onload = function() {
				fb.preloads.count++;
				this.onerror();
			};
			img.src = href;
		}
	}
},
start: function(anchor) {
	if (this !== fb.lastChild) return fb.lastChild.start(anchor);
	var that = this;
	this.preloadImages('', false);
	if (anchor.getAttribute) {
		var a = {
			rel: anchor.getAttribute('rel'),
			rev: anchor.getAttribute('rev'),
			title: anchor.getAttribute('title')
		};
		a.revOptions = this.parseOptionString(a.rev);
		a.href = a.revOptions.href || anchor.href || anchor.getAttribute('href');
		anchor.blur();
	} else {
		var a = anchor;
	}
	this.isRestart = !!this.fbBox;
	if (this.isRestart) {
		if (!a.revOptions.sameBox) return new Floatbox().start(anchor);
		this.setOptions(a.revOptions);
	} else {
		this.clickedAnchor = anchor.getAttribute ? anchor : false;
	}
	a.level = this.children.length + (fb.lastChild.fbBox && !a.revOptions.sameBox ? 1 : 0);
	this.itemsShown = 0;
	fb.previousAnchor = this.currentItem;
	this.buildItemArray(a);
	if (!this.itemCount) return;
	if (this.itemCount === 1 && this.fbNavControls) this.fbNavControls.style.display = 'none';
	self.focus();
	this.revOptions = a.revOptions;
	if (!this.isRestart) {
		this.getOptions();
		this.buildDOM();
		this.addEventHandlers();
		this.initState();
	}
	this.collapse();
	this.updatePanels();
	var fetchAndGo = function() {
		that.fetchContent(function() {
			that.clearTimeout('slowLoad');
			that.calcSize();
		} );
	};
	if (this.fbBox.style.visibility  || this.isRestart) {
		fetchAndGo();
	} else {
		var offset = this.initialSize/2,
			size = { id: 'fbBox', left: that.pos.fbBox.left - offset, top: that.pos.fbBox.top - offset,
			width: that.initialSize, height: that.initialSize, borderWidth: that.outerBorder };
		if (this.splitResize) {
			var oncomplete = function() {
				that.setSize(fetchAndGo, size);
			};
		} else {
			this.timeouts.slowLoad = setTimeout(function() {
				that.setSize(size);
			}, this.slowLoadDelay);
			var oncomplete = fetchAndGo;
		}
		this.fadeOpacity(this.fbOverlay, this.overlayOpacity, this.overlayFadeDuration, oncomplete);
	}
},
buildItemArray: function(a) {
	this.itemCount = this.items.length = this.currentIndex = 0;
	this.justImages = true;
	this.hasImages = false;
	var isSingle = /^(floatbox|gallery|iframe|lytebox|lyteframe|lightbox)$/i.test(a.rel);
	for (var i = 0, len = this.anchors.length; i < len; i++) {
		var a_i = this.anchors[i];
		if (a_i.rel === a.rel && a_i.level === a.level) {
			if (a_i.revOptions.showThis !== false) {
				var isMatch = a_i.rev === a.rev && a_i.title === a.title && a_i.href === a.href.substr(a.href.length - a_i.href.length);
				if (isMatch || !isSingle) {
					a_i.seen = false;
					this.items.push(a_i);
					if (a_i.type === 'img') {
						this.hasImages = true;
					} else {
						this.justImages = false;
					}
					if (isMatch) this.currentIndex = this.items.length - 1;
				}
			}
		}
	}
	if (a.revOptions.showThis === false && a.href) {
		i = this.items.length;
		while (i--) {
			var href = this.items[i].href;
			if (href === a.href.substr(a.href.length - href.length)) {
				this.currentIndex = i;
			}
		}
	}
  	this.itemCount = this.items.length;
  	this.currentItem = this.items[this.currentIndex];
},
getOptions: function() {
	if (this.isChild) {
		for (var name in this.defaultOptions) {
			if (this.defaultOptions.hasOwnProperty(name)) this[name] = this.fbParent[name];
		}
		this.setOptions(this.childOptions);
	} else {
		this.setOptions(this.defaultOptions);
	}
	this.doSlideshow = this.loadPageOnClose = this.sameBox = false;
	if (!(this.isChild || this.fbBox)) {
		if (typeof setFloatboxOptions === 'function') setFloatboxOptions();
		if (typeof fbPageOptions === 'object') this.setOptions(fbPageOptions);
		if (this.enableCookies) {
			var match = /fbOptions=(.+?)(;|$)/.exec(document.cookie);
			if (match) this.setOptions(this.parseOptionString(match[1]));
			var strOptions = '';
			for (var name in this.defaultOptions) {
				if (this.defaultOptions.hasOwnProperty(name)) {
					strOptions += ' ' + name + ':' + this[name];
				}
			}
			var strPath = '/';
			if (this.cookieScope === 'folder') {
				strPath = location.pathname;
				strPath = strPath.substring(0, strPath.lastIndexOf('/') + 1);
			}
			document.cookie = 'fbOptions=' + strOptions + '; path=' + strPath;
		}
	}
	this.setOptions(this.revOptions);
	this.setOptions(this.parseOptionString(location.search.substring(1)));
	if (this.theme === 'grey') this.theme = 'white';
	if (this.endTask === 'cont') this.endTask = 'loop';
	if (this.navType === 'upper') this.navType = 'overlay';
	if (this.navType === 'lower') this.navType = 'button';
	if (this.upperOpacity) this.controlOpacity = this.upperOpacity;
	if (this.upperNavWidth) this.navOverlayWidth = this.upperNavWidth;
	if (this.upperNavPos) this.navOverlayPos = this.upperNavPos;
	if (this.showUpperNav) this.showNavOverlay = this.showUpperNav;
	if (this.dropShadow) this.shadowType = 'drop';
	if (!/^(auto|black|white|blue|yellow|red|custom)$/.test(this.theme)) this.theme='auto';
	if (!/^(overlay|button|both|none)$/i.test(this.navType)) this.navType = 'button';
	if (!/^(auto|wh|hw)$/.test(this.splitResize)) this.splitResize = false;
	if (this.webkitOld && (this.navType === 'overlay' || this.navType === 'both') ) {
		this.navType = 'button';
	}
	if (this.itemCount > 1) {
		this.isSlideshow = this.doSlideshow || /^(slideshow|lyteshow)/i.test(this.currentItem.rel);
		var overlayRequest = /overlay|both/i.test(this.navType),
			buttonRequest = /button|both/i.test(this.navType);
		this.navOverlay = this.justImages && overlayRequest;
		this.navButton = buttonRequest || (!this.justImages && overlayRequest);
		this.lclShowItemNumber = this.showItemNumber;
		this.lclNumIndexLinks = this.numIndexLinks;
	} else {
		this.isSlideshow = this.navOverlay = this.navButton = this.lclShowItemNumber = this.lclNumIndexLinks = false;
	}
	this.isPaused = this.startPaused;
	if ((this.lclTheme = this.theme) === 'auto') {
		this.lclTheme = this.currentItem.type === 'img' ? 'black' : /flash|quicktime/.test(this.currentItem.type) ? 'blue' : 'white';
	}
	if (!this.doAnimations) {
		this.resizeDuration = this.imageFadeDuration = this.overlayFadeDuration = 0;
	}
	if (!this.resizeDuration) this.zoomImageStart = false;
	if (!/[tb][lr]/.test(this.controlPos)) this.controlPos = '';
	if (!/[tb][lcr]/.test(this.infoPos)) this.infoPos = '';
	this.controlTop = this.controlPos.charAt(0) === 't';
	this.controlLeft = this.controlPos.charAt(1) === 'l';
	this.infoTop = this.infoPos.charAt(0) === 't';
	this.infoCenter = this.infoPos.charAt(1) === 'c';
	this.infoLeft = this.infoPos.charAt(1) === 'l' || (this.infoCenter && this.controlTop === this.infoTop && !this.controlLeft);
	if (this.infoLeft === this.controlLeft && this.infoTop === this.controlTop) {
		this.infoLeft = true;
		this.controlLeft = false;
	}
	if (this.indexLinksPanel === 'info') {
		this.indexCenter = this.infoCenter;
		this.indexLeft = this.infoLeft;
		this.indexTop = this.infoTop;
	} else {
		this.indexLeft = this.controlLeft;
		this.indexTop = this.controlTop;
	}
	if (!/^(drop|halo|none)$/.test(this.shadowType)) this.shadowType='drop';
	if (!/^(8|12|16|24)$/.test(this.shadowSize + '')) this.shadowSize = 8;
	this.shadowSize = +this.shadowSize;
	if (this.opera || (this.mac && !this.webkitNew)) {
		this.resizeTool = 'topleft';
	} else {
		this.resizeTool = this.resizeTool.toLowerCase();
		if (!/topleft|cursor|both/.test(this.resizeTool)) this.resizeTool = 'cursor';
	}
	if (this.ieOld) this.shadowType = 'none';
	if (this.padding + this.outerBorder === 0) this.zoomPopBorder = 0;
	if (this.overlayOpacity > 1) this.overlayOpacity /= 100;
	if (this.controlOpacity > 1) this.controlOpacity /= 100;
},
parseOptionString: function(str) {
	if (!str) return {};
	var quotes = [], match,
		rex = /`([^`]*?)`/g;
	while ((match = rex.exec(str))) quotes.push(match[1]);
	if (quotes.length) str = str.replace(rex, '``');
	str = str.replace(/\s*[:=]\s*/g, ':');
	str = str.replace(/\s*[;&]\s*/g, ' ');
	str = str.replace(/^\s+|\s+$/g, '');
	var pairs = {},
		aVars = str.split(' '),
		i = aVars.length;
	while (i--) {
		var aThisVar = aVars[i].split(':'),
			name = aThisVar[0],
			value = aThisVar[1];
		if (typeof value === 'string') {
			if (!isNaN(value)) value = +value;
			else if (value === 'true') value = true;
			else if (value === 'false') value = false;
		}
		if (value === '``') value = quotes.pop() || '';
		pairs[name] = value;
	}
	return pairs;
},
setOptions: function(pairs) {
	for (var name in pairs) {
		if (pairs.hasOwnProperty(name)) this[name] = pairs[name];
	}
},
buildDOM: function() {
	this.fbOverlay		= this.newNode('div', 'fbOverlay', this.bod);
	this.fbZoomDiv		= this.newNode('div', 'fbZoomDiv', this.bod);
	this.fbZoomImg		= this.newNode('img', 'fbZoomImg', this.fbZoomDiv);
	this.fbBox			= this.newNode('div', 'fbBox');
	this.fbShadowTop	= this.newNode('div', 'fbShadowTop', this.fbBox);
	this.fbShadowRight	= this.newNode('div', 'fbShadowRight', this.fbBox);
	this.fbShadowBottom	= this.newNode('div', 'fbShadowBottom', this.fbBox);
	this.fbShadowLeft	= this.newNode('div', 'fbShadowLeft', this.fbBox);
	this.fbShadowCorner	= this.newNode('div', 'fbShadowCorner', this.fbBox);
	this.fbLoader		= this.newNode('div', 'fbLoader', this.fbBox);
	this.fbCanvas		= this.newNode('div', 'fbCanvas', this.fbBox);
	this.fbMainDiv		= this.newNode('div', 'fbMainDiv', this.fbCanvas);
	this.fbLeftNav		= this.newNode('a', 'fbLeftNav', this.fbMainDiv);
	this.fbRightNav		= this.newNode('a', 'fbRightNav', this.fbMainDiv);
	this.fbOverlayPrev	= this.newNode('a', 'fbOverlayPrev', this.fbMainDiv, this.strings.hintPrev);
	this.fbOverlayNext	= this.newNode('a', 'fbOverlayNext', this.fbMainDiv, this.strings.hintNext);
	this.fbResizer		= this.newNode('a', 'fbResizer', this.fbMainDiv, this.strings.hintResize);
	this.fbInfoPanel	= this.newNode('div', 'fbInfoPanel', this.fbCanvas);
	this.fbCaptionDiv	= this.newNode('div', 'fbCaptionDiv', this.fbInfoPanel);
	this.fbCaption		= this.newNode('span', 'fbCaption', this.fbCaptionDiv);
	this.fbInfoDiv		= this.newNode('div', 'fbInfoDiv', this.fbInfoPanel);
	if (this.infoLeft || this.infoCenter) {
		this.fbInfoLink		= this.newNode('span', 'fbInfoLink', this.fbInfoDiv);
		this.fbPrintLink	= this.newNode('span', 'fbPrintLink', this.fbInfoDiv);
		this.fbItemNumber	= this.newNode('span', 'fbItemNumber', this.fbInfoDiv);
	} else {
		this.fbItemNumber	= this.newNode('span', 'fbItemNumber', this.fbInfoDiv);
		this.fbPrintLink	= this.newNode('span', 'fbPrintLink', this.fbInfoDiv);
		this.fbInfoLink		= this.newNode('span', 'fbInfoLink', this.fbInfoDiv);
	}
	this.fbControlPanel	= this.newNode('div', 'fbControlPanel', this.fbCanvas);
	this.fbControls		= this.newNode('div', 'fbControls', this.fbControlPanel);
	this.fbNavControls	= this.newNode('div', 'fbNavControls', this.fbControls);
	this.fbPrev			= this.newNode('a', 'fbPrev', this.fbNavControls, this.strings.hintPrev);
	this.fbNext			= this.newNode('a', 'fbNext', this.fbNavControls, this.strings.hintNext);
	this.fbSubControls	= this.newNode('div', 'fbSubControls', this.fbControls);
	this.fbPlayPause	= this.newNode('div', 'fbPlayPause', this.fbSubControls);
	this.fbPlay			= this.newNode('a', 'fbPlay', this.fbPlayPause, this.strings.hintPlay);
	this.fbPause		= this.newNode('a', 'fbPause', this.fbPlayPause, this.strings.hintPause);
	this.fbClose		= this.newNode('a', 'fbClose', this.fbSubControls, this.strings.hintClose);
	this.fbIndexLinks	= this.newNode('span', 'fbIndexLinks', this.indexLinksPanel === 'info' ? this.fbInfoPanel : this.fbControlPanel);
	this.bod.appendChild(this.fbBox);
},
newNode: function(nodeType, id, parentNode, title) {
	if (this[id] && this[id].parentNode) {
		this[id].parentNode.removeChild(this[id]);
	}
	var node = document.createElement(nodeType);
	node.id = id;
	node.className = id + '_' + (id.indexOf('fbShadow') === -1 ? this.lclTheme : this.shadowType + this.shadowSize);
	if (nodeType === 'a') {
		if (!this.operaOld) node.setAttribute('href', '');
		if (this.ieOld) node.setAttribute('hideFocus', 'true');
		node.style.outline = 'none';
	} else if (nodeType === 'iframe') {
		node.setAttribute('scrolling', this.itemScroll);
		node.setAttribute('frameBorder', '0');
		node.setAttribute('align', 'middle');
		node.src = this.iframeSrc;
	}
	if (this.isChild && this.fbParent[id]) title = this.fbParent[id].getAttribute('title');
	if (title && this.showHints !== 'never') node.setAttribute('title', title);
	if (this.zIndex[id]) node.style.zIndex = this.zIndex.base + this.zIndex[id];
	node.style.display = 'none';
	if (parentNode) parentNode.appendChild(node);
	this.nodeNames.push(id);
	return node;
},
addEventHandlers: function() {
	var that = this,
	leftNav = this.fbLeftNav.style,
	rightNav = this.fbRightNav.style,
	overlayPrev = this.fbOverlayPrev.style,
	overlayNext = this.fbOverlayNext.style,
	prev = this.fbPrev.style,
	next = this.fbNext.style;
	if (this.showHints === 'once') {
		this.hideHint = function(id) {
			if (that[id].title) {
				that.timeouts[id] = setTimeout(function() {
					that[id].title = that.fbParent[id].title = '';
					var id2 = '';
					if (/fbOverlay(Prev|Next)/.test(id)) {
						id2 = id.replace('Overlay', '');
					} else if (/fb(Prev|Next)/.test(id)) {
						id2 = id.replace('fb', 'fbOverlay');
					}
					if (id2) that[id2].title = that.fbParent[id2].title = '';
				}, that.showHintsTime);
			}
		};
	} else {
		this.hideHint = function() {};
	}
	this.fbPlay.onclick = function() {
		that.setPause(false);
		if (window.event) event.returnValue = false;
		return false;
	};
	this.fbPause.onclick = function() {
		that.setPause(true);
		if (window.event) event.returnValue = false;
		return false;
	};
	this.fbClose.onclick = function() {
		that.end();
		if (window.event) event.returnValue = false;
		return false;
	};
	if (this.outsideClickCloses) {
		this.fbOverlay.onclick = this.fbShadowTop.onclick = this.fbShadowRight.onclick =
		this.fbShadowBottom.onclick = this.fbShadowLeft.onclick = this.fbShadowCorner.onclick = this.fbClose.onclick;
	}
	this[this.rtl ? 'fbNext' : 'fbPrev'].onclick = function(step) {
		if (typeof step !== 'number') step = 1;
		var newIndex = (that.currentIndex - step) % that.itemCount;
		if (newIndex < 0) newIndex += that.itemCount;
		if (that.enableWrap || newIndex < that.currentIndex) {
			that.newContent(newIndex);
			if (that.isSlideshow && that.pauseOnPrev && !that.isPaused) {
				that.setPause(true);
			}
		}
		if (window.event) event.returnValue = false;
		return false;
	};
	this[this.rtl ? 'fbPrev' : 'fbNext'].onclick = function(step) {
		if (typeof step !== 'number') step = 1;
		var newIndex = (that.currentIndex + step) % that.itemCount;
		if (that.enableWrap || newIndex > that.currentIndex) {
			that.newContent(newIndex);
			if (that.isSlideshow && that.pauseOnNext && !that.isPaused) {
				that.setPause(true);
			}
		}
		if (window.event) event.returnValue = false;
		return false;
	};
	this.fbLeftNav.onclick = this.fbOverlayPrev.onclick = this.fbPrev.onclick;
	this.fbRightNav.onclick = this.fbOverlayNext.onclick = this.fbNext.onclick;
	this.fbLeftNav.onmouseover = this.fbLeftNav.onmousemove =
	this.fbOverlayPrev.onmousemove = function() {
		if (!that.timeouts.fbCanvas) overlayPrev.visibility = '';
		if (that.navButton) prev.backgroundPosition = that.onPos;
		return true;
	};
	this.fbRightNav.onmouseover = this.fbRightNav.onmousemove =
	this.fbOverlayNext.onmousemove = function() {
		if (!that.timeouts.fbCanvas) overlayNext.visibility = '';
		if (that.navButton) next.backgroundPosition = that.onPos;
		return true;
	};
	this.fbOverlayPrev.onmouseover = this.fbOverlayNext.onmouseover = function() {
		this.onmousemove();
		that.hideHint(this.id);
		return true;
	};
	this.fbLeftNav.onmouseout = function() {
		overlayPrev.visibility = 'hidden';
		if (that.navButton) prev.backgroundPosition = that.offPos;
	};
	this.fbRightNav.onmouseout = function() {
		overlayNext.visibility = 'hidden';
		if (that.navButton) next.backgroundPosition = that.offPos;
	};
	this.fbOverlayPrev.onmouseout = this.fbOverlayNext.onmouseout = function() {
		this.style.visibility = 'hidden';
		that.clearTimeout(this.id);
	};
	this.fbLeftNav.onmousedown = this.fbRightNav.onmousedown = function(e) {
		e = e || window.event;
		if (e.button === 2) {
			leftNav.visibility = rightNav.visibility = 'hidden';
			that.timeouts.hideNavOverlay = setTimeout(function() {
				leftNav.visibility = rightNav.visibility = '';
			}, 600);
		}
	};
	this.fbPlay.onmouseover = this.fbPause.onmouseover = this.fbClose.onmouseover =
	this.fbPrev.onmouseover = this.fbNext.onmouseover = function() {
		this.style.backgroundPosition = that.onPos;
		that.hideHint(this.id);
		return true;
	};
	this.fbResizer.onmouseover = function() {
		that.hideHint(this.id);
		return true;
	};
	this.fbPlay.onmouseout = this.fbPause.onmouseout = this.fbClose.onmouseout =
	this.fbPrev.onmouseout = this.fbNext.onmouseout = function() {
		this.style.backgroundPosition = that.offPos;
		that.clearTimeout(this.id);
	};
	this.fbResizer.onmouseout = function() {
		that.clearTimeout(this.id);
	};
	if (this.enableKeyboardNav) {
		if (!document.keydownSet) {
			this.priorOnkeydown = document.onkeydown;
			document.onkeydown = this.keydownHandler;
			document.keydownSet = true;
		}
	} else if (document.keydownSet) {
		document.onkeydown = this.priorOnkeydown;
		document.keydownSet = false;
	}
	if (this.opera && !document.keypressSet) {
		this.priorOnkeypress = document.onkeypress;
		document.onkeypress = function() { return false; };
		document.keypressSet = true;
	}
	if (this.enableDrag) this.fbBox.onmousedown = this.dragonDrop();
},
keydownHandler: function(e) {
	e = e || window.event;
	var that = fb.lastChild,
		keyCode = e.keyCode || e.which;
	switch (keyCode) {
		case 37: case 39:
			if (that.itemCount > 1) {
				that[keyCode === 37 ? 'fbPrev' : 'fbNext'].onclick((e.ctrlKey || e.metaKey) ? that.ctrlJump : 1);
				if (that.showHints === 'once') {
					that.fbPrev.title = that.fbNext.title =
					that.fbOverlayPrev.title = that.fbOverlayNext.title = '';
				}
			}
			return false;
		case 32:
			if (that.isSlideshow) {
				that.setPause(!that.isPaused);
				if (that.showHints === 'once') that.fbPlay.title = that.fbPause.title = '';
			}
			return false;
		case 9:
			if (that.fbResizer.onclick) {
				that.fbResizer.onclick();
				if (that.showHints === 'once') that.fbResizer.title = '';
			}
			return false;
		case 27:
			if (that.showHints === 'once') that.fbClose.title = '';
			that.end();
			return false;
		case 13:
			return false;
	}
},
dragonDrop: function() {
	var that = this,
		fbBox = this.fbBox;
	return function(e) {
		e = e || window.event;
		if (/fb(Box|Canvas|Info|Caption|Item|Control|Index)/.test((e.target || e.srcElement).id)) {
			var startX = e.clientX,
				startY = e.clientY,
				box = that.fbBox.style,
				content = that.fbContent.style,
				pos = that.pos.fbBox,
				boxX = pos.left,
				boxY = pos.top;
			pos.dx = pos.dy = 0;
			var moveHandler = function(e) {
				if (that.currentItem.type === 'iframe' && !(that.ie || that.opera) && !content.visibility) content.visibility = 'hidden';
				if (that.isSlideshow && !that.isPaused) that.setPause(true);
				e = e || window.event;
				pos.dx = e.clientX - startX;
				pos.dy = e.clientY - startY;
				box.left = (boxX + pos.dx) + 'px';
				box.top = (boxY + pos.dy) + 'px';
				(e.stopPropagation && e.stopPropagation()) || (e.cancelBubble = true);
				that.clearTimeout('dragonDrop');
				that.timeouts.dragonDrop = setTimeout(upHandler, 1500);
				return false;
			};
			var upHandler = function(e) {
				that.clearTimeout('dragonDrop');
				e = e || window.event;
				if (document.removeEventListener) {
					document.removeEventListener("mouseup", upHandler, true);
					document.removeEventListener("mousemove", moveHandler, true);
				} else if (fbBox.detachEvent) {
					fbBox.detachEvent("onlosecapture", upHandler);
					fbBox.detachEvent("onmouseup", upHandler);
					fbBox.detachEvent("onmousemove", moveHandler);
					fbBox.releaseCapture();
				}
				if (e) (e.stopPropagation && e.stopPropagation()) || (e.cancelBubble = true);
				pos.left += pos.dx;
				pos.top += pos.dy;
				content.visibility = '';
				return false;
			};
			if (document.addEventListener) {
				document.addEventListener("mousemove", moveHandler, true);
				document.addEventListener("mouseup", upHandler, true);
			} else if (fbBox.attachEvent) {
				fbBox.setCapture();
				fbBox.attachEvent("onmousemove", moveHandler);
				fbBox.attachEvent("onmouseup", upHandler);
				fbBox.attachEvent("onlosecapture", upHandler);
			}
			return false;
		}
	};
},
initState: function() {
	var that = this,
		box = this.fbBox.style,
		mainDiv = this.fbMainDiv.style,
		canvas = this.fbCanvas.style,
		zoomDiv = this.fbZoomDiv.style,
		zoomImg = this.fbZoomImg.style;
	if (this.currentItem.popup) this.currentItem.anchor.onmouseover();
	var anchorPos = this.getAnchorPos(this.clickedAnchor, this.currentItem.anchor === this.clickedAnchor && this.currentItem.type === 'img');
	if (anchorPos.width) {
		this.pos.fbZoomDiv = anchorPos;
		zoomDiv.borderWidth = this.zoomPopBorder + 'px';
		zoomDiv.left = (anchorPos.left - this.zoomPopBorder) + 'px';
		zoomDiv.top = (anchorPos.top - this.zoomPopBorder) + 'px';
		zoomDiv.width = (this.fbZoomImg.width = anchorPos.width) + 'px';
		zoomDiv.height = (this.fbZoomImg.height = anchorPos.height) + 'px';
		this.fbZoomImg.src = anchorPos.src;
		box.visibility = 'hidden';
		this.timeouts.slowLoad = setTimeout(function() {
			if (that.fbOverlay.style.display) that.fadeOpacity(that.fbOverlay, that.overlayOpacity, that.overlayFadeDuration);
			that.fbZoomImg.src = that.slowZoomImg;
			zoomDiv.display = zoomImg.display = '';
		}, this.slowLoadDelay);
	} else {
		this.pos.fbBox = anchorPos;
		this.pos.fbBox.borderWidth = 0;
		this.pos.fbMainDiv = { width:0, height:0 };
	}
	box.position = 'absolute';
	box.left = box.top = box.width = box.height = box.borderWidth = '0';
	mainDiv.borderWidth = this.innerBorder + 'px';
	mainDiv.left = this.padding + 'px';
	this.fbControlPanel.style[this.controlLeft ? 'left' : 'right'] =
	this.fbInfoPanel.style[this.infoLeft ? 'left' : 'right'] = Math.max(this.padding, this.panelPadding) + 'px';
	canvas.visibility = 'hidden';
	box.display = canvas.display = '';
	if (this.shadowType === 'none') {
		this.shadowSize = 0;
	} else {
		var shadowTop = this.fbShadowTop.style,
			shadowRight = this.fbShadowRight.style,
			shadowBottom = this.fbShadowBottom.style,
			shadowLeft = this.fbShadowLeft.style,
			shadowCorner = this.fbShadowCorner.style;
		shadowRight.top = shadowBottom.left = shadowLeft.top = -this.outerBorder + 'px';
		shadowRight.paddingRight = shadowBottom.paddingBottom =
		shadowCorner.paddingRight = shadowCorner.paddingBottom = (this.outerBorder + this.shadowSize) + 'px';
		if (this.shadowType === 'halo') {
			shadowTop.paddingRight = shadowRight.paddingBottom =
			shadowBottom.paddingRight = shadowLeft.paddingBottom = (this.outerBorder*2 + this.shadowSize) + 'px';
			shadowTop.top = shadowTop.left = shadowRight.top = shadowLeft.left = -(this.outerBorder + this.shadowSize) + 'px';
		} else {
			shadowBottom.backgroundPosition = 'bottom left';
			shadowRight.paddingBottom = shadowBottom.paddingRight = this.outerBorder*2 + 'px';
		}
	}
	if (this.navOverlay) {
		if (fb.showNavOverlay === 'never' || (fb.showNavOverlay === 'once' && fb.navOverlayShown)) {
			fb.showNavOverlay = false;
		} else {
			this.fbOverlayPrev.style.backgroundPosition = this.fbOverlayNext.style.backgroundPosition = this.onPos;
			this.fadeOpacity(this.fbOverlayPrev, this.controlOpacity);
			this.fadeOpacity(this.fbOverlayNext, this.controlOpacity);
		}
	}
	this.initPanels();
	this.lastShown = false;
	if (this.hideFlash) this.hideElements('flash');
	if (this.hideJava) this.hideElements('applet');
	if (this.ieOld) {
		this.hideElements('select');
		this.fbOverlay.style.position = 'absolute';
		this.stretchOverlay()();
		attachEvent('onresize', this.stretchOverlay());
		attachEvent('onscroll', this.stretchOverlay());
	}
},
hideElements: function(type, thisWindow) {
	if (!thisWindow) {
		this.hideElements(type, self);
	} else {
		var tagName, tagNames = type === 'flash' ? ['object', 'embed'] : [type];
		try {
			while ((tagName = tagNames.pop())) {
				var els = thisWindow.document.getElementsByTagName(tagName),
					i = els.length;
				while (i--) {
					var el = els[i];
					if (el.style.visibility !== 'hidden' && (tagName !== 'object' ||
					(el.getAttribute('type') && el.getAttribute('type').toLowerCase() === 'application/x-shockwave-flash') ||
					(el.getAttribute('classid') && el.getAttribute('classid').toLowerCase() === 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000') ||
					/data\s*=\s*"?[^>"]+\.swf\b/i.test(el.innerHTML) ||
					/param\s+name\s*=\s*"?(movie|src)("|\s)[^>]+\.swf\b/i.test(el.innerHTML))) {
						this.hiddenEls.push(el);
						el.style.visibility = 'hidden';
					}
				}
			}
		} catch(e) {}
		var iframes = thisWindow.frames, i = iframes.length;
		while (i--) {
			try {
				if (typeof iframes[i].window === 'object') this.hideElements(type, iframes[i].window);
			} catch(e) {}
		}
	}
},
getAnchorPos: function(anchor, useThumb) {
	var display = this.getDisplaySize(),
		scroll = this.getScroll(),
		noAnchorPos = { left: display.width/2 + scroll.left, top: display.height/3 + scroll.top, width: 0, height: 0 };
	var thumb = useThumb ? this.getThumb(anchor) : false;
	if (thumb && this.zoomImageStart) {
		var pos = this.getLeftTop(thumb),
			border = (thumb.offsetWidth - thumb.width)/2;
		pos.left += border;
		pos.top += border;
		pos.width = thumb.width;
		pos.height = thumb.height;
		pos.src = thumb.src;
	} else if (this.startAtClick && anchor && anchor.offsetWidth && anchor.tagName.toLowerCase() === 'a') {
		var pos = this.getLayout(thumb || anchor);
	} else {
		return noAnchorPos;
	}
	var centerPos = { left: pos.left + pos.width/2, top: pos.top + pos.height/2, width: 0, height: 0 };
	if (centerPos.left < scroll.left || centerPos.left > (scroll.left + display.width) ||
	centerPos.top < scroll.top || centerPos.top > (scroll.top + display.height)) {
		return noAnchorPos;
	}
	return (thumb && this.zoomImageStart ? pos : centerPos);
},
getThumb: function(anchor) {
	var nodes = anchor && anchor.childNodes, i = (nodes && nodes.length) || 0;
	while (i--) {
		if ((nodes[i].tagName || '').toLowerCase() === 'img') return nodes[i];
	}
	return false;
},
initPanels: function() {
	var infoPanel = this.fbInfoPanel.style,
		infoLink = this.fbInfoLink.style,
		printLink = this.fbPrintLink.style,
		itemNumber = this.fbItemNumber.style;
	if (this.infoCenter) {
		var infoPos = ' posCenter';
		infoPanel.textAlign = 'center';
		infoLink.paddingLeft = printLink.paddingLeft = itemNumber.paddingLeft =
		infoLink.paddingRight = printLink.paddingRight = itemNumber.paddingRight = (this.infoLinkGap/2) + 'px';
	} else if (this.infoLeft) {
		var infoPos = ' posLeft';
		infoPanel.textAlign = 'left';
		infoLink.paddingRight = printLink.paddingRight = this.infoLinkGap + 'px';
	} else {
		var infoPos = ' posRight';
		infoPanel.textAlign = 'right';
		infoLink.paddingLeft = printLink.paddingLeft = this.infoLinkGap + 'px';
	}
	this.fbInfoPanel.className += infoPos;
	this.fbInfoDiv.className += infoPos;
	infoPanel.width = '400px';
	var controlPanel = this.fbControlPanel.style,
		controls = this.fbControls.style,
		subControls = this.fbSubControls.style;
	if (this.controlLeft) {
		var controlPos = ' posLeft';
		controlPanel.textAlign = 'left';
	} else {
		var controlPos = ' posRight';
		controlPanel.textAlign = 'right';
		controls.right = '0';
	}
	this.fbControlPanel.className += controlPos;
	this.fbSubControls.className += controlPos;
	if (!this.ieOld) this.fbControls.className += controlPos;
	if (this.navButton) {
		var prev = this.fbPrev.style,
			next = this.fbNext.style,
			navControls = this.fbNavControls.style;
		prev.backgroundPosition = next.backgroundPosition = this.offPos;
		navControls['padding' + (this.controlLeft ? 'Left' : 'Right')] = this.controlSpacing + 'px';
		this.fbNavControls.className += controlPos;
		controlPanel.display = navControls.display = prev.display = next.display = '';
	}
	var width = 0;
	if (this.showClose) {
		var close = this.fbClose.style;
		close.backgroundPosition = this.offPos;
		this.fbClose.className += controlPos;
		controlPanel.display = controls.display = subControls.display = close.display = '';
		width = this.fbClose.offsetWidth;
	}
	if (this.showPlayPause && this.isSlideshow) {
		var play = this.fbPlay.style,
			pause = this.fbPause.style,
			playPause = this.fbPlayPause.style;
		play.backgroundPosition = pause.backgroundPosition = this.offPos;
		playPause['padding' + (this.controlLeft ? 'Left' : 'Right')] = this.controlSpacing + 'px';
		this.fbPlayPause.className += controlPos;
		controlPanel.display = controls.display = subControls.display = playPause.display = play.display = pause.display = '';
		play.top = this.isPaused ? '' : '-9999px';
		pause.top = this.isPaused ? '-9999px' : '';
		width += this.fbPlayPause.offsetWidth;
	}
	subControls.width = width + 'px';
	controlPanel.width = controls.width = (width + this.fbNavControls.offsetWidth) + 'px';
	if (this.lclNumIndexLinks) {
		var indexLinks = this.fbIndexLinks.style;
		if (this.indexLinksPanel === 'info') {
			this.fbIndexLinks.className += infoPos;
			infoPanel.display = '';
			if (this.showIndexThumbs) infoPanel.overflow = 'visible';
		} else {
			this.fbIndexLinks.className += controlPos;
			controlPanel.display = '';
			if (this.showIndexThumbs) controlPanel.overflow = 'visible';
			indexLinks['padding' + (this.indexLeft ? 'Left' : 'Right')] = '2px';
		}
		indexLinks.width = '250px';
		indexLinks.display = '';
	}
},
fetchContent: function(callback, phase) {
	var that = this;
	if (!phase) {
		if (this.fbContent) {
			this.fbMainDiv.removeChild(this.fbContent);
			delete this.fbContent;
			return this.timeouts.fetch = setTimeout(function() { that.fetchContent(callback, 1); }, 10);
		}
	}
	var item = this.currentItem;
	item.nativeWidth = item.revOptions.width;
	item.nativeHeight = item.revOptions.height;
	if (item.type !== 'img') {
		item.nativeWidth = item.nativeWidth || (fb.previousAnchor && fb.previousAnchor.nativeWidth) || this.defaultWidth;
		item.nativeHeight = item.nativeHeight || (fb.previousAnchor && fb.previousAnchor.nativeHeight) || this.defaultHeight;
	}
	if (this.ieOld) this.fbMainDiv.style.backgroundColor = item.type === 'img' ? '#000' : '';
	this.itemScroll = item.revOptions.scrolling || item.revOptions.scroll || 'auto';
	if (/img|iframe/.test(item.type)) {
		this.fbContent = this.newNode(item.type, 'fbContent', this.fbMainDiv);
		if (item.type === 'img') {
			var loader = new Image();
			loader.onload = function() {
				item.nativeWidth = item.nativeWidth || loader.width;
				item.nativeHeight = item.nativeHeight || loader.height;
				that.fbContent.src = loader.src;
				if (callback) callback();
			};
			loader.onerror = function() {
				if (this.src !== that.notFoundImg) this.src = that.notFoundImg;
			};
			loader.src = item.href;
		}
	} else {
		this.fbContent = this.newNode('div', 'fbContent', this.fbMainDiv);
		this.fbContent.style.overflow = this.itemScroll === 'yes' ? 'scroll' : (this.itemScroll === 'no' ? 'hidden' : 'auto');
		if (item.type === 'inline') {
			var el = item.sourceEl.cloneNode(true);
			el.style.display = el.style.visibility = '';
			try { this.fbContent.appendChild(el); }
			catch(e) { this.setInnerHTML(this.fbContent, el.innerHTML); }
			this.tagAnchors(this.fbContent);
		} else if (item.type === 'ajax') {
			this.xhr.getResponse(item.href, function(xhr) {
				if ((xhr.status === 200 || xhr.status === 203 || xhr.status === 304) && xhr.responseText) {
					that.setInnerHTML(that.fbContent, xhr.responseText);
					that.tagAnchors(that.fbContent);
				} else {
					that.setInnerHTML(that.fbContent, '<p style="color:#000; background:#fff; margin:1em; padding:1em;">' +
					'Unable to fetch content from ' + item.href + '</p>');
				}
			});
		}
	}
	this.fbContent.style.border = '0';
	this.fbContent.style.display = '';
	if (item.type !== 'img' && callback) callback();
},
updatePanels: function() {
	var infoPanel = this.fbInfoPanel.style,
		captionDiv = this.fbCaptionDiv.style,
		caption = this.fbCaption.style,
		infoDiv = this.fbInfoDiv.style,
		infoLink = this.fbInfoLink.style,
		printLink = this.fbPrintLink.style,
		itemNumber = this.fbItemNumber.style,
		item = this.currentItem,
		str;
	infoPanel.display = captionDiv.display = caption.display = infoDiv.display = infoLink.display = printLink.display = itemNumber.display = 'none';
	if (this.showCaption) {
		str = item.revOptions.caption || item.title || '';
		if (str === 'href') {
			str = this.encodeHTML(this.currentItem.href);
		} else {
			str = this.decodeHTML(str).replace(/&/g, '&amp;');
		}
		if (this.setInnerHTML(this.fbCaption, str) && str) infoPanel.display = captionDiv.display = caption.display = '';
	}
	if (item.revOptions.info) {
		str = this.encodeHTML(this.decodeHTML(item.revOptions.info));
		var options = item.revOptions.infoOptions || '';
		if (options) options = this.encodeHTML(this.decodeHTML(options));
		str = '<a href="' + str + '" rel="floatbox" rev="' + options + '"><b>' +
		(item.revOptions.infoText || this.strings.infoText) + '</b></a>';
		if (this.setInnerHTML(this.fbInfoLink, str)) infoPanel.display = infoDiv.display = infoLink.display = '';
	}
	if (item.revOptions.showPrint) {
		var css = item.revOptions.printCSS || '';
		str = '<a href="' + this.encodeHTML(this.currentItem.href) +
		'" onclick="fb.printContents(null, \'' + css + '\'); if (window.event) event.returnValue = false; return false;"><b>' +
		(item.revOptions.printText || this.strings.printText) + '</b></a>';
		if (this.setInnerHTML(this.fbPrintLink, str)) infoPanel.display = infoDiv.display = printLink.display = '';
	}
	if (this.lclShowItemNumber) {
		str = this.justImages ? this.strings.imgCount : (this.hasImages ? this.strings.mixedCount : this.strings.nonImgCount);
		str = str.replace('%1', this.currentIndex + 1);
		str = str.replace('%2', this.itemCount);
		if (this.setInnerHTML(this.fbItemNumber, str)) infoPanel.display = infoDiv.display = itemNumber.display = '';
	}
	var w = this.fbInfoLink.offsetWidth + this.fbPrintLink.offsetWidth + this.fbItemNumber.offsetWidth;
	if (this.ie) {
		if (this.fbInfoLink.offsetWidth) w += this.infoLinkGap;
		if (this.fbPrintLink.offsetWidth) w += this.infoLinkGap;
		if (this.fbItemNumber.offsetWidth) w += this.infoLinkGap;
	}
	infoDiv.width = w + 'px';
	if (this.lclNumIndexLinks) {
		str = '';
		var max = this.itemCount - 1,
			loRange, hiRange;
		if (this.lclNumIndexLinks === -1) {
			loRange = 0;
			hiRange = max;
		} else {
			var range = Math.floor(this.lclNumIndexLinks/2) - 1;
			loRange = this.currentIndex - range;
			hiRange = this.currentIndex + range;
			if (loRange <= 0) hiRange += Math.min(1 - loRange, range);
			if (this.currentIndex === 0) hiRange++;
			if (hiRange - max >= 0) loRange -= Math.min(1 + hiRange - max, range);
			if (this.currentIndex === max) loRange--;
		}
		var pos = this.indexTop ? 'down' : 'up',
			i = 0;
		while (i < this.itemCount) {
			if (i !== 0 && i < loRange) {
				str += '... ';
				i = loRange;
			} else if (i !== max && i > hiRange) {
				str += '... ';
				i = max;
			} else {
				if (i !== this.currentIndex) {
					var item = this.items[i];
					str += '<a class="fbPop' + pos + '" rel="nofloatbox" href="' + item.href +
					'" onclick="fb.newContent(' + i + '); if (window.event) event.returnValue = false; return false;">' + ++i;
					try {
						if (this.showIndexThumbs && item.thumb) {
							str += '<img src="' + item.thumb.src + '" />';
						}
					} catch(e) {}
					str += '</a> ';
				} else {
					str += ++i + ' ';
				}
			}
		}
		if (this.setInnerHTML(this.fbIndexLinks, str)) {
			if (this.indexLinksPanel === 'info') {
				infoPanel.display = '';
			} else {
				this.tagAnchors(this.fbIndexLinks);
			}
		}
	}
	if (!infoPanel.display) this.tagAnchors(this.fbInfoPanel);
},
calcSize: function(fit, pass) {
	var that = this;
	if (!this.fbBox) return;
	var boxX, boxY, boxW, boxH, mainW, mainH ;
	if (typeof fit === 'undefined') {
		fit = this.currentItem.type === 'img' ? this.autoSizeImages : this.autoSizeOther;
	}
	var box = this.fbBox.style,
		infoPanel = this.fbInfoPanel.style,
		controlPanel = this.fbControlPanel.style,
		indexLinks = this.fbIndexLinks.style,
		captionDiv = this.fbCaptionDiv.style,
		itemNumber = this.fbItemNumber.style;
	if (!pass) {
		this.displaySize = this.getDisplaySize();
		if (this.showCaption && this.fbCaption.innerHTML) captionDiv.display = '';
		if (this.lclShowItemNumber) itemNumber.display = '';
	}
	this.upperSpace = Math.max(this.infoTop ? this.fbInfoPanel.offsetHeight : 0, this.controlTop ? this.fbControlPanel.offsetHeight : 0);
	this.lowerSpace = Math.max(this.infoTop ? 0 : this.fbInfoPanel.offsetHeight, this.controlTop ? 0 : this.fbControlPanel.offsetHeight);
	if (this.upperSpace) this.upperSpace += 2*this.panelPadding;
	if (this.lowerSpace) this.lowerSpace += 2*this.panelPadding;
	this.upperSpace = Math.max(this.upperSpace, this.padding);
	this.lowerSpace = Math.max(this.lowerSpace, this.padding);
	var extraSpace;
	if (this.shadowType === 'none') {
		extraSpace = 2*this.autoSizeSpace;
	} else if (this.shadowType === 'halo') {
		extraSpace = 2*this.shadowSize + this.autoSizeSpace;
	} else {
		extraSpace = this.shadowSize + 1.5*this.autoSizeSpace;
	}
	var pad = 2*(this.outerBorder + this.innerBorder) + extraSpace,
		maxW = Math.floor(this.displaySize.width - pad - 2*this.padding),
		maxH = Math.floor(this.displaySize.height - pad - this.upperSpace - this.lowerSpace),
		hardW = false, hardH = false;
	mainW = this.currentItem.nativeWidth + '';
	if (mainW === 'max') {
		mainW = maxW;
	} else if (mainW.substr(mainW.length - 1) === '%') {
		mainW = Math.floor(maxW * parseInt(mainW, 10) / 100);
	} else {
		mainW = parseInt(mainW, 10);
		hardW = true;
	}
	mainH = this.currentItem.nativeHeight + '';
	if (mainH === 'max') {
		mainH = maxH;
	} else if (mainH.substr(mainH.length - 1) === '%') {
		mainH = Math.floor(maxH * parseInt(mainH, 10) / 100);
	} else {
		mainH = parseInt(mainH, 10);
		hardH = true;
	}
	this.scaledBy = this.oversizedBy = 0;
	if (fit) {
		var scaleW = maxW/mainW,
			scaleH = maxH/mainH,
			fullW = mainW, fullH = mainH;
		if (hardW && hardH) scaleW = scaleH = Math.min(scaleW, scaleH);
		if (scaleW < 1) mainW = Math.round(mainW * scaleW);
		if (scaleH < 1) mainH = Math.round(mainH * scaleH);
		this.scaledBy = Math.max(fullW - mainW, fullH - mainH);
		if (this.scaledBy && this.scaledBy < this.outerBorder + extraSpace + this.panelPadding) {
			mainW = fullW;
			mainH = fullH;
			this.scaledBy = 0;
		}
	}
	boxW = mainW + 2*(this.innerBorder + this.padding);
	boxH = mainH + 2*this.innerBorder + this.upperSpace + this.lowerSpace;
	var infoH = this.fbInfoPanel.offsetHeight,
		controlH = this.fbControlPanel.offsetHeight;
	var infoW = boxW - 2*Math.max(this.padding, this.panelPadding);
	if (this.infoTop === this.controlTop && this.fbControls.offsetWidth) {
		infoW -= this.fbControls.offsetWidth + this.panelGap;
	}
	if (infoW < 0) infoW = 0;
	infoPanel.width = infoW + 'px';
	if (!this.lclNumIndexLinks) {
		var indexW = 0;
	} else if (this.indexLinksPanel === 'info' || this.infoTop !== this.controlTop) {
		var indexW = infoW;
	} else if (this.indexLinksPanel !== 'info' && this.infoTop === this.controlTop && this.infoCenter) {
		var indexW = Math.max(this.minIndexWidth, this.fbControls.offsetWidth);
	} else {
		var infoUsed = Math.max(this.fbCaption.offsetWidth, this.fbInfoLink.offsetWidth + this.fbPrintLink.offsetWidth + this.fbItemNumber.offsetWidth);
		var indexW = Math.max(this.minIndexWidth, this.fbControls.offsetWidth, (boxW - infoUsed - 2*Math.max(this.padding, this.panelPadding)));
		if (infoUsed) indexW -= this.panelGap;
	}
	if (indexW) indexLinks.width = (indexW - (this.indexLinksPanel !== 'info' ? 2 : 0)) + 'px';
	controlPanel.width = Math.max(indexW, this.fbControls.offsetWidth) + 'px';
	var changed = this.fbInfoPanel.offsetHeight !== infoH || this.fbControlPanel.offsetHeight !== controlH;
	if (this.showCaption) {
		if (this.minInfoWidth > infoW && !captionDiv.display) {
			captionDiv.display = 'none';
			changed = true;
		}
	}
	if (this.lclShowItemNumber) {
		if (this.fbInfoLink.offsetWidth + this.fbPrintLink.offsetWidth + this.fbItemNumber.offsetWidth > infoW && !itemNumber.display) {
			itemNumber.display = 'none';
			changed = true;
		}
	}
	if (changed && pass !== 3) return this.calcSize(fit, (pass || 0) + 1);
	if (!fit) this.oversizedBy = Math.max(boxW - this.displaySize.width, boxH - this.displaySize.height) + 2*this.outerBorder + extraSpace;
	if (this.oversizedBy < 0) this.oversizedBy = 0;
	if (this.shadowType === 'halo') {
		extraSpace = this.shadowSize + this.autoSizeSpace/2;
	} else {
		extraSpace = this.autoSizeSpace;
	}
	if (typeof this.boxLeft === 'number') {
		boxX = this.boxLeft;
	} else if (mainW === maxW) {
		boxX = extraSpace;
	} else {
		var freeSpace = this.displaySize.width - boxW - 2*this.outerBorder;
		boxX = Math.floor(freeSpace/2);
		if (boxX < this.autoSizeSpace) {
			boxX = this.autoSizeSpace;
		} else {
			if (typeof this.boxLeft === 'string' && this.boxLeft.substr(this.boxLeft.length - 1) === '%') {
				boxX += parseInt(this.boxLeft, 10)/100 * boxX;
			}
		}
	}
	if (typeof this.boxTop === 'number') {
		boxY = this.boxTop;
	} else if (mainH === maxH) {
		boxY = extraSpace;
	} else {
		var freeSpace = this.displaySize.height - boxH - 2*this.outerBorder,
			ratio = freeSpace / this.displaySize.height, factor;
		if (ratio <= 0.15) {
			factor = 2;
		} else if (ratio >= 0.3) {
			factor = 3;
		} else {
			factor = 1 + ratio/0.15;
		}
		boxY = Math.floor(freeSpace/factor);
		if (boxY < this.autoSizeSpace) {
			boxY = this.autoSizeSpace;
		} else {
			if (typeof this.boxTop === 'string' && this.boxTop.substr(this.boxTop.length - 1) === '%') {
				boxY += parseInt(this.boxTop, 10)/100 * boxY;
			}
		}
	}
	var boxPosition = box.position;
	if (this.ieOld) {
		box.display = 'none';
		this.stretchOverlay()();
	} else {
		this.setPosition(this.fbBox, 'fixed');
	}
	var scroll = this.getScroll();
	this.setPosition(this.fbBox, boxPosition);
	box.display = '';
	boxX += scroll.left;
	boxY += scroll.top;
	if (this.isChild) {
		var rex = /max|%/i,
			pos = this.fbParent.pos.fbBox,
			childX = rex.test(this.currentItem.nativeWidth) ? 99999 : (pos.left + boxX)/2,
			childY = rex.test(this.currentItem.nativeHeight) ? 99999 : (pos.top + boxY)/2;
		if (scroll.left < childX && scroll.top < childY) {
			boxX = Math.min(boxX, childX);
			boxY = Math.min(boxY, childY);
		}
	}
	var split = (pos = this.pos.fbBox) && !this.liveResize && this.splitResize;
	if (split === 'auto') split = boxW - pos.width <= boxH - pos.height ? 'wh' : 'hw';
	var oncomplete2 = function() {
		that.fbBox.style.visibility ? that.zoomIn() : that.showContent();
	};
	var oncomplete = function() {
		that.setSize(split,
			{ id: 'fbBox', left: boxX, top: boxY, width: boxW, height: boxH, borderWidth: that.outerBorder },
			{ id: 'fbMainDiv', width: mainW, height: mainH, top: that.upperSpace },
			function() { that.timeouts.showContent = setTimeout(oncomplete2, 10); }
		);
	};
	this.timeouts.setSize = setTimeout(oncomplete, 10);
},
setPosition: function(el, position) {
	if (el.style.position === position) return;
	var scroll = this.getScroll();
	if (position === 'fixed') {
		scroll.left = -scroll.left;
		scroll.top = -scroll.top;
	}
	if (this.pos[el.id]) {
		this.pos[el.id].left += scroll.left;
		this.pos[el.id].top += scroll.top;
	}
	el.style.left = (el.offsetLeft + scroll.left) + 'px';
	el.style.top = (el.offsetTop + scroll.top) + 'px';
	el.style.position = position;
},
collapse: function(callback, phase) {
	var that = this;
	if (!phase) {
		this.setPosition(this.fbBox, 'absolute');
		this.fbResizer.onclick = null;
		this.fbResizer.style.display = 'none';
		if (this.fbContent) {
			this.fbContent.onclick = null;
			this.fbContent.style.cursor = '';
		}
		if (this.navOverlay) {
			this.fbLeftNav.style.display = this.fbRightNav.style.display =
			this.fbOverlayPrev.style.display = this.fbOverlayNext.style.display = 'none';
		}
		var opacity = 0, duration = 0;
		if (this.currentItem.type === 'img' && !this.fbCanvas.style.visibility) {
			if (this.currentItem === this.lastShown && this.liveImageResize) opacity = 1;
			duration = this.imageFadeDuration;
		}
		this.liveResize = (opacity === 1);
		var oncomplete = function() { that.collapse(callback, 1); };
		return this.fadeOpacity(this.fbCanvas, opacity, duration, oncomplete);
	}
	if (!this.liveResize) {
		this.fbMainDiv.style.display = 'none';
		if (this.fbContent) this.fbContent.style.display = 'none';
		this.clearTimeout('loader');
		this.timeouts.loader = setTimeout(function() { that.fbLoader.style.display = ''; }, this.loaderDelay);
	}
	var infoPanel = this.fbInfoPanel.style,
		controlPanel = this.fbControlPanel.style;
	infoPanel.visibility = controlPanel.visibility = 'hidden';
	infoPanel.left = controlPanel.left = '0';
	infoPanel.top = controlPanel.top = '-9999px';
	if (callback) callback();
},
restore: function(callback, phase) {
	var that = this;
	if (!phase) {
		if (this.fbShadowRight.style.display && this.shadowType !== 'none') {
			this.fbShadowRight.style.display = this.fbShadowBottom.style.display = '';
			if (this.shadowType === 'halo') {
				this.fbShadowTop.style.display = this.fbShadowLeft.style.display = '';
			} else {
				this.fbShadowCorner.style.display = '';
			}
		}
		var infoPanel = this.fbInfoPanel.style,
			controlPanel = this.fbControlPanel.style,
			pad = this.upperSpace + this.pos.fbMainDiv.height + 2*this.innerBorder;
		infoPanel.top = (((this.infoTop ? this.upperSpace : this.lowerSpace) - this.fbInfoPanel.offsetHeight) / 2 - 1 + (this.infoTop ? 0 : pad)) + 'px';
		controlPanel.top = (((this.controlTop ? this.upperSpace : this.lowerSpace) - this.fbControlPanel.offsetHeight) / 2 + (this.controlTop ? 0 : pad)) + 'px';
		var pad = Math.max(this.padding, this.panelPadding) + 'px';
		infoPanel.left = [this.infoLeft ? pad : ''];
		controlPanel.left = [this.controlLeft ? pad : ''];
		infoPanel.visibility = controlPanel.visibility = '';
		this.clearTimeout('loader');
		this.fbLoader.style.display = 'none';
		this.fbMainDiv.style.display = this.fbContent.style.display = '';
		var duration = (this.currentItem.type === 'img' && !this.fbCanvas.style.visibility) ? this.imageFadeDuration : 0,
			oncomplete = function() { that.restore(callback, 1); };
		return this.fadeOpacity(this.fbCanvas, 1, duration, oncomplete);
	}
	if (this.currentItem.type === 'img' ? this.resizeImages : this.resizeOther) {
		var scale = 0;
		if (this.scaledBy > 35) {
			scale = 1;
		} else if (this.oversizedBy > 28){
			scale = -1;
		}
		if (scale) {
			this.fbResizer.onclick = function() {
				if (that.isSlideshow && that.pauseOnResize && !that.isPaused) {
					that.setPause(true);
				}
				that.collapse(function() { that.calcSize(scale === -1); });
				if (window.event) event.returnValue = false;
				return false;
			};
			if (this.currentItem.type === 'img' && /cursor|both/.test(this.resizeTool)) {
				this.fbContent.style.cursor = 'url(' + (scale === -1 ? this.resizeDownCursor : this.resizeUpCursor) +'), default';
				this.fbContent.onclick = this.fbResizer.onclick;
			}
			if (this.currentItem.type !== 'img' || /topleft|both/.test(this.resizeTool)) {
				this.fbResizer.style.backgroundPosition = (scale === -1 ? 'bottom' : 'top');
				this.fadeOpacity(this.fbResizer, this.controlOpacity);
			}
		}
	}
	if (this.navOverlay) {
		var leftNav = this.fbLeftNav.style,
			rightNav = this.fbRightNav.style,
			overlayPrev = this.fbOverlayPrev.style,
			overlayNext = this.fbOverlayNext.style;
		leftNav.width = rightNav.width = Math.max(this.navOverlayWidth/100 * this.pos.fbMainDiv.width, this.fbOverlayPrev.offsetWidth) + 'px';
		leftNav.display = rightNav.display = '';
		if (fb.showNavOverlay) {
			overlayPrev.visibility = overlayNext.visibility = 'hidden';
			overlayPrev.display = overlayNext.display = '';
			overlayPrev.top = overlayNext.top = ((this.pos.fbMainDiv.height - this.fbOverlayPrev.offsetHeight) * this.navOverlayPos/100) + 'px';
		}
	}
	if (callback) callback();
},
setSize: function(order) {
	var that = this,
		oncomplete = function() {},
		arr = [[], []],
		defer = {},
		node,
		i = arguments.length;
	if (order === 'wh') {
		defer.top = 1;
		defer.height = 1;
	} else if (order === 'hw') {
		defer.left = 1;
		defer.width = 1;
	}
	while (i--) {
		if (typeof arguments[i] === 'object' && (node = this[arguments[i].id])) {
			var obj = arguments[i];
			if (!this.pos[obj.id]) this.pos[obj.id] = {};
			for (var property in obj) {
				if (obj.hasOwnProperty(property) && property !== 'id') {
					var idx = defer[property] || 0;
					var start = this.pos[obj.id][property];
					if (typeof start !== 'number' || node.style.display || node.style.visibility) {
						start = obj[property];
					}
					arr[idx].push({ node: node, property: property, start: start, finish: obj[property] });
					if (obj.id === 'fbMainDiv') {
						arr[idx].push({ node: this.fbContent, property: property, start: start, finish: obj[property] });
					}
					if (obj.id === 'fbZoomDiv' && /\b(width|height)\b/i.test(property)) {
						arr[idx].push({ node: this.fbZoomImg, property: property, start: start, finish: obj[property] });
					}
					this.pos[obj.id][property] = obj[property];
				}
			}
		} else if (typeof arguments[i] === 'function') {
			oncomplete = arguments[i];
		}
	}
	this.resizeGroup(arr[0], function() { that.resizeGroup(arr[1], oncomplete); });
},
showContent: function(phase) {
	var that = this;
	if (!phase) {
		var displaySize = this.getDisplaySize();
		if (!this.resized) {
			var vscrollChanged = displaySize.width !== this.displaySize.width,
				hscrollChanged = displaySize.height !== this.displaySize.height;
			if ((vscrollChanged && Math.abs(this.pos.fbBox.width - displaySize.width) < 50) ||
			(hscrollChanged && Math.abs(this.pos.fbBox.height - displaySize.height) < 50)) {
				this.resized = true;
				return this.calcSize(this.scaledBy);
			}
		}
		this.resized = false;
		self.focus();
		if (this.ieOld) this.stretchOverlay()();
		if ((this.disableScroll || (this.ffOld && /iframe|quicktime/i.test(this.currentItem.type))) && !(this.ieOld || this.webkitOld || this.ie8b2)) {
			if (this.pos.fbBox.width <= displaySize.width && this.pos.fbBox.height <= displaySize.height) {
				this.setPosition(this.fbBox, 'fixed');
			}
		}
		if (this.currentItem.type === 'iframe') {
			this.fbContent.src = this.currentItem.href;
		} else if (/flash|quicktime/.test(this.currentItem.type)) {
			this.setInnerHTML(this.fbContent, this.objectHTML(this.currentItem.href,
			this.currentItem.type, this.pos.fbMainDiv.width, this.pos.fbMainDiv.height));
		}
		this.prevIndex = this.currentIndex ? this.currentIndex - 1 : this.itemCount - 1;
		this.nextIndex = this.currentIndex < this.itemCount - 1 ? this.currentIndex + 1 : 0;
		var prevHref = this.enableWrap || this.currentIndex !== 0 ? this.items[this.prevIndex].href : '',
			nextHref = this.enableWrap || this.currentIndex !== this.itemCount - 1 ?  this.items[this.nextIndex].href : '';
		if (this.navButton) {
			if (prevHref) {
				if (!this.operaOld) this.fbPrev.href = prevHref;
				this.fbPrev.title = this.fbOverlayPrev.title;
			} else {
				this.fbPrev.removeAttribute('href');
				this.fbPrev.title = '';
			}
			if (nextHref) {
				if (!this.operaOld) this.fbNext.href = nextHref;
				this.fbNext.title = this.fbOverlayNext.title;
			} else {
				this.fbNext.removeAttribute('href');
				this.fbNext.title = '';
			}
			var prevOn = this.fbPrev.className.replace('_off', ''),
				nextOn = this.fbNext.className.replace('_off', '');
			this.fbPrev.className = prevOn + (prevHref ? '' : '_off');
			this.fbNext.className = nextOn + (nextHref ? '' : '_off');
		}
		if (this.navOverlay) {
			if (!this.operaOld) {
				this.fbLeftNav.href = this.fbOverlayPrev.href = prevHref;
				this.fbRightNav.href = this.fbOverlayNext.href = nextHref;
			}
			this.fbLeftNav.style.visibility = prevHref ? '' : 'hidden';
			this.fbRightNav.style.visibility = nextHref ? '' : 'hidden';
			fb.navOverlayShown = true;
		}
		this.fbCanvas.style.visibility = '';
		return this.restore(function() {
			that.timeouts.showContent = setTimeout(function() { that.showContent(1); }, 10);
		} );
	}
	this.lastShown = this.currentItem;
	if (!this.currentItem.seen) {
		this.currentItem.seen = true;
		this.itemsShown++;
	}
	if (this.isSlideshow && !this.isPaused) {
		this.timeouts.slideshow = setTimeout(function() {
			if (that.endTask === 'loop' || that.itemsShown < that.itemCount) {
				that.newContent(that.nextIndex);
			} else if (that.endTask === 'exit') {
				that.end();
			} else {
				that.setPause(true);
				var i = that.itemCount;
				while (i--) that.items[i].seen = false;
				that.itemsShown = 0;
			}
		}, this.slideInterval*1000);
	}
	this.timeouts.preload = setTimeout(function() {
			that.preloadImages(nextHref || prevHref || '', true);
	}, 10);
},
objectHTML: function(href, type, width, height) {
	if (type === 'flash') {
		var classid = 'classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"',
			mime = 'type="application/x-shockwave-flash"',
			pluginurl = 'http://get.adobe.com/flashplayer/',
			match = /\bwmode=(\w+?)\b/i.exec(href),
			wmode = match ? match[1] : 'window',

//			match = /\bbase=(\w+?)\b/i.exec(href),
			match = /\bbase=(.*)$/i.exec(href),
			base = match ? match[1] : '.',
					
			match = /\bbgcolor=(#\w+?)\b/i.exec(href),
			bgcolor = match ? match[1] : '',
			match = /\bscale=(\w+?)\b/i.exec(href),
			scale = match ? match[1] : 'exactfit',
			params = { wmode:wmode, bgcolor:bgcolor, scale:scale, quality:'high', base:base,
			flashvars:'autoplay=1&amp;ap=true&amp;border=0&amp;rel=0' };
		if (this.ffOld) params.wmode = this.ffMac ? 'window' : 'opaque';
		if (this.ffNew && href.indexOf('YV_YEP.swf') !== -1) params.wmode = 'window';
	} else {
		var classid = 'classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"',
			mime = 'type="video/quicktime"',
			pluginurl = 'http://www.apple.com/quicktime/download/',
			params = { autoplay:'true', controller:'true', showlogo:'false', scale:'tofit' };
	}
	var html = '<object id="fbObject" name="fbObject" width="' + width + '" height="' + height + '" ';
	if (this.ie) {
		html += classid + '>';
		params[type === 'flash' ? 'movie' : 'src'] = this.encodeHTML(href);
	} else {
		html += mime + ' data="' + this.encodeHTML(href) + '">';
	}
	for (var name in params) {
		if (params.hasOwnProperty(name)) {
			html += '<param name="' + name + '" value="' + params[name] + '" />';
		}
	}
	if (type === 'quicktime' && this.webkitMac) {
		html += '<embed src="' + this.encodeHTML(href) +
		'" width="' + width + '" height="' + height + '" autoplay="true" controller="true" showlogo="false" scale="tofit" pluginspage="' +
		pluginurl + '"></embed></object>';
	} else {
		html += '<p style="color:#000; background:#fff; margin:1em; padding:1em;">' +
		(type === 'flash' ? 'Flash' : 'QuickTime') + ' player is required to view this content.' +
		'<br /><a href="' + pluginurl + '">download player</a></p></object>';
	}
	return html;
},
newContent: function(index) {
	var that = this;
	this.clearTimeout('slideshow');
	this.clearTimeout('resize');
	this.currentIndex = index;
	fb.previousAnchor = this.currentItem;
	this.currentItem = this.items[index];
	if (this.showNavOverlay == 'once' && this.navOverlayShown) this.showNavOverlay = false;
	var oncomplete = function() {
		that.updatePanels();
		that.fetchContent(function() { that.calcSize(); });
	};
	this.collapse(function() {
		that.timeouts.fetch = setTimeout(oncomplete, 10);
	} );
},
end: function(all) {
	if (this !== fb.lastChild) return fb.lastChild.end(all);
	var that = this;
	this.endAll = this.endAll || all;
	this.fbOverlay.onclick = null;
	if (this.isChild) {
		if (this.endAll) this.imageFadeDuration = this.overlayFadeDuration = this.resizeDuration = 0;
	} else {
		if (document.keydownSet) {
			document.onkeydown = this.priorOnkeydown;
			document.keydownSet = false;
		}
		if (document.keypressSet) {
			document.onkeypress = this.priorOnkeypress;
			document.keypressSet = false;
		}
		parent.focus();
	}
	if (this.ieOld) {
		detachEvent('onresize', this.stretchOverlay());
		detachEvent('onscroll', this.stretchOverlay());
	}
	for (var key in this.timeouts) {
		if (this.timeouts.hasOwnProperty(key)) this.clearTimeout(key);
	}
	if (this.fbBox.style.visibility) {
		if (!this.lastShown) this.fbZoomDiv.style.display = 'none';
	} else if (this.currentItem.type === 'img' && this.zoomImageStart) {
		if (this.currentItem.popup) this.currentItem.anchor.onmouseover();
		var anchorPos = this.getAnchorPos(this.currentItem.anchor, true);
		if (this.currentItem.popup) this.currentItem.anchor.onmouseout();
		if (anchorPos.width) {
			this.fbZoomDiv.style.borderWidth = this.zoomPopBorder + 'px';
			anchorPos.left -= this.zoomPopBorder;
			anchorPos.top -= this.zoomPopBorder;
			this.pos.thumb = anchorPos;
			return this.zoomOut();
		}
	}
	if (!this.fbBox.style.visibility) {
		var anchorPos = this.getAnchorPos(this.currentItem.anchor, !this.currentItem.popup),
			offset = this.initialSize/2,
			initialPos = { id: 'fbBox', left: anchorPos.left - offset, top: anchorPos.top - offset, width: this.initialSize, height: this.initialSize },
			zeroPos = { id: 'fbBox', left: anchorPos.left, top: anchorPos.top, width: 0, height: 0, borderWidth: 0 },
			split = this.splitResize;
		if (split === 'wh') {
			split = 'hw';
		} else if (split === 'hw') {
			split = 'wh';
		} else if (split === 'auto') {
			split = this.pos.fbBox.width <= this.pos.fbBox.height ? 'hw' : 'wh';
		}
		var oncomplete3 = function() {
			setTimeout(function() {
				that.fbBox.style.visibility = 'hidden';
				that.end();
			}, 10);
		};
		if (split) {
			var oncomplete2 = function() {
				that.setSize(split, initialPos, function() {
					that.setSize(zeroPos, oncomplete3);
				});
			};
		} else {
			var oncomplete2 = function() {
				that.setSize(zeroPos, oncomplete3);
			};
		}
		var oncomplete = function() {
			if (that.fbContent) {
				that.fbMainDiv.removeChild(that.fbContent);
				delete that.fbContent;
			}
			that.fbLoader.style.display = '';
			that.fbCanvas.style.display = that.fbShadowTop.style.display = that.fbShadowRight.style.display =
			that.fbShadowBottom.style.display = that.fbShadowLeft.style.display = that.fbShadowCorner.style.display = 'none';
			oncomplete2();
		};
		return this.collapse(oncomplete);
	}
	this.fbBox.style.display = 'none';
	var level = this.children.length + 1,
		i = this.anchors.length;
	while(i && this.anchors[i-1].level >= level) i--;
	this.anchors.length = i;
	if (this.isChild) this.children.length--;
	fb.lastChild = this.children[this.children.length-1] || fb;
	var oncomplete2 = function() {
		setTimeout(function() {
			while (that.nodeNames.length) {
				var id = that.nodeNames.pop();
				if (that[id] && that[id].parentNode) {
					that[id].parentNode.removeChild(that[id]);
					delete that[id];
				}
			}
			if (that.endAll && that.isChild) {
				return fb.end(true);
			} else if (that.loadPageOnClose) {
				if (that.loadPageOnClose === 'self' || that.loadPageOnClose === 'this') {
					location.reload(true);
				} else if (that.loadPageOnClose === 'back') {
					history.back();
				} else {
					location.replace(that.loadPageOnClose);
				}
			}
		}, 10);
	};
	var oncomplete = function() {
		while(that.hiddenEls.length) {
			var el = that.hiddenEls.pop();
			el.style.visibility = 'visible';
			if (this.ffOld && this.ffMac) {
				el.focus();
				el.blur();
			}
		}
		var overlay = that.fbOverlay.style;
		overlay.display = 'none';
		overlay.width = overlay.height = '0';
		var duration = that.currentItem.popup ? 6.5 : 0;
		that.fbZoomDiv.style.opacity = '1';
		that.fadeOpacity( that.fbZoomDiv, 0, duration, oncomplete2);
		that.currentItem = fb.previousAnchor = null;
	};
	this.fadeOpacity(this.fbOverlay, 0, this.overlayFadeDuration, oncomplete);
},
zoomIn: function(phase) {
	var that = this,
		zoomDiv = this.fbZoomDiv.style;
	if (!phase) {
		this.clearTimeout('slowLoad');
		zoomDiv.display = this.fbZoomImg.style.display = '';
		if (this.currentItem.popup) this.currentItem.anchor.onmouseout();
		var pad = this.outerBorder + this.innerBorder - this.zoomPopBorder;
		var oncomplete = function () {
			that.fbZoomImg.src = that.currentItem.href;
			that.setSize(
				{ id: 'fbZoomDiv', width: that.pos.fbMainDiv.width, height: that.pos.fbMainDiv.height,
				left: that.pos.fbBox.left + pad + that.padding, top: that.pos.fbBox.top + pad + that.upperSpace },
				function() { that.zoomIn(1); } );
		};
		return this.fadeOpacity(this.fbOverlay, this.overlayOpacity, this.overlayFadeDuration, oncomplete);
	}
	if (phase === 1) {
		var boxPos = {
			left: this.pos.fbBox.left, top: this.pos.fbBox.top,
			width: this.pos.fbBox.width, height: this.pos.fbBox.height
		};
		var pad = 2*(this.zoomPopBorder - this.outerBorder);
		this.pos.fbBox = {
			left: this.pos.fbZoomDiv.left, top: this.pos.fbZoomDiv.top,
			width: this.pos.fbZoomDiv.width + pad, height: this.pos.fbZoomDiv.height + pad
		};
		this.fbBox.style.visibility = '';
		var oncomplete = function() {
			that.restore(function() { that.zoomIn(2); });
		};
		return this.setSize(
			{ id: 'fbBox', left: boxPos.left, top: boxPos.top,
			width: boxPos.width, height: boxPos.height},
			oncomplete);
	}
	var show = function() {
		zoomDiv.display = 'none';
		that.fbZoomImg.src = '';
		zoomDiv.left = zoomDiv.top = zoomDiv.width = zoomDiv.height = that.fbZoomImg.width = that.fbZoomImg.height = '0';
		that.showContent();
	};
	this.timeouts.showContent = setTimeout(show, 10);
},
zoomOut: function(phase) {
	var that = this;
	if (!phase) {
		this.fbZoomImg.src = this.currentItem.href;
		var pad = this.outerBorder + this.innerBorder - this.zoomPopBorder;
		this.setSize(
		{ id: 'fbZoomDiv', width: this.pos.fbMainDiv.width, height: this.pos.fbMainDiv.height,
		left: this.pos.fbBox.left + pad + this.padding, top: this.pos.fbBox.top + pad + this.upperSpace },
		function() { that.zoomOut(1); } );
	}
	if (phase === 1) {
		this.fbZoomDiv.style.display = this.fbZoomImg.style.display = '';
		this.fbCanvas.style.visibility = 'hidden';
		return this.collapse(function() { that.zoomOut(2); });
	}
	if (phase === 2) {
		var pad = 2*(this.zoomPopBorder - this.outerBorder);
		return this.setSize(
			{ id: 'fbBox', left: this.pos.fbZoomDiv.left, top: this.pos.fbZoomDiv.top,
			width: this.pos.fbZoomDiv.width + pad, height: this.pos.fbZoomDiv.height + pad },
			function() { that.zoomOut(3); }
		);
	}
	this.fbBox.style.visibility = 'hidden';
	var end = function() {
		that.fbZoomImg.src = that.pos.thumb.src;
		that.end();
	};
	this.setSize(
		{ id: 'fbZoomDiv', left: this.pos.thumb.left, top: this.pos.thumb.top,
		width: this.pos.thumb.width, height: this.pos.thumb.height },
		end);
},
setPause: function(pause) {
	this.isPaused = pause;
	if (pause) {
		this.clearTimeout('slideshow');
	} else {
		this.newContent(this.nextIndex);
	}
	if (this.showPlayPause) {
		this.fbPlay.style.top = pause ? '' : '-9999px';
		this.fbPause.style.top = pause ? '-9999px' : '';
	}
},
fadeOpacity: function(el, opacity, duration, callback) {
	var startOp = +(el.style.opacity || 0);
	duration = duration || 0;
	this.clearTimeout['fade' + el.id];
	var fadeIn = (startOp <= opacity && opacity > 0);
	if (duration > 10) duration = 10;
	if (duration < 0) duration = 0;
	if (duration === 0) {
		startOp = opacity;
		var incr = 1;
	} else {
		var root = Math.pow(100, 0.1),
			power = duration + ((10 - duration)/9) * (Math.log(2)/Math.log(root) - 1),
			incr = 1/Math.pow(root, power);
	}
	if (fadeIn) {
		el.style.display = el.style.visibility = '';
	} else {
		incr = -incr;
	}
	this.stepFade(el, startOp, opacity, incr, fadeIn, callback);
},
stepFade: function(el, thisOp, finishOp, incr, fadeIn, callback) {
	if (!el) return;
	var that = this;
	if ((fadeIn && thisOp >= finishOp) || (!fadeIn && thisOp <= finishOp)) thisOp = finishOp;
	if (this.ie) el.style.filter = 'alpha(opacity=' + thisOp*100 + ')';
	el.style.opacity = thisOp + '';
	if (thisOp === finishOp) {
		if (this.ie && finishOp >= 1) el.style.removeAttribute('filter');
		if (callback) callback();
	} else {
		this.timeouts['fade' + el.id] = setTimeout(function() { that.stepFade(el, thisOp + incr, finishOp, incr, fadeIn, callback); }, 20);
	}
},
resizeGroup: function(arr, callback) {
	var i = arr.length;
	if (!i) return callback ? callback() : null;
	this.clearTimeout('resize');
	var diff = 0;
	while (i--) {
		diff = Math.max(diff, Math.abs(arr[i].finish - arr[i].start));
	}
	var duration = this.resizeDuration * (this.liveResize ? 0.65 : 1);
	var rate = diff && duration ? Math.pow(Math.max(1, 2.2 - duration/10), (Math.log(diff))) / diff : 1;
	i = arr.length;
	while (i--) arr[i].diff = arr[i].finish - arr[i].start;
	this.stepResize(rate, rate, arr, callback);
},
stepResize: function(increment, rate, arr, callback) {
	var that = this;
	if (increment > 1) increment = 1;
	var i = arr.length;
	while (i--) {
		var node = arr[i].node,
			prop = arr[i].property,
			val = Math.round(arr[i].start + arr[i].diff * increment),
			tag = node.tagName.toLowerCase();
		if (tag === 'img' || tag === 'iframe') {
			node[prop] = val;
		} else {
			node.style[prop] = val + 'px';
		}
	}
	if (increment >= 1) {
		delete this.timeouts.resize;
		if (callback) callback();
	} else {
		this.timeouts.resize = setTimeout(function() { that.stepResize(increment + rate, rate, arr, callback); }, 20);
	}
},
getDisplaySize: function() {
	return { width: this.getDisplayWidth(), height: this.getDisplayHeight() };
},
getDisplayWidth: function() {
	return this.html.clientWidth || this.bod.clientWidth;
},
getDisplayHeight: function() {
	if (this.webkitOld) return window.innerHeight;
	if (!this.html.clientHeight || this.operaOld || document.compatMode === 'BackCompat') {
		return this.bod.clientHeight;
	}
	return this.html.clientHeight;
},
getScroll: function(win) {
	if (!(win && win.document)) win = self;
	var doc = win.document,
		html = doc.documentElement,
		bod = doc.body || doc.getElementsByTagName('body')[0],
		left = win.pageXOffset || bod.scrollLeft || doc.documentElement.scrollLeft || 0;
	if (this.ie && this.rtl) left -= html.scrollWidth - html.clientWidth;
	return {
		left: left,
		top: win.pageYOffset || bod.scrollTop || doc.documentElement.scrollTop || 0
	};
},
getLeftTop: function(el, local) {
	var left = el.offsetLeft || 0,
		top = el.offsetTop || 0,
		doc = el.ownerDocument || el.document,
		bod = doc.body || doc.getElementsByTagName('body')[0],
		win = doc.defaultView || doc.parentWindow || doc.contentWindow,
		scroll = this.getScroll(win),
		position = this.getStyle(el, 'position', win),
		rex = /absolute|fixed/,
		elFlow = !rex.test(position),
		inFlow = elFlow,
		node = el;
	if (position === 'fixed') {
		left += scroll.left;
		top += scroll.top;
	}
	while (position !== 'fixed' && (node = node.offsetParent)) {
		var borderLeft = 0,
			borderTop = 0,
			nodeFlow = true,
			position = this.getStyle(node, 'position', win),
			nodeFlow = !rex.test(position);
		if (this.opera) {
			if (local && node !== bod) {
				left += node.scrollLeft - node.clientLeft;
				top += node.scrollTop - node.clientTop;
			}
		} else if (this.ie) {
			if (node.currentStyle.hasLayout && node !== doc.documentElement) {
				borderLeft = node.clientLeft;
				borderTop = node.clientTop;
			}
		} else {
			borderLeft = parseInt(this.getStyle(node, 'border-left-width', win), 10);
			borderTop = parseInt(this.getStyle(node, 'border-top-width', win), 10);
			if (this.ff && node === el.offsetParent && !nodeFlow && (this.ffOld || !elFlow)) {
				left += borderLeft;
				top += borderTop;
			}
		}
		if (!nodeFlow) {
			if (local) return { left: left, top: top };
			inFlow = false;
		}
		if (node.offsetLeft > 0) left += node.offsetLeft;
		left += borderLeft;
		top += node.offsetTop + borderTop;
		if (position === 'fixed') {
			left += scroll.left;
			top += scroll.top;
		}
		if (!(this.opera && elFlow) && node !== bod && node !== doc.documentElement) {
			left -= node.scrollLeft;
			top -= node.scrollTop;
		}
	}
	if (this.ff && inFlow) {
		left += parseInt(this.getStyle(bod, 'border-left-width', win), 10);
		top += parseInt(this.getStyle(bod, 'border-top-width', win), 10);
	}
	if (this.webkitOld) {
		var scriptElement = doc.createElement('script');
		scriptElement.innerHTML = 'document.parentWindow=self';
		doc.documentElement.appendChild(scriptElement);
		doc.documentElement.removeChild(scriptElement);
		win = doc.parentWindow;
	}
	if (!local && win !== self) {
		var iframes = win.parent.document.getElementsByTagName('iframe'),
			i = iframes.length;
		while (i--) {
			var node = iframes[i],
				idoc = false;
			try {
				idoc = node.contentDocument || node.contentWindow;
				idoc = idoc.document || idoc;
			} catch(e) {}
			if (idoc === doc || (typeof idoc !== 'object' && node.src === win.location.href.substr(win.location.href.length - node.src.length))) {
				if (this.webkitOld) win = doc.defaultView;
				var pos = this.getLeftTop(node);
				left += pos.left - scroll.left;
				top += pos.top - scroll.top;
				if (this.ie || this.opera) {
					var padLeft = 0, padTop = 0;
					if (!this.ie || elFlow) {
						padLeft = parseInt(this.getStyle(node, 'padding-left', win), 10);
						padTop = parseInt(this.getStyle(node, 'padding-top', win), 10);
					}
					left += node.clientLeft + padLeft;
					top += node.clientTop + padTop;
				} else {
					left += parseInt(this.getStyle(node, 'border-left-width', win), 10) +
					parseInt(this.getStyle(node, 'padding-left', win), 10);
					top += parseInt(this.getStyle(node, 'border-top-width', win), 10) +
					parseInt(this.getStyle(node, 'padding-top', win), 10);
				}
				break;
			}
		}
	}
	return { left: left, top: top };
},
getStyle: function(el, prop, win) {
	if (!(el && prop)) return '';
	if (!win) {
		var doc = el.ownerDocument || el.document;
		win = doc.defaultView || doc.parentWindow || doc.contentWindow;
	}
	if (el.currentStyle) {
		return el.currentStyle[prop.replace(/-(\w)/g, function(match, p1) { return p1.toUpperCase(); })] || '';
	} else {
		if (!win) {
			var doc = el.ownerDocument || el.document;
			win = doc.defaultView || doc.parentWindow || doc.contentWindow;
		}
		return (win.getComputedStyle && win.getComputedStyle(el, '').getPropertyValue(prop)) || '';
	}
},
getLayout: function(el) {
	var lay = this.getLeftTop(el);
	lay.width = el.offsetWidth;
	lay.height = el.offsetHeight;
	return lay;
},
clearTimeout: function(key) {
	if (this.timeouts[key]) {
		clearTimeout(this.timeouts[key]);
		delete this.timeouts[key];
	}
},
stretchOverlay: function() {
	var that = this;
	return function() {
		if (arguments.length === 1) {
			that.clearTimeout('stretch');
			that.timeouts.stretch = setTimeout(function() { that.stretchOverlay()(); }, 25);
		} else {
			delete that.timeouts.stretch;
			if (!that.fbBox) return;
			var width = that.fbBox.offsetLeft + that.fbBox.offsetWidth,
				height = that.fbBox.offsetTop + that.fbBox.offsetHeight,
				display = that.getDisplaySize(),
				scroll = that.getScroll(),
				overlay = that.fbOverlay.style;
			overlay.width = overlay.height = '0';
			var rtlAdjust = (that.rtl && scroll.left) ? that.html.clientWidth - that.html.scrollWidth : 0;
			overlay.left = rtlAdjust + 'px';
			overlay.width = Math.max(width, that.bod.scrollWidth, that.bod.clientWidth, that.html.clientWidth, display.width + scroll.left) + 'px';
			overlay.height = Math.max(height, that.bod.scrollHeight, that.bod.clientHeight, that.html.clientHeight, display.height + scroll.top) + 'px';
		}
	};
},
encodeHTML: function(str) {
	return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
},
decodeHTML: function(str) {
	return str.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&apos;/g, "'").replace(/&#39;/g, "'");
},
getXMLHttpRequest: function() {
	var xhr, that = this;
	if (window.XMLHttpRequest) {
		if (!(xhr = new XMLHttpRequest())) return false;
	} else {
		try { xhr = new ActiveXObject("Msxml2.XMLHTTP"); } catch(e) {
			try { xhr = new ActiveXObject("Microsoft.XMLHTTP"); } catch(e) { return false; }
		}
	}
	return {
		getResponse: function(url, callback) {
			try {
				xhr.open('GET', url, true);
				xhr.setRequestHeader('If-Modified-Since', 'Thu, 1 Jan 1970 00:00:00 GMT');
				xhr.setRequestHeader('Cache-Control', 'no-cache');
				xhr.onreadystatechange = function() {
					if (xhr.readyState === 4) {
						xhr.onreadystatechange = function() {};
						callback(xhr);
					}
				};
				xhr.send(null);
			} catch(e) {}
		}
	};
},
setInnerHTML: function(el, strHTML) {
	try {
		var range = document.createRange();
		range.selectNodeContents(el);
		range.deleteContents();
		if (strHTML) {
			var xmlDiv = new DOMParser().parseFromString('<div xmlns="http://www.w3.org/1999/xhtml">' + strHTML + '</div>', 'application/xhtml+xml'),
				childNodes = xmlDiv.documentElement.childNodes;
			for (var i = 0, len = childNodes.length; i < len; i++) {
				el.appendChild(document.importNode(childNodes[i], true));
			}
		}
		return true;
	} catch (e) {}
	try {
		el.innerHTML = strHTML;
		return true;
	} catch(e) {}
	return false;
},
printContents: function(el, style) {
	if (el && el.offsetWidth) {
		var width = el.offsetWidth,
			height = el.offsetHeight;
	} else {
		el = fb.lastChild.fbContent;
		var pos = fb.lastChild.pos.fbMainDiv,
			width = pos.width,
			height = pos.height;
	}
	var win = window.open('', '', 'width=' + width + ', height=' + height),
		doc = win && win.document;
	if (!doc) {
		alert('Popup windows are being blocked by your browser.\nUnable to print.');
		return false;
	}
	if (/\.css$/i.test(style)) {
		style = '<link rel="stylesheet" type="text/css" href="' + style + '" />';
	} else {
		style = '<style type="text/css"> html,body{border:0;margin:0;padding:0;}' + (style || '') + '</style>';
	}
	var div = document.createElement('div');
	div.appendChild(el.cloneNode(true));
	doc.open('text/html');
	doc.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' +
	' "http://www.w3.org/TR/html4/loose.dtd"><html><head>' +
	style + '</head><body><div>' + div.innerHTML + '</div></body></html>');
	doc.close();
	setTimeout(function() { win && win.print(); win && win.close(); }, 200);
	return true;
},
loadAnchor: function(href, rev, title) {
	if (href.setAttribute) {
		var anchor = href;
		if (!anchor.getAttribute('rel')) anchor.setAttribute('rel', 'floatbox');
		fb.lastChild.start(this.tagOneAnchor(anchor));
	} else {
		fb.lastChild.start(this.tagOneAnchor({ href: href, rev: rev, title: title, rel: 'floatbox' }));
	}
},
goBack: function() {
	var a = fb.previousAnchor;
	if (a) this.loadAnchor(a.href, a.rev + ' sameBox:true', a.title);
},
resize: function(width, height) {
	var changed = false;
	if (width && fb.lastChild.currentItem && fb.lastChild.currentItem.nativeWidth != width) {
		fb.lastChild.currentItem.nativeWidth = width;
		changed = true;
	}
	if (height && fb.lastChild.currentItem && fb.lastChild.currentItem.nativeHeight != height) {
		fb.lastChild.currentItem.nativeHeight = height;
		changed = true;
	}
	if (changed) fb.lastChild.calcSize(false);
}
};
function initfb() {
	if (arguments.callee.done) return;
	var fbWindow = 'self';
	if (self !== parent) {
		try {
			if (self.location.host === parent.location.host && self.location.protocol === parent.location.protocol) fbWindow = 'parent';
		} catch(e) {}
		if (fbWindow === 'parent' && !parent.fb) return setTimeout(initfb, 50);
	}
	arguments.callee.done = true;
	if (document.compatMode === 'BackCompat') {
		alert('Floatbox does not support quirks mode.\nPage needs to have a valid a doc type.');
		return;
	}
	fb = (fbWindow === 'self' ? new Floatbox() : parent.fb);
	fb.tagAnchors(self.document.body || self.document.getElementsByTagName('body')[0]);
	if (fb.autoStart) {
		fb.start(fb.autoStart);
		if (typeof fb !== 'undefined') delete fb.autoStart;
	} else {
		fb.preloadImages('', true);
	}
}
if (document.addEventListener) {
	document.addEventListener('DOMContentLoaded', initfb, false);
}
(function() {
	/*@cc_on
	if (document.body) {
		try {
			document.createElement('div').doScroll('left');
			return initfb();
		} catch(e) {}
	}
	/*@if (false) @*/
	if (/loaded|complete/.test(document.readyState)) return initfb();
	/*@end @*/
	if (!initfb.done) setTimeout(arguments.callee, 50);
})();
fb_prevOnload = window.onload;
window.onload = function() {
	if (arguments.callee.done) return;
	arguments.callee.done = true;
	if (typeof fb_prevOnload === 'function') fb_prevOnload();
	initfb();
};