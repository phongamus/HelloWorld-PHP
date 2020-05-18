<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

$config = include('config.php');

session_start();

$dataService = DataService::Configure(array(
    'auth_mode' => 'oauth2',
    'ClientID' => $config['client_id'],
    'ClientSecret' =>  $config['client_secret'],
    'RedirectURI' => $config['oauth_redirect_uri'],
    'scope' => $config['oauth_scope'],
    'baseUrl' => "development"
));

$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
$authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();




// Store the url in PHP Session Object;
$_SESSION['authUrl'] = $authUrl;

//set the access token using the auth object
if (isset($_SESSION['sessionAccessToken'])) {

    $accessToken = $_SESSION['sessionAccessToken'];
    $accessTokenJson = array('token_type' => 'bearer',
        'access_token' => $accessToken->getAccessToken(),
        'refresh_token' => $accessToken->getRefreshToken(),
        'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
        'expires_in' => $accessToken->getAccessTokenExpiresAt()
    );
    $dataService->updateOAuth2Token($accessToken);
    $oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
    $CompanyInfo = $dataService->getCompanyInfo();
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="apple-touch-icon icon shortcut" type="image/png" href="https://plugin.intuitcdn.net/sbg-web-shell-ui/6.3.0/shell/harmony/images/QBOlogo.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script>

        var url = '<?php echo $authUrl; ?>';

        var OAuthCode = function(url) {

            this.loginPopup = function (parameter) {
                this.loginPopupUri(parameter);
            }

            this.loginPopupUri = function (parameter) {

                // Launch Popup
                var parameters = "location=1,width=800,height=650";
                parameters += ",left=" + (screen.width - 800) / 2 + ",top=" + (screen.height - 650) / 2;

                var win = window.open(url, 'connectPopup', parameters);
                var pollOAuth = window.setInterval(function () {
                    try {

                        if (win.document.URL.indexOf("code") != -1) {
                            window.clearInterval(pollOAuth);
                            win.close();
                            location.reload();
                        }
                    } catch (e) {
                        console.log(e)
                    }
                }, 100);
            }
        }


        var apiCall = function() {
            this.getCompanyInfo = function() {
                /*
                AJAX Request to retrieve getCompanyInfo
                 */
                $.ajax({
                    type: "GET",
                    url: "apiCall.php",
                }).done(function( msg ) {
                    $( '#apiCall' ).html( msg );
                });
            }

            this.refreshToken = function() {
                $.ajax({
                    type: "POST",
                    url: "refreshToken.php",
                }).done(function( msg ) {
                    $( '#refreshToken' ).html( msg );
                });
            }

            this.postToDb = function() {
                $mid = $("#mid").prop("value");
                $paytraceApiUsername = $("#paytraceApiUsername").prop("value");
                $paytraceApiPassword = $("#paytraceApiPassword").prop("value");
                console.log("mid: " + $mid);
                $.ajax({
                    type: "GET",
                    url: "postToDb.php",
                    data: {
                        mid: $mid,
                        paytraceApiUsername: $paytraceApiUsername,
                        paytraceApiPassword:  $paytraceApiPassword
                    },
                }).done(function( msg ) {
                    $( '#postToDb' ).html( msg );
                });
            }
        }

        var oauth = new OAuthCode(url);
        var apiCall = new apiCall();
    </script>
</head>
<body>

<div class="container">

    <h1>
    <img src="skylinelogo.png" id="headerLogo">
    </h1>

    <hr>

    <div class="well text-center">

        
        <h1>Quickbooks Online Connector</h1>
        <br>

    </div>

    <p>If there is no access token or the access token is invalid, click the <b>Connect to QuickBooks</b> button below.</p>
    <pre id="accessToken">
        <style="background-color:#efefef;overflow-x:scroll"><?php
    $displayString = isset($accessTokenJson) ? "QuickBooks Token Generated" : "No Access Token Generated Yet";
    // $displayString = isset($accessTokenJson) ? print_r($_SESSION['sessionAccessToken']) : "No Access Token Generated Yet";
    echo json_encode($displayString, JSON_PRETTY_PRINT); ?>
    </pre>
    <a class="imgLink" href="#" onclick="oauth.loginPopup()"><img src="C2QB_green_btn_lg_default.png" width="178" /></a>
    <hr />


    <h2>Make an API call</h2>
    <p>If there is no access token or the access token is invalid, click either the <b>Connect to QucikBooks</b> button above.</p>
    <pre id="apiCall"></pre>
    <button  type="button" class="btn btn-success" onclick="apiCall.getCompanyInfo()">Get Company Info</button>

    <!-- <h2>Refresh</h2>
    <p>If there is no access token or the access token is invalid, click either the <b>Connect to QucikBooks</b> button above.</p>
    <pre id="refreshToken"></pre>
    <button  type="button" class="btn btn-success" onclick="apiCall.refreshToken()">refresh</button> -->

    <h2>Post to Skyline</h2>
    <p>If QBO API Call successful, post to Skyline Manager</p>
    <pre id="postToDb"></pre>
    <div class="btn">Skyline MID: </div>
    <input id="mid" type="text"  placeholder = "Insert MID"></input>
    </br>
    <div class="btn">Paytrace API Username: </div>
    <input id="paytraceApiUsername" type="text"  placeholder = "Insert Paytrace API Username"></input>
    </br>
    <div class="btn">Paytrace API Password:</div>
    <input id="paytraceApiPassword" type="text"  placeholder = "Insert Paytrace API Password"></input>
    </br>
    <button  type="button" class="btn btn-success" onclick="apiCall.postToDb()">Post to Skyline</button>


    <hr />

</div>
</body>
</html>
