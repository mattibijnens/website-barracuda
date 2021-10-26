<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

//Connect to database
require('tools/db.php');
$conn = new mysqli($servername, $username, $password, $database);

// Handle connection issues
if ($conn->connect_error) {
    die(json_encode("Connection failed: " . $conn->connect_error));
}

// Get newest date as default date
$sql = "SELECT date FROM substitutions ORDER BY date DESC LIMIT 1";
$result = $conn->query($sql);
$date = $result->fetch_row();
$date = date("d.m.Y", strtotime($date[0]));
?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script src="https://kit.fontawesome.com/4c47024a0d.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js"></script>
<script src="js/editable.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css" />
<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/black-tie/jquery-ui.min.css" />
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="admin.php">VPlan X Dashboard</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="admin.php">Home <span class="sr-only">(current)</span></a>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
    </ul>
  </div>
</nav>



<form class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
	<div class="form-group mx-sm-3 mb-2">
		<label for="filterdate">Date</label>
	</div>
	<input id="filterdate" type="text" onchange="updateTable()" class="form-control" value="<?php echo $date;?>" data-provide="datepicker-inline" data-date-format="dd.mm.yyyy" data-date-today-highlight="true" data-date-language="de">
</form>
<div id="jsGrid"></div>

<div id="detailsDialog">
    <form id="detailsForm">
        <div class="details-form-field">
            <label for="date">Datum:</label>
            <input id="date" name="date" type="text" data-provide="datepicker-inline" data-date-format="dd.mm.yyyy" data-date-today-highlight="true" data-date-language="de" />
        </div>
        <div class="details-form-field">
            <label for="grade">Klasse:</label>
            <input id="grade" name="grade" type="text" />
        </div>
        <div class="details-form-field">
            <label for="lesson">Stunde:</label>
            <input id="lesson" name="lesson" type="text" />
        </div>
        <div class="details-form-field">
            <label for="subject">Fach:</label>
            <input id="subject" name="subject" type="text" />
        </div>
        <div class="details-form-field">
            <label for="room">Raum:</label>
            <input id="room" name="room" type="text" />
        </div>
        <div class="details-form-field">
            <label for="info">Info:</label>
            <input id="info" name="info" type="text" />
        </div>
        <div class="details-form-field">
            <button type="submit" id="save">Speichern</button>
        </div>
		<input type="hidden" autofocus="true" />
    </form>
</div>
</body>
</html>
