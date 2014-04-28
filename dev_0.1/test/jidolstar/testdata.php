<?php
header("Content-type: application/json;charset=utf-8");
$data = array(
    "a" => "b"
);
echo json_encode($data);