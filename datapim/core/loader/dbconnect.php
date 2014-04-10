<?
$mysqli = new database();

if($mysqli->connect_error && DISPLAY_ERRORS){
    die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
?>