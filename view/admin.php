<!Doctype hmtl>
<html>
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?php echo Config::getDefaultSite() . "style/graphics/adminfavicon.ico"; ?>" type="image/x-icon" />
    <title>Administration panel</title>
    <link rel="stylesheet" href="<?php echo Config::getDefaultSite() . "style/admin.css"; ?>" />
</head>
<body>
    <h1>Administration panel</h1>
    <?php  if ($loggedIn["group"] === "admin"): ?>
        <h2>
            <?php echo "Welcome " . $loggedIn["login"] . ", remember to log out when You finish"; ?>
        </h2>
        <a href="<?php echo Config::getSite() ."&request=admin&extension=menu"?>">Menu</a>
        <a href="<?php echo Config::getSite() ."&request=admin&extension=buttons"?>">Buttons</a>
        <a href="<?php echo Config::getSite() ."&request=admin&extension=articles"?>">Articles</a>
        <a href="<?php echo Config::getSite() ."&request=admin&extension=images"?>">Images</a>
        <a href="<?php echo Config::getSite() ."&request=admin&extension=files"?>">Files</a>
        <a href="<?php echo Config::getSite() ."&request=admin&extension=sliders"?>">Sliders</a>
        <a href="<?php echo Config::getSite() ."&request=admin&extension=dictionary"?>">Dictionary</a>
        <a href="<?php echo Config::getSite() ."&request=admin&extension=password"?>">Password</a>
        <a href="<?php echo Config::getSite() ."&request=admin&extension=users"?>">Users</a>
        <a href="<?php echo Config::getSite() ."&request=admin&action=logout"?>">Log out</a>
        <?php Controller::load("tools"); ?>
    <?php else: ?>
        <a href="<?php  echo Config::getSite() . "&request=page"; ?>">Return to default page</a>
        <?php Controller::load("login"); ?>
        <?php Controller::load("generate"); ?>
    <?php  endif; ?>
    <script src="<?php echo Config::getDefaultSite() . "script/info.js"; ?>"></script>
</body>
</html>
