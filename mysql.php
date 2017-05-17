<?php
$mysql_string = getenv('CLEARDB_DATABASE_URL');
if($mysql_string) {
    //Heroku with Clear DB add-on
    $mysql_url = parse_url($mysql_string);
}
else {
    //local or DigitalOcean
    $mysql_url = [];
    $mysql_url['host'] = "localhost:8889";
    $mysql_url['user'] = "root";
    $mysql_url['pass'] = "root";
    $mysql_url['path'] = "/blueiguana";
}
$c = mysql_connect($mysql_url['host'], $mysql_url['user'], $mysql_url['pass']) or die(mysql_error());
$db_name = str_replace("/", "", $mysql_url['path']);
mysql_select_db($db_name, $c);

?>