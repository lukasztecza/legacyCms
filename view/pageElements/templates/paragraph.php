<?php if ($content["type"] === "paragraph"): ?>
    <p style="
        <?php echo "text-align:" . $content["align"] . ";"; ?>
        <?php echo "font-weight:" . $content["weight"] . ";"; ?>
        <?php echo "font-style:" . $content["style"] . ";"; ?>
        <?php echo "font-size:" . $content["size"] . ";"; ?>
    "><?php echo nl2br(Dictionary::get($content["text"])); ?></p>
<?php elseif ($content["type"] === "preformatted"): ?>
    <pre style="
        <?php echo "text-align:" . $content["align"] . ";"; ?>
        <?php echo "font-weight:" . $content["weight"] . ";"; ?>
        <?php echo "font-style:" . $content["style"] . ";"; ?>
        <?php echo "font-size:" . $content["size"] . ";"; ?>
        white-space:pre-wrap;
    "><?php echo Dictionary::get($content["text"]); ?></pre>
<?php endif; ?>
