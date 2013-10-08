(function(global){
	function cookieStorage(maxage, path){
		this.cookie = (function(){
			var cookie = {};
			var all = document.cookie;
			if(all === "") return cookie;
			var list = all.split("; ");
			for(var i = 0, l = list.length; i < l; i++){
				var c = list[i];
				var p = c.indexOf("=");
				var name = c.substring(0, p);
				var value = c.substring(p + 1);
				value = decodeURIComponent(value);
				cookie[name] = value;
			}
			return cookie;
		})();
		
		var keys = [];
		for(var key in this.cookie) keys.push(key);
		this.length = keys.length;
		
		this.key = function(n){
			if(n < 0 || n >= keys.length) return null;
			return keys[n];
		};
		
		this.getItem = function(name){
			return this.cookie[name] || "";
		};
		
		this.setItem = function(key, value){
			if(!(key in this.cookie)){
				keys.push(key);
				this.length++;
			}
			this.cookie[key] = value;
			
			var cookie = key + '=' + encodeURIComponent(value);
			
			if(maxage) cookie += '; max-age=' + maxage;
			if(path) cookie += '; path=' + path;
			
			document.cookie = cookie;
		};
		this.removeItem = function(key){
			if(! (key in this.cookie)) return;
			delete this.cookie[key];
			for(var i = 0; i < keys.length; i++){
				if(keys[i] === key){
					keys.splice(i, 1);
					break;
				}
			}
			this.length--;
			document.cookie = key + '=; max-age=0';
		};
		this.clear = function(){
			for(var i = 0; i < keys.length; i++){
				document.cookie = keys[i] + '=; max-age=0';
			}
			cookie = {};
			keys = [];
			this.length = 0;
		}
	}
	global.cookieStorage = cookieStorage;
})(window)