<header>
    <div>
        <?php foreach ($data->links as $code => $data): ?>
            <a class="<?php echo Config::getLanguage() == $code ? "active": ""; ?>" href="<?php echo $data["url"]; ?>">
            <?php if (!empty($data["image"])): ?>
                <img
                     width="30px"
                     height="15px"
                     src="<?php echo Config::getDefaultSite() . "uploads/min/" . $data["image"]; ?>"
                     alt="<?php echo $code; ?>"
                     title="<?php echo $code; ?>"
                 />
            <?php else: ?>
                <?php echo $code; ?>
            <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <a style="color:transparent;float:right;font-size:12px;" href="<?php echo Config::getSite() . "&request=admin"; ?>">
        <?php echo "admin"; ?>
    </a>
</header>
