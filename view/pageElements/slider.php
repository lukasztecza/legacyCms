<?php foreach ($data->content as $element): ?>
    <?php switch ($element["type"]): case "search": ?>
        <?php Controller::load("searchbarForm", array("description" => $element["content"])); ?>
    <?php break;case "contact": ?>
        <?php Controller::load("emailForm", array(
            "email" => $element["content"]["email"],
            "description" => $element["content"]["description"],
            "buttonId" => $data->buttonId
        )); ?>
    <?php break;case "sidebox": ?>
        <div id="sidebox"><div><pre style="white-space:pre-wrap;"><?php echo Dictionary::get($element["content"]); ?></pre></div></div>
    <?php break;case "facebook": ?>
        <a
            style="display:inline-block;width:50px;height:50px;background-image:url(style/graphics/facebook.jpg);"
            id="facebook"
            href="<?php echo $element["content"]; ?>" target="_blank">
        </a>
    <?php break;case "twitter": ?>
        <a
            style="display:inline-block;width:50px;height:50px;background-image:url(style/graphics/twitter.jpg);"
            id="twitter"
            href="<?php echo $element["content"]; ?>" target="_blank">
        </a>
    <?php break;case "youtube": ?>
        <a
            style="display:inline-block;width:50px;height:50px;background-image:url(style/graphics/youtube.jpg);"
            id="youtube"
            href="<?php echo $element["content"]; ?>" target="_blank">
        </a>
    <?php break;case "googleplus": ?>
        <a
            style="display:inline-block;width:50px;height:50px;background-image:url(style/graphics/googleplus.jpg);"
            id="googleplus"
            href="<?php echo $element["content"]; ?>" target="_blank">
        </a>
    <?php break;case "linkedin": ?>
        <a
            style="display:inline-block;width:50px;height:50px;background-image:url(style/graphics/linkedin.jpg);"
            id="linkedin"
            href="<?php echo $element["content"]; ?>" target="_blank">
        </a>
    <?php break;case "github": ?>
        <a
            style="display:inline-block;width:50px;height:50px;background-image:url(style/graphics/github.jpg);"
            id="github"
            href="<?php echo $element["content"]; ?>" target="_blank">
        </a>
    <?php break;endswitch; ?>
<?php endforeach; ?>
