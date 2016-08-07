<form action="<?php echo Config::getSite() . "&request=admin&extension=files"; ?>" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Files</legend>
        <div class="hint">HINT
            <p>Add a file to enlarge number of files in selectors in article files</p>
            <p>Accepted extensions are: txt, pdf, odt, ods, doc, docx, xls, xlsx, odp, ppt, pptx, mp3</p>
        </div>
        <p class="error"><?php echo $data->error; ?></p>
        <p class="message"><?php echo $data->message; ?></p>
        <hr />
        <label>
            <input type="checkbox" name="files_clear" value="1">
            Delete unused files
        </label><br /><br />
        <label><?php echo "Upload a file: "; ?></sapn>
            <input
                type="file"
                name="<?php echo "varied_file"; ?>"
            />
        </label>
        <input type="submit" value="<?php echo "Confirm"; ?>" />
        <hr />
        <div class="twocolumn">
        <?php foreach ($data->files as $file): ?>
            <span><?php echo $file; ?></span><br />
            <a href="<?php echo Config::getSite() . "&request=admin&extension=files&download=" . $file; ?>">Download</a>
            <a href="<?php echo Config::getSite() . "&request=admin&extension=files&delete=" . $file; ?>">Delete</a>
            <br /><br />
        <?php endforeach; ?>
        </div>
    </fieldset>
</form>
