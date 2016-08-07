<form action="<?php echo Config::getSite() . "&request=admin&extension=users&action=manage"; ?>" method="post">
    <fieldset>
        <legend>Users</legend>
        <div class="hint">HINT
            <p>If you want to allow user to access secured button contents create an account for him typing his email and confirm</p>
            <p>Every time you add user account an email is send to him with login and password generated for him</p>
            <p>If you want to delete users account so he can not access secured button contents choose his email from removing list and confirm</p>
            <p>If your user lost his password simply remove his account and add new one for same email which will send newly generated login and password to him</p>
        </div>
        <p class="error"><?php echo $data->error; ?></p>
        <p class="message"><?php echo $data->message; ?></p>
        <hr /><br />
        <label>Delete user account:
            <select name="<?php echo "user_delete"; ?>">
                <option value=""></option>
                <?php foreach ($data->users as $email): ?>
                    <option value="<?php echo $email; ?>">
                        <?php echo $email; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <br /><br />
        <label>Type email here to add user account (new login and password will be send on this email):
            <input type="text" name="user_create" value="">
        </label><br /><br />
        
        <hr />
        <input type="submit" value="<?php echo "Confirm"; ?>" />
    </fieldset>
</form>
