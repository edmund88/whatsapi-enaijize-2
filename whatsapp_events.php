<?php

	function onGetMessage( $mynumber, $from, $id, $type, $time, $name, $body ) {
		$from = chop($from,'@s.whatsapp.net');
		$body = strtoupper($body);
		
		$dbservername = "us-cdbr-iron-east-01.cleardb.net";
		$dbusername = "bcdca4bcfe9366";
		$dbpassword = "bfc05937";
		$dbname = "heroku_555506e4f7e7997";

		$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
	
		$sql = "SELECT * FROM messages WHERE sender = '" . $from . "'";
		$result = mysqli_query($conn, $sql);
		$lastmessage = mysqli_fetch_array($result, MYSQLI_ASSOC);

		if(!$lastmessage) {
			$sql = "INSERT INTO messages (sender, time_sent, message, new) VALUES ('" . $from . "', " . $time . ", '" . $body . "', 1)";
			if ($conn->query($sql) === TRUE) {
				echo "New record created successfully<br/>";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
		else {
			$sql = "UPDATE messages SET time_sent='" . $time . "', message='" . $body . "', prev_message='" . $lastmessage['message'] . "', new=1 WHERE sender='" . $from . "'";		
			if ($conn->query($sql) === TRUE) {
				echo "Record updated successfully<br/>";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}			
		}
		
		$conn->close();
	}
?>