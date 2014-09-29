<?php
// echo "fileUploader entered";

	$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
// echo 'header : ' . $_SERVER['HTTP_X_FILENAME'];
	echo print_r($_SERVER);
	// echo print_r(getallheaders());

	$fileName = 'abc.txt';
		// AJAX call
	file_put_contents(
		'uploads/' . $fileName,
		file_get_contents('php://input')
	);
	echo "$fileName uploaded";
	exit();
?>