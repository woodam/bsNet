<?php
    $url = 'http://apexsoft-svr1.iptime.org/bsJScorsProxy/dev_0.1/test/hanmomhanda/mockTarget.php';
    
    $t0 = curl_init();
    curl_setopt( $t0, CURLOPT_HEADER, FALSE );
    curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $t0, CURLOPT_POST, TRUE );
    curl_setopt( $t0, CURLOPT_URL, $url );
    curl_setopt( $t0, CURLOPT_POSTFIELDS, http_build_query(array('p1'=>'abcde', 'p2' => '오명운')) ); // urlencoded    
//    if( func_num_args() > 1 ) curl_setopt( $t0, CURLOPT_POSTFIELDS, array_shift( func_get_args() ) );
    $t1 = curl_exec( $t0 );
    curl_close( $t0 );
print_r($t1);
    return $t0 === FALSE ? curl_error( $t0 ) : $t1;
/*
    $t0 = curl_init();

    curl_setopt( $t0, CURLOPT_URL, $decodedObj->url);
    curl_setopt( $t0, CURLOPT_HEADER, FALSE );
    curl_setopt( $t0, CURLOPT_RETURNTRANSFER, TRUE );
//    curl_setopt( $t0, CURLOPT_POST, TRUE );
    curl_setopt( $t0, CURLOPT_POSTFIELDS, http_build_query(getRealUserDataArray($post)) ); // urlencoded    
//    curl_setopt( $t0, CURLOPT_HTTPHEADER, getRequestHeaders($svr, $post)); //-> CURLOPT_HTTPHEADER를 수동으로 지정해주면 Bad Request 발생. 이유 모름.
    $t1 = curl_exec($t0);
    curl_close($t0);

//echo "\n" . '=== getResultFromTarget ===' . "\n";
    return $t1 === FALSE ? curl_error($t0) : $t1;

//    return AssociativeArray
//*/
?>