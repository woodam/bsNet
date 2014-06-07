<?php
header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
header('Access-Control-Max-Age: 5');   
header('Access-Control-Allow-Methods: POST, OPTIONS');   
header('Access-Control-Allow-header: Content-Type, Cache-Control');       
header('Access-Control-Allow-Credentials: true');

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

if( !isset($_POST['url']) || !isset($_POST['method']) || !isset($_POST['data']) || !isset($_POST['header'] ) || !isset($_POST['key']) || !isset($_POST['cookie']) ) {
	__error('Wrong parameters');
	exit;
}

$url = $_POST['url'];
$method = $_POST['method'];
$cookie = $_POST['cookie'];
parse_str($_POST['header'], $temps);
$header = array(); 
foreach( $temps as $k => $v ) $header[] = $k.": ".$v;
$data = $_POST['data'];

switch( $method ) {
case 'GET': echo __curl( $url, $header, $cookie, '', CURLOPT_POST, FALSE ); break;
case 'POST': echo __curl( $url, $header, $cookie, $data, CURLOPT_POST, TRUE ); break;
case 'PUT': echo __curl( $url, $header, $cookie, $data, CURLOPT_POST, TRUE, CURLOPT_CUSTOMREQUEST, $method ); break;
case 'DELETE': echo __curl( $url, $header, $cookie, $data, CURLOPT_POST, TRUE, CURLOPT_CUSTOMREQUEST, $method ); break;
}
exit;
function __curl( $url, $header, $cookie, $data ){
	$t0 = curl_init();
	curl_setopt( $t0, CURLOPT_HEADER, FALSE );
	curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
	curl_setopt( $t0, CURLOPT_URL, $url );
	for( $i = 4, $args = func_get_args(), $cnt = count($args); $i < $cnt; ){
		curl_setopt( $t0, $args[$i++], $args[$i++] );
	}
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