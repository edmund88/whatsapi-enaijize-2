<html>
<head>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" />
</head>
<body>
<div class="panel panel-default">
  <div class="panel-heading">Console</div>
  <div class="panel-body jumbotron">
  <?php	
		require_once('src/whatsprot.class.php');
		require_once('whatsapp_config.php');
		require_once('whatsapp_events.php');

		$w = new WhatsProt($userPhone, $userIdentity, $userName, $debug);
		
		$w->eventManager()->bind("onGetMessage", "onGetMessage");
		
		echo "<b>Connecting...</b>";
		$w->Connect();
		echo "<b>Logging in...</b><br/>";
		$w->LoginWithPassword($password);
 
		echo "<b>Connecting to database...</b>";
		$dbservername = "us-cdbr-iron-east-01.cleardb.net";
		$dbusername = "b10de5ada49e20";
		$dbpassword = "ea88f234";
		$dbname = "heroku_3d91432389d0eb8";
		$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		while(true) {
			echo "<b>Polling...</b><br/>";
			$w->pollMessage();
			
			echo "<b>Processing...</b><br/>";
			$sql = "SELECT * FROM messages WHERE new = 1";
			$result = mysqli_query($conn, $sql);

			while($message = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				switch($message["message"]) {
					case "YES":
						if($message["prev_message"] == "YES" || !$message["prev_message"])
							$reply = "Nothing to confirm. Please enter a valid command.";
						else	
							$reply = "Previous message '" . $message["prev_message"] . "' has been confirmed.";
						break;
					default:
						$reply = "You entered '" . $message["message"] . "'. Please reply with 'YES' to confirm.";
				}
					
				$sql = "UPDATE messages SET new=0 WHERE sender='" . $message["sender"] . "'";		
				if ($conn->query($sql) === TRUE) {
					echo "Record updated successfully";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
					
				echo "<b>Sending...</b><br/>";
				$w->sendMessage($message["sender"], $reply);

			}
		}

	?>
  </div>
</div>
</body>
</html>