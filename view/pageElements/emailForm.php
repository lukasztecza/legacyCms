<form
    action="<?php echo Config::getSite() . "&request=page&article=" . $data->buttonId . "#contact"; ?>"
    method="post"
    autocomplete="off"
    charset="utf-8"
    id="contact"
>
    <fieldset>
        <legend><?php echo Dictionary::get($data->description); ?></legend>
        <label>
            <input
                type="text"
                name="userEmail"
                placeholder="<?php echo Dictionary::get(Dictionary::$texts[1]); ?>"
                value="<?php echo $data->userEmail; ?>"
            >
        </label><br/>
        <label>
            <textarea
                rows="4"
                name="userMessage"
                placeholder="<?php echo Dictionary::get(Dictionary::$texts[2]); ?>"
            ><?php echo $data->userMessage; ?></textarea>
        </label>
        <p class="error"><?php echo Dictionary::get($data->error); ?></p>
        <p class="message"><?php echo Dictionary::get($data->confirmSending); ?></p>
        <input type="hidden" name="recieverEmail" value="<?php echo $data->recieverEmail; ?>">
        <input type="submit" value="<?php echo Dictionary::get(Dictionary::$texts[3]); ?>">
    </fieldset>
</form>
