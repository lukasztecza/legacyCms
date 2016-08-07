<?php include(Config::getDirectory() . "view/pageElements/nav.php"); ?>
<article>
    <?php if (!empty($data->article)): ?>
        <?php  if (!$data->article["secured"] || ($data->article["secured"] && $data->loggedIn)): ?>
            <h1><?php echo Dictionary::get($data->article["title"]); ?></h1>
            <?php if (!empty($data->article["elements"])): ?>
                <?php foreach ($data->article["elements"] as $element => $content): ?>
                    <?php include(Config::getDirectory() . "view/pageElements/templates/" . substr($element, 0, strpos($element, "_")) . ".php"); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php else: ?>
            <form action="<?php echo Config::getSite() . "&request=page&article=" . $data->article["buttonId"] . "&action=login"; ?>" method="post" charset="utf-8">
                <fieldset>
                    <legend>If you do not have access data send email to owner of the page so he can create an account for you</legend>
                    <label><input type="text" name="login" placeholder="Username" /></label><br />
                    <label><input type="password" name="password" placeholder="Password" /></label>
                    <div class="error"><?php echo $data->loginError; ?></div>
                    <input type="submit" value="<?php echo "Login"; ?>">
                </fieldset>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</article>
