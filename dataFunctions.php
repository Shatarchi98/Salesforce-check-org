<?php
/*

 * This file contains all methods of getting data from Salesforce.
 * Written by Shatarchi Goyal
 * Last Updated on 02-Mar-2020 

*/
	// method to get logged in user's details
	function getUserData(){	
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

        return $rtnString;
    }

    // method to count all sObjects 
    function getAllSobjects(){
		$rtnString = '<hr><h2>Count of all Custom Objects and Custom Settings</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");
	
		$query_url = $instance_url.'/services/data/v45.0/tooling/query';
		$query_url .= '?q='.urlencode('select count() from CustomObject');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");

		$final_data_json = json_decode($query_request_body);
		$rtnString .= $final_data_json->size;
		return $rtnString; 
    }

    $GLOBALS['countOfValues'] = array();
    $GLOBALS['dataSet'] = array("ApexClass"=>"Apex Classes", "ApexTrigger"=>"Triggers", "ApexPage"=>"Visualforce Pages", "ApexComponent"=>"Visualforce Component", "AuraDefinitionBundle"=>"Lightning Components", "StaticResource"=>"Static Resources", "Community"=>"Community", "EmailTemplate"=>"Email Template", "Report"=>"Report", "Dashboard"=>"Dashboard","Profile"=>"Profile", "PermissionSet"=>"Permission Set");
    // method to get all data
    function getAllSObjectDetails(){
    	$rtnString = '';
		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");

	    foreach($GLOBALS['dataSet'] as $key=>$value){
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

			// array_push($countValues[], $value);

			// $countOfValues[$value] = $total_size;
			array_push($GLOBALS['countOfValues'], $value.' = '.$total_size);

			$rtnString .= $total_size . "<br><br>";


			$records = $query_request_data['records'];
			$query_url = '';

	    }

		return $rtnString;
 
    }

    // method to get storage limits [Currently:- Data Storage (total/utilized) & File Storage (total/utilized)]
    function getAllStorageDetails(){
		
		$rtnString = '<hr><h2>Storage Details</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");

		$query_url = $instance_url.'/services/data/v45.0/limits';
		// $query_url .= '?q='.urlencode('select id, name from Contact');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");
		$final_data_json = json_decode($query_request_body);

		$rtnString .= "<h3>Data Storage</h3>";
		$rtnString .= "<h5>Max</h5>";		
		$rtnString .= (int) $final_data_json->DataStorageMB->Max;

		$rtnString .= "<h5>Remaining</h5>";		
		$rtnString .= (int) $final_data_json->DataStorageMB->Remaining;

		$rtnString .= "<br>";
		$rtnString .= "<h3>File Storage</h3>";
		
		$rtnString .= "<h5>Max</h5>";		
		$rtnString .= (int) $final_data_json->FileStorageMB->Max;

		$rtnString .= "<h5>Remaining</h5>";		
		$rtnString .= (int) $final_data_json->FileStorageMB->Remaining;

		return $rtnString; 
    }

    //method to get fields per object
    function getAllFieldsFromObject(){
    	$rtnString = '<hr><h2>Fields per Object</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");

		$query_url = $instance_url.'/services/data/v48.0/tooling/sObjects/';
		// $query_url .= '?q='.urlencode('select id, name from Contact');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");
		$final_data_json = json_decode($query_request_body);

		return $rtnString;	
    }

    // getting validation rules per object
    function getValidationRulesPerObject(){
	    $rtnString = '<hr><h2>validation Rules per Object</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");

		$query_url = $instance_url.'/services/data/v45.0/tooling/query';
		$query_url .= '?q='.urlencode('select id, EntityDefinition.DeveloperName, description from validationRule');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");
		$final_data_json = json_decode($query_request_body);

		// var_dump($final_data_json->records);
		$objectsArray = array();
		foreach ($final_data_json->records as $record) {
			// print_r($record->EntityDefinition->DeveloperName);
			array_push($objectsArray, $record->EntityDefinition->DeveloperName);
		}

		$vals = array_count_values($objectsArray);

		
		
		$rtnString .= '<table class="table table-striped">
				    <thead>
				      <tr>
				        <th>Object Name</th>
				        <th>Number of Validation Rules</th>
				      </tr>
				    </thead>
		
				    <tbody>';
		foreach ($vals as $key => $value) {
			$rtnString .= '<tr> <td>'.$key.'</td> <td>'.$value.'</td> </tr>';
    	}    
		$rtnString .= '</tbody></table>';
	  
		return $rtnString; 	
    }

    // getting triggers per object
    function getTriggersPerObject(){
	    $rtnString = '<hr><h2>Triggers per Object</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");

		$query_url = $instance_url.'/services/data/v45.0/tooling/query';
		$query_url .= '?q='.urlencode('select id, Name, EntityDefinition.DeveloperName from ApexTrigger');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");
		$final_data_json = json_decode($query_request_body);

		$objectNameTriggerNameArray = array();
		$objectsArray = array();
		foreach ($final_data_json->records as $record) {
			array_push($objectsArray, $record->EntityDefinition->DeveloperName);

		}
		

		$vals = array_count_values($objectsArray);

		
		
		$rtnString .= '<table class="table table-striped triggersTable">
				    <thead>
				      <tr>
				        <th>Object Name</th>
				        <th>Number of Triggers</th>
				        <th>Name of Trigger(s)</th>
				      </tr>
				    </thead>
		
				    <tbody>';
		foreach ($vals as $key => $value) {	
			if($value > 1){
				$rtnString .= "<tr class='bg-warning'> <td>".$key."</td> <td data-toggle='tooltip' data-placement='top' title='More than one Triggers'>".$value."</td> <td>".$value->Name."</td> </tr>";
			}
			else{
				$rtnString .= "<tr> <td>".$key."</td> <td>".$value."</td> <td>".$value->Name."</td> </tr>";
			}
    	}    
		$rtnString .= '</tbody></table>';
	  
		return $rtnString; 	
    }

    // getting record types per object (return only objects with more than 1)
    function getRecordTypesPerObject(){
	    $rtnString = '<hr><h2>Record Types per Object</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");

		$query_url = $instance_url.'/services/data/v45.0/tooling/query';
		$query_url .= '?q='.urlencode('select id, EntityDefinition.DeveloperName from RecordType');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");
		$final_data_json = json_decode($query_request_body);

		// var_dump($final_data_json->records);
		$objectsArray = array();
		foreach ($final_data_json->records as $record) {
			// print_r($record->EntityDefinition->DeveloperName);
			array_push($objectsArray, $record->EntityDefinition->DeveloperName);
		}

		$vals = array_count_values($objectsArray);

		
		
		$rtnString .= '<table class="table table-striped">
				    <thead>
				      <tr>
				        <th>Object Name</th>
				        <th>Number of Record Types</th>
				      </tr>
				    </thead>
		
				    <tbody>';
		foreach ($vals as $key => $value) {
			$rtnString .= '<tr> <td>'.$key.'</td> <td>'.$value.'</td> </tr>';
    	}    
		$rtnString .= '</tbody></table>';
	  
		return $rtnString; 	
    }

    // getting Record Types per objects
    function getPageLayoutPerObject(){
	    $rtnString = '<hr><h2>Page Layout per Object</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");

		$query_url = $instance_url.'/services/data/v45.0/tooling/query';
		$query_url .= '?q='.urlencode('select id, DeveloperName from CompactLayout');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");
		$final_data_json = json_decode($query_request_body);

		// var_dump($final_data_json);
		$objectsArray = array();
		foreach ($final_data_json->records as $record) {
			// print_r($record->EntityDefinition->DeveloperName);
			array_push($objectsArray, $record->EntityDefinition->DeveloperName);
		}

		$vals = array_count_values($objectsArray);

		
		
		$rtnString .= '<table class="table table-striped">
				    <thead>
				      <tr>
				        <th>Object Name</th>
				        <th>Number of Page Layouts</th>
				      </tr>
				    </thead>
		
				    <tbody>';
		foreach ($vals as $key => $value) {
			$rtnString .= '<tr> <td>'.$key.'</td> <td>'.$value.'</td> </tr>';
    	}    
		$rtnString .= '</tbody></table>';
	  
		return $rtnString; 	
    }

    // checking if trigger has logic
    function checkIfTriggerHasLogic(){
	    $rtnString = '<hr><h2>Triggers With Logic</h2>';

		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		error_log("access_token: '$access_token'");

		$query_url = $instance_url.'/services/data/v45.0/tooling/query';
		$query_url .= '?q='.urlencode('select id, Name, EntityDefinition.DeveloperName, body from ApexTrigger');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		$query_request_body = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");
		$final_data_json = json_decode($query_request_body);


		$rtnString .= '<table class="table table-striped triggersTable">
		    <thead>
		      <tr>
		        <th>Object Name</th>
		        <th>Trigger Name</th>
		        <th>Trigger has Logic</th>
		      </tr>
		    </thead>

		    <tbody>';
		foreach ($final_data_json->records as $record) {		
			$triggerBody = (String)$record->Body;
			if (strpos($triggerBody, 'if(') == true) {
				$rtnString .= "<tr> <td>".$record->EntityDefinition->DeveloperName."</td> <td>".$record->Name."</td><td>Yes</td> </tr>";		
			}else{
				$rtnString .= "<tr> <td>".$record->EntityDefinition->DeveloperName."</td> <td>".$record->Name."</td><td>No</td> </tr>";		
				}
    	}    
		$rtnString .= '</tbody></table>';

		return $rtnString; 	
    }

?>