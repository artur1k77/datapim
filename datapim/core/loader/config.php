<?

// load paths
define('BASE_PATH', '/var/www/vhosts/dota2essentials.com/httpdocs/');
define('CLASS_PATH', '/var/www/vhosts/dota2essentials.com/httpdocs/core/classes/');
define('MODULE_PATH', '/var/www/vhosts/dota2essentials.com/httpdocs/modules/');
define('PAGE_PATH', '/var/www/vhosts/dota2essentials.com/httpdocs/pages/');
define('AJAX_PATH', '/var/www/vhosts/dota2essentials.com/httpdocs/core/ajax/requests/');
define('MEDIA_PATH', '/var/www/vhosts/dota2essentials.com/httpdocs/media/');
define('MEDIA_PATH_COSMETICS', '/var/www/vhosts/dota2essentials.com/httpdocs/media/cosmetics/');
define('MEDIA_PATH_HEROS', '/var/www/vhosts/dota2essentials.com/httpdocs/media/heros/');
define('MEDIA_PATH_ITEMS', '/var/www/vhosts/dota2essentials.com/httpdocs/media/items/');
define('MEDIA_PATH_LIVESTREAMS', '/var/www/vhosts/dota2essentials.com/httpdocs/media/livestreams/');
define('MEDIA_PATH_USERS', '/var/www/vhosts/dota2essentials.com/httpdocs/media/users/');
define('MEDIA_PATH_YOUTUBE', '/var/www/vhosts/dota2essentials.com/httpdocs/media/youtube/');
define('CRONJOBS_PATH', '/var/www/vhosts/dota2essentials.com/httpdocs/cronjobs/');

// db 
define('DB_DATABASE', 'dota2essentials');
define('DB_USER', 'dota2');
define('DB_PASS', 'essentials123');
define('DB_HOST', 'localhost');

// error reporting
define('DISPLAY_ERRORS',true);

// Steam API KEU
define('STEAM_API_KEY', '15C4421EA926C59BD2D320DA4A4051C8'); // staat ook in de class zelf gedefined

// google keys
define('GOOGLE_DEV_KEY','AI39si49UikZ-3i5s9-H4k3xw6Nc98vYuyS4v6AlSUxTZpQ7Vtj6o5QzGFP6O8TnCdXPzx6nXghCaITY0KstyUUOvXnbmupqow');
define('GOOGLE_API_KEY','AIzaSyBdsDyS2XomwQk-bncesdB9wBp9u83wNzo');

// PHP Timezone
//define('PHP_TIMEZONE','Europe/Amsterdam');
define('PHP_TIMEZONE','America/Los_Angeles');
?>