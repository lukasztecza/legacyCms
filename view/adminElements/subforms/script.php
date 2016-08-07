Script:<br />
<div class="hint">HINT
    <p>You can add javascript, html, css etc. as content of script element will not be escaped (include it only if you are sure it is safe)</p>
    <p>Clear text and confirm to delete script</p>
    <p>If you want to add for instance iframe add to it attribute style="widht:100%;" to make it fill all width of the article</p>
</div>
<input 
    type="hidden" 
    name="<?php echo "article_" . $data->article["buttonId"] . "_script_" . $counter . "_id"; ?>"
    value="<?php echo $content["id"]; ?>">
<label>Content:
    <textarea
        rows="4"
        name="<?php echo "article_" . $data->article["buttonId"] . "_script_" . $counter . "_string"; ?>"
    ><?php echo $content["string"]; ?></textarea>
</label>
<br /><br /><br />
<hr />