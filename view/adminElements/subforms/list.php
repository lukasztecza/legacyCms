List:<br />
<div class="hint">HINT
    <p>Fill up list element text and confirm to add list row</p>
    <p>Clear list element text and confirm to delete list row</p>
    <p>Clear all list elements texts and confirm to delete list</p>
    <p>If you want list element to be a link fill up list element text and corresponding url and confirm</p>
    <p>Url should contain full path for instance: https://www.google.com</p>
</div>
<label>Type:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_list_" . $counter . "_type"; ?>">
        <option value="disc" <?php echo ($content["type"] === "disc") ? "selected" : null; ?>>disc</option>
        <option value="circle" <?php echo ($content["type"] === "circle") ? "selected" : null; ?>>circle</option>
        <option value="square" <?php echo ($content["type"] === "square") ? "selected" : null; ?>>square</option>
        <option value="decimal" <?php echo ($content["type"] === "decimal") ? "selected" : null; ?>>numbers</option>
        <option value="upper-roman" <?php echo ($content["type"] === "upper-roman") ? "selected" : null; ?>>roman numbers</option>
        <option value="none" <?php echo ($content["type"] === "none") ? "selected" : null; ?>>none</option>
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
List content:<br />
<?php $listCounter = 0; ?>
<?php foreach ($content["rows"] as $listElement): ?>
    <?php $listCounter++; ?>
    <label>Text:
        <input
            type="text"
            name="<?php echo "article_" . $data->article["buttonId"] . "_list_" . $counter . "_row_" . $listCounter . "_text"; ?>"
            value="<?php echo $listElement["text"]; ?>"
        />
    </label>
    <label>Url:
        <input
            type="text"
            name="<?php echo "article_" . $data->article["buttonId"] . "_list_" . $counter . "_row_" . $listCounter . "_url"; ?>"
            value="<?php echo $listElement["url"]; ?>"
        />
    </label><br />
<?php endforeach; ?>
<label>Text:
    <input
        type="text"
        name="<?php echo "article_" . $data->article["buttonId"] . "_list_" . $counter . "_row_" . ($listCounter + 1) . "_text"; ?>"
        value=""
    />
</label>
<label><?php echo"Url: "; ?>
    <input
        type="text"
        name="<?php echo "article_" . $data->article["buttonId"] . "_list_" . $counter . "_row_" . ($listCounter + 1) . "_url"; ?>"
        value=""
    />
</label>
<br /><br /><br />
<hr />

