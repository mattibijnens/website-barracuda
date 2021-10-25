<?php
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

header('Content-Type: application/json');

// Include database config
require('db.php');

// Connect to database
$conn = new mysqli($servername, $username, $password, $database);

// Handle connection issues
if ($conn->connect_error) {
    die(json_encode("Connection failed: " . $conn->connect_error));
}

switch($_SERVER["REQUEST_METHOD"]) {
    case "POST":
		// Insert substitution
		$sql = "INSERT INTO substitutions (date, grade, lesson, subject, room, info) VALUES (?, ?, ?, ?, ?, ?)";
		$statement = $conn->prepare($sql);
		$date = date('Y-m-d', strtotime($_POST['date']));
		$statement->bind_param("ssssss", $date, $_POST['grade'], $_POST['lesson'], $_POST['subject'], $_POST['room'], $_POST['info']);
		$success = $statement->execute();
		if($success){
			// Get the id from the insert to display it in the table
			$_POST["id"] = $statement->insert_id;
			$data = $_POST;
		} else {
			$data = "error";
		}
    break;
    case "PUT":
		// Update substitution
		parse_str(file_get_contents("php://input"), $_PUT);
		$sql = "UPDATE substitutions SET date=?, grade=?, lesson=?, subject=?, room=?, info=? WHERE id=?";
		$statement = $conn->prepare($sql);
		$date = date('Y-m-d', strtotime($_PUT['date']));
		$statement->bind_param("ssssssi", $date, $_PUT['grade'], $_PUT['lesson'], $_PUT['subject'], $_PUT['room'], $_PUT['info'], $_PUT['id']);
		$success = $statement->execute();
		if($success){
			$data = $_PUT;
		} else {
			$data = "error";
		}
    break;
    case "DELETE":
		// Delete substitution
		parse_str(file_get_contents("php://input"), $_DELETE);
		$sql = "DELETE FROM substitutions WHERE id=?";
		$statement = $conn->prepare($sql);
		$statement->bind_param("i", $_DELETE['id']);
		$success = $statement->execute();
		$data = $success;
    break;
    default:
		// Get substitutions on certain date
		$date = date('Y-m-d', strtotime($_GET['date']));
		$sql = "SELECT * FROM substitutions WHERE date = ?;";
		$statement = $conn->prepare($sql);
		$statement->bind_param("s", $date);
		$success = $statement->execute();
		$result = $statement->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		foreach($data as &$substitution){
			$substitution["date"] = date("d.m.Y", strtotime($substitution["date"]));
		}
    break;
	
}

// Close statement and connection
$statement->close();
$conn->close();

echo json_encode($data);
?>
