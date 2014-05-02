<?php
$svr = $_SERVER;
$_br = '<br/>' . "\n";

function showServerInfo($svr) {
    global $_br;
    echo '##### bsCORS.php $_SERVER #####' . $_br;
     foreach( $svr as $k => $v ) {
     	echo $k . ': ' . $v . $_br;
     }
     echo '##### bsCORS.php $_SERVER #####' . $_br;
}

function showAllReqHeaders() {
    global $_br;
    echo '##### bsCORS.php SHOW-ALL-REQ-HEADERS #####' . $_br;
    foreach( apache_request_headers() as $k => $v ) {
        echo $k . ': ' . $v . '<br/>' . "\n";			
    }
    echo '##### bsCORS.php SHOW-ALL-REQ-HEADERS #####' . $_br;
}

function showCheckOPTIONS($svr) {
    global $_br;
    echo '%%% OPTIONS CHECK %%%' . $_br;
    echo 'REQUEST_METHOD == OPTIONS : ' . ($svr['REQUEST_METHOD'] == 'OPTIONS') . $_br;
    echo 'abc : ' . (1 == 2) . $_br;
    echo '%%% OPTIONS CHECK %%%' . $_br;
}

function showJSONdata($post) {
    global $_br;
    echo '%%% SHOW JSON %%%' . $_br;
    echo 'JSON Parameter : ' . $_br;
    echo $post['postdata'] . $_br;
    echo '%%% SHOW JSON %%%' . $_br;
}

function showURLinJSON($post) {
    global $_br;
    echo '%%% SHOW URL in JSON %%%' . $_br;
    $decodedObj = json_decode($post['postdata'], true);
    echo $decodedObj['url'] . $_br;
    echo '%%% SHOW URL in JSON %%%' . $_br;
}

function showCustomHeaderValue($svr) {
    global $_br;
    echo '%%% SHOW CUSTOM Header %%%' . $_br;
    echo '$svr["HTTP_BSPLUGIN_CORSPROXY"] : ' . $svr['HTTP_BSPLUGIN_CORSPROXY'] . $_br;
    echo '%%% SHOW CUSTOM Header %%%' . $_br;
}

processCORS($svr, $_POST);



//////////
// CORS Requestor에게 결과 Hash를 JSON으로 encode하여 반환
//
function processCORS($svr, $post) {
//showServerInfo($svr);
//showAllReqHeaders();
//showJSONdata($post);
//showURLinJSON($post);
    setResponseHeaders($svr, $post); 
    getResultFromTarget($svr, $post);
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
// 
// 위에꺼 틀렸음
// PHP 프로그램에서 Req Header를 분석하여
// Actual 인지 Preflight 인지 구분하는 것이 아니라
// header()에 정해진대로 웹서버가 알아서 구분하여 처리
// 즉, header()에 정해진대로 OPTIONS 안의 내용에 대해 웹서버가 알아서 처리
// PHP 프로그램에서는 Preflight request에 대한 요청을 직접 처리할 수 없고
// 찍어서 나오는 값은 언제나 Actual request에 대한 내용이다.
// 
function setResponseHeaders($svr, $post) {

//showCheckOPTIONS($svr);

//showCustomHeaderValue($svr);
//echo isset($svr['HTTP_BSPLUGIN_CORSPROXY']) . "\n";
//var_dump(isset($svr['HTTP_BSPLUGIN_CORSPROXY']));

    // isset($svr['HTTP_BSPLUGIN_CORSPROXY']) 이 참을 리턴해도 아래 if 절에서 걸린다.
//    if ( isset($svr['HTTP_BSPLUGIN_CORSPROXY']) ) {
        header('Access-Control-Allow-Origin: ' . $svr['HTTP_ORIGIN']);
        header("Content-Type: application/x-www-form-urlencoded;charset=utf-8");    
        header('Access-Control-Allow-Methods: POST, OPTIONS');     
        header('Access-Control-Allow-Headers: Cache-Control, Content-Type, bscors-real-target-url, bscors-original-method');
        header('Access-Control-Max-Age: 5');
//    }

}

//////////
// curl Target에서 받은 Result를 Hash로 만들어 반환
//
function getResultFromTarget($svr, $post) {

    $decodedObj = json_decode($post['postdata']);
    print_r( $decodedObj->url );
//    return http_build_query($decodedArr);
//    return $svr['HTTP_BSCORS_REAL_TARGET_URL'];
//    return $decodedArr;
/*
    $t0 = curl_init();

    curl_setopt( $t0, CURLOPT_URL, $decodedObj->url);
    curl_setopt( $t0, CURLOPT_HEADER, FALSE );
    curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
//    curl_setopt( $t0, CURLOPT_POST, TRUE );
    curl_setopt( $t0, CURLOPT_POSTFIELDS, http_build_query(getRealUserDataArray($post)) ); // urlencoded    
//    curl_setopt( $t0, CURLOPT_HTTPHEADER, getRequestHeaders($svr, $post)); //-> CURLOPT_HTTPHEADER를 수동으로 지정해주면 Bad Request 발생. 이유 모름.
    $t1 = curl_exec($t0);
    curl_close($t0);

//echo "\n" . '=== getResultFromTarget ===' . "\n";
    return $t1 === FALSE ? curl_error($t0) : $t1;

//    return AssociativeArray
//*/
}

//////////
// $_POST 에서 CORS를 위한 데이터(url, customheader, method)를 제외하고
// 실제 사용자의 데이터만 연관 배열로 반환
//
function getRealUserDataArray($post) {
    $decodedObj = json_decode($post['postdata']);
    getURLfromPost($post);

    foreach($decodedObj as $k => $v) {        
        if ( $k != 'url' && $k != 'customheader' && $k != 'method') {
            $resultArr[$k] =$v;
        }
    }
    return $resultArr;
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
// 유효하지 않은 CORS Request 처리
//
///*
function sendError() {
    echo 'error during CORS';
}
//*/

?>
