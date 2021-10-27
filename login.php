<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to admin page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: admin.php");
    exit;
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	require('tools/db.php');
	
	$link = mysqli_connect($servername, $username, $password, $database);
 
	$username = $password = $confirm_password = "";
	$username_err = $password_err = $confirm_password_err = "";
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Bitte geben Sie einen Benutzernamen ein.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Bitte geben Sie ein Passwort ein.";
    } else{
        $password = $_POST["password"];
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: admin.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "Ihr Benutzername/Passwort ist nicht korrekt.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "Ihr Benutzername/Passwort ist nicht korrekt.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
	mysqli_close($link);
}
    
?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body class="bg-dark">
<div style="margin-top: 120px;">
<h3 class="text-center text-white pt-5">VPlan X</h3>
<div class="row justify-content-center align-items-center">
<div class="col-md-6">
<?php
if(!empty($username_err)){
	echo "<div class='alert alert-danger' role='alert'>".$username_err."</div>";
}
if(!empty($password_err) && empty($username_err)){
	echo "<div class='alert alert-danger' role='alert'>".$password_err."</div>";
}
?>
<form action="login.php" method="POST">
  <div class="form-group">
    <label for="username" class="text-white">Benutzername</label>
    <input type="text" class="form-control" id="username" name="username">
  </div>
  <div class="form-group">
    <label for="password" class="text-white">Passwort</label>
    <input type="password" class="form-control" id="password" name="password">
  </div>
  <button type="submit" class="btn btn-primary">Login</button>
</form>
</div>
</div>
</div>
</body>
</html>
