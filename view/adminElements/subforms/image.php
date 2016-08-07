Image:<br />
<div class="hint">HINT
    <p>Clear name and confirm to delete</p>
    <p>To enlarge number of images in selector add more in section images</p>
</div>
<label>Name:
    <input
        type="text"
        name="<?php echo "article_" . $data->article["buttonId"] . "_image_" . $counter . "_title"; ?>"
        value="<?php echo $content["title"] ?>"
    />
</label>
<label>File name:
<select name="<?php echo "article_" . $data->article["buttonId"] . "_image_" . $counter . "_file"; ?>">
    <option value=""></option>
    <?php foreach ($data->pictures as $fileName): ?>
        <option value="<?php echo $fileName; ?>" <?php echo ($content["file"] === $fileName) ? "selected" : null; ?>>
            <?php echo strlen($fileName) > 100 ? substr($fileName, 0, 100) . "..." : $fileName; ?>
        </option>
    <?php endforeach; ?>
</select>
</label>
<label>Style:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_image_" . $counter . "_type"; ?>">
        <option value="100%" <?php echo ($content["type"] === "big") ? "selected" : null; ?>>big</option>
        <option value="left" <?php echo ($content["type"] === "left") ? "selected" : null; ?>>little on the left</option>
        <option value="right" <?php echo ($content["type"] === "right") ? "selected" : null; ?>>little on the right</option>
        <option value="centered" <?php echo ($content["type"] === "centered") ? "selected" : null; ?>>little centerd</option>
    </select>
</label>
<br /><br /><br />
<hr>
