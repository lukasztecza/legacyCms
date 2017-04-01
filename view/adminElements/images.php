<form action="<?php echo Config::getSite() . "&request=admin&extension=images"; ?>" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Images</legend>
        <div class="hint">HINT
            <p>Add an image to enlarge number of images in selectors in article galleries, article images, button images or dictionary codes</p>
            <p>Accepted extensions are: jpg, jpeg, png, gif</p>
            <p>All images will be resized to max width 1000px (medium and minimal version of image will also be stored for gallery or admin panel)</p>
        </div>
        <p class="error"><?php echo $data->error; ?></p>
        <p class="message"><?php echo $data->message; ?></p>
        <hr />
        <label>
            <input type="checkbox" name="images_clear" value="1">
            Delete unused images
        </label><br /><br />
        <label><?php echo "Upload an image: "; ?></sapn>
            <input
                type="file"
                name="<?php echo "image_new_file"; ?>"
            />
        </label>
        <input type="submit" value="<?php echo "Confirm"; ?>" />
        <hr />
        <div class="fourcolumn">
        <?php foreach ($data->pictures as $picture): ?>
            <img
                src="<?php echo Config::getDefaultSite() . "uploads/min/" . $picture; ?>"
                alt="<?php echo $picture; ?>"
                width="60px"
                height="60px"
                title="<?php echo $picture; ?>"
            />
            <a href="<?php echo Config::getSite() . "&request=admin&extension=images&download=" . $picture; ?>">Download</a>
            <a href="<?php echo Config::getSite() . "&request=admin&extension=images&delete=" . $picture; ?>">Delete</a>
            <br /><br />
        <?php endforeach; ?>
        </div>
    </fieldset>
</form>
