<div class="gallery" style="display:table;width:100%;">
    <?php $imageNumber = 0; ?>
    <?php $imageSize = $content["columns"] > 3 ? "min" : ($content["columns"] > 1 ? "med" : "max");?>
    <?php $allImages = count($content["images"]) ?>
    <?php foreach ($content["images"] as $image): ?>
        <?php $imageNumber++; ?>
        <?php if (!($inRow = ($imageNumber - 1) % $content["columns"])): ?>
            <div class="galleryRow" style="display:table-row">
        <?php endif; ?>
        <div class="galleryCell" style="display:table-cell" id="<?php echo $element . '_' . $imageNumber; ?>">
            <a href="<?php echo Config::getSite() . "&request=preview&file=" . $image["file"] . '&back=' . $element . '_' . $imageNumber; ?>">
                <img
                    style="width:100%;vertical-align:middle;"
                    src="<?php echo Config::getDefaultSite() . "uploads/" . $imageSize . "/" . $image["file"]; ?>"
                    alt="<?php echo Dictionary::get($image["title"]); ?>"
                    title="<?php echo Dictionary::get($image["title"]); ?>"
                />
            </a>
        </div>
        <?php if ($inRow === ($content["columns"] - 1) || $imageNumber >= $allImages): ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
