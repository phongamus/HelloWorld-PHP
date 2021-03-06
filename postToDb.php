<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

session_start();

echo '<pre>';
// print_r($_GET['mid']);
$mid = $_GET['mid'];
$paytraceApiUsername = $_GET['paytraceApiUsername'];
$paytraceApiPassword = $_GET['paytraceApiPassword'];
$paytracePublicKey = $_GET['paytracePublicKey'];

function postToDb($mid, $paytraceApiUsername, $paytraceApiPassword, $paytracePublicKey)
{
    $accessToken = $_SESSION['sessionAccessToken'];
    $auth_server = 'https://57g9kbpknl.execute-api.us-east-1.amazonaws.com/maddySyncManager';
    // $auth_server = 'https://fpzxkuf1lj.execute-api.us-east-1.amazonaws.com/'; //old
    // $auth_server = 'https://enxjdbj6871kl.x.pipedream.net';
    try{
        $client = new GuzzleHttp\Client();
        $res1 = $client->post($auth_server, [
            'json' => [
                'mid' => $mid,
                'qboCredentials' => [
                    'refreshTokenKey' => $accessToken->getRefreshToken(),
                    'QBORealmID' => $accessToken->getRealmID(),
                    'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
                ],
                'gatewayCredentials' => [
                    'gatewayType' => 'paytrace',
                    'apiUsername' => $paytraceApiUsername,
                    'apiPassword' => $paytraceApiPassword,
                    'publicKey' => $paytracePublicKey
                ]
            ]
        ]);

        // grab the results (as JSON)
        $body = $res1->getBody();
        $json = json_decode($body);

        // writeMsg($body);
        echo("Sucessfully sent credentials");
        // $result = $json->{'result'};
        $result = $body;
        return $result;
    }
    catch(Exception $e){
        echo("Didn't get a token, something went wrong </br>");
        echo($e);
        return FALSE;
    }
    
}

if(isset($_GET['mid']) && $_GET['mid'] != ""){
    $response = postToDb($mid, $paytraceApiUsername, $paytraceApiPassword, $paytracePublicKey);
    echo '</br>';
    echo($response);

    echo '</br>Done';
    echo '</pre>';
} else{
    echo 'something is missing';
}


?>