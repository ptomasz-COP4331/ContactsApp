<?php
	$inData = getRequestInfo();
	
	$name = $inData["name"];
	$email = $inData["email"];
	$phone = $inData["phone"];
	$cid = $inData["cid"];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("UPDATE Contacts SET Name = ?, Email = ?, Phone = ? WHERE ID = ?");
		$stmt->bind_param("ssss", $name, $email, $phone, $cid);
		$stmt->execute();

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