<div class="table">
    <table style="width:100%;">
        <caption>
            <?php echo Dictionary::get($content["caption"]); ?>
        </caption>
        <?php $header = 1; ?>
        <?php foreach ($content["rows"] as $row): ?>
            <?php if ($header): ?>
                <thead>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <th
                                <?php echo "align=" . $content["align"]; ?>
                                style="
                                    <?php echo "font-weight:" . $content["weight"] . ";"; ?>
                                    <?php echo "font-size:" . $content["size"] . ";"; ?>
                                "
                            ><?php echo Dictionary::get($cell); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                <?php $header = 0; ?>
            <?php else: ?>
                <tr>
                    <?php foreach ($row as $cell): ?>
                        <td
                            <?php echo "align=" . $content["align"]; ?>
                        ><?php echo Dictionary::get($cell); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
