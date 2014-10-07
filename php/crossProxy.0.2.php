<?php
/* bsNet - php v0.2
 * Copyright (c) 2013 by ProjectBS Committe and contributors. 
 * http://www.bsplugin.com All rights reserved.
 * Licensed under the BSD license. See http://opensource.org/licenses/BSD-3-Clause
 */
function __curl( $url, $header, $cookie, $data ){
	static $curlBase = array( CURLOPT_RETURNTRANSFER, TRUE, CURLOPT_SSL_VERIFYPEER, FALSE, CURLOPT_SSL_VERIFYHOST, 2 );
	$t0 = curl_init();
	for( $i = 0, $j = count($curlBase); $i < $j; ){
		curl_setopt( $t0, $curlBase[$i++], $curlBase[$i++] );
	}
	for( $i = 4, $args = func_get_args(), $j = func_num_args(); $i < $j; ){
		curl_setopt( $t0, $args[$i++], $args[$i++] );
	}
	curl_setopt( $t0, CURLOPT_URL, $url );

	if( $cookie && strlen($cookie) > 0 ) curl_setopt( $t0, CURLOPT_COOKIE, $cookie );
	if( $header && count($header) > 0 ) curl_setopt( $t0, CURLOPT_HTTPHEADER, $header ); 
	if( $data && strlen($data) > 0 ) curl_setopt( $t0, CURLOPT_POSTFIELDS, $data );
	$t1 = curl_exec( $t0 );
	curl_close( $t0 );
	return $t1 === FALSE ? curl_error( $t0 ) : $t1;
}
function __exception_handler( $e ) {      
	$t0 = pathinfo( $e->getFile() );
	$t1 = '['.$e->getCode().']';
	$t1 .= strip_tags( $e->getMessage() );
	$t1 .= ' [file]'.$t0['basename'];
	$t1 .= ' [line]'.$e->getLine();
	__error( $t1 );
}
function __error_handler( $code, $message, $filename, $lineno ) {
	$e = new ErrorException( $message, $code, 0, $filename, $lineno );
	__exception_handler( $e );
}
function __shutdown_handler() {
	$e = error_get_last();
	if( $e ) {
		__error_handler( $e['type'], $e['message'], $e['file'], $e['line'] ); 
	}    
}
function __error( $msg ) {
	header( "HTTP/1.0 400 ".$msg );
	exit;
}
function getData( &$v, $k ){
	$t0 = &$v;
	for( $k = explode( '.', $k ), $i = 0, $j = count($k) ; $i < $j ; $i++ ) $t0 = &$t0[$k[$i]];
	return $t0;
}
function http( $method, $url, $header, $data, $cookie ){
	switch( $method ){
	case 'GET': return __curl( $url, $header, $cookie, '', CURLOPT_POST, FALSE );
	case 'POST': return __curl( $url, $header, $cookie, $data, CURLOPT_POST, TRUE );
	case 'PUT': return __curl( $url, $header, $cookie, $data, CURLOPT_POST, TRUE, CURLOPT_CUSTOMREQUEST, $method );
	case 'DELETE': return __curl( $url, $header, $cookie, $data, CURLOPT_POST, TRUE, CURLOPT_CUSTOMREQUEST, $method );
	}
}
header("Access-Control-Allow-Origin: *");
header('Access-Control-Max-Age: 5');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-header: Content-Type, Cache-Control');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: text/html; charset=utf-8');

error_reporting( E_ALL );
ini_set( 'display_errors', 0 );
ini_set( 'log_errors', 1 );
ini_set( 'url_rewriter.tags', '' );
ini_set( 'session.use_trans_sid', 0 );
register_shutdown_function( '__shutdown_handler' );
set_exception_handler( '__exception_handler' );
set_error_handler( '__error_handler' );

if( count($_POST) == 0 && isset($HTTP_RAW_POST_DATA) ){
	$data = explode('&', $HTTP_RAW_POST_DATA);
	for( $i = 0, $j = count($data); $i < $j; ++$i ){
		$val = $data[$i];
		if( !empty($val) ){
			list( $key, $val ) = explode('=', $val);
			$_POST[$key] = urldecode($val);
		}
	}
}

if( !isset($_POST['url']) || !isset($_POST['method']) || !isset($_POST['data']) || !isset($_POST['header'] ) || !isset($_POST['key']) ) {
	__error('Wrong parameters');
	exit;
}

$url = $_POST['url'];
$method = $_POST['method'];
$cookie = isset($_POST['cookie']) ? urldecode($_POST['cookie']) : null;
parse_str($_POST['header'], $temps);
$header = array(); 
$command = '';
$cData = NULL;
foreach( $temps as $k => $v ){
	if( $k == 'bsNet' ){
		$cData = json_decode( $v, true );
		$command = $cData['command'];
		unset($header['bsNet']);
	}else{
		$header[] = $k.": ".$v;
	}
}
$data = $_POST['data'];

$result = http( $method, $url, $header, $data ? $data : json_encode(array('test'=>'test')), $cookie );
switch( $command ){
case'restMix':
	$result = json_decode( $result, TRUE );
	$type = $cData['type'];
	$key = $cData['key'];
	$target = $cData['target'];
	$url = $cData['url'];
	$t0 = &$result;
	$k = $cData['lookup'];
	for( $k = explode( '.', $k ), $i = 0, $j = count($k) ; $i < $j ; $i++ ) $t0 = &$t0[$k[$i]];
	if( $cData['method'] == 'array' ){
		for( $i = 0, $j = count($t0) ; $i < $j ; $i++ ){
			$t1 = str_replace( '@key@', urlencode($t0[$i][$key]), $url );
			$t0[$i][$target] =  $type == 'data' ? json_decode( http( $method, $t1, $header, $cookie ), TRUE ) : $t1;
		}
	}else{
		$limit = explode( ',', getData( $result, $cData['limit'] ) );
		foreach( $limit as $k ){
			if( isset( $t0[$k] ) ){
				$t1 = str_replace( '@key@', urlencode($t0[$k][$key]), $url );
				$t0[$k][$target] = $type == 'data' ? json_decode( http( $method, $t1, $header, $cookie ), TRUE ) : $t1;
			}
		}
	}
	$result = json_encode($result);
	break;
}
echo $result;
?>