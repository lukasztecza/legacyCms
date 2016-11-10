<fieldset>
    <legend>Articles editor</legend>
    <div class="hint">HINT
        <p>Click button name to edit article which is assingned to it</p>
    </div>
    <p class="error"><?php echo $data->buttonsError; ?></p>
    <hr />
    <?php foreach ($data->buttons as $button): ?>
    <a href="<?php echo Config::getSite() . "&request=admin&extension=articles&article=" . $button["id"]; ?>">
        <?php echo $button["name"]; ?>
    </a>&nbsp
    <?php endforeach; ?>
</fieldset><br />
<?php if (!empty($data->article)): ?>
    <form
        action="<?php echo Config::getSite() . "&request=admin&extension=articles&article=" . $data->article["buttonId"]; ?>"
        method="post"
    >
        <fieldset>
            <legend><?php echo "Article assigned to button " . $data->article["buttonName"]; ?></legend>
            <div class="hint">HINT
                <p>To change the order in which elements are displayed, change the position and confirm</p>
                <p>Positions are assigned from the top to the bottom, so it is impossible to put element which is in the end into any of the previous position</p>
                <p>To put element into the middle for instance, add element to the beginning and then assign required position to it</p>
                <p>If You want to delete article it is better to delete button which contains it</p>
            </div>
            <p class="error"><?php echo $data->error; ?></p>
            <p class="message"><?php echo $data->message; ?></p>
            <hr />
            <br />
            <div class="hint">HINT
                <p>To change title fill it up and confirm</p>
                <p>Title is not required</p>
            </div>
            <label>Title
                <input
                    type="text"
                    name="<?php echo "article_" . $data->article["buttonId"] . "_title"; ?>"
                    value="<?php echo $data->article["title"] ?>"
                >
            </label>
            <br /><br /><br />
            <hr />
            <?php $counter = 0; ?>
            <?php if($data->article["elements"]): ?>
                <?php foreach ($data->article["elements"] as $element => $content): ?>
                    <?php $counter++; ?>
                    <?php $elementName = substr($element, 0, strpos($element, "_")); ?>
                    <br /><br />
                    <label>Position:
                        <input
                            type="number"
                            name="<?php echo "article_" . $data->article["buttonId"] . "_" . $elementName . "_" . $counter . "_position"; ?>"
                            value="<?php echo $counter; ?>"
                            min="1"
                        />
                    </label>
                    <?php
                        include(Config::getDirectory() . "view/adminElements/subforms/" . $elementName . ".php");
                    ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <br />
            Add element to the beginning:
            <select name="<?php echo "article_" . $data->article["buttonId"] . "_addFieldFirst_" . ($counter + 1); ?>">
                <option value=""></option>
                <option value="<?php echo "paragraph"; ?>">Paragraph</option>
                <option value="<?php echo "list"; ?>">List (links)</option>
                <option value="<?php echo "table"; ?>">Table</option>
                <option value="<?php echo "file"; ?>">File</option>
                <option value="<?php echo "image"; ?>">Image</option>
                <option value="<?php echo "gallery"; ?>">Gallery</option>
                <option value="<?php echo "map"; ?>">Map</option>
                <option value="<?php echo "contact"; ?>">Contact box</option>
                <option value="<?php echo "script"; ?>">Script</option>
            </select>
            Add element to the end:
            <select name="<?php echo "article_" . $data->article["buttonId"] . "_addFieldLast_" . ($counter + 2); ?>">
                <option value=""></option>
                <option value="<?php echo "paragraph"; ?>">Paragraph</option>
                <option value="<?php echo "list"; ?>">List (links)</option>
                <option value="<?php echo "table"; ?>">Table</option>
                <option value="<?php echo "file"; ?>">File</option>
                <option value="<?php echo "image"; ?>">Image</option>
                <option value="<?php echo "gallery"; ?>">Gallery</option>
                <option value="<?php echo "map"; ?>">Map</option>
                <option value="<?php echo "contact"; ?>">Contact box</option>
                <option value="<?php echo "script"; ?>">Script</option>
            </select>
            <br /><br />
            <hr />
            <input type="submit" value="<?php echo "Confirm"; ?>" />
        </fieldset>
    </form>
<?php endif; ?>
