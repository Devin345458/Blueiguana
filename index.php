<?
session_start();
if ($_SESSION['location']) {
  if ($_SESSION['location'] == -1) {
    header("Location: admin.php");
    exit;
  }
  header("Location: evening_pull.php");
  exit;
}
header("Location: login.php");
exit;
?>
