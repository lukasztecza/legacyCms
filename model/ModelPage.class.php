<?php
/**
 * Database queries for page
 * Model methods are executed with $conn and optionally $parameters arguments
 * Model methods should return arrays
 * Needs database tables: menu, button, article, slider(filled)
 * See config/readme.txt for details
 */
class ModelPage extends Model
{
    /** Retrieve default button from database */
    protected function getDefaultButton($conn)
    {
        $stmt = $conn->prepare("
            SELECT `article`.`button_id`, `article`.`title`, `article`.`content`
            FROM `article`
            JOIN `button` ON `button`.`id` = `article`.`button_id`
            JOIN `menu` ON `menu`.`button_id` = `button`.`id`
            AND `button`.`is_active` = 1
            AND `menu`.`button_id` = `menu`.`parent_button_id`
            ORDER BY `menu`.`id` ASC
            LIMIT 1;
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "buttonId" => $row["button_id"]
            );
        }
        return $return;
    }
    
    /** Retrieve parent button for passed button id */
    protected function getParentButtonById($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            SELECT `parent_button_id`
            FROM `menu`
            WHERE `button_id` = :buttonId
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"]
        ));
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "parentButtonId" => $row["parent_button_id"]
            );
        }
        return $return;
    }
    
    /** Retrieve buttons from database */
    protected function getButtons($conn)
    {
        $stmt = $conn->prepare("
            SELECT `button`.`id`, `button`.`name`, `button`.`image_first`, `button`.`image_second`
            FROM `button`
            JOIN `menu` ON `menu`.`button_id` = `button`.`id`
            AND `button`.`is_active` = 1
            AND `menu`.`button_id` = `menu`.`parent_button_id`
            ORDER BY `menu`.`id` ASC
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return[] = array(
                "buttonId" => $row["id"],
                "name" => $row["name"],
                "imageFirst" => $row["image_first"],
                "imageSecond" => $row["image_second"]
            );
        }
        return $return;
    }
    
    /** Retrieve active article from database by button id */
    protected function getArticleByButtonId($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            SELECT `article`.`button_id`, `article`.`title`, `article`.`content`, `button`.`secured`
            FROM `article`
            JOIN `button` ON `button`.`id` = `article`.`button_id`
            WHERE `button_id` = :buttonId
            AND `button`.`is_active` = 1
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"]
        ));
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "buttonId" => $row["button_id"],
                "title" => $row["title"],
                "content" => $row["content"],
                "secured" => $row["secured"]
            );
        }
        return $return;
    }
    
    /** Get script string */
    protected function getScriptStringById($conn, $parameters = array()) {
        $stmt = $conn->prepare("
            SELECT `script`.`string`
            FROM `script`
            WHERE `script`.`id` = :id
        ");
        $stmt->execute(array(
            "id" => $parameters["id"]
        ));
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "string" => $row["string"]
            );
        }
        return $return;
    }
    
    /** Get slider elements */
    protected function getSliderElements($conn)
    {
        $stmt = $conn->prepare("
            SELECT `type`, `content`
            FROM `slider`
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return[] = array(
                "type" => $row["type"],
                "content" => $row["content"]
            );
        }
        return $return;
    }
    
    /** Get submenu buttons of corresponding button */
    protected function getSubmenuById($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            SELECT `menu`.`button_id`, `button`.`name`, `button`.`image_first`, `button`.`image_second`
            FROM `menu`
            JOIN `button` ON `button`.`id` = `menu`.`button_id`
            AND `menu`.`parent_button_id` = :buttonId
            AND `menu`.`button_id` <> `menu`.`parent_button_id`
            AND `button`.`is_active` = 1
            ORDER BY `menu`.`id`
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"]
        ));
        $return = array();
        while ($row = $stmt->fetch()) {
            $return[] = array(
                "buttonId" => $row["button_id"],
                "name" => $row["name"],
                "imageFirst" => $row["image_first"],
                "imageSecond" => $row["image_second"],
            );
        }
        return $return;
    }
    
    /** Get articles or buttons which contain passed string */
    protected function getArticlesByPattern($conn, $parameters = array())
    {
        //if dictionary is used prepare base of translations for search
        if (class_exists("Dictionary")) {
            //get bases of translations where search text was found
            $stmt = $conn->prepare("
                SELECT `base`
                FROM `dictionary`
                WHERE LOWER(`translation`) LIKE LOWER(:search)
            ");
            $stmt->execute(array(
                "search" => "%" . $parameters["search"] . "%"
            ));
            $bases = array();
            while ($row = $stmt->fetch()) {
                $bases[] = $row["base"];
            }
            
            //get results for prepared bases
            $stmt = $conn->prepare("
                SELECT `article`.`button_id`, `article`.`title`
                FROM `article`
                JOIN `button` ON `button`.`id` = `article`.`button_id`
                AND `button`.`is_active` = 1
                WHERE LOWER(`article`.`content`) LIKE LOWER(:search1)
                OR LOWER(`article`.`title`) LIKE LOWER(:search2)
                OR LOWER(`button`.`name`) LIKE LOWER(:search3)
                GROUP BY `article`.`button_id`
                ORDER BY `article`.`button_id`
            ");
            $return = array();
            foreach ($bases as $base) {
                $stmt->execute(array(
                    "search1" => "%" . $base . "%",
                    "search2" => "%" . $base . "%",
                    "search3" => "%" . $base . "%"
                )); 
                while ($row = $stmt->fetch()) {
                    $return[] = array(
                        "buttonId" => $row["button_id"],
                        "title" => $row["title"]
                    );
                }
            }
        }
        
        //get default search results
        $stmt = $conn->prepare("
            SELECT `article`.`button_id`, `article`.`title`
            FROM `article`
            JOIN `button` ON `button`.`id` = `article`.`button_id`
            AND `button`.`is_active` = 1
            WHERE LOWER(`article`.`content`) LIKE LOWER(:search1)
            OR LOWER(`article`.`title`) LIKE LOWER(:search2)
            OR LOWER(`button`.`name`) LIKE LOWER(:search3)
            GROUP BY `article`.`button_id`
            ORDER BY `article`.`button_id`
        ");
        $stmt->execute(array(
            "search1" => "%" . $parameters["search"] . "%",
            "search2" => "%" . $parameters["search"] . "%",
            "search3" => "%" . $parameters["search"] . "%"
        ));
        if (empty($return)) {
            $return = array();
        }
        while ($row = $stmt->fetch()) {
            $return[] = array(
                "buttonId" => $row["button_id"],
                "title" => $row["title"]
            );
        }
        
        //remove duplicated records
        $return = array_unique($return, SORT_REGULAR);
        return $return;
    }
    
}
