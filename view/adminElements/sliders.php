<form action="<?php echo Config::getSite() . "&request=admin&extension=sliders"?>" method="post">
    <fieldset>
        <legend>Sliders</legend>
        <div class="hint">HINT
            <p>Slider elements will be added as buttons on page sides which will be clickable or scrollable on click</p>
            <p>Fill up search bar description to add search bar which allows to find articles which include specified text</p>
            <p>Search bar will find all articles (related to used and unused buttons) with specified text but will omit all related to buttons from retrieve button selector</p>
            <p>Search bar will find articles by original language text value, and by translations where searched text occurs</p>
            <p>Fill up side box and confirm to add scrollable panel on the side of the page, text in side box will respect all spaces and line breaks</p>
            <p>Clear side box and confirm to delete scrollable panel</p>
            <p>To add e-mail contact box scrollable on click fill up receiver e-mail and confirm, you can also add description to e-mail contact box</p>
            <p>Clear receiver e-mail and confirm, to delete e-mail contact box</p>
            <p>To add facebook, twitter, youtube or google &plus; button fill up url and confirm</p>
            <p>Facebook link should start with https://www.facebook.com/ for instance: https://www.facebook.com/yourprofile</p>
            <p>Twitter link should start with https://www.twitter.com/ for instance: https://www.twitter.com/yourprofile</p>
            <p>YouTube link should start with https://www.youtube.com/ for instance: https://www.youtube.com/yourprofile</p>
            <p>Google &plus; link should start with https://www.plus.google.com/ for instance: https://www.plus.google.com/yourprofile</p>
            <p>Clear facebook, twitter, youtube or google &plus; url and confirm, to delete corresponding button</p>
        </div>
        <p class="error"><?php echo $data->error; ?></p>
        <p class="message"><?php echo $data->message; ?></p>
        <hr />
        <br />
        <label>Search bar description:
            <input type="text" name="<?php echo "search"; ?>" value="<?php echo $data->contents["search"]; ?>" />
        </label>
        <br /><br />
        <hr />
        <br />
        <label>Side box:
            <textarea
                rows="4"
                name="<?php echo "sidebox"; ?>"
            ><?php echo $data->contents["sidebox"]; ?></textarea>
        </label>
        <br /><br />
        <hr />
        <br />
        <label>Contact box receiver e-mail:
            <input type="text" name="<?php echo "contact_email"; ?>" value="<?php echo $data->contents["emailAddress"]; ?>" />
        </label>
        <label>Contact box description:
            <input type="text" name="<?php echo "contact_description"; ?>" value="<?php echo $data->contents["emailDescription"]; ?>" />
        </label>
        <br /><br />
        <hr />
        <br />
        <label>Facebook url:
            <input type="text" name="<?php echo "facebook"; ?>" value="<?php echo $data->contents["facebook"]; ?>" />
        </label>
        <br /><br />
        <hr />
        <br />
        <label>Twitter url:
            <input type="text" name="<?php echo "twitter"; ?>" value="<?php echo $data->contents["twitter"]; ?>" />
        </label>
        <br /><br />
        <hr />
        <br />
        <label>YouTube url:
            <input type="text" name="<?php echo "youtube"; ?>" value="<?php echo $data->contents["youtube"]; ?>" />
        </label>
        <br /><br />
        <hr />
        <br />
        <label>Google &plus; url:
            <input type="text" name="<?php echo "googleplus"; ?>" value="<?php echo $data->contents["googleplus"]; ?>" />
        </label>
        <br /><br />
        <hr />
        <br />
        <label>LinkedIn url:
            <input type="text" name="<?php echo "linkedin"; ?>" value="<?php echo $data->contents["linkedin"]; ?>" />
        </label>
        <br /><br />
        <hr />
        <input type="submit" value="<?php echo "Confirm"; ?>" />
    </fieldset>
</form>
