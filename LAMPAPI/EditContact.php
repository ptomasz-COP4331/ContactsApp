<?php
	$inData = getRequestInfo();
	
	$name = $inData["name"];
	$email = $inData["email"];
	$phone = $inData["phone"];
	//$date = $inData["date"];
	$userid = $inData["userid"];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("UPDATE Contacts SET 'Name'=$name 'Phone'=$phone 'Email'=$email WHERE UserID=$userid");
		$stmt->execute();

		$result = $stmt->get_result();

		$row = $result->fetch_assoc();
		returnWithInfo( $row['Name'], $row['Email'], $row['Phone'], $row['DateCreated'] );

		$stmt->close();
		$conn->close();
		returnWithError("");
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
	
	function returnWithInfo( $Name, $Email, $Phone, $Date )
	{
		$retValue = '{"name": '. $Name .', "email": '. $Email.', "phone": '. $Phone .', "date": ' . $Date .' }';
		sendResultInfoAsJson( $retValue );
	}

	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>