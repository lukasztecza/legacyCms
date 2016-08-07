<form action="<?php echo Config::getSite() . "&request=admin&action=generate" ?>" method="post" charset="utf-8">
    <fieldset>
        <p class="error"><?php echo $data->error; ?></p>
        <p class="message"><?php echo $data->message; ?></p>
        <legend>Forgot password send me an e-mail with new one</legend>
        <input type="text" name="email" placeholder="<?php echo "Your e-mail"; ?>" /><br />
        <input type="submit" value="<?php echo "Send"; ?>">
    </fieldset>
</form>
