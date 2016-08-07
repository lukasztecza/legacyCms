Paragraph:<br />
<div class="hint">HINT
    <p>Clear text and confirm to delete paragraph</p>
    <p>Regular paragraph will respect line breaks but will reduce multiple spaces</p>
    <p>Preformatted paragraph will respect all spaces and line breaks</p>
</div>
<label>Type:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_paragraph_" . $counter . "_type"; ?>">
        <option value="paragraph" <?php echo ($content["type"] === "paragraph") ? "selected" : null; ?>>regular</option>
        <option value="preformatted" <?php echo ($content["type"] === "preformatted") ? "selected" : null; ?>>preformatted</option>
    </select>
</label>
<label>Align:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_paragraph_" . $counter . "_align"; ?>">
        <option value="left" <?php echo ($content["align"] === "left") ? "selected" : null; ?>>left</option>
        <option value="center" <?php echo ($content["align"] === "center") ? "selected" : null; ?>>center</option>
        <option value="right" <?php echo ($content["align"] === "right") ? "selected" : null; ?>>right</option>
        <option value="justify" <?php echo ($content["align"] === "justify") ? "selected" : null; ?>>justify</option>                
    </select>
</label>
<label>Weight:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_paragraph_" . $counter . "_weight"; ?>">
        <option value="normal" <?php echo ($content["weight"] === "normal") ? "selected" : null; ?>>normal</option>
        <option value="bold" <?php echo ($content["weight"] === "bold") ? "selected" : null; ?>>bold</option>
    </select>
</label>
<label>Style:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_paragraph_" . $counter . "_style"; ?>">
        <option value="normal" <?php echo ($content["style"] === "normal") ? "selected" : null; ?>>normal</option>
        <option value="italic" <?php echo ($content["style"] === "italic") ? "selected" : null; ?>>italic</option>
    </select>
</label>
<label>Size:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_paragraph_" . $counter . "_size"; ?>">
        <option value="1em" <?php echo ($content["size"] === "1em") ? "selected" : null; ?>>normal</option>
        <option value="1.25em" <?php echo ($content["size"] === "1.25em") ? "selected" : null; ?>>big</option>
        <option value="1.5em" <?php echo ($content["size"] === "1.5em") ? "selected" : null; ?>>huge</option>
    </select>
</label><br />
<label>Text:
    <textarea
        rows="4"
        name="<?php echo "article_" . $data->article["buttonId"] . "_paragraph_" . $counter . "_text"; ?>"
    ><?php echo $content["text"]; ?></textarea>
</label>
<br /><br /><br />
<hr />