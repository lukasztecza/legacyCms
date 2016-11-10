<form action="<?php echo Config::getSite() . "&request=admin&extension=buttons"?>" method="post">
    <fieldset>
        <legend>Buttons editor</legend>
        <div class="hint">HINT
            <p>Fill button name and confirm to add button</p>
            <p>Clear button name and confirm to delete button, button with corresponding article will be deleted and will not be accessible via url but still possible to retrieve</p>
            <p>Choose button to retrieve and confirm to put it back in the list, if button contained article it will be also retrieved</p>
            <p>Select clear deleted buttons and confirm to delete buttons from retrieving selector with corresponding articles permanently</p>
            <p>Buttons are displayed in alphabetical order with note if they are used or not, if not used they are not in menu but accessible after typing proper url</p>
            <p>You can add first image if you want to display image when mouse is out and text when mouse is over button</p>
            <p>You can add first image and second image if you want to display first image when mouse is out and second image when mouse is over button</p>
            <p>If you want to secure content of the button check secured (users will need to provide login and password to access)</p>
            <p>Remember that images and files included in secured button contents will not be visible but still accessible via url</p>
            <p>Go to users section to add new accounts which will get logins and passwords to secured buttons contents</p>
        </div>
        <p class="error"><?php echo $data->error; ?></p>
        <p class="message"><?php echo $data->message; ?></p>
        <hr />
        <?php foreach ($data->buttons as $button): ?>
            <label><?php echo "Name: "; ?>
                <input type="text" name="<?php echo "button_" . $button["id"] . "_name"; ?>" value="<?php echo $button["name"] ?>" />
            </label>
            <?php if (!empty($button["used"])): ?>
                <span class="message">used</span>
            <?php else: ?>
                <span class="error">unused</span>
            <?php endif; ?>
            <br />
            <label>First image:
                <select name="<?php echo "button_" . $button["id"] . "_imageFirst"; ?>">
                    <option value=""></option>
                    <?php foreach ($data->pictures as $fileName): ?>
                        <option value="<?php echo $fileName; ?>" <?php  echo ($button["imageFirst"] === $fileName) ? "selected" : null; ?>>
                            <?php echo strlen($fileName) > 100 ? substr($fileName, 0, 100) . "..." : $fileName; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <br />
            <label>Second image:
                <select name="<?php echo "button_" . $button["id"] . "_imageSecond"; ?>">
                    <option value=""></option>
                    <?php foreach ($data->pictures as $fileName): ?>
                        <option value="<?php echo $fileName; ?>" <?php  echo ($button["imageSecond"] === $fileName) ? "selected" : null; ?>>
                            <?php echo strlen($fileName) > 100 ? substr($fileName, 0, 100) . "..." : $fileName; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <br />
            <label>
            <input type="checkbox" name="<?php echo "button_" . $button["id"] . "_secured"; ?>" value="1" <?php echo $button["secured"] ? "checked" : ""; ?>>Secured
            </label>
            <hr />
        <?php endforeach; ?>
        <label>Name:
            <input type="text" name="<?php echo "button_new_name"; ?>" value="" />
        </label>
        <br />
        <label>First image:
            <select name="<?php echo "button_new_imageFirst"; ?>">
                <option value=""></option>
                <?php foreach ($data->pictures as $fileName): ?>
                    <option value="<?php echo $fileName; ?>">
                        <?php echo strlen($fileName) > 100 ? substr($fileName, 0, 100) . "..." : $fileName; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <br />
        <label>Second image:
            <select name="<?php echo "button_new_imageSecond"; ?>">
                <option value=""></option>
                <?php foreach ($data->pictures as $fileName): ?>
                    <option value="<?php echo $fileName; ?>">
                        <?php echo strlen($fileName) > 100 ? substr($fileName, 0, 100) . "..." : $fileName; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label><br />
        <input type="checkbox" name="<?php echo "button_new_secured"; ?>" value="1">Secured
        </label>
        <hr />
        <label>Retrieve button
            <select name="<?php echo "button_retrieve_id"; ?>">
                <option value=""></option>
                <?php foreach ($data->inactiveButtons as $button): ?>
                    <option value="<?php echo $button["id"]; ?>">
                        <?php echo strlen($button["name"]) > 100 ? substr($button["name"], 0, 100) . "..." : $button["name"]; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <input type="checkbox" name="button_clear_all" value="1">
            Clear deleted buttons
        </label>
        <hr />
        <input type="submit" value="<?php echo "Confirm"; ?>" />
    </fieldset>
</form>
