File<br />
<div class="hint">HINT
    <p>Clear name and confirm to delete</p>
    <p>To enlarge number of files in selector add more in section files</p>
</div>
<label>Name:
    <input
        type="text"
        name="<?php echo "article_" . $data->article["buttonId"] . "_file_" . $counter . "_title"; ?>"
        value="<?php echo $content["title"] ?>"
    />
</label>
<label>File name:
<select name="<?php echo "article_" . $data->article["buttonId"] . "_file_" . $counter . "_file"; ?>">
    <option value=""></option>
    <?php foreach ($data->files as $fileName): ?>
        <option value="<?php echo $fileName; ?>" <?php echo ($content["file"] === $fileName) ? "selected" : null; ?>>
            <?php echo strlen($fileName) > 100 ? substr($fileName, 0, 100) . "..." : $fileName; ?>
        </option>
    <?php endforeach; ?>
</select>
</label>
<br /><br /><br />
<hr>
