<div
    id="map"
    <?php if ($content["size"] === "huge"): ?>
        style="width:100%;height:600px;"    
    <?php elseif ($content["size"] === "large"): ?>
        style="width:100%;height:300px;"
    <?php elseif ($content["size"] === "small"): ?>
        style="margin-left:20%;width:60%;height:300px;"
    <?php endif; ?>
    <?php $counter = 0; ?>
    <?php foreach ($content["points"] as $point): ?>
        <?php $counter++; ?>
        data-description-<?php echo $counter; ?>="<?php echo Dictionary::get($point["description"]); ?>"
        data-latitude-<?php echo $counter; ?>="<?php echo $point["latitude"]; ?>"
        data-longitude-<?php echo $counter; ?>="<?php echo $point["longitude"]; ?>"
    <?php endforeach; ?>
>
    <?php echo Dictionary::get(Dictionary::$texts[8]); ?>
</div>
