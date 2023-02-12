<?php
	$inData = getRequestInfo();

	$firstname = $inData["firstname"];
	$lastname = $inData["lastname"];
	$login = $inData["login"];
	$password = $inData["password"];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		try {
			$stmt = $conn->prepare("INSERT into Users (FirstName,LastName,Login,Password) VALUES(?,?,?,?)");
			$stmt->bind_param("ssss", $firstname, $lastname, $login, $password);
			$stmt->execute();

			$stmt->close();
			$conn->close();

			http_response_code(200);
			returnWithError("User added!");		
		} catch (PDOException $e) {
			if ($e->getCode() == 23000) {
				http_response_code(409);
				returnWithError("Username Taken");
			}
		}
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