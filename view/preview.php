<!Doctype hmtl>
<html>
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview</title>
    <link rel="stylesheet" href="<?php echo Config::getDefaultSite() . "style/defaultpreview.css"; ?>" />
</head>
<body>
    <div class="previewContent">
        <img
            src="<?php echo Config::getDefaultSite() . "uploads/max/" . $preview ?>"
            alt="<?php echo $preview; ?>"
            title="<?php echo $preview; ?>"
        />
        <a href="
            <?php echo Config::getPreviousSite() . "#" . preg_replace(
                '/[^0-9a-z_]/', '', substr(Config::getCurrentQuery(), strpos(Config::getCurrentQuery() , "back=") + 5)
            ); ?>
        "><?php echo Dictionary::get(Dictionary::$texts[0]); ?></a>
    </div>
</body>
</html>
