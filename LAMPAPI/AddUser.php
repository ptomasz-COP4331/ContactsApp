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
			//PDO query execution goes here.
			$stmt = $conn->prepare("INSERT into Users (FirstName,LastName,Login,Password) VALUES(?,?,?,?)");
			$stmt->bind_param("ssss", $firstname, $lastname, $login, $password);
			$stmt->execute();

			if( mysql_errno() == 1062) {
				http_response_code(409);
				returnWithError("Username Taken");
			}
			$stmt->close();
			$conn->close();
			returnWithError("Added User!");
		}
		catch (\PDOException $e) {
				if ($e->errorInfo[1] == 1062) {
					http_response_code(409);
					returnWithError("Username Taken");
						//The INSERT query failed due to a key constraint violation.
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