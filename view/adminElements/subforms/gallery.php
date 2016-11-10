Gallery:<br />
<div class="Hint">HINT
    <p>Fill up image name and confirm to add image, you should also assign an image file to it</p>
    <p>Delete name of an image and confirm to delete</p>
    <p>To enlarge number of images in selector add more in section images</p>
    <p>Images of galleries are clickable, which will lead to open zooming gallery (desktop) or preview (mobile)</p>
    <p>Choose amount of columns in which images are to be displayed, note that amount of columns can not be larger than amount of images</p>
</div>
<label>Number of columns:
    <input
        type="number"
        name="<?php echo "article_" . $data->article["buttonId"] . "_gallery_" . $counter . "_columns"; ?>"
        value="<?php echo $content["columns"] ?>"
        min="1"
    />
</label><br />
Gallery images:<br />
<?php $galleryCounter = 0; ?>
<?php foreach ($content["images"] as $image): ?>
    <?php $galleryCounter++; ?>
    <label>Name:
        <input
            type="text"
            name="<?php echo
                "article_" . $data->article["buttonId"] .
                 "_gallery_" . $counter .
                 "_image_" . $galleryCounter . "_title";
            ?>"
            value="<?php echo $image["title"] ?>"
        />
    </label>
    <label>File:
        <select
            name="<?php echo
                "article_" . $data->article["buttonId"] .
                "_gallery_" . $counter . 
                "_image_" . $galleryCounter . "_file";
            ?>"
        >
            <option value=""></option>
            <?php foreach ($data->pictures as $fileName): ?>
                <option value="<?php echo $fileName; ?>" <?php echo ($image["file"] === $fileName) ? "selected" : null; ?>>
                    <?php echo strlen($fileName) > 100 ? substr($fileName, 0, 100) . "..." : $fileName; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <br />
<?php endforeach; ?>
<label>Name:
    <input
        type="text"
        name="<?php echo
            "article_" . $data->article["buttonId"] .
            "_gallery_" . $counter .
            "_image_" . ($galleryCounter + 1) . "_title";
        ?>"
        value=""
    />
</label>
<label>File:
    <select
        name="<?php echo
            "article_" . $data->article["buttonId"] .
            "_gallery_" . $counter .
            "_image_" . ($galleryCounter + 1) . "_file";
        ?>"
    >
        <option value=""></option>
        <?php foreach ($data->pictures as $fileName): ?>
            <option value="<?php echo $fileName; ?>">
                <?php echo strlen($fileName) > 100 ? substr($fileName, 0, 100) . "..." : $fileName; ?>
            </option>
        <?php endforeach; ?>
    </select>
</label>
<br /><br /><br />
<hr>
