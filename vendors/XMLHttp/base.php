<?php

require_once dirname(__FILE__) . '/pear/request.php';
require_once dirname(__FILE__) . '/pear/json.php';
require_once dirname(__FILE__) . '/session.php';

class XMLHTTPRequest extends HTTP_Request {

  function getAllResponseHeaders() {
    return $this->_response->_headers;
  }

}

class XMLHTTP {

  var $_url = false;
  var $_aborted = false;
  var $_completed = true;
  var $_data = '';

  function XMLHTTP($params, $respond = true, $limiturl=false) {
	$this->_url = $limiturl;
    $this->_session = new XMLHTTPSession($params['id'], $params['total']);
    $this->_session->save($params['data'], $params['part']);
    $this->_request = new XMLHTTPRequest();
	$this->request();
    if($respond) { $this->_respond($params['id']); }
	
  }

  function _decode($session) {
    if($session == '') { $this->throwError('Incomplete request'); }
    $data = explode("&", $session);
    $this->_setMethod($data[0]);
    $this->_setURL(base64_decode($data[1]));
    $this->_data = base64_decode($data[2]);
    $this->_setHeaders(array_map("base64_decode", array_splice($data, 3, -1)));
  }

  function _setMethod($method) {
	if($this->_aborted) { return false; }
    if(!in_array($method, Array('GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'TRACE'))) {
      return $this->throwError('Incorrect method: only GET, HEAD, POST, PUT, DELETE, OPTIONS, TRACE supported');
	}
    $this->_request->setMethod($method);
  }

  function _setURL($url) {
	if($this->_url && strpos($url, $this->_url) !== 0) { return false; }
	if($this->_aborted) { return false; }
    if(!(substr(strtolower($url), 0, 7) == 'http://' ||
    substr(strtolower($url), 0, 8) == 'https://')) {
      return $this->throwError('Incorrect URL: only http:// and https:// protocols supported '); }
    $this->_request->setURL($url);
  }

  function _setHeaders($array) {
	if($this->_aborted) { return false; }
    foreach($array as $header) {
      $item = split(":", $header, 2);
      $this->_request->addHeader($item[0], $item[1]);
    }
  }

  function request() {
    $session = $this->_session->get();
	if($session == '') {
		$this->_completed = false;
		return $this->throwError('Incomplete request: more parts expected'); }
    $this->_decode($session);
    $this->_fetch();
  }

  function _fetch() {
    if($this->_aborted) { return false; }
    $this->_request->addHeader('Forwarded', 'by '.$_SERVER['HTTP_HOST'].' for '.$_SERVER['REMOTE_ADDR']);
    if (PEAR::isError($this->_request->sendRequest($this->_data)) ||
    $this->_request->getResponseCode() == 400) {
	  $this->throwError('Error occurred while connecting to the specified host'); }
  }

  function _respond($id) {
    $this->json = new Services_JSON();
    header('Content-Type: text/javascript; charset=UTF-8');
    echo $id."(".$this->json->encode($this->response()).")";
  }

  function response() {
	if(!$this->_completed) { return; }
    return (!$this->_aborted) ? Array(
      'success' => 1,
      'responseHeaders' => $this->_request->getAllResponseHeaders(),
      'cookies' => $this->_request->getResponseCookies(),
      'status' => $this->_request->getResponseCode(),
      'responseText' => $this->_request->getResponseBody(),
    ) : Array(
      'success' => 0,
      'description' => $this->_errorDescription
    );
  }

  function throwError($desc) {
    $this->_aborted = true;
    $this->_errorDescription = $desc;
    return false;
  } 

}

?>