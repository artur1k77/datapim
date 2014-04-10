<?
$user = user::getInstance();
?>

<section>
<div class="mcwrap">
	<div class="mcheader"><h1>User profile</h1></div>
    <form name="user_preferences">
    <div class="pref_container">
    <div class="header">Personal settings</div>
    	<div class="pref_content">
        
            <div class="pref_el">
                <label for="test" class="form_el">Email</label>
                <input id="test" type="email" class="form_el">
           </div>
                   
           <div class="pref_el">
                <label for="personal_mesage" class="form_el">Profile Message</label>
                <textarea id="personal_mesage" name="personal_msg" class="form_el"></textarea>
            </div>
            
        </div>
    </div>
    

    

    </form>
    <div class="pref_save_button">
	    <div id="pref_save" class="mcbutton">Save</div>
    </div>
</div>
</section>


<!--
            <div class="pref_el">
                <label for="radio" class="form_el">radio</label>
                <input id="radio" type="radio" name="group1" value="dota0">
                <input type="radio" name="group1" value="dota1">
                <input type="radio" name="group1" value="dota2">
           </div>
           
-->