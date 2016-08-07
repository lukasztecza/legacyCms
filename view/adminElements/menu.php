<form action="<?php echo Config::getSite() . "&request=admin&extension=menu"?>" method="post">
    <fieldset>
        <legend>Menu editor</legend>
        <div class="hint">HINT
            <p>You need at least one button added in buttons section to edit menu</p>
            <p>On the left there are main buttons which can contain lower level buttons which are on the right</p>
            <p>Select button and confirm to add it</p>
            <p>Select empty field and confirm to delete button</p>
            <p>If deleted main button contained lower level buttons, all will be shifted to higher main button</p>            
            <p>Buttons can not duplicate, if they do then only first occurrence will be saved</p>
        </div>
        <p class="error"><?php echo $data->error; ?></p>
        <p class="message"><?php echo $data->message; ?></p>
        <hr />
        <?php $counter = 1; ?>
        <?php foreach ($data->menu as $item): ?>
            <label>Main button:
                <select name="<?php echo "menu_" . $counter; ?>">
                    <option value=""></option>
                    <?php foreach ($data->buttons as $button): ?>
                        <option value="<?php echo $button["id"]; ?>" <?php  echo ($button["name"] === $item["name"]) ? "selected" : null; ?>>
                            <?php echo strlen($button["name"]) > 100 ? substr($button["name"], 0, 100) . "..." : $button["name"]; ?>
                        </option>
                    <?php endforeach; ?>
                </select>                
            </label>
            <br />
            <br />
            <?php $subcounter = 1; ?>
            <?php if (!empty($item["submenu"])): ?>
                <?php foreach ($item["submenu"] as $subitem): ?>
                    <label class="leftindent">
                        <select name="<?php echo "menu_" . $counter . "_" . $subcounter; ?>">
                            <option value=""></option>
                            <?php foreach ($data->buttons as $button): ?>
                                <option value="<?php echo $button["id"]; ?>" <?php  echo ($button["name"] === $subitem["name"]) ? "selected" : null; ?>>
                                    <?php echo strlen($button["name"]) > 100 ? substr($button["name"], 0, 100) . "..." : $button["name"]; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <br />
                    <br />
                    <?php $subcounter++; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <label class="leftindent">
                <select name="<?php echo "menu_" . $counter . "_" . $subcounter; ?>">
                    <option value=""></option>
                    <?php foreach ($data->buttons as $button): ?>
                        <option value="<?php echo $button["id"]; ?>">
                            <?php echo strlen($button["name"]) > 100 ? substr($button["name"], 0, 100) . "..." : $button["name"]; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <br />
            <hr />
            <?php $counter++; ?>
        <?php endforeach; ?>
        <label>Main button:
            <select name="<?php echo "menu_" . $counter; ?>">
                <option value=""></option>
                <?php foreach ($data->buttons as $button): ?>
                    <option value="<?php echo $button["id"]; ?>">
                        <?php echo strlen($button["name"]) > 100 ? substr($button["name"], 0, 100) . "..." : $button["name"]; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <br />
        <br />
        <label class="leftindent">
            <select name="<?php echo "menu_" . $counter . "_1"; ?>">
                <option value=""></option>
                <?php foreach ($data->buttons as $button): ?>
                    <option value="<?php echo $button["id"]; ?>">
                        <?php echo strlen($button["name"]) > 100 ? substr($button["name"], 0, 100) . "..." : $button["name"]; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <hr />
        <input type="submit" value="Confirm" />
    </fieldset>
</form>
