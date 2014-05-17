<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');   
header('Access-Control-Allow-Headers: bscorsproxy, Content-Type');       
header('Access-Control-Max-Age: 5');
header("Content-Type: application/x-www-form-urlencoded;charset=utf-8");        

error_reporting( E_ALL );
ini_set( 'display_errors', 0 );
ini_set( 'log_errors', 1 );
register_shutdown_function( '__shutdown_handler' );
set_exception_handler( '__exception_handler' );
set_error_handler( '__error_handler' );

if( !isset($_POST['url']) || !isset($_POST['method']) || !isset($_POST['data']) || !isset($_POST['headers']) ) __error('wrong request'); 

$url = $_POST['url'];
$method = $_POST['method'];
parse_str($_POST['data'], $data);
parse_str($_POST['headers'], $headers);

echo "[corsProxy] \n";
echo ' - url = '.$url."\n";
echo ' - method = '.$method."\n";
echo ' - data = '.json_encode($data)."\n";
echo ' - headers = '.json_encode($headers)."\n";

switch( $method ) {
    case 'GET': $ret = __get( $url, $headers ); break;
    case 'POST': $ret = __post( $url, $headers, $data ); break;
    case 'PUT': $ret = __put( $url, $headers, $data ); break;
    case 'DELETE': $ret = __delete( $url, $headers, $data ); break;
    default; $ret = ''; break;
}
echo " - return = ".$ret;
exit;
function __get( $url, $headers ){
    $t0 = curl_init();
    curl_setopt( $t0, CURLOPT_HEADER, FALSE );
    curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $t0, CURLOPT_POST, FALSE );
    curl_setopt( $t0, CURLOPT_URL, $url );
    if( $headers && count($headers) > 0 ) curl_setopt( $t0, CURLOPT_HTTPHEADER, $headers ); 
    $t1 = curl_exec( $t0 );
    curl_close( $t0 );
    return $t0 === FALSE ? curl_error( $t0 ) : $t1;
}
function __post( $url, $headers, $data ){
    $t0 = curl_init();
    curl_setopt( $t0, CURLOPT_HEADER, FALSE );
    curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $t0, CURLOPT_POST, TRUE );
    curl_setopt( $t0, CURLOPT_URL, $url );
    if( $headers && count($headers) > 0 ) curl_setopt( $t0, CURLOPT_HTTPHEADER, $headers );
    if( $data && count($data) > 0 ) curl_setopt( $t0, CURLOPT_POSTFIELDS, $data );
    $t1 = curl_exec( $t0 );
    curl_close( $t0 );
    return $t0 === FALSE ? curl_error( $t0 ) : $t1;
}
function __put( $url, $headers, $data ){
    $t0 = curl_init();
    curl_setopt( $t0, CURLOPT_CUSTOMREQUEST, "PUT" );
    curl_setopt( $t0, CURLOPT_HEADER, FALSE );
    curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $t0, CURLOPT_POST, TRUE );
    curl_setopt( $t0, CURLOPT_URL, $url );
    if( $headers && count($headers) > 0 ) curl_setopt( $t0, CURLOPT_HTTPHEADER, $headers );
    if( $data && count($data) > 0 ) curl_setopt( $t0, CURLOPT_POSTFIELDS, http_build_query($data) );
    $t1 = curl_exec( $t0 );
    curl_close( $t0 );
    return $t0 === FALSE ? curl_error( $t0 ) : $t1;
}
function __delete( $url, $headers, $data ){
    $t0 = curl_init();
    curl_setopt( $t0, CURLOPT_CUSTOMREQUEST, "DELETE" );
    curl_setopt( $t0, CURLOPT_HEADER, FALSE );
    curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $t0, CURLOPT_POST, TRUE );
    curl_setopt( $t0, CURLOPT_URL, $url );
    if( $headers && count($headers) > 0 ) curl_setopt( $t0, CURLOPT_HTTPHEADER, $headers );
    if( $data && count($data) > 0 ) curl_setopt( $t0, CURLOPT_POSTFIELDS, http_build_query($data) );
    $t1 = curl_exec( $t0 );
    curl_close( $t0 );
    return $t0 === FALSE ? curl_error( $t0 ) : $t1;
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
    $ret = array( 
        'success' => false,
        'message' => $msg
    );
    echo json_encode($ret);
    exit;
}