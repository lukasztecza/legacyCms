Map:<br />
<div class="hint">HINT
    <p>Fill up point description and confirm to add point to the map</p>
    <p>Delete all points descriptions to delete map</p>
    <p>An article can contain one map</p>
    <p>Take coordinates from <a href="https://www.google.pl/maps" target="_blank">google maps</a>, click with right button on the map and select "What's here?", coordinates will appear at the bottom</p>
</div>
<br />
<label>Size:
    <select name="<?php echo "article_" . $data->article["buttonId"] . "_map_" . $counter . "_size"; ?>">
        <option value="huge" <?php echo ($content["size"] === "huge") ? "selected" : null; ?>>huge</option>
        <option value="large" <?php echo ($content["size"] === "large") ? "selected" : null; ?>>large</option>
        <option value="small" <?php echo ($content["size"] === "small") ? "selected" : null; ?>>small</option>
    <select>
</label><br />
Points:<br />
<?php $pointNumber = 0; ?>
<?php foreach ($content["points"] as $point): ?>
    <?php $pointNumber++; ?>
    <label>Description:
        <input
            type="text"
            name="<?php echo "article_" . $data->article["buttonId"] . "_map_" . $counter . "_point_" . $pointNumber . "_description"; ?>"
            value="<?php echo $point["description"]; ?>"
        />
    </label>
    <label>Latitude:
        <input
            type="number"
            name="<?php echo "article_" . $data->article["buttonId"] . "_map_" . $counter . "_point_" . $pointNumber . "_latitude"; ?>"
            value="<?php echo $point["latitude"]; ?>"
            step="0.000001"
        />
    </label>
    <label>Longitude:
        <input
            type="number"
            name="<?php echo "article_" . $data->article["buttonId"] . "_map_" . $counter . "_point_" . $pointNumber . "_longitude"; ?>"
            value="<?php echo $point["longitude"]; ?>"
            step="0.000001"
        />
    </label><br />
<?php endforeach; ?>
<label>Description:
    <input
        type="text"
        name="<?php echo "article_" . $data->article["buttonId"] . "_map_" . $counter . "_point_" . ($pointNumber + 1) . "_description"; ?>"
        value=""
    />
</label>
<label>Latitude:
    <input
        type="number"
        name="<?php echo "article_" . $data->article["buttonId"] . "_map_" . $counter . "_point_" . ($pointNumber + 1) . "_latitude"; ?>"
        value=""
        step="0.000001"
    />
</label>
<label>Longitude:
    <input
        type="number"
        name="<?php echo "article_" . $data->article["buttonId"] . "_map_" . $counter . "_point_" . ($pointNumber + 1) . "_longitude"; ?>"
        value=""
        step="0.000001"
    />
    </label>
<br /><br /><br />
<hr />
