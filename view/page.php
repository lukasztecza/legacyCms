<!Doctype hmtl>
<html lang="<?php echo Config::getLanguage(); ?>">
<head>
    <meta charset="utf-8" />
    <meta name="description" content="Your description" />
    <meta name="keywords" content="Your content" />
    <meta name="author" content="You" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="<?php echo Config::getDefaultSite() . "style/graphics/favicon.ico"; ?>" type="image/x-icon" />
    <title>Your title</title>
    <?php echo Config::getCss(); ?>
</head>
<body>
    <?php //internet explorer exlusion ?>
    <!--[if gt IE 8]><!-->
        <script>
            //for chrome smooth unfade effect, add only not for mobile
            document.querySelector("body").style.visibility = "hidden";            
        </script>
    <!--<![endif]-->
    
    <?php //blocks to include ?>
    <?php Controller::load("header"); ?>
    <?php Controller::load("nav"); ?>
    <?php Controller::load("section"); ?>
    <?php Controller::load("footer"); ?>
    
    <?php //internet explorer exlusion ?>
    <!--[if gt IE 8]><!-->
        <?php  echo Config::getJs(); ?>
    <!--<![endif]-->
</body>
</html>
