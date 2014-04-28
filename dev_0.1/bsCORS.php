<?php
$svr = $_SERVER;

echo '##### METHOD : ' . $svr['REQUEST_METHOD'] . ' #####' . "\n";

// echo '##### bsCORS.php $_SERVER #####' . "\n";
// foreach( $_SERVER as $k => $v ) {
// 	echo $k . ': ' . $v . "\n";			
// }
// echo '##### bsCORS.php $_SERVER #####' . "\n";

processCORS($svr, $_POST);



//////////
// CORS Requestor에게 결과 Hash를 JSON으로 encode하여 반환
//
function processCORS($svr, $post) {
    setResponseHeaders($svr); 
    echo getResultFromTarget($svr, $post);    
//        var_dump(getJsonResult($r0));
//		echo $r0;
}

//////////
// Preflight Request, Actual Request에 맞게 Response Header 설정
// return false when this is a Preflight Response.
// return true  when this is a Actual Response.
// 원래 Actual Request와 Preflight Request를 구분하려 했으나
// bsJS가 header에 Cache-Control을 자동으로 추가하여
// 언제나 Preflight Request로 요청이 오게 됨
function setResponseHeaders($svr) {
//    setPreflightResponseHeader($svr);
    setActualResponseHeader($svr);

//    $isActual = TRUE;
//    if ( isset($svr['HTTP_ORIGIN']) ) {
//        if ( $svr['REQUEST_METHOD'] == 'OPTIONS' ) {
//            if ( isset($svr['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) ) {
//                setPreflightResponseHeader($svr);
//                $isActual = FALSE;
//            } else {
//                setActualResponseHeader($svr);
//            }
//        } else {
//            setActualResponseHeader($svr);
//         }
//        // Cookie Allowed
//        header('Access-Control-Allow-Credentials: true');
//     } else {
//         sendError();
//     }
//    return $isActual;
}

//////////
// Preflight Request에 대한 Response Header 설정
//
function setPreflightResponseHeader($svr) {
echo 'PREFLIGHT RESPONSE HEADER' . "\n";
//    if ( isRequestMethodValid($svr['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Cache-Control, x-jidolstar');
        header('Access-Control-Max-Age: 1728000');
//        header("Content-Length: 0");
        header("Content-Type: text/plain");
//    } else {
//        sendError();
//    }
}

//////////
// Actual Request에 대한 Response Header 설정
//
function setActualResponseHeader($svr) {
echo 'ACTUAL RESPONSE HEADER' . "\n";
    header('Access-Control-Allow-Origin: ' . $svr['HTTP_ORIGIN']);
//    header('Access-Control-Expose-Headers: Content-Length');
    header('Access-Control-Allow-Headers: Cache-Control');
    header('Content-Type: application/json');

}
//*/

//////////
// curl Target에서 받은 Result를 Hash로 만들어 반환
//
function getResultFromTarget($svr, $post) {
/*
echo "\n" . '=== getResultFromTarget ===' . "\n";
echo '$post' . "\n";
var_dump($post);
echo '-----' . "\n";
//var_dump(http_build_query($post));
echo 'getRequestHeaders($svr, $post)' . "\n";
var_dump(getRequestHeaders($svr, $post));
echo '-----' . "\n";
*/
    $t0 = curl_init();
    curl_setopt( $t0, CURLOPT_URL, $post['url']);
    curl_setopt( $t0, CURLOPT_HEADER, FALSE );
    curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
//    curl_setopt( $t0, CURLOPT_POST, TRUE );
    curl_setopt( $t0, CURLOPT_POSTFIELDS, http_build_query($post) ); // urlencoded    
//    curl_setopt( $t0, CURLOPT_HTTPHEADER, getRequestHeaders($svr, $post)); //-> CURLOPT_HTTPHEADER를 수동으로 지정해주면 Bad Request 발생. 이유 모름.
    $t1 = curl_exec($t0);
    curl_close($t0);

//echo "\n" . '=== getResultFromTarget ===' . "\n";
    return $t1 === FALSE ? curl_error($t0) : $t1;

//    return AssociativeArray

}

//////////
// CORS requestor의 실제 헤더 정보를 추출하여 배열로 반환
//
function getRequestHeaders($svr, $post) {

	$br = '<br/>' . "\n";
	
	$url_from_bsAgent = $post['url'];
	$pOfSlash = strpos($url_from_bsAgent, '/', 7);
	$server_host = $pOfSlash ? substr($url_from_bsAgent, 0, $pOfSlash ) : $url_from_bsAgent;
	
	$h_arr = array('Host: ' . $server_host);
	array_push($h_arr, 'User-Agent: ' . $svr['HTTP_USER_AGENT']);
	//	array_push($h_arr, 'Origin: ' . $svr['HTTP_ORIGIN']);
	/*
		$ac_req_method = $svr['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
		isset($ac_req_method) ? array_push($h_arr, 'Access-Control-Request-Method: ' . $ac_req_method) : 0;
		
		$ac_req_headers = $svr['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'];
		isset($ac_req_headers) ? array_push($h_arr, 'Access-Control-Request-Headers: ', $ac_req_headeers) : 0;
	*/
		
	// 	$i = count($h_arr);
	// 	while($i--) echo $h_arr[$i] . $br;
	return $h_arr;
}
//*/

//////////
// CORS requestor의 요청 정보를 추출하여 반환
// ToDo:
//*
function getJsonResult($r) {
	/*
    if ( $r == 'json' ) return $r;
    if ( $r == 'associativearray' ) return json_encode($r);
    if ( $r == 'xml' ) return json_encode(simplexml_load_string($r));
    */
	var_dump($r);
}
//*/



//////////
// CORS requestor의 요청 정보를 추출하여 반환
//
/*
function getRequestBody($post) {
    $b = '';
    foreach( $post as $k=>$v ) {
        $b .= ($k . '=' . $v . '&');
    }
    return substr($b, 0, strlen($b)-1);
}
//*/

//////////
// HTTP Method의 유효성 검증
//
///*
function isRequestMethodValid($m) {

    $methods = array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS');
    return in_array($m, $methods);

}
//*/

//////////
// 유효하지 않은 CORS Request 처리
//
///*
function sendError() {
    echo 'error during CORS';
}
//*/

/* HEADER INFOS
// SIMPLE
#REQUEST_METHOD #url>#DOCUMENT_URI $SERVER_PROTOCOL
#SERVER_NAME
$HTTP_USER_AGENT
$HTTP_ACCEPT
$HTTP_ACCEPT_LANGUAGE
$HTTP_ACCEPT_ENCODING
Accept-Charset : ISO-8859-1,utf-8;q=0.7,*;q=0.7
$HTTP_CONNECTION
Referer : URI of Request Document
Origin : http://domain

// Preflight
$HTTP_ACCESS_CONTROL_REQUEST_METHOD
$HTTP_ACCESS_CONTROL_REQUEST_HEADERS

// Actual
Custom-Header
$CONTENT_TYPE
Referer : URI of Request Document
$CONTENT_LENGTH
Pragma : no-cache
Cache-Control : no-cache

*/

?>
