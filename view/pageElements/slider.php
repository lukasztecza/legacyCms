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
        <a id="facebook" href="<?php echo $element["content"]; ?>" target="_blank">
            <img
                 width="50px"
                 height=50px"
                 src="<?php echo Config::getDefaultSite() . "style/graphics/facebook.jpg"; ?>"
                 alt="facebook"
                 title="facebook"
             />
        </a>
    <?php break;case "twitter": ?>
        <a id="twitter" href="<?php echo $element["content"]; ?>" target="_blank">
            <img
                 width="50px"
                 height=50px"
                 src="<?php echo Config::getDefaultSite() . "style/graphics/twitter.jpg"; ?>"
                 alt="twitter"
                 title="twitter"
             />
        </a>
    <?php break;case "youtube": ?>
        <a id="youtube" href="<?php echo $element["content"]; ?>" target="_blank">
            <img
                 width="50px"
                 height=50px"
                 src="<?php echo Config::getDefaultSite() . "style/graphics/youtube.jpg"; ?>"
                 alt="youtube"
                 title="youtube"
             />
        </a>
    <?php break;case "googleplus": ?>
        <a id="googleplus" href="<?php echo $element["content"]; ?>" target="_blank">
            <img
                 width="50px"
                 height=50px"
                 src="<?php echo Config::getDefaultSite() . "style/graphics/googleplus.jpg"; ?>"
                 alt="googleplus"
                 title="googleplus"
             />
        </a>
    <?php break;case "linkedin": ?>
        <a id="linkedin" href="<?php echo $element["content"]; ?>" target="_blank">
            <img
                 width="50px"
                 height=50px"
                 src="<?php echo Config::getDefaultSite() . "style/graphics/linkedin.jpg"; ?>"
                 alt="linkedin"
                 title="linkedin"
             />
        </a>
    <?php break;case "github": ?>
        <a id="github" href="<?php echo $element["content"]; ?>" target="_blank">
            <img
                 width="50px"
                 height=50px"
                 src="<?php echo Config::getDefaultSite() . "style/graphics/github.jpg"; ?>"
                 alt="github"
                 title="github"
             />
        </a>
    <?php break;endswitch; ?>
<?php endforeach; ?>
