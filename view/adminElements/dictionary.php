<form action="<?php echo Config::getSite() . "&request=admin&extension=dictionary"; ?>" method="post">
    <fieldset>
        <legend>Dictionary</legend>
        <div class="hint">HINT
            <?php if (Config::getDefaultLanguage() !== "en"): ?>
                <p>Although administration panel is by default in en, language of Your page visible for user is set to <?php echo Config::getDefaultLanguage(); ?></p>
                <p>Default messages for user are in en so You should add translations to each in Your default language which is <?php echo Config::getDefaultLanguage(); ?></p>
            <?php endif; ?>
            <p>Add language and confirm to enlarge number of languages in selector for translation, it will add link in the page header for changing page language</p>
            <p>Language code has to contain 2 characters, see <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">language codes</a></p>
            <p>You can add image which will be displayed instead of language code, country flag is suggested (30px x 15px)</p>
            <p>To change image of existing language code write code in add language field, select image and confirm, current language code will be overwritten</p>
            <p>Default language of the page is <?php echo Config::getDefaultLanguage(); ?> so if you add for instance language fr you should also add <?php echo Config::getDefaultLanguage(); ?>, so user can change back to <?php echo Config::getDefaultLanguage(); ?></p>
            <p>If you want to change default en messages (for instance E-mail is required) you can add en translation for this message, it will be overwritten</p>
            <p>To add or edit translation choose one from prepare for translation selector and confirm (in square parenthesis are noted existing translations)</p>
            <p>Selector for preparing translation contains all default messages and all texts which you added including articles, buttons and names etc.</p>
            <p>Check delete unused translations and confirm to clear dictionary, it will not delete used translations</p>
        </div>
        <p class="error"><?php echo $data->error; ?></p>
        <p class="message"><?php echo $data->message; ?></p>
        <hr /><br />
        <label>Add language:
            <input type="text" size="2" maxlength="2" name="language_value" value="">
        </label>
        <label>Flag image:
            <select name="<?php echo "language_image"; ?>">
                <option value=""></option>
                <?php foreach ($data->pictures as $fileName): ?>
                    <option value="<?php echo $fileName; ?>">
                        <?php echo strlen($fileName) > 100 ? substr($fileName, 0, 100) . "..." : $fileName; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Delete language:
            <select name="<?php echo "language_delete"; ?>">
                <option value=""></option>
                <?php foreach ($data->codes as $code => $image): ?>
                    <option value="<?php echo $code; ?>">
                        <?php echo $code; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <br /><br />
        <hr />
        <label>
            <input type="checkbox" name="dictionary_clear" value="1">
            Delete unused translations
        </label><br /><br />
        <label>Prepare for translation:
            <select name="<?php echo "dictionary_string"; ?>">
                <option value=""></option>
                <?php foreach ($data->strings as $string => $codes): ?>
                    <option value="<?php echo $string ?>">
                        <?php echo '[' . $codes . ']'; ?>
                        <?php echo strlen($string) > 100 ? substr($string, 0, 100) . "..." : $string; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <input type="submit" value="<?php echo "Confirm"; ?>" />
    </fieldset>
</form>
<?php if (!empty($data->translation)): ?>
    <form action="<?php echo Config::getSite() . "&request=admin&extension=dictionary"; ?>" method="post">
        <fieldset>
            <legend>Translator</legend>
            <div class="hint">HINT
                <p>Choose language for translation and fill up the translation field and confirm to add translation</p>
            </div>
            <hr />
            <label>Base text:
                <textarea
                    rows="4"
                    name="translate_base"
                    readonly
                ><?php echo $data->translation["base"]; ?></textarea>
            </label>
            <?php if (!empty($data->translation["codes"])): ?>
                <?php foreach ($data->translation["codes"] as $code => $string): ?>
                <label><?php echo "Translation for " . $code . ":"; ?>
                    <textarea
                        rows="4"
                        name="translate_preview"
                        readonly
                    ><?php echo $string; ?></textarea>
                </label>
                <?php endforeach; ?>
            <?php endif; ?>
            <label>Translate to:
                <select name="<?php echo "translate_code"; ?>">
                    <?php foreach ($data->codes as $code => $image): ?>
                        <option value="<?php echo $code ?>">
                            <?php echo $code ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <textarea
                    rows="4"
                    name="translate_translation"
                ></textarea>
            </label>
            <input type="submit" value="<?php echo "Confirm"; ?>" />
        </fieldset>
    </form>
<?php endif; ?>
