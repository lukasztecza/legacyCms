<form action="<?php echo Config::getSite() . "&request=admin&action=login" ?>" method="post" charset="utf-8">
    <fieldset>
        <p class="error"><?php echo $data->loginError; ?></p>
        <legend>Login form</legend>
        <input type="text" name="login" placeholder="Username" /><br />
        <input type="password" name="password" placeholder="Password" /><br />
        <input type="submit" value="<?php echo "Login"; ?>">
    </fieldset>
</form>
