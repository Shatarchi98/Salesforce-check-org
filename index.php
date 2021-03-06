<?php
/*

 * This is the index file of the Project. This file calls all actions and handles input from user
 * Written by Shatarchi Goyal
 * Last Updated on 02-Mar-2020 

*/

?>
<style>
	.container{
		height: auto;	
	}
	.container form{
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.container form input[type=submit]{
		margin: 10px;
	}
	.mainHeading{
		text-align: center;
	}
	.loader {
		position: absolute;
		top: 50% !important;
		left: 50% !important;
		display: none;
	}
	.circles {
	  position: absolute;
	  left: -5px;
	  top: 0;
	  height: 60px;
	  width: 180px;
	}
	.circles span {
	  position: absolute;
	  top: 25px;
	  height: 12px;
	  width: 12px;
	  border-radius: 12px;
	  background-color: #262626;
	}
	.circles span.one {
	  right: 80px;
	}
	.circles span.two {
	  right: 40px;
	}
	.circles span.three {
	  right: 0px;
	}
	.circles {
	  -webkit-animation: animcircles 0.5s infinite linear;
	  animation: animcircles 0.5s infinite linear;
	}
	@-webkit-keyframes animcircles {
	  0% {
	    -webkit-transform: translate(0px, 0px);
	    transform: translate(0px, 0px);
	  }
	  100% {
	    -webkit-transform: translate(-40px, 0px);
	    transform: translate(-40px, 0px);
	  }
	}
	@keyframes animcircles {
	  0% {
	    -webkit-transform: translate(0px, 0px);
	    transform: translate(0px, 0px);
	  }
	  100% {
	    -webkit-transform: translate(-40px, 0px);
	    transform: translate(-40px, 0px);
	  }
	}
	.pacman {
	  position: absolute;
	  left: 0;
	  top: 0;
	  height: 60px;
	  width: 60px;
	}
	.pacman span {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 60px;
	  width: 60px;
	}
	.pacman span::before {
	  content: "";
	  position: absolute;
	  left: 0;
	  height: 30px;
	  width: 60px;
	  background-color: #0d98ba;
	}
	.pacman .top::before {
	  top: 0;
	  border-radius: 60px 60px 0px 0px;
	}
	.pacman .bottom::before {
	  bottom: 0;
	  border-radius: 0px 0px 60px 60px;
	}
	.pacman .left::before {
	  bottom: 0;
	  height: 60px;
	  width: 30px;
	  border-radius: 60px 0px 0px 60px;
	}
	.pacman .top {
	  -webkit-animation: animtop 0.5s infinite;
	  animation: animtop 0.5s infinite;
	}
	@-webkit-keyframes animtop {
	  0%,
	  100% {
	    -webkit-transform: rotate(0deg);
	    transform: rotate(0deg);
	  }
	  50% {
	    -webkit-transform: rotate(-45deg);
	    transform: rotate(-45deg);
	  }
	}
	@keyframes animtop {
	  0%,
	  100% {
	    -webkit-transform: rotate(0deg);
	    transform: rotate(0deg);
	  }
	  50% {
	    -webkit-transform: rotate(-45deg);
	    transform: rotate(-45deg);
	  }
	}
	.pacman .bottom {
	  -webkit-animation: animbottom 0.5s infinite;
	  animation: animbottom 0.5s infinite;
	}
	@-webkit-keyframes animbottom {
	  0%,
	  100% {
	    -webkit-transform: rotate(0deg);
	    transform: rotate(0deg);
	  }
	  50% {
	    -webkit-transform: rotate(45deg);
	    transform: rotate(45deg);
	  }
	}
	@keyframes animbottom {
	  0%,
	  100% {
	    -webkit-transform: rotate(0deg);
	    transform: rotate(0deg);
	  }
	  50% {
	    -webkit-transform: rotate(45deg);
	    transform: rotate(45deg);
	  }
	}
</style> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<body>
	<div class="fixed-top loader">
	  <div class="circles">
	    <span class="one"></span>
	    <span class="two"></span>
	    <span class="three"></span>
	  </div>
	  <div class="pacman">
	    <span class="top"></span>
	    <span class="bottom"></span>
	    <span class="left"></span>
	  </div>
	</div>	
<div class="container">
	
<?php
	include 'dataFunctions.php';


    // Report all errors (ignore Notices)
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
    session_start();
 
    // Define our State class
    class State 
    {	
        public $passthroughState1;  // Arbitary state we want to pass to the Authentication request
        public $passthroughState2;  // Arbitary state we want to pass to the Authentication request
 
        public $code;               // Authentication code received from Salesforce
        public $token;              // Session token
        public $refreshToken;       // Refresh token
        public $instanceURL;        // Salesforce Instance URL
        public $userId;             // Current User Id
         
        public $codeVerifier;       // 128 bytes of random data used to secure the request
 
        public $error;              // Error code
        public $errorDescription;   // Error description
 
        /**
         * Constructor - Takes 2 pieces of optional state we want to preserve through the request
         */
        function __construct($state1 = "", $state2 = "")
        {
            // Initialise arbitary state
            $this->passthroughState1 = $state1;
            $this->passthroughState2 = $state2;
 
            // Initialise remaining state
            $this->code = "";
            $this->token = "";
            $this->refreshToken = "";
            $this->instanceURL = "";
            $this->userId = "";
             
            $this->error = "";
            $this->errorDescription = "";
 
            // Generate 128 bytes of random data
            $this->codeVerifier = bin2hex(openssl_random_pseudo_bytes(128));
        }
 
        /**
         * Helper function to populate state following a call back from Salesforce
         */
        function loadStateFromRequest()
        {
            $stateString = "";
 
            // If we've arrived via a GET request, we can assume it's a callback from Salesforce OAUTH
            // so attempt to load the state from the parameters in the request
            if ($_SERVER["REQUEST_METHOD"] == "GET") 
            {
                $this->code = $this->sanitizeInput($_GET["code"]);
                $this->error = $this->sanitizeInput($_GET["error"]);
                $this->errorDescription = $this->sanitizeInput($_GET["error_description"]);
                $stateString = $this->sanitizeInput($_GET["state"]);
 
                // If we have a state string, then deserialize this into state as it's been passed
                // to the salesforce request and back
                if ($stateString)
                {
                    $this->deserializeStateString($stateString);
                }
            }
        }
 
        /**
         * Helper function to sanitize any input and prevent injection attacks
         */
        function sanitizeInput($data) 
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
 
        /**
         * Helper function to serialize our arbitary state we want to send accross the request
         */
        function serializeStateString()
        {
            $stateArray = array("passthroughState1" => $this->passthroughState1, 
                                "passthroughState2" => $this->passthroughState2
                                );
 
            return rawurlencode(base64_encode(serialize($stateArray)));
        }
 
        /**
         * Helper function to deserialize our arbitary state passed back in the callback
         */
        function deserializeStateString($stateString)
        {
            $stateArray = unserialize(base64_decode(rawurldecode($stateString)));
 
            $this->passthroughState1 = $stateArray["passthroughState1"];
            $this->passthroughState2 = $stateArray["passthroughState2"];
        }
 
        /**
         * Helper function to generate the code challenge for the code verifier
         */
        function generateCodeChallenge()
        {
            $hash = pack('H*', hash("SHA256", $this->generateCodeVerifier()));
 
            return $this->base64url_encode($hash);
        }
 
        /**
         * Helper function to generate the code verifier
         */
        function generateCodeVerifier()
        {
            return $this->base64url_encode(pack('H*', $this->codeVerifier));
        }
 
        /**
         * Helper function to Base64URL encode as per https://tools.ietf.org/html/rfc4648#section-5
         */
        function base64url_encode($string)
        {
            return strtr(rtrim(base64_encode($string), '='), '+/', '-_');
        }
 
        /**
         * Helper function to display the current state values
         */
        function debugState($message = NULL)
        {
            if ($message != NULL)
            {
                echo "<pre>$message</pre>";
            }
         }
    }
 
    // If we have not yet initialised state, are resetting or are Authenticating then Initialise State
    // and store in a session variable.
    if ($_SESSION['state'] == NULL || $_POST["reset"] || $_POST["authenticate"])
    {
        $_SESSION['state'] = new State('ippy', 'dippy');
    }
 
    $state = $_SESSION['state'];
 
    // Attempt to load the state from the page request
    $state->loadStateFromRequest();
 
    // if an error is present, render the error
    if ($state->error != NULL)
    {
        renderError();      
    }
 
    // Determine the form action
    if ($_POST["authenticate"]) // Authenticate button clicked
    {
        doOAUTH();  
        loginViaAuthenticationCode();
        if (!loginViaAuthenticationCode())
        {
            renderError();
            return;
        }
 
    }
    else if ($_POST["login_via_code"])  // Login via Authentication Code button clicked
    {
        if (!loginViaAuthenticationCode())
        {
            renderError();
            return;
        }
 
        renderPage();
    }
    else if ($_POST["login_via_refresh_token"]) // Login via Refresh Token button clicked
    {
        if (!loginViaRefreshToken())
        {
            renderError();
            return;
        }
 
        renderPage();
    }
    else if ($_POST["get_user"])    // Get User button clicked
    {
        // Get the user data from Salesforce
        $userDataHTML = getUserData();
 		$userDataHTML .= getAllSobjects();
 		$userDataHTML .= getAllSObjectDetails();
 		$userDataHTML .= getAllStorageDetails();
        $userDataHTML .= getAllFieldsFromObject();
        $userDataHTML .= getValidationRulesPerObject();
        $userDataHTML .= getTriggersPerObject();
        $userDataHTML .= getRecordTypesPerObject();
        $userDataHTML .= getPageLayoutPerObject();
        $userDataHTML .= checkIfTriggerHasLogic();


        // Render the page passing in the user data
        renderPage($userDataHTML);
        
    }
    else    // Otherwise render the page
    {
        renderPage();
    }
 
    // Render the Page
    function renderPage($userDataHTML = NULL)
    {       
        $loginPath = array('login.salesforce.com', 'test.salesforce.com');

        $state = $_SESSION['state'];
 
        echo "<!DOCTYPE html>";
?>
        <html>
            <head>
                <title>Salesforce Org Checkup By Techila</title>
                <meta charset="UTF-8">
            </head>
 
            <body>
                <h1 class="mainHeading">Salesforce Org Checkup By Techila</h1>
<?php
                // Show the current state values
                $state->debugState();
 
                // If we have some user data to display then do so
                if ($userDataHTML)
                {
                    echo $userDataHTML;
                }

?>          
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <select id="type_of_org" name="type_of_org" class="custom-select" style="max-width: 20%; box-shadow: none;">            
                        <option value="production" selected="selected">Production/Developer</option>
                        <option value="sandbox">Sandbox</option>
                    </select>
                    <input type="submit" name="reset" class="btn btn-success" value="Reset App" />
                    <input type="submit" name="authenticate" class="btn btn-primary" value="Authenticate Your Org" />
                    <input type="submit" name="login_via_code" class="btn btn-success" value="Login via Authentication Code" />
                    <!-- <input type="submit" name="login_via_refresh_token" class="btn btn-info" value="Login via Refresh Token" /> -->
                    <input type="submit" name="get_user" class="btn btn-primary startBtn" value="Start Checkup" />
                </form>
 
            </body>
        </html>

<?php
    }
 
    /**
     * Redirect page to Salesforce to authenticate
     */
    function doOAUTH()
    {
        $state = $_SESSION['state'];
 
        // Set the Authentication URL
        // Note we pass in the code challenge

        if($_POST["type_of_org"] == 'production'){
            $href = "https://login.salesforce.com/services/oauth2/authorize?response_type=code";    
        }else if($_POST["type_of_org"] == 'sandbox'){
            $href = "https://test.salesforce.com/services/oauth2/authorize?response_type=code";
        }

        // $href = "https://test.salesforce.com/services/oauth2/authorize?response_type=code" . 
        $href .= "&client_id=" . getClientId() . 
                "&redirect_uri=" . getCallBackURL() . 
                "&scope=api refresh_token" . 
                "&prompt=consent" . 
                "&code_challenge=" . $state->generateCodeChallenge() .
                "&state=" . $state->serializeStateString();
 
        // Wipe out arbitary state values to demonstrate passing additional state to salesforce and back
        $state->passthroughState1 = NULL;
        $state->passthroughState2 = NULL;
 
        // Perform the redirect
        loginViaAuthenticationCode();
        header("location: $href");
    }
 
    /**
     * Login via an Authentication Code
     */
    function loginViaAuthenticationCode()
    {
        $state = $_SESSION['state'];
 
        // Create the Field array to pass to the post request
        // Note we pass in the code verifier and the authentication code
        $fields = array('grant_type' => 'authorization_code', 
                        'client_id' => getClientId(),
                        'client_secret' => getClientSecret(),
                        'redirect_uri' => getCallBackURL(),
                        'code_verifier' => $state->generateCodeVerifier(),
                        'code' => $state->code,
                        );
         
        // perform the login to Salesforce
        return doLogin($fields, false);
    }
 
    /**
     * Login via a Refresh Token
     */
    function loginViaRefreshToken()
    {
        $state = $_SESSION['state'];
 
        // Create the Field array to pass to the post request
        // Note we pass in the refresh token
        $fields = array('grant_type' => 'refresh_token', 
                        'client_id' => getClientId(),
                        'client_secret' => getClientSecret(),
                        'redirect_uri' => getCallBackURL(),
                        'refresh_token' => $state->refreshToken,
                        );
 
        // perform the login to Salesforce
        return doLogin($fields, true);
    }
 
    /**
     * Login to Salesforce to get a Session Token using CURL
     */
    function doLogin($fields, $isViaRefreshToken)
    {
        $state = $_SESSION['state'];

        if($_POST["type_of_org"] == 'production'){
            $postURL = 'https://login.salesforce.com/services/oauth2/token';
        }else if($_POST["type_of_org"] == 'sandbox'){
            $postURL = 'https://test.salesforce.com/services/oauth2/token';
        }
        // Set the POST url to call
        // $postURL = 'https://test.salesforce.com/services/oauth2/token';
 
        // Header options
        $headerOpts = array('Content-type: application/x-www-form-urlencoded');
 
        // Create the params for the POST request from the supplied fields  
        $params = "";
         
        foreach($fields as $key=>$value) 
        { 
            $params .= $key . '=' . $value . '&';
        }
 
        $params = rtrim($params, '&');
 
        // Open the connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data etc
        curl_setopt($ch, CURLOPT_URL, $postURL);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerOpts);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Execute POST
        $result = curl_exec($ch);
 
        // Close the connection
        curl_close($ch);
 
        //record the results into state
        $typeString = gettype($result);
        $resultArray = json_decode($result, true);
 
        $state->error = $resultArray["error"];
        $state->errorDescription = $resultArray["error_description"];
 
        // If there are any errors return false
        if ($state->error != null)
        {
            return false;
        }
 
        $state->instanceURL = $resultArray["instance_url"];
        $state->token = $resultArray["access_token"];
 
        // If we are logging in via an Authentication Code, we want to store the 
        // resulting Refresh Token
        if (!$isViaRefreshToken)
        {
            $state->refreshToken = $resultArray["refresh_token"];
        }
 
        // Extract the user Id
        if ($resultArray["id"] != null)
        {
            $trailingSlashPos = strrpos($resultArray["id"], '/');
 
            $state->userId = substr($resultArray["id"], $trailingSlashPos + 1);
        }
 
        // verify the signature
        $baseString = $resultArray["id"] . $resultArray["issued_at"];
        $signature = base64_encode(hash_hmac('SHA256', $baseString, getClientSecret(), true));
 
        if ($signature != $resultArray["signature"])
        {
            $state->error = 'Invalid Signature';
            $state->errorDescription = 'Failed to verify OAUTH signature.';
 
            return false;
        }
 
        // Debug that we've logged in via the appropriate method
        echo "<pre>Logged into ". $instanceURL . ($isViaRefreshToken ? " via refresh token" : "via authorisation code") . "</pre>";
 
        return true;
    }
 

    /**
     * Helper function to render an Error
     */
    function renderError()
    {
        $state = $_SESSION['state'];
 
        echo '<div class="error"><span class="error_msg">' . $state->error . '</span> <span class="error_desc">' . $state->errorDescription . '</span></div>';
    }
 
    /**
     * Get the hard coded Client Id for the Conected Application
     */
    function getClientId()
    {
        return "3MVG9G9pzCUSkzZuwNy2sUFMBRAu9r5GGQVC_h0M.nFfAcXgnBJ.t1dtRvReZlXrj.xChH6FrmRKJ7JT8yWe9";
    }
 
    /**
     * Get the hard coded Client Secret for the Conected Application
     */
    function getClientSecret()
    {
        return "3F389C366C84CCE862377D3759F388BD48AA1AFCAD3F82B9F0BE47AA30E225DF";
    }
 
    /**
     * Get the Call back URL (the current php script)
     */
    function getCallBackURL()
    {
        $callbackURL = ($_SERVER['HTTPS'] == NULL || $_SERVER['HTTPS'] == false ? "http://" : "https://") .
            $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
 
        return $callbackURL;
    }


?>
</div>
<script>
	$(document).ready(function(){
		$(".startBtn").click(function(){
   			$(".loader").css("display", "block"); 
   			$(".container").css("opacity","0.4");
		});
	});
</script>
</body>