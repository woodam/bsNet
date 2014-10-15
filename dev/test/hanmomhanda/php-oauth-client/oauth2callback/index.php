<?php
// oauth2call/index.php

require('../config.php');
require('../HttpPost.class.php');

/**
 * the OAuth server should have brought us to this page with a $_GET['code']
 */
echo "callback invoked\n";
echo $_SERVER;
//if (isset($_GET['code'])) {
    // try to get an access token
//    $code = $_GET['code'];
    $url = 'https://accounts.google.com/o/oauth2/token';

    // this will be our POST data to send back to the OAuth server in exchange for an access token
    $params = array(
//        'code' => $code,
        'client_id' => $oauth2_client_id,
        'client_secret' => $oauth2_secret,
        'redirect_uri' => $oauth2_redirect,
        'grant_type' => 'authorization_code'
    );

    // build a new HTTP Post request
    $request = new HttpPost($url);
    $request->setPostData($params);
    $request->send();

    // decode the incoming string as JSON
    $responseObj = json_decode($request->getHttpResponse());

    // Tada: we have an access token!
    echo 'OAuth2 server provided access token: ' . $responseObj->access_token;
    echo "\n";
    print_r($responseObj);
//}

?>
