<?php if ($content["type"] === "decimal" || $content["type"] === "upper-roman"): ?>
    <ol class="list" style="
        <?php echo "list-style-type:" . $content["type"] . ";"; ?>
        <?php echo "text-align:" . $content["align"] . ";"; ?>
        <?php echo "font-weight:" . $content["weight"] . ";"; ?>
        <?php echo "font-style:" . $content["style"] . ";"; ?>
        <?php echo "font-size:" . $content["size"] . ";"; ?>
        margin: 0 40px;
    ">
        <?php foreach ($content["rows"] as $row): ?>
            <?php if(!empty($row["url"])): ?>
                <li>
                    <a href="<?php echo $row["url"]; ?>" target="_blank">
                        <?php echo Dictionary::get($row["text"]); ?>
                    </a>
                </li>
            <?php else: ?>
                <li><?php echo Dictionary::get($row["text"]); ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
<?php else: ?>
    <ul class="list" style="
        <?php echo "list-style-type:" . $content["type"] . ";"; ?>
        <?php echo "text-align:" . $content["align"] . ";"; ?>
        <?php echo "font-weight:" . $content["weight"] . ";"; ?>
        <?php echo "font-style:" . $content["style"] . ";"; ?>
        <?php echo "font-size:" . $content["size"] . ";"; ?>
        <?php echo  $content["type"] !== "none" ? "margin:0 40px;" : ""; ?>
    ">
        <?php foreach ($content["rows"] as $row): ?>
            <?php if(!empty($row["url"])): ?>
                <li>
                    <a href="<?php echo $row["url"]; ?>" target="_blank">
                        <?php echo Dictionary::get($row["text"]); ?>
                    </a>
                </li>
            <?php else: ?>
                <li><?php echo Dictionary::get($row["text"]); ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
