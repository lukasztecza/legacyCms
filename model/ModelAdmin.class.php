<?php
/**
 * Database queries for amdin
 * Model methods are executed with $conn and optionally $parameters arguments
 * Model methods should return arrays
 * Needs database tables: menu, button, article, slider(filled)
 * See config/readme.txt for details
 */
class ModelAdmin extends Model
{    
    /** Retrieve active buttons from database */
    protected function getButtons($conn)
    {
        $stmt = $conn->prepare("
            SELECT `button`.`id`, `button`.`name`, `button`.`image_first`, `button`.`image_second`, `button`.`secured`, `menu`.`id` as used
            FROM `button`
            LEFT JOIN `menu` ON `menu`.`button_id` = `button`.`id`
            WHERE `button`.`is_active` = 1
            GROUP BY `button`.`id`
            ORDER BY LOWER(`button`.`name`) ASC
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return[] = array(
                "id" => $row["id"],
                "name" => $row["name"],
                "imageFirst" => $row["image_first"],
                "imageSecond" => $row["image_second"],
                "secured" => $row["secured"],
                "used" => $row["used"]
            );
        }
        return $return;
    }
    
    /** Retrieve inactive buttons from database */
    protected function getInactiveButtons($conn)
    {
        $stmt = $conn->prepare("
            SELECT `button`.`id`, `button`.`name`, `button`.`image_first`, `button`.`image_second`
            FROM `button`
            WHERE `button`.`is_active` = 0
            ORDER BY LOWER(`button`.`name`) ASC
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return[] = array(
                "id" => $row["id"],
                "name" => $row["name"],
                "imageFirst" => $row["image_first"],
                "imageSecond" => $row["image_second"]
            );
        }
        return $return;
    }
    
    /** Deactivate button by id */
    protected function deactivateButtonById($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            UPDATE `button`
            SET `is_active` = 0
            WHERE `id` = :buttonId
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"]
        ));
        $stmt = $conn->prepare("
            DELETE FROM `menu`
            WHERE `button_id` = :buttonId
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"]
        ));
        $stmt = $conn->prepare("
            SELECT `button`.`name`, `article`.`title`
            FROM `article`
            JOIN `button`
            ON `button`.`id` = `article`.`button_id`
            WHERE `button`.`id` = :buttonId
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"]
        ));
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "name" => $row["name"],
                "title" => $row["title"]
            );
        }
        return $return;
    }
    
    /** Reactivate button by id */
    protected function reactivateButtonById($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            UPDATE `button`
            SET `is_active` = 1
            WHERE `id` = :buttonId
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"]
        ));
        $stmt = $conn->prepare("
            SELECT `name`
            FROM `button`
            WHERE `id` = :buttonId
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"]
        ));
        while ($row = $stmt->fetch()) {
            $return = array(
                "name" => $row["name"],
            );
        }
        return $return;
    }
    
    /** Clear database from inactive buttons and articles */
    protected function removeInactiveButtonsAndArticles($conn)
    {
        $stmt = $conn->prepare("
            DELETE `article`
            FROM `article` 
            LEFT JOIN `button` ON (`article`.`button_id` = `button`.`id`)
            WHERE `button`.`id` IS NULL OR `button`.`is_active` = 0;
        ");
        $stmt->execute();
        $stmt = $conn->prepare("
            DELETE FROM `button` WHERE `is_active` = 0;
        ");
        $stmt->execute();
    }
    
    /** Update button by id */
    protected function updateButtonById($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            UPDATE `button`
            SET `name` = :buttonName, `image_first` = :buttonImageFirst, `image_second` = :buttonImageSecond, `secured` = :secured
            WHERE `id` = :buttonId
        ");
        $stmt->execute(array(
            "buttonName" => $parameters["buttonName"],
            "buttonId" => $parameters["buttonId"],
            "buttonImageFirst" => $parameters["buttonImageFirst"],
            "buttonImageSecond" => $parameters["buttonImageSecond"],
            "secured" => $parameters["secured"]
        ));
    }
    
    /** Create button and corresponding article */
    protected function createButtonAndArticle($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            INSERT INTO `button` (`name`, `is_active`, `image_first`, `image_second`, `secured`)
            VALUES (:buttonName, 1, :buttonImageFirst, :buttonImageSecond, :secured)
        ");
        $stmt->execute(array(
            "buttonName" => $parameters["buttonName"],
            "buttonImageFirst" => $parameters["buttonImageFirst"],
            "buttonImageSecond" => $parameters["buttonImageSecond"],
            "secured" => $parameters["secured"]
        ));
        $stmt = $conn->prepare("
            SELECT `id`, `name`
            FROM `button`
            ORDER BY `id` DESC
            LIMIT 1
        ");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $lastButtonId = $row["id"];
            $lastButtonName = $row["name"];
        }
        $stmt = $conn->prepare("
            INSERT INTO `article` (`button_id`, `title`)
            VALUES (:lastButtonId, :lastButtonName)
        ");
        $stmt->execute(array(
            "lastButtonId" => $lastButtonId,
            "lastButtonName" => $lastButtonName
        ));
    }
    
    /** Retrieve articles from database */
    protected function getArticleByButtonId($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            SELECT `button`.`name`, `article`.`button_id`, `article`.`title`, `article`.`content`
            FROM `article`
            JOIN `button`
            ON `button`.`id` = `article`.`button_id`
            AND `button_id` = :buttonId
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"]
        ));
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "buttonName" => $row["name"],
                "buttonId" => $row["button_id"],
                "title" => $row["title"],
                "content" => $row["content"]
            );
        }
        return $return;
    }
    
    /** Update article by button id */
    protected function updateArticleByButtonId($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            UPDATE `article`
            SET `title` = :title, `content` = :content
            WHERE `button_id` = :buttonId
        ");
        $stmt->execute(array(
            "title" => $parameters["title"],
            "content" => $parameters["content"],
            "buttonId" => $parameters["buttonId"]
        ));
    }
    
    /** Save script string */
    protected function saveScriptString($conn, $parameters = array()) {
        $stmt = $conn->prepare("
            INSERT INTO `script` (`string`)
            VALUES (:string)            
        ");
        $stmt->execute(array(
            "string" => $parameters["string"]
        ));
        return $conn->lastInsertId();
    }
    
    /** Update script string by id */
    protected function updateScriptStringById($conn, $parameters = array()) {
        $stmt = $conn->prepare("
            UPDATE `script`
            SET `string` = :string
            WHERE `id` = :id
        ");
        $stmt->execute(array(
            "string" => $parameters["string"],
            "id" => $parameters["id"]
        ));
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
    
    /** Delete script by id */
    protected function deleteScriptStringById($conn, $parameters = array()) {
        $stmt = $conn->prepare("
            DELETE FROM `script`
            WHERE `script`.`id` = :id
        ");
        $stmt->execute(array(
            "id" => $parameters["id"]
        ));
    }
    
    /** Check if image is used */
    protected function checkIfImageIsUsed($conn, $parameters = array())
    {
        //check if image is used in article
        $stmt = $conn->prepare("
            SELECT `title`
            FROM `article`
            JOIN `button`
            ON `button`.`id` = `article`.`button_id`
            AND `content` LIKE :image
            AND `button`.`is_active` = 1
            LIMIT 1
        ");
        $stmt->execute(array(
            "image" => "%" . $parameters["image"] . "%"
        ));
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "title" => $row["title"]
            );
        }
        //check if image is used in button
        if (empty($return)) {
            $stmt = $conn->prepare("
                SELECT `name`
                FROM `button`
                WHERE (`image_first` = ?
                OR `image_second` = ?)
                AND `button`.`is_active` = 1
                LIMIT 1
            ");
            $stmt->execute(array(
                $parameters["image"], 
                $parameters["image"]
            ));
            $return = array();
            while ($row = $stmt->fetch()) {
                $return = array(
                    "name" => $row["name"]
                );
            }
        }
        //check if image is used in code
        if (empty($return)) {
            $stmt = $conn->prepare("
                SELECT `code`, `image`
                FROM `code`
                WHERE `image` = :image
                LIMIT 1
            ");
            $stmt->execute(array(
                "image" => $parameters["image"]
            ));
            $return = array();
            while ($row = $stmt->fetch()) {
                $return = array(
                    "code" => $row["code"]
                );
            }
        }
        return $return;
    }
    
    /** Check if file is used */
    protected function checkIfFileIsUsed($conn, $parameters = array())
    {
        //check if image is used in article
        $stmt = $conn->prepare("
            SELECT `title`
            FROM `article`
            JOIN `button`
            ON `button`.`id` = `article`.`button_id`
            AND `content` LIKE :file
            AND `button`.`is_active` = 1
            LIMIT 1
        ");
        $stmt->execute(array(
            "file" => "%" . $parameters["file"] . "%"
        ));
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "title" => $row["title"]
            );
        }
        return $return;
    }
    
    /** Get contents of slider element */
    protected function getSliderContents($conn)
    {
        $stmt = $conn->prepare("
            SELECT `type`, `content`
            FROM `slider`
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return[] = array(
                "type" => $row["type"], "content" => $row["content"]
            );
        }
        return $return;
    }
    
    /** Update slider element */
    protected function updateSlider($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            UPDATE `slider`
            SET `content` = :content
            WHERE `type` = :type
        ");
        $stmt->execute(array(
            "type" => $parameters["type"],
            "content" => $parameters["content"]
        ));
    }
    
    /** Get menu for main button */
    protected function getMenu($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            SELECT `menu`.`button_id`, `menu`.`parent_button_id`, `button`.`name`
            FROM `menu`
            JOIN `button` ON `button`.`id` = `menu`.`button_id`
            AND `button`.`is_active` = 1
            ORDER BY `menu`.`id` ASC
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return[] = array(
                "buttonId" => $row["button_id"],
                "parentButtonId" => $row["parent_button_id"],
                "name" => $row["name"]
            );
        }
        return $return;
    }
    
    /** Clear menu table */
    protected function clearMenu($conn)
    {
        $stmt = $conn->prepare("
            DELETE FROM `menu`
        ");
        $stmt->execute();
    }
    
    /** Put menu row */
    protected function putMenuRow($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            INSERT INTO `menu` (`button_id`, `parent_button_id`)
            VALUES (:buttonId, :parentButtonId)
        ");
        $stmt->execute(array(
            "buttonId" => $parameters["buttonId"],
            "parentButtonId" => $parameters["parentButtonId"]
        ));
    }
    
    /** Get slider contact box */
    protected function getSliderContactBox($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            SELECT `content`
            FROM `slider`
            WHERE `type` = 'contact'
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "content" => $row["content"]
            );
        }
        return $return;
    }
    
    /** Get article contact box */
    protected function getArticleContactBox($conn, $parameters = array())
    {
        $stmt = $conn->prepare("
            SELECT `title`
            FROM `article`
            WHERE `content` LIKE :pattern
            LIMIT 1
        ");
        $stmt->execute(array(
            "pattern" => "%contact\_%"
        ));
        $return = array();
        while ($row = $stmt->fetch()) {
            $return = array(
                "title" => $row["title"]
            );
        }
        return $return;
    }
    
    /** Get users */
    protected function getUsers($conn)
    {
        $stmt = $conn->prepare("
            SELECT `email`
            FROM `user`
            WHERE `group` = 'user'
        ");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch()) {
            $return[] = $row["email"];
        }
        return $return;
    }
    
}
