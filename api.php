<?php
session_start();
require('mysql.php');

$action = $_GET['action'];

$current_time = (string) time();


if($action == "get_deposit_log") {
  $month = (int) $_POST['month'];
  $year = (int) $_POST['year'];
  $q = mysql_query("SELECT * FROM deposit_log WHERE year={$year} AND month={$month} AND location={$_SESSION['location']} ORDER by year,month DESC") or die(mysql_error());
  while ($r = mysql_fetch_assoc($q))
  {
    $results = base64_decode($r['data']);
    if(!$results) {
      $results = "{}";
    }
    print $results;
    die();
  }
  $results = [];
  print json_encode($results);
  die();
}

if($action == "save_deposit_log") {
  $month = (int) $_POST['month'];
  $year = (int) $_POST['year'];
  $data = mysql_real_escape_string(base64_encode($_POST['data']));
  $q = mysql_query("SELECT * FROM deposit_log WHERE year={$year} AND month={$month} AND location={$_SESSION['location']} ORDER by year,month DESC") or die(mysql_error());
  $exists = mysql_num_rows($q);
  if($exists > 0) {
    //update
    mysql_query("UPDATE deposit_log SET data='{$data}',last_modified={$current_time} WHERE year={$year} AND month={$month} AND location={$_SESSION['location']}") or die(mysql_error());
  }
  else {
    //insert
    mysql_query("INSERT INTO deposit_log (year, month, data, last_modified, location) VALUES({$year}, {$month}, '{$data}', {$current_time}, {$_SESSION['location']})") or die(mysql_error());
  }
  $results = [];
  $results['status'] = "success";
  print json_encode($results);
  die();
}

if($action == "get_evening_pull") {
  $month = (int) $_POST['month'];
  $day = (int) $_POST['day'];
  $year = (int) $_POST['year'];
  $q = mysql_query("SELECT * FROM evening_pull WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']} ORDER by year,month,day DESC") or die(mysql_error());
  while ($r = mysql_fetch_assoc($q))
  {
    $results = base64_decode($r['data']);
    if(!$results) {
      $results = "{}";
    }
    print $results;
    die();
  }
  $results = [];
  print json_encode($results);
  die();
}

if($action == "save_evening_pull") {
  $month = (int) $_POST['month'];
  $day = (int) $_POST['day'];
  $year = (int) $_POST['year'];
  $data = mysql_real_escape_string(base64_encode($_POST['data']));
  $q = mysql_query("SELECT * FROM evening_pull WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']} ORDER by year,month,day DESC") or die(mysql_error());
  $exists = mysql_num_rows($q);
  if($exists > 0) {
    //update
    mysql_query("UPDATE evening_pull SET data='{$data}',last_modified={$current_time} WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']}") or die(mysql_error());
  }
  else {
    //insert
    mysql_query("INSERT INTO evening_pull (year, month, day, data, last_modified, location) VALUES({$year}, {$month}, {$day}, '{$data}', {$current_time}, {$_SESSION['location']})") or die(mysql_error());
  }
  $results = [];
  $results['status'] = "success";
  print json_encode($results);
  die();
}

if($action == "get_morning_pull") {
  $month = (int) $_POST['month'];
  $day = (int) $_POST['day'];
  $year = (int) $_POST['year'];
  $results = [];
  $results['morning_pull'] = [];
  $results['evening_pull'] = [];

  $q = mysql_query("SELECT * FROM morning_pull WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']} ORDER by year,month,day DESC") or die(mysql_error());
  while ($r = mysql_fetch_assoc($q))
  {
    $the_data = base64_decode($r['data']);
    if($the_data) {
      $results['morning_pull'] = $the_data;
    }
  }

  $q = mysql_query("SELECT * FROM evening_pull WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']} ORDER by year,month,day DESC") or die(mysql_error());
  while ($r = mysql_fetch_assoc($q))
  {
    $the_data = base64_decode($r['data']);
    if($the_data) {
      $results['evening_pull'] = $the_data;
    }
  }

  if (!count($results['morning_pull'])) {
    $results['morning_pull'] = "{}";
  }
  if (!count($results['evening_pull'])) {
    $results['evening_pull'] = "{}";
  }

  print json_encode($results);
  die();
}

if($action == "save_morning_pull") {
  $month = (int) $_POST['month'];
  $day = (int) $_POST['day'];
  $year = (int) $_POST['year'];
  $data = mysql_real_escape_string(base64_encode($_POST['data']));
  $q = mysql_query("SELECT * FROM morning_pull WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']} ORDER by year,month,day DESC") or die(mysql_error());
  $exists = mysql_num_rows($q);
  if($exists > 0) {
    //update
    mysql_query("UPDATE morning_pull SET data='{$data}',last_modified={$current_time} WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']}") or die(mysql_error());
  }
  else {
    //insert
    mysql_query("INSERT INTO morning_pull (year, month, day, data, last_modified, location) VALUES({$year}, {$month}, {$day}, '{$data}', {$current_time}, {$_SESSION['location']})") or die(mysql_error());
  }
  $results = [];
  $results['status'] = "success";
  print json_encode($results);
  die();
}


if($action == "get_manager_pull") {
  $month = (int) $_POST['month'];
  $day = (int) $_POST['day'];
  $year = (int) $_POST['year'];
  $results = [];
  $results['morning_pull'] = "[]";
  $results['evening_pull'] = "[]";
  $results['manager_pull'] = "[]";

  $q = mysql_query("SELECT * FROM morning_pull WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']} ORDER by year,month,day DESC") or die(mysql_error());
  while ($r = mysql_fetch_assoc($q))
  {
    $the_data = base64_decode($r['data']);
    if($the_data) {
      $results['morning_pull'] = $the_data;
    }
  }

  $q = mysql_query("SELECT * FROM evening_pull WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']} ORDER by year,month,day DESC") or die(mysql_error());
  while ($r = mysql_fetch_assoc($q))
  {
    $the_data = base64_decode($r['data']);
    if($the_data) {
      $results['evening_pull'] = $the_data;
    }
  }

  $q = mysql_query("SELECT * FROM manager_tab WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']} ORDER by year,month,day DESC") or die(mysql_error());
  while ($r = mysql_fetch_assoc($q))
  {
    $the_data = base64_decode($r['data']);
    if($the_data) {
      $results['manager_pull'] = $the_data;
    }
  }

  if (!count($results['morning_pull'])) {
    $results['morning_pull'] = "{}";
  }
  if (!count($results['evening_pull'])) {
    $results['evening_pull'] = "{}";
  }
  if (!count($results['manager_pull'])) {
    $results['manager_pull'] = "{}";
  }

  print json_encode($results);
  die();
}

if($action == "save_manager_pull") {
  $month = (int) $_POST['month'];
  $day = (int) $_POST['day'];
  $year = (int) $_POST['year'];
  $data = mysql_real_escape_string(base64_encode($_POST['data']));
  $q = mysql_query("SELECT * FROM manager_tab WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']} ORDER by year,month,day DESC") or die(mysql_error());
  $exists = mysql_num_rows($q);
  if($exists > 0) {
    //update
    mysql_query("UPDATE manager_tab SET data='{$data}',last_modified={$current_time} WHERE year={$year} AND month={$month} AND day={$day} AND location={$_SESSION['location']}") or die(mysql_error());
  }
  else {
    //insert
    mysql_query("INSERT INTO manager_tab (year, month, day, data, last_modified, location) VALUES({$year}, {$month}, {$day}, '{$data}', {$current_time}, {$_SESSION['location']})") or die(mysql_error());
  }
  $results = [];
  $results['status'] = "success";
  print json_encode($results);
  die();
}

?>