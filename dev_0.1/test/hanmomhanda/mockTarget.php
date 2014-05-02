<?php
$br = '<br/>' . "\n";
echo 'Mock for Target' . $br;

echo '====== MockTarget : Infos in $_SERVER ======' . $br;
foreach( $_SERVER as $k=>$v ) {
    echo $k . ' : ' . $v . $br;
}

echo '====== MockTarget : Infos in $_POST ======' . $br;
foreach( $_REQUEST as $k=>$v ) {
	echo $k . ' : ' . $v . $br;
}

?>
