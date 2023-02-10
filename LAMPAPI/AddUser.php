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
		// Check to make sure username isn't taken first!
		$stmt = $conn->prepare("SELECT * FROM Users WHERE Login=?");
		$stmt->bind_param("s", $login);
		$stmt->execute();
		$res = $stmt->get_result();
		$rows = mysqli_num_rows($res);

		// If username taken, then return 409 conflict
		if ($rows > 0) {
			http_response_code(409);
			return_with_error("409 State Conflict: Username Taken")
		}

		$stmt = $conn->prepare("INSERT into Users (FirstName,LastName,Login,Password) VALUES(?,?,?,?)");
		$stmt->bind_param("ssss", $firstname, $lastname, $login, $password);
		$new_user_id = $conn->insert_id;
		$stmt->execute();
		$stmt->close();
		$conn->close();

		http_response_code(200);
		returnWithInfo($firstname, $lastname, $new_user_id);
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

	function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>