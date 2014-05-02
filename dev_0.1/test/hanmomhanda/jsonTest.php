<?php
$br = '<br/>';
echo '##### jsonTest #####' . $br;


$jsonstr = '{"url":"http://apexsoft-svr1.iptime.org/bsJScorsProxy/dev_0.1/test/hanmomhanda/mockTarget.php","p1":"abcde","p2":"오명운"}';
//$jsonstr = '{"url":"!#$","p1":"abcde","p2":"오명운"}';
echo $jsonstr . $br;
$decodedObj = json_decode($jsonstr);
$decodedArray = json_decode($jsonstr, true);

//echo $decodedObj . $br;
echo $decodedArray['url'] . $br;

echo function_exists('http_build_query') . $br;;

echo http_build_query($decodedArray) . $br;;

echo '##### jsonTest #####' . $br;

?>