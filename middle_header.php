<?
$nav_date_str = "";
if($_GET['date']) {
  $nav_date_str = "?date=".$_GET['date'];
}
?>


<nav class="navbar navbar-default" style="margin-bottom: 0px !important;">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="<?=$evening_pull?>">
          <a href="evening_pull.php<?=$nav_date_str?>">Evening Pull</a>
        </li>
        <li class="<?=$morning_pull?>">
          <a href="morning_pull.php<?=$nav_date_str?>">Morning Pull</a>
        </li>
        <?
        if($_SESSION['ulevel'] < 2) {
        ?>
        <li class="<?=$manager?>">
          <a href="manager.php<?=$nav_date_str?>">Manager</a>
        </li>
        <?
        }
        ?>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
