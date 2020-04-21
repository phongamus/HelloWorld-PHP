<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

echo '<pre>';
// print_r($_GET['mid']);
$mid = $_GET['mid'];

session_start();

function postToDb($mid)
{
    $accessToken = $_SESSION['sessionAccessToken'];
    $auth_server = 'https://fpzxkuf1lj.execute-api.us-east-1.amazonaws.com/';
    // $auth_server = 'https://enb1oanvszux.x.pipedream.net';
    try{
        $client = new GuzzleHttp\Client();
        $res1 = $client->post($auth_server, [
            'json' => [
                'mid' => $mid,
                'refreshTokenKey' => $accessToken->getRefreshToken(),
                'QBORealmID' => $accessToken->getRealmID(),
                'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt()

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
    $response = postToDb($mid);
    echo '</br>';
    echo($response);

    echo '</br>Done';
    echo '</pre>';
} else{
    echo 'something is missing';
}


?>