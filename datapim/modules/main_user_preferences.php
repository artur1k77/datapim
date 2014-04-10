<?
$user = user::getInstance();
?>

<section>
<div class="mcwrap">
	<div class="mcheader"><h1>Preferences</h1></div>
    <form name="user_preferences">
    <div class="pref_container">
    <div class="header">Site settings</div>
    	<div class="pref_content">
        
           
            <div class="pref_el">
                <label for="select" class="form_el">Language</label>
                <select id="select" name="language">
                    <option value="en">english</option>
                    <option disabled="disabled">More to come</option>
               </select>
           </div>
           
            <div class="pref_el">
                <label for="select" class="form_el">Currency</label>
                <select id="select" name="currency">
                    <option value="dollar">Dollars</option>
                    <option disabled="disabled">More to come</option>
               </select>
           </div>
            
        </div>
    </div>
    
    <div class="pref_container">
    <div class="header">Trading</div>
    	<div class="pref_content">
        
            <div class="pref_el">
                <label for="select" class="form_el">Notifications</label>
                <select id="select" name="tradenotify">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
               </select>
           </div>
            
            <div class="pref_el">
                <label for="select" class="form_el">Receive emails</label>
                <select id="select" name="tradeemail">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
               </select>
           </div>
            
        </div>
    </div>
    

    </form>
    <div class="pref_save_button">
	    <div id="pref_save" class="mcbutton">Save</div>
    </div>
</div>
</section>