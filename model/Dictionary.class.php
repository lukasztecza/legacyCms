<?php
/**
 * Translation module
 * Needs existing connection accesible via static::$conn
 * Needs database tables: code, dictionary
 * See config/readme.txt for details
 */
class Dictionary extends Model
{
    /* @var Will store prepared statement */
    private static $stmt = null;
    
    /* @var Will store current code */
    private static $code = null;
    
    public static $texts = array(
        "Go to the previous site",                              //0
        "Your e-mail",                                          //1
        "Your message",                                         //2
        "Send",                                                 //3
        "E-mail is required",                                   //4
        "Wrong e-mail address",                                 //5
        "Message is required",                                  //6
        "Message sent",                                         //7
        "Enable javascript in Your browser to see the map",     //8
        "Search for text",                                      //9
        "Search",                                               //10
        "No results",                                           //11
        "Type at least 3 characters",                           //12
        "Wrong username or password, if you do not have account send email to the owner of this page and ask him to open one for you" //13
    );
    
    /**
     * Search for the translation in the dictionary table
     * @param string $base String which is supposed to be translated
     * @return string Translation or passed string if no translation has been found
     */
    public static function get($base, $code = null)
    {
        //get connection
        if (!static::$conn) {
            Model::setModel();
        }
        $conn = static::$conn;
        
        //prepare statement
        if (!static::$stmt) {
            static::$stmt = $conn->prepare("
                SELECT `translation`
                FROM `dictionary`
                WHERE `code` = :code
                AND `base` = :base
                ORDER BY `id` DESC
                LIMIT 1
            ");
        }
        
        //get code for translation
        if (!static::$code) {
            static::$code = Config::getLanguage();
        }
        $code = !empty($code) ? $code : static::$code;
        
        //retrieve translation if exists else return passed string
        $stmt = static::$stmt;
        $stmt->execute(array(
            "code" => $code,
            "base" => $base
        ));
        $return = "";
        while ($row = $stmt->fetch()) {
            $return = $row["translation"];
        }
        return empty($return) ? $base : $return;
    }
    
    /**
     * Prepare connection for dictionary, use model connection
     */
    public function __construct()
    {
        //prepare connection if not set
        if (!static::$conn) {
            Model::setModel();
        }
        $this->connection = static::$conn;
    }
    
    /**
     * Prepare strings for translation
     * @return array with strings which can be translated
     */    
    public function getStrings()
    {
        //default messages for translation
        $return = static::$texts;
        
        //get connection
        $conn = $this->connection;
        
        //retrieve button names
        $stmt = $conn->prepare("
            SELECT `name`
            FROM `button`
            WHERE `is_active` = 1
        ");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            if (!empty($row["name"])) {$return[] = $row["name"];}
        }
        
        //retrieve article titles
        $stmt = $conn->prepare("
            SELECT `article`.`title`
            FROM `article`
            JOIN `button` ON `button`.`id` = `article`.`button_id`
            WHERE `button`.`is_active` = 1
        ");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            if (!empty($row["title"])) {$return[] = $row["title"];}
        }
        
        //retrieve slider contets
        $stmt = $conn->prepare("
            SELECT `content`
            FROM `slider`
            WHERE `type` = 'contact'
        ");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $description = json_decode($row["content"], true)["description"];
            if (!empty($description)) {$return[] = $description;}        
        }
        $stmt = $conn->prepare("
            SELECT `content`
            FROM `slider`
            WHERE `type` = 'sidebox' OR `type` = 'searchbar'
        ");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            if (!empty($row["content"])) {$return[] = $row["content"];}
        }
        
        //retrieve article contents
        $stmt = $conn->prepare("
            SELECT `article`.`content`
            FROM `article`
            JOIN `button` ON `button`.`id` = `article`.`button_id`
            WHERE `button`.`is_active` = 1
        ");
        $stmt->execute();
        $contents = array();
        while ($row = $stmt->fetch()) {
            $content = json_decode($row["content"], true);
            $contents[] = !empty($content) ? $content : array();
        }
        
        // retrieve strings to translate from contents array
        foreach ($contents as $content => $elements) {
            if (!empty($elements)) {
                foreach ($elements as $element => $data) {
                    $name = explode("_", $element)[0];
                    switch ($name) {
                        case "paragraph":
                            if (!empty($data["text"])) {$return[] = $data["text"];}
                            break;
                        case "list":
                            foreach ($data["rows"] as $item) {
                                if (!empty($item["text"])) {$return[] = $item["text"];}
                            }
                            break;
                        case "table":
                            if (!empty($data["caption"])) {$return[] = $data["caption"];}
                            foreach ($data["rows"] as $tableRow) {
                                foreach ($tableRow as $tableCell) {
                                    if (!empty($tableCell)) {$return[] = $tableCell;}
                                }
                            }
                            break;
                        case "file":
                            if (!empty($data["title"])) {$return[] = $data["title"];}
                            break;
                        case "image":
                            if (!empty($data["title"])) {$return[] = $data["title"];}
                            break;
                        case "gallery":
                            foreach ($data["images"] as $image) {
                                if (!empty($image['title'])) {$return[] = $image['title'];}
                            }
                            break;
                        case "map":
                            foreach ($data["points"] as $point) {
                                if (!empty($point['description'])) {$return[] = $point['description'];}
                            }
                            break;
                        case "contact":
                            if (!empty($data["description"])) {$return[] = $data["description"];}
                            break;
                    }
                }
            }
        }
        
        //return sorted array of strings which can be translated with currently available translations
        sort($return, SORT_NATURAL | SORT_FLAG_CASE);
        $returnWithCodes = array();
        $stmt = $conn->prepare("
            SELECT GROUP_CONCAT(`code`) AS `codes`
            FROM `dictionary`
            WHERE `base` = :base
        ");
        foreach ($return as $key => $base) {
            $stmt->execute(array(
                'base' => $base
            ));
            $codes = null;
            while ($row = $stmt->fetch()) {
                if (!empty($row["codes"])) {$codes = $row["codes"];}
            }
            $returnWithCodes[$base] = $codes;
        }
        return $returnWithCodes;
    }
    
    /**
     * Get all codes
     * @return array Array contains: code => image
     */
    public function getCodes()
    {
        $conn = $this->connection;
        $stmt = $conn->prepare("
            SELECT `code`, `image`
            FROM `code`
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return[$row["code"]] = $row["image"];
        }
        return $return;
    }
    
    /**
     * Add code
     * @param string $code Code
     * @param string $image Codes image
     */
    public function addCode($code, $image)
    {
        $conn = $this->connection;
        $stmt = $conn->prepare("
            SELECT `code`
            FROM `code`
            WHERE `code` = :code
        ");
        $stmt->execute(array(
            "code" => $code
        ));
        $existingCode = null;
        while ($row = $stmt->fetch()) {
            $existingCode = $row["code"];
        }
        
        //update code if exists else insert new
        if (empty($existingCode)) {
            $stmt = $conn->prepare("
                INSERT INTO `code` (`code`, `image`)
                VALUES (:code, :image)
            ");
            $stmt->execute(array(
                "code" => $code,
                "image" => $image
            ));
        } else {
            $stmt = $conn->prepare("
                UPDATE `code`
                SET `image` = :image
                WHERE `code` = :code
            ");
            $stmt->execute(array(
                "code" => $code,
                "image" => $image
            ));
        }
    }
    
    /**
     * Delete code
     * @param string $code Code
     */
    public function deleteCode($code)
    {
        $conn = $this->connection;
        $stmt = $conn->prepare("
            DELETE FROM `code`
            WHERE `code` = :code
        ");
        $stmt->execute(array(
            "code" => $code
        ));
    }
    
    /**
     * Add translation
     * @param string $base Default text for translation
     * @param string $code Translation language
     * @param string $translation Value of the translation 
     */
    public function addTranslation($base, $code, $translation)
    {
        $conn = $this->connection;
        $stmt = $conn->prepare("
            INSERT INTO `dictionary` (`base`, `code`, `translation`)
            VALUES (:base, :code, :translation)
        ");
        $stmt->execute(array(
            "base" => $base,
            "code" => $code,
            "translation" => $translation
        ));
    }
    
    /**
     * Delete all unused translations from database
     */
    public function clearDictionary()
    {
        $usedStrings = $this->getStrings();
        $usedCodes = $this->getCodes();
        $usedIds = array();
        $conn = $this->connection;
        
        //leave only latest translation for each used string
        foreach ($usedStrings as $base) {
            //devide process by country code 
            foreach ($usedCodes as $code => $image) {
                //get ids of all translations for base
                $stmt = $conn->prepare("
                    SELECT `id` 
                    FROM `dictionary`
                    WHERE `base` = :base
                    AND `code` = :code
                    ORDER BY `id` DESC
                ");
                $stmt->execute(array(
                    "base" => $base,
                    "code" => $code
                ));
                $ids = array();
                while ($row = $stmt->fetch()) {
                    $ids[] = $row["id"];
                }
                
                //if translations exist delete all except latest which store in $usedIds
                if (!empty($ids)) {
                    $usedIds[] = array_shift($ids);
                    //delete translations which are old
                    $stmt = $conn->prepare("
                        DELETE FROM `dictionary`
                        WHERE `id` = :id
                    ");
                    foreach ($ids as $id) {
                        $stmt->execute(array(
                            "id" => $id
                        ));
                    }
                }
            }
        }
        
        //delete all unused translations
        $stmt = $conn->prepare("
            SELECT `id` 
            FROM `dictionary`
        ");
        $stmt->execute();
        $allIds = array();
        while ($row = $stmt->fetch()) {
            $allIds[] = $row["id"];
        }
        $ids = array_diff($allIds, $usedIds);
        $stmt = $conn->prepare("
            DELETE FROM `dictionary`
            WHERE `id` = :id
        ");
        foreach ($ids as $id) {
            $stmt->execute(array(
                "id" => $id
            ));
        }
        
        //delete all recorde which have base same as translation
        $stmt = $conn->prepare("
            DELETE FROM `dictionary` 
            WHERE `base` = `translation`
        ");
        $stmt->execute();
    }
    
}
