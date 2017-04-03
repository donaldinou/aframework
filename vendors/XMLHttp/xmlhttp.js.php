// XMLHTTP JS class, a part of the AjaxExtended project
// Version 1.0 RC1, 2005 (c) Alex Serebryakov
// AjaxExtended is freely distributable under MIT license
// For more information, consult www.ajaxextended.com

XMLHTTP = function() {

  var _options = {
   maxRequestLength: 1500,
   apiURL: '<?=ROOT?>/?controller=ajaxserver',
   apiKey: '',
   overrideMime: ''
  }
  
  this.onreadystatechange =  function() {  }
  this.onerror = function() { }
  this.onload = function() { }
  
  this.abort = function() {
    _stop = true
    _transport.abort()
  }

  this.setRequestHeader = function(name, value) {
    _request.headers[name] = value
  }
 
  this.getAllResponseHeaders = function() {
    var result = ''
    for (property in _responseHeaders)
      result += property + ': ' + _responseHeaders[property] + '\r\n'
    return result
  }
  
  this.getResponseHeader = function(name) {
    for(property in _responseHeaders) {
      if(property.toLowerCase() == name.toLowerCase())
        return _responseHeaders[property]
    }
    return ''
  }
  
  this.overrideMimeType = function(type) {
    _options.overrideMime = type
  }

  this.setAPI = function(url) {
    _options.apiURL = url
  }

  this.setKey = function(key) {
    _options.apiKey = key
  }
  
  this.open = function(method, url, sync, username, password) {
    _request.method = _is_defined(method)
    _request.url = _is_defined(url)
    var username = _is_defined(username)
    var password = _is_defined(password)
    var pos = _request.url.indexOf('://') + 3
    if((username || password) && pos > 2) {
      _request.url = _request.url.substr(0, pos)
        + username + ':'
        + password + '@'
        + _request.url.substr(pos)
    }
    _setReadyState(1)
  }
  
  this.openRequest = function(method, url, sync, username, password) {
    return this.open(method, url, sync, username, password)
  }
  
  this.send = function(data) {
    if (_stop) return
    _recallCookies()
    _request.data = _is_defined(data)
    _transport.send(_request, _options)
  }
  
  var _is_defined = function(value) {
    return ('undefined' == typeof value) ? '' : value
  }

  var _throwError = function(description) {
    self.onerror(description)
    self.abort()
    return false
  }
  
  var _setReadyState = function(number) {
    self.readyState = number
    self.onreadystatechange()
    if(number == 4) self.onload()
  }

  var _parse = function(object) {
    if(_stop) return
    if('object' != typeof object)
	  return _throwError('There seems to be a problem with your server script')
    if(object.multipart)
      return
    if(!object.success)
      return _throwError(object.description)
    _responseHeaders = object.responseHeaders
    if (_options.overrideMime)
      var mime = _options.overrideMime
    else
      var mime = self.getResponseHeader('Content-type')
    self.status = object.status
    self.statusText = object.statusText
    self.responseText = object.responseText
    self.responseXML = _xmlparser.parse(object.responseText, mime)
	_recordCookies(object.cookies)
    _setReadyState(4)
  }

  var _recordCookies = function(cookies) {
	if (!cookies) return
	for (i in cookies)
      XMLHTTP.Cookies.save(cookies[i])
  }

  var _recallCookies = function() {
    cookies = XMLHTTP.Cookies.find(_request.url)
	self.setRequestHeader('Cookie', cookies)
  }

  var self = this
  var _request = {
    headers: {
      "HTTP-Referer": document.location,
      "Content-Type": "application/x-www-form-urlencoded"
    },
    method: 'GET',
    data: '',
    url: 'http://www.ajaxextended.com/'
  }

  this.status = null
  this.statusText = null
  this.responseText = null
  this.responseXML = null
  this.synchronous = false
  this.readyState = 0

  var _response = { }
  var _responseHeaders = { }
  var _stop = false

  var _transport = new XMLHTTP.Transport(_parse)
  var _xmlparser = new XMLHTTP.XMLParser()
    
}

XMLHTTP.Cookies = {

  data: [],

  save: function(cookie) {
	var entry = cookie
	entry.expires = Date.parse(entry.expires)
	this.data.push(entry)
  },

  check: function(cookie, domain, path) {
    if (cookie.expires < Date()) return false
    if (path.indexOf(cookie.path) != 0) return false
    if (cookie.domain == domain) return true
	if (domain.indexOf(cookie.domain.substr(1)) > -1 &&
	   cookie.domain.substr(0,1) == '.') return true
	return false
  },

  find: function(url) {
	var result = ''
	url = url.split('/')
	domain = url[2]
	path = '/' + url.slice(3).join('/')
    for (i in this.data) {
	  var cookie = this.data[i]
	  if(this.check(cookie, domain, path))
	    result += cookie.name + '='
	           + cookie.value + '; '
    }
    return result
  }

}

XMLHTTP.Transport = function(handler) {

  var _registerCallback = function(handler) {
    _id = 'v' + Math.random().toString().substr(2)
    window[_id] = _onComplete
    _handler = handler
  }

  var _onComplete = function(data) {
    if(_parts-- == 1) _destroyScripts()
    handler(data)
  }

  var _encode = function(params) {
    var headers = ''
    for (property in params.headers)
      headers += _encodeUTF(property +
	    ': ' + params.headers[property]) + '&'
    var data = params.method
      + '&' + _encodeUTF(params.url)
      + '&' + _encodeUTF(params.data)
      + '&' + headers
    return base64encode(data)
  }

  var _encodeUTF = function(string) {
    return base64encode(utf8encode(string))
  }

  var _split = function(data, options) {
    var max = options.maxRequestLength - options.apiURL.length - 60
    var urls = [], total = Math.floor(data.length / max) + 1
	
    for (var part = 0; part < total; part++) {
      urls.push(options.apiURL +
        ( options.apiURL.indexOf("?") > -1 ? "&" : "?" ) + 
		'id=' + _id +
		'&key=' + options.apiKey +
        '&part=' + part +
        '&total=' + total +
        '&data=' + data.substr(0, max))
      data = data.substr(max)
    }
    _parts = urls.length
    return urls
  }

  this.send = function(params, options) {
    var urls = _split(_encode(params), options)
    for(var i = 0; i < urls.length; i++)
      _createScript(urls[i])
  }

  var _createScript = function(url) {
    var script = document.createElement('script')
    script.src = url
    script.type = 'text/javascript'
    script.charset = 'utf-8'
    script = document.getElementsByTagName('head')[0].appendChild(script)
    _scripts.push(script)
  }

  var _destroyScripts = function() {
    for(var i = 0; i < _scripts.length; i++)
      if(_scripts[i].parentNode)
        _scripts[i].parentNode.removeChild(_scripts[i])
  }

  var self = this
  var _id, _scripts = []
  var _parts = 0
  var _handler = function() { }

  _registerCallback(handler)

}

XMLHTTP.XMLParser = function() {

  this.parse = function(text, type) {
    if(!(type.indexOf('html') > -1 || type.indexOf('xml') > -1)) return
    if(document.implementation &&
      document.implementation.createDocument &&
      navigator.userAgent.indexOf('Opera') == -1) {
        var parser = new DOMParser()
        return parser.parseFromString(text, "text/xml")
      } else if (window.ActiveXObject) {
        var xml = new ActiveXObject('MSXML2.DOMDocument.3.0')
        if (xml.loadXML(text)) return xml
      } else {
        var xml = document.body.appendChild(document.createElement('div'))
        xml.style.display = 'none'
        xml.innerHTML = self.responseText
        _cleanWhitespace(xml, true)
        return xml.childNodes[0]
     }
  }

  var _cleanWhitespace = function(element, deep) {
    var i = element.childNodes.length
    if (i == 0) return
    do {
      var node = element.childNodes[--i]
      if (node.nodeType == 3 && !_cleanEmptySymbols(node.nodeValue))
        element.removeChild(node)
      if (node.nodeType == 1 && deep)
        _cleanWhitespace(node, true)
    } while(i > 0)
  }

}

var base64encode = function(input) {
  if(typeof input == "undefined") input = "";
  var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
  var output = "", chr1, chr2, chr3, enc1, enc2, enc3, enc4, i = 0;
  do {
    chr1 = input.charCodeAt(i++)
    chr2 = input.charCodeAt(i++)
    chr3 = input.charCodeAt(i++)
    enc1 = chr1 >> 2
    enc2 = ((chr1 & 3) << 4) | (chr2 >> 4)
    enc3 = ((chr2 & 15) << 2) | (chr3 >> 6)
    enc4 = chr3 & 63
    if (isNaN(chr2)) {
       enc3 = enc4 = 64
    } else if (isNaN(chr3)) {
       enc4 = 64
    }
    output = output + keyStr.charAt(enc1) + keyStr.charAt(enc2) +
      keyStr.charAt(enc3) + keyStr.charAt(enc4)
  } while (i < input.length)
  return output
}


function utf8encode(input) {
  if ('string' != typeof input) return ''
  input = input.replace(/\r\n/g,"\n");
  var output = "";
  for(var n = 0; n < input.length; n++) {
    var c = input.charCodeAt(n)
    if('null' != typeof c) {
      if (c < 128) {
        output += String.fromCharCode(c); }
      else if((c > 127) && (c < 2048)) {
        output += String.fromCharCode((c >> 6) | 192);
        output += String.fromCharCode((c & 63) | 128); }
      else {
        output += String.fromCharCode((c >> 12) | 224);
        output += String.fromCharCode(((c >> 6) & 63) | 128);
        output += String.fromCharCode((c & 63) | 128);}
    }
  }
  return output;
}
