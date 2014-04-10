<?
// handles default ajax requests
require '/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/autoload.php';

$request = new ajax($_REQUEST);

$request->call_ajax();

echo $request->response();

?>