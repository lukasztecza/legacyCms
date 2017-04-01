Table:<br />
<div class="hint">HINT
    <p>Fill up header to add column</p>
    <p>Fill up any cell in empty row to add row</p>
    <p>Clear all headers to delete table</p>
    <p>Clear single header to delete column which is assigned to it</p>
    <p>Clear all cells in a row to delete row</p>
    <p>If column has not filled up header it will not be saved</p>
    <p>Table name is not required</p>
</div>
<label>Table name:
    <input
        type="text"
        name="<?php  echo "article_" . $data->article["buttonId"] . "_table_" . $counter . "_caption"; ?>"
        value="<?php echo $content["caption"]; ?>"
    />
</label>
<label>All cells align:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_table_" . $counter . "_align"; ?>">
        <option value="left" <?php echo ($content["align"] === "left") ? "selected" : null; ?>>left</option>
        <option value="center" <?php echo ($content["align"] === "center") ? "selected" : null; ?>>center</option>
        <option value="right" <?php echo ($content["align"] === "right") ? "selected" : null; ?>>right</option>
        <option value="justify" <?php echo ($content["align"] === "justify") ? "selected" : null; ?>>justify</option>                
    </select>
</label>
<label>Headers weight:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_table_" . $counter . "_weight"; ?>">
        <option value="normal" <?php echo ($content["weight"] === "normal") ? "selected" : null; ?>>normal</option>
        <option value="bold" <?php echo ($content["weight"] === "bold") ? "selected" : null; ?>>bold</option>
    </select>
</label>
<label>Headers size:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_table_" . $counter . "_size"; ?>">
        <option value="1em" <?php echo ($content["size"] === "1em") ? "selected" : null; ?>>normal</option>
        <option value="1.25em" <?php echo ($content["size"] === "1.25em") ? "selected" : null; ?>>big</option>
        <option value="1.5em" <?php echo ($content["size"] === "1.5em") ? "selected" : null; ?>>huge</option>
    </select>
</label><br />
Table content:<br />
<?php $rowCounter = 0; ?>
<?php foreach ($content["rows"] as $row): ?>
    <?php $rowCounter++; ?>
    <?php $cellCounter = 0; ?>
    <?php foreach ($row as $cell): ?>
        <?php $cellCounter++; ?>
        <?php if ($cellCounter <= 6): ?>
            <input
                type="text"
                name="<?php echo
                    "article_" . $data->article["buttonId"] .
                    "_table_" . $counter .
                    "_rows_" . $rowCounter . "_" . $cellCounter;
                ?>"
                value="<?php echo $cell; ?>"
            />
        <?php endif; ?>
    <?php endforeach; ?>
    <?php $cellCounter++; ?>
    <?php if ($cellCounter <= 6): ?>
        <input
            type="text"
            name="<?php
                echo "article_" . $data->article["buttonId"] . "_table_" . $counter . "_rows_" . $rowCounter . "_" . $cellCounter;
            ?>"
            value=""
        />
    <?php endif; ?>
    <br />
<?php endforeach; ?>
<?php $rowCounter++; ?>
<?php $cellCounter = $cellCounter > 6 ? 6 : $cellCounter; ?>
<?php for ($i = 1; $i <= $cellCounter; $i++): ?>
    <input
        type="text"
        name="<?php
            echo "article_" . $data->article["buttonId"] . "_table_" . $counter . "_rows_" . $rowCounter . "_" . $i;
        ?>"
        value=""
    />
<?php endfor; ?>
<br /><br /><br />
<hr />
