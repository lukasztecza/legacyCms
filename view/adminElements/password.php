<form action="<?php echo Config::getSite() . "&request=admin&action=password&extension=password"?>" method="post">
    <fieldset>
        <div class="hint">HINT
            <p>Fill up new password and confirm to change your password</p>
            <p>Password has to contain at least 6 characters</p>
            <p>Password must not contain \ character</p>
        </div>
        <legend>Change Your password</legend>
        <p class="error"><?php echo $data->error; ?></p>
        <p class="message"><?php echo $data->message; ?></p>
        New password:
        <input
            type="password"
            name="password"
        >
        <input type="submit" value="<?php echo "Confirm"; ?>" />
    </fieldset>
</form>
