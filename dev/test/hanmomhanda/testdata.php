<?php
header("Content-type: application/json;charset=utf-8");
$data = array("method"=>$_SERVER['REQUEST_METHOD']);
switch( $_SERVER['REQUEST_METHOD'] ) {
    case 'POST': $data = array_merge( $data, $_POST ); break;
    case 'GET': $data = array_merge( $data, $_GET ); break;    
    case 'PUT': parse_str(file_get_contents('php://input'), $put); $data = array_merge( $data, $put ); break;    
    case 'DELETE': parse_str(file_get_contents('php://input'), $del); $data = array_merge( $data, $del ); break;  
}
echo json_encode($data);