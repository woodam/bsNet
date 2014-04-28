<?php
header("Content-type: text/plain;charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS");
header("Access-Control-Allow-Headers: bsplugin-corsproxy"); //preflight request
$data = [
    "a" => "b"
];
echo json_encode($data);
?>
