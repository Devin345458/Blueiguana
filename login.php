<?
session_start();
require('mysql.php');
if($_POST['username'] && $_POST['password']) {
  $username = mysql_real_escape_string($_POST['username']);
  $password = md5($_POST['password']);
  $q = mysql_query("SELECT * FROM user WHERE username='{$username}' AND password='{$password}'") or die(mysql_error());
  $exists = mysql_num_rows($q);
  $invalid_error = "";
  if($exists > 0) {
    while ($r = mysql_fetch_assoc($q))
    {
      //set in session username
      //set in session location id
      $_SESSION['location'] = $r['location'];
      $_SESSION['username'] = $r['username'];
      $_SESSION['ulevel'] = $r['ulevel'];
      if($r['location'] == -1)  {
        //redirect to admin
        header('Location: admin.php');
        die();
      }
      else {
        //redirect to eveningpull
        $_SESSION['location_name'] = "";
        $qq = mysql_query("SELECT * FROM location WHERE id={$r['location']}") or die(mysql_error());
        while ($rr = mysql_fetch_assoc($qq)) {
          $_SESSION['location_name'] = $rr['name'];
        }
        header('Location: evening_pull.php');
        die();
      }
    }
  }
  else {
    $invalid_error = "Invalid username or password.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Blue Iguana Car Wash</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <style>
    body {
      padding-top: 40px;
      padding-bottom: 40px;
      background-color: #eee;
    }

    .form-signin {
      max-width: 330px;
      padding: 15px;
      margin: 0 auto;
    }
    .form-signin .form-signin-heading,
    .form-signin .checkbox {
      margin-bottom: 10px;
    }
    .form-signin .checkbox {
      font-weight: normal;
    }
    .form-signin .form-control {
      position: relative;
      height: auto;
      -webkit-box-sizing: border-box;
         -moz-box-sizing: border-box;
              box-sizing: border-box;
      padding: 10px;
      font-size: 16px;
    }
    .form-signin .form-control:focus {
      z-index: 2;
    }
    #username {
      margin-bottom: -1px;
      border-bottom-right-radius: 0;
      border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
      margin-bottom: 10px;
      border-top-left-radius: 0;
      border-top-right-radius: 0;
    }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/jquery.min.js"><\/script>')</script>
    <script src="js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <script>
      if ("<?=$invalid_error?>") {
        alert("<?=$invalid_error?>");
      }
    </script>
  </head>

  <body>

    <div class="container">

      <form class="form-signin" action='?' method='post'>
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="username" class="sr-only">Username</label>
        <input type="text" id="username" name='username' class="form-control" placeholder="Username" required autofocus>
        <label for="password" class="sr-only">Password</label>
        <input type="password" id="password" name='password' class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div> <!-- /container -->


  </body>
</html>
