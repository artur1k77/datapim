<?
require('/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/autoload.php');

$youtube = new steamapi(false,'https://www.googleapis.com/youtube/v3/activities?part=id%2Csnippet%2CcontentDetails&channelId=UCNRQ-DWUXf4UVN9L31Y9f3Q&key='.GOOGLE_API_KEY.'',false);
$output = $youtube->sendrequest();

echo '<h3>output<h3><br>';
echo '<pre>';
$array = json_decode($output,true);
print_r($array);

?>