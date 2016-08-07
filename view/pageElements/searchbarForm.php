<form
    action="<?php echo Config::getSite() . "&request=page&article=" . $data->buttonId . "#search"; ?>"
    method="post"
    autocomplete="off"
    charset="utf-8"
    id="search"
>
    <fieldset>
        <legend><?php echo Dictionary::get($data->description); ?></legend>
        <label>
            <input
                type="text"
                name="search"
                placeholder="<?php echo Dictionary::get(Dictionary::$texts[9]); ?>"
                value="<?php echo $data->pattern; ?>"
            >
        </label><br/>
        <input type="submit" value="<?php echo Dictionary::get(Dictionary::$texts[10]); ?>">
        <ul>
        <?php foreach ($data->results as $result): ?>
            <li>
                <a href="<?php echo Config::getSite() . "&request=page&article=" . $result["buttonId"]; ?>">
                    <?php echo Dictionary::get($result["title"]); ?>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
        <p class="error"><?php echo $data->searchError; ?></p>
    </fieldset>
</form>
