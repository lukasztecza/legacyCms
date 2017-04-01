<?php //currently not used ?>
Email contact box:<br />
<div class="hint">HINT
    <p>Specify receiver e-mail and confirm, You can also add description to e-mail contact box</p>
    <p>Clear receiver e-mail and confirm, to delete e-mail contact box</p>
</div>
<label>Contact box receiver e-mail:
    <input
        type="text"
        name="<?php echo "article_" . $data->article["buttonId"] . "_contact_" . $counter . "_email"; ?>"
        value="<?php echo $content["email"]; ?>"
    />
</label>
<label>Contact box description:
    <input
        type="text"
        name="<?php echo "article_" . $data->article["buttonId"] . "_contact_" . $counter . "_description"; ?>"
        value="<?php echo $content["description"]; ?>"
    />
</label>
<br /><br /><br />
<hr />
