<nav>
    <ul>
        <?php foreach ($data->buttons as $button): ?>
            <?php if (!empty($button["imageFirst"]) && !empty($button["imageSecond"])): ?>
                <li class="<?php echo $data->activeButton == $button["buttonId"] ? "active": ""; ?>">
                    <a href="<?php echo Config::getSite() . "&request=page&article=" . $button["buttonId"]; ?>">
                        <div style="<?php echo "background-image: url(" . Config::getDefaultSite() . "uploads/min/" . $button["imageFirst"] . ");"; ?>"></div>
                        <span><?php echo Dictionary::get($button["name"]); ?></span>
                        <div style="<?php echo "background-image: url(" . Config::getDefaultSite() . "uploads/min/" . $button["imageSecond"] . ");"; ?>"></div>
                    </a>
                </li>
            <?php elseif (!empty($button["imageFirst"]) && empty($button["imageSecond"])): ?>
                <li class="<?php echo $data->activeButton == $button["buttonId"] ? "active": ""; ?>">
                    <a href="<?php echo Config::getSite() . "&request=page&article=" . $button["buttonId"]; ?>">
                        <div style="<?php echo "background-image: url(" . Config::getDefaultSite() . "uploads/min/" . $button["imageFirst"] . ");"; ?>"></div>
                        <p><?php echo Dictionary::get($button["name"]); ?></p>
                    </a>
                </li>
            <?php else: ?>
                <li class="<?php echo $data->activeButton == $button["buttonId"] ? "active": ""; ?>">
                    <a href="<?php echo Config::getSite() . "&request=page&article=" . $button["buttonId"]; ?>">
                        <?php echo Dictionary::get($button["name"]); ?>
                    </a>
                </li>                      
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
