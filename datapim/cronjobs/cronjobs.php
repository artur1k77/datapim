<?
// this file controls all the cronfiles 
// place this in you cronjob of your hosting provider execute every minute or whatever...
ini_set('memory_limit', '256M');
require('/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/autoload.php');

$cronjobs = new runcronjobs();
$cronjobs->output();



?>