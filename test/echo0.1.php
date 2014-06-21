<?php
header("Content-type: application/json;charset=utf-8");
if (!function_exists('getallheaders')) {
	function getallheaders() {
		foreach ($_SERVER as $name => $value) {
			if (strtolower(substr($name, 0, 5)) == 'http_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}
$data = array(
	"header" => getallheaders(),
	"method" => $_SERVER['REQUEST_METHOD']
);
switch( $_SERVER['REQUEST_METHOD'] ) {
case 'POST': $data['data'] = $_POST; break;
case 'GET': $data['data'] = $_GET; break;    
case 'PUT': parse_str(file_get_contents('php://input'), $put); $data['data'] = $put; break;    
case 'DELETE': parse_str(file_get_contents('php://input'), $del); $data['data'] = $del; break;  
}
echo json_encode($data);