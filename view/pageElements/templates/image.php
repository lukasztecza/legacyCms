<?php if ($content["type"] === "100%"): ?>
    <img
         style="width:100%"
         src="<?php echo Config::getDefaultSite() . "uploads/max/" . $content["file"]; ?>"
         alt="<?php echo Dictionary::get($content["title"]); ?>"
         title="<?php echo Dictionary::get($content["title"]); ?>"
    />
<?php elseif($content["type"] === "left"): ?>
    <img
         style="width:30%;float:left;margin-right:5px;"
         src="<?php echo Config::getDefaultSite() . "uploads/med/" . $content["file"]; ?>"
         alt="<?php echo Dictionary::get($content["title"]); ?>"
         title="<?php echo Dictionary::get($content["title"]); ?>"
    />
<?php elseif($content["type"] === "right"): ?>
    <img
         style="width:30%;float:right;margin-left:5px;"
         src="<?php echo Config::getDefaultSite() . "uploads/med/" . $content["file"]; ?>"
         alt="<?php echo Dictionary::get($content["title"]); ?>"
         title="<?php echo Dictionary::get($content["title"]); ?>"
    />
<?php elseif($content["type"] === "centered"): ?>
    <img
         style="width:40%;margin-left:30%;"
         src="<?php echo Config::getDefaultSite() . "uploads/med/" . $content["file"]; ?>"
         alt="<?php echo Dictionary::get($content["title"]); ?>"
         title="<?php echo Dictionary::get($content["title"]); ?>"
    />
<?php endif; ?>

