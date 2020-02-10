<?php


	function getUserData()
    {	
        $state = $_SESSION['state'];
 
        // Set our GET request URL
        $getURL = $state->instanceURL . '/services/data/v20.0/sobjects/User/' . $state->userId . '?fields=Name';
 
        // Header options
        $headerOpts = array('Authorization: Bearer ' . $state->token);
 
        // Open connection
        $ch = curl_init();
 
        // Set the url and header options
        curl_setopt($ch, CURLOPT_URL, $getURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerOpts);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Execute GET
        $result = curl_exec($ch);
 
        // Close connection
        curl_close($ch);
 
        // Get the results
        $typeString = gettype($result);
        $resultArray = json_decode($result, true);
 
        // Return them as an html String
        $rtnString = '<hr><h2>Logged In user</h2>';
 
        if(empty($resultArray)){
        	$rtnString .= "<h4>Error getting data. Please try again!</h4>";
        	// return;
        }
        else{        	
	        foreach($resultArray as $key=>$value) 
	        {	if($key == "Name"){
	            	$rtnString .= "<h4>$value</h4>";
	        	} 
	        }
        }

        //method to call sObjects
        // getAllSobjects($state->token, $state->instanceURL);

        return $rtnString;
    }

    // method to count all sObjects 
    function getAllSobjects(){
		$rtnString = '<hr><h2>Count of all sObjects</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");
	
		$query_url = $instance_url.'/services/data/v32.0/sobjects/';
		// $query_url .= '?q='.urlencode('select id, name from Contact');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");

		$records = explode(' ', $query_request_body);

		$rtnString .= sizeof($records).'<br><br>';
		return $rtnString; 
    }

    // method to count all ApexClasses
    function getAllSObjectDetails(){
    	$rtnString = '';
		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");
    	$dataSet = array("ApexClass"=>"Apex Classes", "ApexTrigger"=>"Triggers", "ApexPage"=>"Visualforce Pages", "ApexComponent"=>"Visualforce Component", "AuraDefinitionBundle"=>"Lightning Components", "StaticResource"=>"Static Resources", "Community"=>"Community", "EmailTemplate"=>"Email Template", "Report"=>"Report", "Dashboard"=>"Dashboard","Profile"=>"Profile", "PermissionSet"=>"Permission Set");

	    foreach($dataSet as $key=>$value){
			$query_url = $instance_url.'/services/data/v32.0/query';
			$rtnString .= '<hr><h2>Count of all ';
	    	$rtnString .= $value . " </h2>  ";
			$query_url .= '?q='.urlencode('select count() from ' . $key);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $query_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
			$query_request_body = curl_exec($ch)
			    or die("Query API call failed: '$query_url'");

			$query_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (($query_response_code<200)||($query_response_code>=300)||empty($query_request_body))
			{
			    unset($_SESSION['access_token']);
			    unset($_SESSION['instance_url']);
			    die("Query API call failed with $query_response_code: '$query_url' - '$query_request_body'");
			}
			$query_request_data = json_decode($query_request_body, true);
			if (empty($query_request_data))
			    die("Couldn't decode '$query_request_data' as a JSON object");
			if (!isset($query_request_data['totalSize'])||
			    !isset($query_request_data['records']))
			    die("Missing expected data from ".print_r($query_request_data, true));
			// Grab the information we're interested in
			$total_size = $query_request_data['totalSize'];
			$rtnString .= $total_size . "<br><br>";

			$records = $query_request_data['records'];
			$query_url = '';

	    }

		return $rtnString;
 
    }

    function getAllStorageDetails(){
		
		$rtnString = '<hr><h2>Storage Details</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");

		$query_url = $instance_url.'/services/data/v32.0/limit/';
		// $query_url .= '?q='.urlencode('select id, name from Contact');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");

		$records = explode(' ', $query_request_body);
		print_r($records);
		$rtnString .= sizeof($records).'<br><br>';
		return $rtnString; 
    }

?>