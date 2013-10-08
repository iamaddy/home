(function($) {

// 扩展jQuery
jQuery.extend({
	isDefine: function(obj) { return typeof obj != 'undefined'; }
});

// 变量Url
var Url = (function(url) {
	var a = document.createElement('a');
	a.href = $.isDefine(url)? url: window.location.href;

	return {
		source: a.href,
		host  : a.hostname,
		port  : a.port,
		query : a.search,
		params: (function() {
			var ret = {},
			seg = a.search.replace(/^\?/,'').split('&'),
			len = seg.length, i = 0, s;
			for (;i<len;i++) {
				if (!seg[i]) { continue; }
				s = seg[i].split('=');
				ret[s[0]] = decodeURIComponent(s[1]);
			}
			return ret;
		})(),
		file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
		hash: a.hash.replace('#',''),
		path: a.pathname.replace(/^([^\/])/,'/$1'),
		relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],
		segments: a.pathname.replace(/^\//,'').split('/'),
		protocol: a.protocol.replace(':',''),
		makeurl : function() {
			return (this.protocol? this.protocol+'://': '')
					+ this.host
					+ (this.port? (':'+this.port): '')
					+ this.path
					+ ($.isEmptyObject(this.params)? '': '?'+jQuery.param(this.params))
		}
	};
});

// 跳转
var Redirect = function(params, remove) {
	var $url = new Url(window.location.href);
	$.extend($url.params, params||{}); // 添加参数
	if($.isDefine(remove)) {
		$.each(remove, function(k, v) {
			delete $url.params[v];
		})
	}
	window.location.href = $url.makeurl();
};

Redirect.url = function(url, params, remove) {
	var $url = new Url(url);
	$.extend($url.params, params||{}); // 添加参数
	if($.isDefine(remove)) {
		$.each(remove, function(k, v) {
			delete $url.params[v];
		})
	}
	window.location.href = $url.makeurl();
}

return window.AY = {
	Url: Url,
	Redirect: Redirect,
	baseUrl: function() {
		return $('base').attr('href');
	},
	baseRoot: function() {
		return this.baseUrl().replace('index.php', '');
	}
}
})(window.jQuery);