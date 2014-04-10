<?
$user = user::getInstance();
$page = new pageloader($_GET['page']);
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8" />

<!-- meta -->
<?
$page->generate_metas();
echo $page->outputMetas();
?>

<!-- icon -->
<link rel="shortcut icon" href="..." type="image/x-icon" />

<!-- stylesheets -->
<link rel="stylesheet" type="text/css" media="all" href="/template/css/dota2.css" />


<!-- google analytics -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-45974882-1', 'dota2essentials.com');
  ga('send', 'pageview');

</script>
</head>

<body>
<header>
	<div id="menucontainer">
    	<div id="menucontainercentered">
            <div id="topmenucontainer">
            	<div id="mainlogo">
                	<a href="/" title="Dota 2 Essentials"><img src="/template/img/dota2essentials-logo.png"></a>
                </div>

                <div id="usernav" data-div="user_data">
                	<div id="user_1" class="mainusernavitem">
                	<? 
						echo $user->renderLoginHTML();
					?>
                    </div>
                    <? 
					if($user->getValidated()){
					?>
                    <div class="usersubnav user_1" data-menuitem="user_data">
                        <nav>
                            <div class="subnavwrap">
                        		<div class="subnavimg"></div>
                            	<div class="subnavlink"><a href="/profile/">My Profile</a></div>
                        	</div>
                            <div class="subnavwrap">
                        		<div class="subnavimg"></div>
                            	<div class="subnavlink"><a href="/preferences/">Preferences</a></div>
                        	</div>
                            <div class="subnavwrap">
                        		<div class="subnavimg"></div>
                            	<div class="subnavlink"><a href="/login/?url=/<? echo $_GET['page'];?>/&logout">Logout</a></div>
                        	</div>
                        </nav>
                    </div>
                    <?
					}
					?>
                </div>
            </div>
            <div id="nav">
                <div id="botmenucontainer">
                    <nav>
                        <ul id="mainnav">
                            <li><a class="mainnavitem"  href="/">HOME</a></li>
                            <li><a class="mainnavitem menu_1" id="menu_1" href="/news/">NEWS</a></li>
                            <li><a class="mainnavitem menu_2" id="menu_2" href="/trading/">TRADING</a></li>
                            <li><a class="mainnavitem" href="/livestreams/">LIVESTREAMS</a></li>
                            <li><a class="mainnavitem  menu_3" id="menu_3" href="/tools/">TOOLS</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="subnav menu_1">
                    <nav>
						<div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/my-inventory/">Pro Scene</a></div>
                        </div>
						<div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/my-inventory/">Latest Dota 2 talks</a></div>
                        </div>
						<div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/my-inventory/">Dota Game Updates</a></div>
                        </div>
                    </nav>
                </div>
                <div class="subnav menu_2">
                    <nav class="subnavmenuitems">
                    	<div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/browse-cosmetics/">Browse Items</a></div>
                        </div>
                    	<div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/my-inventory/">My inventory</a></div>
                        </div>
                    	<div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="">Trade items</a></div>
                        </div>
                        <div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/my-wishlist/">My Wishlist</a></div>
                        </div>
                    </nav>
                        <div class="subnavcartoon">
                        	<img src="/template/img/cartoons/roshan-trading.png" width="190px">
                        </div>
                    
                </div>
				<div class="subnav menu_3">
                    <nav>
                    	<div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/divine-courage/">Divine Courage</a></div>
                        </div>
                    	<div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/armory-calculator/">Armory Calculator</a></div>
                        </div>
                        <div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/hero-counterpicker/">Hero Counterpicker</a></div>
                        </div>
                        <div class="subnavwrap">
                        	<div class="subnavimg"></div>
                            <div class="subnavlink"><a href="/market-analist/">Steam Market Analysis</a></div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>


<div id="maincontentcontainer">
    <div id="maincontent">
    	<?
		echo $page->output_page();
		?>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <div id="footer">
    	<div id="footerpartners">
        	<div class="footerblock">
            	<div class="footerblockhead">
                	Valve
                </div>
                <div class="footerblockimg">
                	<img src="/template/img/valvelogo.png" title="Love you guys !!" alt="Valve logo" />
                </div>
            </div>
        	<div class="footerblock">
            	<div class="footerblockhead">
                	Dota 2
                </div>
                <div class="footerblockimg">
                	<img src="/template/img/dota2logo.png" title="Best game ever !!" alt="Dota 2 logo" />
                </div>
            </div>
        	<div class="footerblock">
            	<div class="footerblockhead">
                	MediaTemple Hosting
                </div>
                <div class="footerblockimg">
                	<img src="/template/img/mediatemplelogo.png" title="Hosting done properly !!" alt="Mediatemple logo" />
                </div>
            </div>
        	<div class="footerblock">
            	<div class="footerblockhead">
                	Steam
                </div>
                <div class="footerblockimg">
                	<img src="/template/img/steamlogo.png" title="Best gaming platform known to man !!" alt="Steam logo" />
                </div>
            </div>
        </div>
        <div id="footermenu">
        <br>
        @ Copyright Dota2essentials.com<br>
        <a href="">Contact</a>&nbsp;|&nbsp;<a href="">About us</a>&nbsp;|&nbsp;<a href="">Terms of Service</a><br>
        This site is in no way associated with or endorsed by Valve Corporation ®
        </div>
    </div>
</div>




<!-- javascript -->

<!-- jquery -->
<script type="text/javascript" src="/template/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="/template/js/jquery-slider.js"></script>
<!-- custom JS -->
<script type="text/javascript" src="/template/js/dota2essentials.js"></script>
<script type="text/javascript" src="/template/js/trading.js"></script>


</body>



</html>
