<?php
$br = '<br/>' . "\n";
/*
echo '====== MockTarget : Infos in $_GET ======' . $br;
$data = $_GET;
foreach( $data as $k=>$v ) {
	echo $k . ' : ' . $v . $br;
	echo 'urlencoded' . $br;
	echo $k . ' : ' . urlencode($v) . $br;
}
echo '====== MockTarget : Infos in $_Data1 ======' . $br;
echo $data['q'] . $br;
//*/
$pdata = array(
	'apikey' => 'DAUM_SEARCH_DEMO_APIKEY',
	'output' => 'json',
	'q' => ('자바')
);
echo '-------- arr --------' . $br;
print_r($pdata);
echo $br;
print_r(http_build_query($pdata));
echo $br;
echo '-------- arr --------' . $br;
foreach( $pdata as $k=>$v ) {
	echo $k . ' : ' . $v . $br;
}
echo '-------- curl result --------' . $br;
echo $pdata . $br;
//__curl($url, 0);
__curl($url, 1, $pdata);
function __curl($url, $isPost, $pdata) {
	$t0 = curl_init();
	curl_setopt( $t0, CURLOPT_POST, $isPost );
	if ($isPost) {
		curl_setopt ($t0, CURLOPT_POSTFIELDS, http_build_query($pdata));
		$url = 'http://apis.daum.net/search/book';		
	} else {
		$url = 'http://apis.daum.net/search/book?apikey=DAUM_SEARCH_DEMO_APIKEY&output=json&q=' . ($data['q']);		
	}	
	curl_setopt( $t0, CURLOPT_URL, $url );	
	$t1 = curl_exec( $t0 );
	curl_close( $t0 );
	return $t1 === FALSE ? curl_error( $t0 ) : $t1;
}
//*/

?>
