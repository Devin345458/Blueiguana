<?
session_start();
if (!$_SESSION['location'] || $_SESSION['location'] != -1)
{
    header("Location: login.php");
    exit;
}
require('mysql.php');

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
        padding-top: 50px;
        padding-bottom: 20px;
      }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/jquery.min.js"><\/script>')</script>
    <script src="js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>

  </head>
  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#" style="padding: 7px !important">
            <img src='images/logo_top.png' width="77" height="34" style="display:inline">
            <img src='images/logo_bottom_inverted.png' width="183" height="35" style="display:inline">
          </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <form class="navbar-form navbar-right">
            <a href="?" class="btn btn-default">Admin Home</a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="logout.php" class="btn btn-danger">Logout</a>
          </form>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <div class="container">
<br>
<nav class="navbar navbar-default">
  <div class="container-fluid">

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
<?
$locationsactive = '';
$resetpassactive = "";
if($_GET['action'] == 'reset_admin_password') {
    $resetpassactive = ' class="active"';
}
else {
    $locationsactive = ' class="active"';
}
?>
        <li <?=$locationsactive?>><a href="?">View Locations</a></li>
        <li <?=$resetpassactive?>><a href="admin.php?action=reset_admin_password">Reset Admin Password</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>


<?

if($_GET['action'] == 'reset_admin_password') {
  //Show form to reset admin password
print'
  <div class="panel panel-default">
  <div class="panel-heading"><h4>Reset Admin Password</h4></div>
';
  print "<form method='post' action='?action=reset_admin_password_submit'>";
  print "<table class='table'>";
  print "<tr>";
  print "<th>New Password: </th>";
  print "<td><input class='form-control' type='text' name='password'></td>";
  print "</tr>";
  print "<tr>";
  print "<td>&nbsp;</td>";
  print "<td style='text-align:center;'><input class='form-control btn btn-primary' type='submit' value='Submit'></td>";
  print "</tr>";
  print "</table>";
}
else if($_GET['action'] == 'reset_admin_password_submit') {
    $password = md5($_POST['password']);
    mysql_query("UPDATE user SET password='{$password}' WHERE location=-1") or die(mysql_error());
    print "Admin password updated.<br>&gt; <a href='?'>Back</a>";
}
else if($_GET['action'] == 'edit_location') {
  //show edit location form AND list of users at location

  $id=(int) $_GET['id'];
  $q = mysql_query("SELECT * FROM location WHERE id={$id}") or die(mysql_error());
  while ($r = mysql_fetch_assoc($q))
  {




print'
  <div class="panel panel-default">
  <div class="panel-heading"><h4>Edit Location</h4></div>
';
    print "<form method='post' action='?action=edit_location_submit&id={$r['id']}'>";
    print "<table class='table'>";
    print "<tr>";
    print "<th>ID: </th>";
    print "<td><input class='form-control' type='text' name='id' value='{$r['id']}'></td>";
    print "</tr>";
    print "<tr>";
    print "<th>Name: </th>";
    print "<td><input class='form-control' type='text' name='name' value='{$r['name']}'></td>";
    print "</tr>";
    print "<tr>";
    print "<td>&nbsp;</td>";
    print "<td style='text-align:center;'><input class='form-control btn btn-primary' type='submit' value='Submit'></td>";
    print "</tr>";
    print "</table></div>";
  }

  print "<br><br><br>";
print'
  <div class="panel panel-default">
  <div class="panel-heading"><h4>Users at location [<a href="?action=new_user&location='.$id.'">New</a>]</h4></div>
';

  $q = mysql_query("SELECT * FROM user WHERE location={$id} ORDER BY username ASC") or die(mysql_error());
  print "<table class='table' width='100%' border=1>";
  while ($r = mysql_fetch_assoc($q))
  {
    print "<tr>";
    print "<th>{$r['username']}</th>";
    print "<td>&gt; <a href='?action=edit_user&id=".$r['id']."'>Edit</a></td><td>&gt; <a href='?action=delete_user&location={$id}&id=".$r['id']."'>Delete</a></d>";
    print "</tr>";
  }
  print "</table></div>";

}
else if($_GET['action'] == 'edit_location_submit') {
    $id = (int) $_GET['id'];
    $newid = (int) $_POST['id'];
    $name = mysql_real_escape_string($_POST['name']);
    mysql_query("UPDATE location SET name='{$name}',id={$newid} WHERE id={$id}") or die(mysql_error());
    mysql_query("UPDATE deposit_log SET location={$newid} WHERE location={$id}") or die(mysql_error());
    mysql_query("UPDATE evening_pull SET location={$newid} WHERE location={$id}") or die(mysql_error());
    mysql_query("UPDATE manager_tab SET location={$newid} WHERE location={$id}") or die(mysql_error());
    mysql_query("UPDATE morning_pull SET location={$newid} WHERE location={$id}") or die(mysql_error());
    mysql_query("UPDATE user SET location={$newid} WHERE location={$id}") or die(mysql_error());

    print "Location Edited.<br>&gt; <a href='?action=edit_location&id={$newid}'>Back</a>";
}
else if($_GET['action'] == 'new_location') {
print'
  <div class="panel panel-default">
  <div class="panel-heading"><h4>New Location</h4></div>
';
  print "<form method='post' action='?action=new_location_submit'>";
  print "<table class='table'>";
  print "<tr>";
  print "<th>ID: </th>";
  print "<td><input type='text' class='form-control' name='id'></td>";
  print "</tr>";
  print "<tr>";
  print "<th>Name: </th>";
  print "<td><input type='text' class='form-control' name='name'></td>";
  print "</tr>";
  print "<tr>";
  print "<td>&nbsp;</td>";
  print "<td style='text-align:center;'><input class='form-control btn btn-primary' type='submit' value='Submit'></td>";
  print "</tr>";
  print "</table></div>";
}
else if($_GET['action'] == 'new_location_submit') {
  $id = (int) $_POST['id'];
  $name = mysql_real_escape_string($_POST['name']);
  mysql_query("INSERT INTO location (id, name) VALUES({$id}, '{$name}')") or die(mysql_error());
  print "New Location Created.<br>&gt; <a href='?'>Back</a>";
}
else if($_GET['action'] == 'delete_location') {
  print "Are you sure you wish to delete this location?<br><br>&gt; <a href='?action=delete_location_submit&id={$_GET['id']}'>Yes</a><br>&gt; <a href='?action=edit_location&id={$_GET['location']}'>No</a>";
}
else if($_GET['action'] == 'delete_location_submit') {
    $id = (int) $_GET['id'];
    mysql_query("DELETE FROM location WHERE id={$id}") or die(mysql_error());
    print "Location deleted.<br>&gt; <a href='?'>Back</a>";
}
else if($_GET['action'] == 'new_user') {
  $location = (int) $_GET['location'];
print'
  <div class="panel panel-default">
  <div class="panel-heading"><h4>New User</h4></div>
';
  print "<form method='post' action='?action=new_user_submit&location={$location}'>";
  print "<table class='table'>";
  print "<tr>";
  print "<th>Username: </th>";
  print "<td><input type='text' class='form-control' name='username'></td>";
  print "</tr>";
  print "<tr>";
  print "<th>Password: </th>";
  print "<td><input type='text' class='form-control' name='password'></td>";
  print "</tr>";
  print "<tr>";
  print "<th>Level: </th>";
  print "<td>
  <select class='form-control' name='ulevel'>
    <option value=2>Base</option>
    <option value=1>Manager</option>
  </select>
  </td>";
  print "</tr>";
  print "<tr>";
  print "<td>&nbsp;</td>";
  print "<td style='text-align:center;'><input class='form-control btn btn-primary' type='submit' value='Submit'></td>";
  print "</tr>";
  print "</table></div>";
}
else if($_GET['action'] == 'new_user_submit') {
  $location = (int) $_GET['location'];
  $ulevel = (int) $_POST['ulevel'];
  $username = mysql_real_escape_string($_POST['username']);
  $password = md5($_POST['password']);
  mysql_query("INSERT INTO user (username, password, location, ulevel) VALUES('{$username}', '{$password}', {$location}, {$ulevel})") or die(mysql_error());
  print "New User Created.<br>&gt; <a href='?action=edit_location&id={$location}'>Back</a>";
}
else if($_GET['action'] == 'edit_user') {
  $id=(int) $_GET['id'];
  $q = mysql_query("SELECT * FROM user WHERE id={$id}") or die(mysql_error());
  while ($r = mysql_fetch_assoc($q))
  {
print'
  <div class="panel panel-default">
  <div class="panel-heading"><h4>Edit User</h4></div>
';
    print "<form method='post' action='?action=edit_user_submit&id={$r['id']}'>";
    print "<table class='table'>";
    print "<tr>";
    print "<th>Username: </th>";
    print "<td>{$r['username']}</td>";
    print "</tr>";
    print "<tr>";
    print "<th>Password: </th>";
    print "<td><input class='form-control' type='text' name='password'></td>";
    print "</tr>";
    print "<tr>";
    print "<th>Level: </th>";
    $baseselected = "";
    $managerelected = "";
    if ($r['ulevel'] == 1) {
        $managerelected = " selected";
    } else if ($r['ulevel'] == 2) {
        $baseselected = " selected";
    }
    print "<td>
    <select class='form-control' name='ulevel'>
      <option value=2".$baseselected.">Base</option>
      <option value=1".$managerelected.">Manager</option>
    </select>
    </td>";
    print "</tr>";
    print "<tr>";
    print "<td>&nbsp;</td>";
    print "<td style='text-align:center;'><input class='form-control btn btn-primary' type='submit' value='Submit'></td>";
    print "</tr>";
    print "</table></div>";
  }
}
else if($_GET['action'] == 'edit_user_submit') {
    $id = (int) $_GET['id'];
    $ulevel = (int) $_POST['ulevel'];
    if($_POST['password']){
        $password = md5($_POST['password']);
        mysql_query("UPDATE user SET password='{$password}' WHERE id={$id}") or die(mysql_error());
    }
    mysql_query("UPDATE user SET ulevel={$ulevel} WHERE id={$id}") or die(mysql_error());
    print "User updated.<br>&gt; <a href='?'>Back</a>";
}
else if($_GET['action'] == 'delete_user') {
  print "Are you sure you wish to delete this user?<br><br>&gt; <a href='?action=delete_user_submit&location={$_GET['location']}&id={$_GET['id']}'>Yes</a><br>&gt; <a href='?action=edit_location&id={$_GET['location']}'>No</a>";
}
else if($_GET['action'] == 'delete_user_submit') {
    $id = (int) $_GET['id'];
    mysql_query("DELETE FROM user WHERE id={$id}") or die(mysql_error());
    print "User deleted.<br>&gt; <a href='?action=edit_location&id={$_GET['location']}'>Back</a>";
}
else {
  //show list of locations + reset password option + new location link
  $q = mysql_query("SELECT * FROM location ORDER BY id ASC") or die(mysql_error());

print'
  <div class="panel panel-default">
  <div class="panel-heading"><h4>Locations [<a href="?action=new_location">New</a>]</h4></div>
';

  print "<table class='table' width='100%'>";
  print "<tr><th>ID</th><th>Name</th><th>Edit?</th><th>Delete?</th></tr>";
  while ($r = mysql_fetch_assoc($q))
  {
    print "<tr>";
    print "<td>{$r['id']}</td>";
    print "<td>{$r['name']}</td>";
    print "<td>&gt; <a href='?action=edit_location&id=".$r['id']."'>Edit</a></td>";
    print "<td>&gt; <a href='?action=delete_location&id=".$r['id']."'>Delete</a></td>";
    print "</tr>";
  }
  print "</table></div>";
}


?>


    </div>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>
