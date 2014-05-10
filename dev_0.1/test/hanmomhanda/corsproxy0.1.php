<?php
$_br = '<br/>' . "\n";
processCORS($_SERVER, $_REQUEST);

//////////
// CORS Request를 처리할 Response 헤더를 설정하고,
//   bsJS의 요청이면 Real Target에 요청하여 받은 결과 반환
//   bsJS의 요청이 아니면 Real Target에 요청도 하지 않고 반환값도 없음
//
function processCORS($svr, $req) {    

    $decodedObj = json_decode($req['postdata']);

    setResponseHeaders($svr, $decodedObj);
    if ( isBS($decodedObj) ) {
        echo getResultFromTarget($svr, $decodedObj);
    }    
}

//////////
// 요청이 BS요청인지 확인
// 
function isBS($decodedObj) {
    return (strcasecmp($decodedObj->customheader, 'X_BSJSCORS')==0);
}

//////////
// PHP 데몬이 Req Header를 분석하여
// header()에 정해진대로 웹서버가 알아서 구분하여 처리
// 즉, OPTIONS 인지 아닌지 프로그래머가 직접 구분하는 것이 아니라
// header()에 정해진대로 OPTIONS 이든 아니든 내용에 대해 웹서버가 알아서 처리
// 
// PHP 프로그램 내에서는 Preflight request에 대한 요청을 직접 처리할 수 없고
// 찍어서 나오는 값은 언제나 Actual request에 대한 내용이다.
// 
function setResponseHeaders($svr, $decodedObj) {
    header('Access-Control-Allow-Origin: ' . $svr['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: POST, OPTIONS');        
    header('Access-Control-Allow-Headers: Cache-Control, Content-Type, ABCD');
    header('Access-Control-Allow-Credentials: true');
    header("Content-Type: application/x-www-form-urlencoded;charset=utf-8");        
    header('Access-Control-Max-Age: 5');
}

//////////
// curl로 Target에서 받은 Result를 그대로 반환
//
function getResultFromTarget($svr, $decodedObj) {

    $method = $decodedObj->method;

    $t0 = curl_init();
    curl_setopt( $t0, CURLOPT_URL, $decodedObj->url);
    curl_setopt( $t0, CURLOPT_HEADER, FALSE );
    curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $t0, CURLOPT_CUSTOMREQUEST, $method );
    if ( strcasecmp($method, 'post') == 0 ) {
        curl_setopt( $t0, CURLOPT_POSTFIELDS, http_build_query(getRealUserDataArray($decodedObj)) );    
    }    
    curl_setopt( $t0, CURLOPT_HTTPHEADER, getRequestHeaders($svr));
    $t1 = curl_exec($t0);
    curl_close($t0);

    return $t1 === FALSE ? curl_error($t0) : $t1;
}

//////////
// $_POST 에서 CORS를 위한 데이터(url, customheader, method)를 제외하고
// 실제 사용자의 데이터만 연관 배열로 반환
//
function getRealUserDataArray($decodedObj) {
    
    $resultArr = array();
    
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
function getRequestHeaders($svr) {

    $h_arr = array('Host: ' . $svr['HTTP_HOST']);
    array_push($h_arr, 'User-Agent: ' . $svr['HTTP_USER_AGENT']);
    //	array_push($h_arr, 'Origin: ' . $svr['HTTP_ORIGIN']);
    /*
            $ac_req_method = $svr['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
            isset($ac_req_method) ? array_push($h_arr, 'Access-Control-Request-Method: ' . $ac_req_method) : 0;

            $ac_req_headers = $svr['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'];
            isset($ac_req_headers) ? array_push($h_arr, 'Access-Control-Request-Headers: ', $ac_req_headeers) : 0;
    */
    return $h_arr;
}


//////////////////////////////
// 아래는 디버그용 함수
//////////////////////////////


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
    print_r($post['postdata']);
    echo $_br;
    echo '%%% SHOW JSON %%%' . $_br;
}

function showReqData($req) {
    global $_br;
    echo '%%% SHOW REQ %%%' . $_br;
    echo 'REQ : ' . $_br;
    print_r($req);
    echo $_br;
    echo '%%% SHOW REQ %%%' . $_br;
}

function showPostData($post) {
    global $_br;
    echo '%%% SHOW POST %%%' . $_br;
    echo 'POST : ' . $_br;
    print_r($post);
    echo $_br;
    echo '%%% SHOW POST %%%' . $_br;
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


?>
