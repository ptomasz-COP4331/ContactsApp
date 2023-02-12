<?php

	$inData = getRequestInfo();
	
	$searchResults = "";
	$searchCount = 0;
	

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("select Name, Email, Phone, DateCreated from Contacts where  UserID=?");
		$stmt->bind_param("s",  $inData["userId"]);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if ($searchCount > 0) {
				$searchResults .= ", "
			}
			// $searchResults .= 'firstname: "' . $row["Name"] . '",';
			$searchResults .= '{"name": ' . $row["Name"] . ',';
			$searchResults .= '"email": ' . $row["Email"] . ',';
			$searchResults .= '"phone": ' . $row["Phone"] . '}';
			$searchCount++;

			// returnWithInfo( $row['Name'], $row['Email'], $row['Phone'], $row['DateCreated'] );
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnJsonArray( $searchResults );
		}
		
		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $Name, $Email, $Phone, $Date )
	{
		
		$retValue = '{"name": '. $Name .', "email": '. $Email.', "phone": '. $Phone .', "date": ' . $Date .' }';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnJsonArray ($obj) {
		$retValue = '{"results": [' . $obj . '], "error": "" }';
		sendResultInfoAsJson($retValue);
	}
?>