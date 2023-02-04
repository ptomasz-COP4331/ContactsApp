<?php
	$inData = getRequestInfo();

	// Unsure of needing userID and date?
	// $userID = $inData["userID"];
	$name = $inData["name"];
	$email = $inData["email"];
	$phone = $inData["phone"];
	// $date = $inData["date"];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// Not sure what the line that follow does, if it is unseen by user it should include a UserID and DateCreated I think. (Users should not be in charge of ID making)
		$stmt = $conn->prepare("INSERT into Contacts (Name, Email, Phone) VALUES(?,?,?)");
		$stmt->bind_param("sss", $name, $email, $phone);			// need to figure out what bind param means
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
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
