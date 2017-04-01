<?php
/**
 * Handle actions of corresponding view elements
 */
class ControllerAdmin extends Controller
{    
    protected function toolsAction() {
        //specify allowed actions and redirect to default if improper is requested
        $extension = !empty($_GET["extension"]) ? $this->secure($_GET["extension"]) : null;
        switch ($extension) {
            case "menu":
                $this->extension = "menu";
                break;
            case "buttons":
                $this->extension = "buttons";
                break;
            case "articles":
                $this->extension = "articles";
                break;
            case "images":
                $this->extension = "images";
                break;
            case "files":
                $this->extension = "files";
                break;
            case "sliders":
                $this->extension = "sliders";
                break;
            case "dictionary":
                $this->extension = "dictionary";
                break;
            case "password":
                $this->extension = "password";
                break;
            case "users":
                $this->extension = "users";
                break;
            default:
                header("Location:" . Config::getSite() . "&request=admin&extension=menu");exit();
        }
        
        //clear cache if post request has been sent
        if (!empty($_POST)) {
            $this->clearCache();
        }
    }
    
    protected function menuAction()
    {
        $message = "";
        $error = "";
        
        if (!empty($_POST)) {
            //check if menu form was submitted
            reset($_POST);
            $formName = substr(key($_POST), 0, strpos(key($_POST), "_"));
            if ($formName === "menu") {
                
                //clear menu table
                Model::run("clearMenu");
                
                //put new menu oreder to the menu table
                $usedButtons = array();
                foreach ($_POST as $field => $value) {
                    $fieldElements = explode("_", $field);
                    
                    //put main button row
                    if (empty($fieldElements[2]) && $value !== "") {
                        $currentMain = $this->secure($value);
                        if (!in_array($currentMain, $usedButtons)) {
                            Model::run("putMenuRow", array("buttonId" => $currentMain, "parentButtonId" => $currentMain));
                            $usedButtons[] = $currentMain;
                        }
                        
                    //put subbutton row
                    } elseif (!empty($fieldElements[2]) && $value !== "" && !empty($currentMain)) {
                        $currentSub = $this->secure($value);
                        if (!in_array($currentSub, $usedButtons)) {
                            Model::run("putMenuRow", array("buttonId" => $currentSub, "parentButtonId" => $currentMain));
                            $usedButtons[] = $currentSub;
                        }
                    }
                }
                $message .= "Modifications done";
            }
        }
        
        //prepare current menu
        $dbMenu = Model::run("getMenu");
        $menu = array();
        if (!empty($dbMenu)) {
            //put main buttons to array
            foreach ($dbMenu as $item) {
                if ($item["buttonId"] === $item["parentButtonId"]) {
                    $menu[$item["buttonId"]] = array("name" => $item["name"], "id" => $item["buttonId"]);
                }
            }
            
            //assign submenu buttons to proper main buttons
            foreach ($dbMenu as $item) {
                if ($item["buttonId"] !== $item["parentButtonId"]) {
                    $menu[$item["parentButtonId"]]["submenu"][$item["buttonId"]] = array("name" => $item["name"], "id" => $item["buttonId"]);
                }
            }
        }
        
        //prepare buttons for selector
        $buttons = Model::run("getButtons");
        
        //pass buttons for selector to the view
        if (!empty($buttons)) {
            $this->buttons = $buttons;
        } else {
            $this->buttons = array();
            $error = "You have no buttons, first add one in buttons section";
        }
        
        //pass menu to the view
        $this->menu = !empty($menu) ? $menu : array();
        
        //set messeges for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
    }
    
    protected function buttonsAction()
    {
        $message = "";
        $error = "";
        
        //check if button form was submitted
        if (!empty($_POST)) {
            reset($_POST);
            $formName = substr(key($_POST), 0, strpos(key($_POST), "_"));
            if ($formName === "button") {
                
                //retrieve data from form and put to buttons array or execute requested procedure
                foreach ($_POST as $field => $value) {
                    $fieldElements = explode("_", $field);
                    $buttonId = $fieldElements[1];
                    $fieldName = $fieldElements[2];
                    
                    switch (true) {
                        case ($fieldName === "name"):
                            $buttons[$buttonId]["name"] = $this->secure($value);
                            break;
                        case ($fieldName === "imageFirst"):
                            $buttons[$buttonId]["imageFirst"] = $this->secure($value);
                            break;
                        case ($fieldName === "imageSecond"):
                            $buttons[$buttonId]["imageSecond"] = $this->secure($value);
                            break;
                        case ($fieldName === "secured"):
                            $buttons[$buttonId]["secured"] = $this->secure($value);
                            break;
                        case ($buttonId === "retrieve" && $fieldName === "id"):
                            if (!empty($value)) {
                                $button = Model::run("reactivateButtonById", array("buttonId" => $this->secure($value)));
                                $message .= "Button " . $button["name"] . " has been retrieved<br />";
                            }
                            break;
                        case ($buttonId === "clear" && $fieldName === "all"):
                            if ($value === "1") {
                                $button = Model::run("removeInactiveButtonsAndArticles");
                                $message .= "Buttons from retrieving selector have been deleted<br />";
                            }
                    }
                }
                
                //perform modifications to the database
                foreach ($buttons as $buttonId => $properties) {
                    //if button is specified
                    if (is_numeric($buttonId)) {
                        //delete existing button if name value is empty
                        if ($properties["name"] === "") {
                            $deleted = Model::run("deactivateButtonById", array(
                                "buttonId" => $buttonId
                            ));
                            $message .=
                                "Button " . $deleted["name"] .
                                " has been deleted along with corresponding article " .
                                $deleted["title"] . "<br /> "
                            ;
                            
                        //update existing button
                        } else {
                            Model::run("updateButtonById", array(
                                "buttonName" => $properties["name"],
                                "buttonId" => $buttonId,
                                "buttonImageFirst" => $properties["imageFirst"],
                                "buttonImageSecond" => $properties["imageSecond"],
                                "secured" => !empty($properties["secured"]) ? true : false
                            ));
                        }
                        
                    //add new button with assigned article    
                    } elseif ($buttonId === "new" && $properties["name"] !== "") {
                         Model::run("createButtonAndArticle", array(
                            "buttonName" => $this->secure($properties["name"]),
                            "buttonImageFirst" => $properties["imageFirst"],
                            "buttonImageSecond" => $properties["imageSecond"],
                            "secured" => !empty($properties["secured"]) ? true : false
                         ));
                        $message .= "Button " . $properties["name"] . " has been created<br />";
                    }
                }
                $message .= "Modifications done";
            }
        }
        
        //get images from uploads for form
        $this->pictures = $this->getImages();
        
        //prepare buttons for form
        $this->buttons = array();
        $buttons = Model::run("getButtons");
        if (!empty($buttons)) {
            $this->buttons = $buttons;
        }
        
        //prepare inactive buttons for form
        $this->inactiveButtons = array();
        $inactiveButtons = Model::run("getInactiveButtons");
        if (!empty($inactiveButtons)) {
            $this->inactiveButtons = $inactiveButtons;
        }
        
        //set messeges for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
    }
    
    protected function articlesAction()
    {
        $message = "";
        $error = "";
        
        if (!empty($_POST)) {
            //check if article form was submitted
            reset($_POST);
            $formName = substr(key($_POST), 0, strpos(key($_POST), "_"));
            if ($formName === "article") {
                
                //retrieve data from form and put to articleContent array
                $articleContent = array();
                foreach ($_POST as $field => $value) {
                    $fieldElements = explode("_", $field);
                    $buttonId = $fieldElements[1];
                    $fieldName = $fieldElements[2];
                    
                    //update title
                    if ($fieldName === "title") {
                        $article[$buttonId]["title"] = $this->secure($value);
                    
                    //add new element
                    } elseif ($fieldName === "addFieldFirst" || $fieldName === "addFieldLast") {
                        $fieldId = $fieldElements[3];
                        if ($value !== "") {
                            $newFieldElements = explode("_", $value);
                            $articleContent[$fieldId]["name"] = $newFieldElements[0];
                            
                            //determine if element has to be add as first element or last element
                            if ($fieldName === "addFieldFirst") {
                                $articleContent[$fieldId]["position"] = 0;
                                $message .= "New element has been added at the begining<br />";
                            } elseif ($fieldName === "addFieldLast") {
                                $articleContent[$fieldId]["position"] = $fieldId;
                                $message .= "New element has been added at the end<br />";
                            }
                            
                            //prepare new element content
                            switch ($newFieldElements[0]) {
                                case "paragraph":
                                    $articleContent[$fieldId]["content"] = array(
                                        "type" => "paragraph",
                                        "align" => "left",
                                        "weight" => "normal",
                                        "style" => "normal",
                                        "size" => "1em",
                                        "text" => "New paragraph"
                                    );
                                    break;
                                case "list":
                                    $articleContent[$fieldId]["content"] = array(
                                        "type" => "disc",
                                        "align" => "left",
                                        "weight" => "normal",
                                        "style" => "normal",
                                        "size" => "1em",
                                        "rows" => array(
                                            array(
                                                "text" =>"New list element",
                                                "url" => ""
                                            )
                                        )
                                    );
                                    break;
                                case "table":
                                    $articleContent[$fieldId]["content"] = array(
                                        "caption" => "New table name",
                                        "align" => "left",
                                        "weight" => "normal",
                                        "size" => "1em",
                                        "rows" => array(
                                            array("First header","Second header","Third header"),
                                            array("Cell content","Cell content","Cell content"),
                                            array("Cell content","Cell content","Cell content")
                                        )
                                    );
                                    break;
                                case "file":
                                    $articleContent[$fieldId]["content"] = array(
                                        "title" => "New file description",
                                        "file" => ""
                                    );
                                    break;
                                case "image":
                                    $articleContent[$fieldId]["content"] = array(
                                        "title" => "New image description",
                                        "file" => "",
                                        "type" => "big"
                                    );
                                    break;
                                case "gallery":
                                    $articleContent[$fieldId]["content"] = array(
                                        "columns" => 1,
                                        "images" => array(
                                            array(
                                                "title" => "New image description",
                                                "file" => ""
                                            )
                                        )
                                    );
                                    break;
                                case "map":
                                    $hasMap = false;
                                    foreach ($_POST as $fieldName => $fieldContent) {
                                        if(strpos($fieldName, "_map_")) {
                                            $hasMap = true;
                                            break;
                                        }
                                    }
                                    if (!$hasMap) {
                                        $articleContent[$fieldId]["content"] = array(
                                            "size" => "large",
                                            "points" => array(
                                                array(
                                                    "description" => "Point description",
                                                    "latitude" => 0,
                                                    "longitude" => 0
                                                )
                                            )
                                        );
                                    } else {
                                        $error .= "This article already contains a map, only one map per article is allowed<br />";
                                    }
                                    break;
                                case "contact":
                                    $hasContactBox = false;
                                    $hasSliderContactBox = false;
                                    $sliderContactBox = Model::run("getSliderContactBox");
                                    if (!empty($sliderContactBox)) {
                                        $content = json_decode($sliderContactBox["content"], true);
                                        $hasSliderContactBox = !empty($content["email"]) ? true : false;
                                    }
                                    if ($hasSliderContactBox) {
                                        $error .= "You have added slider contact box, You can use or articles contact boxes or slider contact box<br />";
                                        break;
                                    }
                                    foreach ($_POST as $fieldName => $fieldContent) {
                                        if(strpos($fieldName, "_contact_")) {
                                            $hasContactBox = true;
                                            break;
                                        }
                                    }
                                    if (!$hasContactBox) {
                                        $articleContent[$fieldId]["content"] = array(
                                            "email" => Config::DEV_EMAIL,
                                            "description" => ""
                                        );
                                    } else {
                                        $error .= "This article already contains a contact box, only one contact box per article is allowed<br />";
                                    }
                                    break;
                                case "script":
                                    $articleContent[$fieldId]["content"] = array(
                                        "string" => "New script"
                                    );
                                    break;
                                default: $articleContent[$fieldId]["content"] = null;
                            }
                        }
                        
                    //update article elements
                    } else {
                        $fieldId = $fieldElements[3];
                        $fieldContent = $fieldElements[4];
                        switch ($fieldName) {
                            case "paragraph":
                                if ($fieldContent === "position") {
                                    $articleContent[$fieldId]["name"] = $fieldName;
                                    $articleContent[$fieldId]["position"] = $this->secure($value);
                                } elseif ($fieldContent === "type") {
                                    $articleContent[$fieldId]["content"]["type"] = $this->secure($value);
                                } elseif ($fieldContent === "align") {
                                    $articleContent[$fieldId]["content"]["align"] = $this->secure($value);
                                } elseif ($fieldContent === "weight") {
                                    $articleContent[$fieldId]["content"]["weight"] = $this->secure($value);
                                } elseif ($fieldContent === "style") {
                                    $articleContent[$fieldId]["content"]["style"] = $this->secure($value);
                                } elseif ($fieldContent === "size") {
                                    $articleContent[$fieldId]["content"]["size"] = $this->secure($value);
                                } elseif ($fieldContent === "text") {
                                    $articleContent[$fieldId]["content"]["text"] = $this->secure($value);
                                }
                                break;
                            case "list":
                                if ($fieldContent === "position") {
                                    $articleContent[$fieldId]["name"] = $fieldName;
                                    $articleContent[$fieldId]["position"] = $this->secure($value);
                                } elseif ($fieldContent === "type") {
                                    $articleContent[$fieldId]["content"]["type"] = $this->secure($value);
                                } elseif ($fieldContent === "align") {
                                    $articleContent[$fieldId]["content"]["align"] = $this->secure($value);
                                } elseif ($fieldContent === "weight") {
                                    $articleContent[$fieldId]["content"]["weight"] = $this->secure($value);
                                } elseif ($fieldContent === "style") {
                                    $articleContent[$fieldId]["content"]["style"] = $this->secure($value);
                                } elseif ($fieldContent === "size") {
                                    $articleContent[$fieldId]["content"]["size"] = $this->secure($value);
                                } elseif ($fieldContent === "row") {
                                    $rowNumber = $fieldElements[5];
                                    $rowContent = $fieldElements[6];
                                    if ($rowContent === "text") {
                                        if ($value !== "") {
                                            $articleContent[$fieldId]["content"]["rows"][$rowNumber]["text"] = $this->secure($value);
                                        }
                                    } elseif ($rowContent === "url") {
                                        if (!empty($articleContent[$fieldId]["content"]["rows"][$rowNumber]["text"])) {
                                            $articleContent[$fieldId]["content"]["rows"][$rowNumber]["url"] = $this->secure($value);
                                        }
                                    }
                                }
                                break;
                            case "table":
                                if ($fieldContent === "position") {
                                    $articleContent[$fieldId]["name"] = $fieldName;
                                    $articleContent[$fieldId]["position"] = $this->secure($value);
                                } elseif ($fieldContent === "caption") {
                                    $articleContent[$fieldId]["content"]["caption"] = $this->secure($value);
                                } elseif ($fieldContent === "align") {
                                    $articleContent[$fieldId]["content"]["align"] = $this->secure($value);
                                } elseif ($fieldContent === "weight") {
                                    $articleContent[$fieldId]["content"]["weight"] = $this->secure($value);
                                } elseif ($fieldContent === "size") {
                                    $articleContent[$fieldId]["content"]["size"] = $this->secure($value);
                                } elseif ($fieldContent === "rows") {
                                    $xCoord = (int)$fieldElements[5];
                                    $yCoord = (int)$fieldElements[6];
                                    if ($xCoord === 1 && $value !== "") {
                                        $articleContent[$fieldId]["content"]["rows"][$xCoord][$yCoord] = $this->secure($value);
                                    } elseif (!empty($articleContent[$fieldId]["content"]["rows"][1][$yCoord])) {
                                        $articleContent[$fieldId]["content"]["rows"][$xCoord][$yCoord] = $this->secure($value);
                                    }
                                }
                                break;
                            case "file":
                                if ($fieldContent === "position") {
                                    $articleContent[$fieldId]["name"] = $fieldName;
                                    $articleContent[$fieldId]["position"] = $this->secure($value);
                                } elseif ($fieldContent === "title") {
                                    if ($value !== "") {
                                        $articleContent[$fieldId]["content"]["title"] = $this->secure($value);
                                    }
                                } elseif ($fieldContent === "file") {
                                    if (!empty($articleContent[$fieldId]["content"])) {
                                        $articleContent[$fieldId]["content"]["file"] = $this->secure($value);
                                    }
                                }
                                break;
                            case "image":
                                if ($fieldContent === "position") {
                                    $articleContent[$fieldId]["name"] = $fieldName;
                                    $articleContent[$fieldId]["position"] = $this->secure($value);
                                } elseif ($fieldContent === "title") {
                                    if ($value !== "") {
                                        $articleContent[$fieldId]["content"]["title"] = $this->secure($value);
                                    }
                                } elseif ($fieldContent === "file") {
                                    if (!empty($articleContent[$fieldId]["content"])) {
                                        $articleContent[$fieldId]["content"]["file"] = $this->secure($value);
                                    }
                                } elseif ($fieldContent === "type") {
                                    if (!empty($articleContent[$fieldId]["content"])) {
                                        $articleContent[$fieldId]["content"]["type"] = $this->secure($value);
                                    }
                                }
                                break;
                            case "gallery":
                                if ($fieldContent === "position") {
                                    $articleContent[$fieldId]["name"] = $fieldName;
                                    $articleContent[$fieldId]["position"] = $this->secure($value);
                                } elseif ($fieldContent === "columns") {
                                    $articleContent[$fieldId]["content"]["columns"] = $this->secure($value);
                                } elseif ($fieldContent === "image") {
                                    $imageFieldId = $fieldElements[5];
                                    $imageFieldContent = $fieldElements[6];
                                    if ($imageFieldContent === "title") {
                                        if ($value !== "") {
                                            $articleContent[$fieldId]["content"]["images"][$imageFieldId]["title"] = $this->secure($value);
                                        }
                                    } elseif ($imageFieldContent === "file") {
                                        if (!empty($articleContent[$fieldId]["content"]["images"][$imageFieldId]["title"])) {
                                            $articleContent[$fieldId]["content"]["images"][$imageFieldId]["file"] = $this->secure($value);
                                        }
                                    }
                                }
                                break;
                            case "map":
                                if ($fieldContent === "position") {
                                    $articleContent[$fieldId]["name"] = $fieldName;
                                    $articleContent[$fieldId]["position"] = $this->secure($value);
                                } elseif ($fieldContent === "size") {
                                    $articleContent[$fieldId]["content"]["size"] = $this->secure($value);
                                } elseif ($fieldContent === "point") {
                                    $rowNumber = $fieldElements[5];
                                    $rowContent = $fieldElements[6];
                                    if ($rowContent === "description") {
                                        if ($value !== "") {
                                            $articleContent[$fieldId]["content"]["points"][$rowNumber]["description"] = $this->secure($value);
                                        }
                                    } elseif ($rowContent === "latitude") {
                                        if (!empty($articleContent[$fieldId]["content"]["points"][$rowNumber]["description"])) {
                                            $latitude = $this->secure($value);
                                            $latitude = !empty($latitude) ? $latitude : 0;
                                            $articleContent[$fieldId]["content"]["points"][$rowNumber]["latitude"] = $latitude;
                                        }
                                    } elseif ($rowContent === "longitude") {
                                        if (!empty($articleContent[$fieldId]["content"]["points"][$rowNumber]["description"])) {
                                            $longitude = $this->secure($value);
                                            $longitude = !empty($longitude) ? $longitude : 0;
                                            $articleContent[$fieldId]["content"]["points"][$rowNumber]["longitude"] = $longitude;
                                        }
                                    }
                                }
                                break;
                            case "contact":
                                if ($fieldContent === "position") {
                                    $articleContent[$fieldId]["name"] = $fieldName;
                                    $articleContent[$fieldId]["position"] = $this->secure($value);
                                } elseif ($fieldContent === "email") {
                                    $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                                    if (filter_var($value, FILTER_VALIDATE_EMAIL) || $value === "") {
                                        $articleContent[$fieldId]["content"]["email"] = $this->secure($value);
                                    } else {
                                        $error .= "Wrong e-mail address, expecting for instance: yourname@gmail.com<br />";
                                    }
                                } elseif ($fieldContent === "description") {
                                    if (!empty($articleContent[$fieldId]["content"])) {
                                        $articleContent[$fieldId]["content"]["description"] = $this->secure($value);
                                    }
                                }
                                break;
                            case "script":
                                if ($fieldContent === "position") {
                                    $articleContent[$fieldId]["name"] = $fieldName;
                                    $articleContent[$fieldId]["position"] = $this->secure($value);
                                } elseif ($fieldContent === "id") {
                                    $articleContent[$fieldId]["content"]["id"] = $this->secure($value);
                                } elseif ($fieldContent === "string") {
                                    $articleContent[$fieldId]["content"]["string"] = $value;
                                }
                                break;
                        }
                    }
                }
                
                //delete empty elements from articleContent
                foreach ($articleContent as $elementNumber => $properties) {
                    switch ($properties["name"]) {
                        case "paragraph":
                            if (empty($properties["content"]["text"])) {
                                unset($articleContent[$elementNumber]);
                            }
                            break;
                        case "list":
                            if (empty($properties["content"]["rows"])) {
                                unset($articleContent[$elementNumber]);
                            }
                            break;
                        case "table":
                            if(!empty($properties["content"]["rows"])) {
                                foreach ($properties["content"]["rows"] as $rowNumber => $row) {
                                    $rowContent = 0;
                                    foreach ($row as $cell) {
                                        if ($cell !== "") {
                                            $rowContent++;
                                        }
                                    }
                                    if (!$rowContent) {
                                        unset($articleContent[$elementNumber]["content"]["rows"][$rowNumber]);
                                    }
                                }
                            }
                            if (empty($properties["content"]["rows"][1])) {
                                unset($articleContent[$elementNumber]);
                            }
                            break;
                        case "gallery":
                            if (empty($properties["content"]["images"])) {
                                unset($articleContent[$elementNumber]);
                            }
                            break;
                        case "map":
                            if (empty($properties["content"]["points"])) {
                                unset($articleContent[$elementNumber]);
                            }
                            break;
                        case "contact":
                            if (empty($properties["content"]["email"])) {
                                unset($articleContent[$elementNumber]);
                            }
                            break;
                        case "script":
                            if (empty($properties["content"]["string"])) {
                                Model::run("deleteScriptStringById", array("id" => $properties["content"]["id"]));
                                unset($articleContent[$elementNumber]);
                            } else {
                                if (empty($properties["content"]["id"])) { 
                                    $id = Model::run("saveScriptString", array(
                                        "string" => $properties["content"]["string"]
                                    ));
                                    $articleContent[$elementNumber]["content"]["id"] = $id;
                                } else {
                                    Model::run("updateScriptStringById", array(
                                        "string" => $properties["content"]["string"],
                                        "id" => $properties["content"]["id"]
                                    ));
                                }
                            }
                            break;
                        default:
                            if (empty($properties["content"])) {
                                unset($articleContent[$elementNumber]);
                            }
                    }
                }
                
                //sort articleContent elements by position input value and put to article array
                $sorter = array();
                foreach ($articleContent as $element => $elementProperties) {
                     $counter = $elementProperties["position"];
                     while (!empty($sorter[$counter])) {
                         $counter++;
                     }
                     $sorter[$counter] = array(
                         "position" => $counter,
                         "name" => $elementProperties["name"],
                         "content" => $elementProperties["content"]
                     );
                }
                ksort($sorter);
                $article[$buttonId]["content"] = null;
                $counter = 1;
                foreach ($sorter as $element) {
                    $article[$buttonId]["content"][$element["name"] . "_" . $counter] = $element["content"];
                    $counter++;
                }
                
                //make modifications to the database only if there is no error
                if ($error === "") {
                    if (empty($article[$buttonId]["content"])) {
                        $article[$buttonId]["content"] = "";
                    } else {
                        $article[$buttonId]["content"] = json_encode($article[$buttonId]["content"], JSON_UNESCAPED_UNICODE);
                    }
                    Model::run("updateArticleByButtonId", array(
                        "title" => $article[$buttonId]["title"],
                        "content" => $article[$buttonId]["content"],
                        "buttonId" => $buttonId
                    ));
                    $message .= "Modifications done";
                } else {
                    $message = "";
                }
            }
        }
        
        //prepare buttons for form
        $this->buttons = array();
        $buttons = Model::run("getButtons");
        if (!empty($buttons)) {
            $this->buttons = $buttons;
        } else {
            $buttonsError = "You have no buttons, first add one in buttons section";
        }
        
        
        //if there was article request prepare it for form
        $this->article = null;
        $buttonId = !empty($_GET["article"]) ? $this->secure($_GET["article"]) : null;
        if ($buttonId) {
            $dbArticle = Model::run("getArticleByButtonId", array("buttonId" => $buttonId));
            if (!empty($dbArticle)) {
                $article["buttonName"] = $dbArticle["buttonName"];
                $article["title"] = $dbArticle["title"];
                $article["buttonId"] = $dbArticle["buttonId"];
                $content = json_decode($dbArticle["content"], true);
                $article["elements"] = $content ? $content : null;
                //if article contains script include it
                if (!empty($article["elements"])) {
                    foreach ($article["elements"] as $key => $content) {
                        if (substr($key, 0, strpos($key, "_")) === "script") {
                            $string = Model::run("getScriptStringById", array(
                                "id" => $content["id"]
                            ));
                            $article["elements"][$key]["string"] = $string["string"];
                        }
                    }
                }
                
                $this->article = $article;
                
                //get images from uploads for form
                $this->pictures = $this->getImages();
                
                //get files from uploads for form
                $this->files = $this->getFiles();
            }
        }
        
        //set messeges for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
        $this->buttonsError = !empty($buttonsError) ? $buttonsError : null;
    }
    
    protected function imagesAction()
    {
        $error = "";
        $message = "";
        
        //perform download or delete action if requested
        $download = !empty($_GET["download"]) ? $this->secure($_GET["download"]) : null;
        $delete = !empty($_GET["delete"]) ? $this->secure($_GET["delete"]) : null;
        if ($download) {
            $file = Config::getDirectory() . "uploads/max/" . $download;
            $fileExtension = substr($download, strrpos($download, ".") + 1);
            if (getimagesize($file) && array_key_exists(strtolower($fileExtension), $this->imageCheck)) {
                header("Location:" . Config::getSite() . "&request=download&file=" . $download);exit();
            }
        }
        if ($delete) {
            //check if image is used, if so prevent from deleting
            $isUsed = Model::run("checkIfImageIsUsed", array("image" => $delete));
            if (empty($isUsed)) {
                if (file_exists(Config::getDirectory() . "uploads/max/" . $delete)) {
                    unlink(Config::getDirectory() . "uploads/min/" . $delete);
                    unlink(Config::getDirectory() . "uploads/med/" . $delete);
                    unlink(Config::getDirectory() . "uploads/max/" . $delete);
                    $message .= "Image " . $delete . " has been deleted<br />";
                } else {
                    $error .= "Image " . $delete . " does not exist<br />";
                }
            } else {
                if (!empty($isUsed["title"])) {
                    $error .= "You can not delete image " . $delete . " because it is used in article: " . $isUsed["title"] . "<br />";
                } elseif (!empty($isUsed["name"])) {
                    $error .= "You can not delete image " . $delete . " because it is used in button " . $isUsed["name"] . "<br />";
                } elseif (!empty($isUsed["code"])) {
                    $error .= "You can not delete image " . $delete . " because it is used in dictionary for code " . $isUsed["code"] . "<br />";
                }
            }
        }
        
        //perform cleaning images if requested
        if (!empty($_POST)) {
            if ($_POST["images_clear"] === "1") {
                $allImages = $this->getImages();
                foreach ($allImages as $image) {
                    $isUsed = Model::run("checkIfImageIsUsed", array("image" => $image));
                    if (empty($isUsed)) {
                        unlink(Config::getDirectory() . "uploads/min/" . $image);
                        unlink(Config::getDirectory() . "uploads/med/" . $image);
                        unlink(Config::getDirectory() . "uploads/max/" . $image);
                    }
                }
                $message .= "Unused images have been deleted<br />";
            }
        }
        
        //perform uploading an image if requested
        $uploadedFile = reset($_FILES);
        if (!empty($uploadedFile["tmp_name"])) {
            //check file type, size and if exists in uploads directory
            $error .= !getimagesize($uploadedFile["tmp_name"]) ? "File which You have tried to upload is not an image<br />" : null;
            $error .= file_exists(
                Config::getDirectory() . "uploads/max/" . $this->slugify($uploadedFile["name"])
            ) ? "File which You have tried to upload has the same name as one which already exists on the server<br />" : null;
            $error .= $uploadedFile["size"] > 3000000 ? "File which You have tried to upload is too large (has over 3MB)<br />" : null;
            $fileExtension = strtolower(pathinfo($uploadedFile["name"], PATHINFO_EXTENSION));
            $error .= (
                !array_key_exists($fileExtension, $this->imageCheck) ||
                $this->imageCheck[$fileExtension] !== mime_content_type($uploadedFile["tmp_name"])
            ) ? "File which You have tried to upload has not got proper extension (jpg, jpeg, png or gif) or has got wrong content<br />" : null;
            
            //try to upload
            if ($error !== "") {
                $error .= "It refers to a file " . $uploadedFile["name"] . "<br />";
            } else {
                $this->uploadImage($uploadedFile);
                $message  .= "Image " . $uploadedFile["name"] . " has been uploaded<br />";
            }
        }
        
        //get images from uploads for view
        $this->pictures = $this->getImages();
        
        //display message for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
    }
    
    protected function filesAction()
    {
        $error = "";
        $message = "";
        
        //perform download or delete action if requested
        $download = !empty($_GET["download"]) ? $this->secure($_GET["download"]) : null;
        $delete = !empty($_GET["delete"]) ? $this->secure($_GET["delete"]) : null;
        if ($download) {
            $fileExtension = substr($download, strrpos($download, ".") + 1);
            if (array_key_exists(strtolower($fileExtension), $this->fileCheck)) {
                header("Location:" . Config::getSite() . "&request=download&file=" . $download);exit();
            }
        }
        if ($delete) {
            //check if file is used, if so prevent from deleting
            $isUsed = Model::run("checkIfFileIsUsed", array("file" => $delete));
            if (empty($isUsed)) {
                if (file_exists(Config::getDirectory() . "uploads/files/" . $delete)) {
                    unlink(Config::getDirectory() . "uploads/files/" . $delete);
                    $message .= "File " . $delete . " has been deleted<br />";
                } else {
                    $error .= "File " . $delete . " does not exist<br />";
                }
            } else {
                $error .= "You can not delete file " . $delete . " because it is used in article " . $isUsed["title"] . "<br />";
            }
        }
        
        //perform cleaning files if requested
        if (!empty($_POST)) {
            if ($_POST["files_clear"] === "1") {
                $allFiles = $this->getFiles();
                foreach ($allFiles as $file) {
                    $isUsed = Model::run("checkIfFileIsUsed", array("file" => $file));
                    if (empty($isUsed)) {
                        unlink(Config::getDirectory() . "uploads/files/" . $file);
                    }
                }
                $message .= "Unused files have been deleted<br />";
            }
        }
        
        //perform uploading a file if requested
        $uploadedFile = reset($_FILES);
        if (!empty($uploadedFile["tmp_name"])) {
            //check file type, size and if exists in uploads directory
            $error .= file_exists(
                Config::getDirectory() . "uploads/files/" . $this->secure($this->slugify($uploadedFile["name"]))
            ) ? "File which You have tried to upload has the same name as one which already exists on the server<br />" : null;
            $error .= $uploadedFile["size"] > 10000000 ? "File which You have tried to upload is too large (has over 10MB)<br />" : null;
            $fileExtension = strtolower(pathinfo($uploadedFile["name"], PATHINFO_EXTENSION));
            $error .= (
                !array_key_exists($fileExtension, $this->fileCheck) ||
                $this->fileCheck[$fileExtension] !== mime_content_type($uploadedFile["tmp_name"])
            ) ? "File which You have tried to upload has not got proper extension 
            (txt, pdf, odt, ods, doc, docx, xls, xlsx, odp, ppt, pptx, mp3) or has got wrong content<br />" : null;

            //try to upload
            if (
                $error === "" &&
                !move_uploaded_file(
                    $uploadedFile["tmp_name"], 
                    Config::getDirectory() . "uploads/files/" . $this->secure($this->slugify($uploadedFile["name"]))
                )
            ) {
                $error .= "File which You have tried to upload could not be uploaded<br />";
            }
            if ($error !== "") {
                $error .= "It refers to a file " . $uploadedFile["name"] . "<br />";
            } else {
                $message .= "File " . $uploadedFile["name"] . " has been uploaded<br />";
            }
        }
        
        //get images from uploads for view
        $this->files = $this->getFiles();
        
        //set messeges for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
    }
    
    protected function slidersAction()
    {
        $message = "";
        $error = "";
        
        //in case form was submitted
        if (!empty($_POST)) {
            $emailAddressError = $emailAddress = "";
            
            //get post data and check inputs
            foreach ($_POST as $field => $value) {
                $fieldElements = explode("_", $field);
                $fieldName = $fieldElements[0];
                
                switch (true) {
                    case ($fieldName === "search"):
                        $slider["search"] = $searchbarError = $this->secure($value);
                        break;
                    case ($fieldName === "sidebox"):
                        $slider["sidebox"] = $sideboxError = $this->secure($value);
                        break;
                    case ($fieldName === "contact"):
                        $filedContent = $fieldElements[1];
                        if ($filedContent === "description") {
                            $slider["contact"]["description"] = $emailDescriptionError = $this->secure($value);
                        } elseif ($filedContent === "email") {
                            $inputedEmail = $this->secure($value);
                            if (!empty($inputedEmail)) {
                                $articleContactBox = Model::run("getArticleContactBox");
                            }
                            $hasArticleContactBox = !empty($articleContactBox) ? true : false;
                            if ($hasArticleContactBox) {
                                $error .= "
                                    You have added contact box in article " .
                                    $articleContactBox["title"] .
                                    ", You can use or articles contact boxes or slider contact box<br />
                                ";
                                $emailAddressError = $this->secure($value);
                            }
                            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                            if (filter_var($value, FILTER_VALIDATE_EMAIL) || $value === "") {
                                $slider["contact"]["email"] = $emailAddress = $this->secure($value);
                            } else {
                                $error .= "Wrong e-mail address, expecting for instance: yourname@gmail.com<br />";
                                $emailAddressError = $this->secure($value);
                            }
                        }
                        break;
                    case ($fieldName === "facebook"):
                        $slider["facebook"] = $facebookError = $this->secure($value);
                        break;
                    case ($fieldName === "twitter"):
                        $slider["twitter"] = $twitterError = $this->secure($value);
                        break;
                    case ($fieldName === "youtube"):
                        $slider["youtube"] = $youtubeError = $this->secure($value);
                        break;
                    case ($fieldName === "googleplus"):
                        $slider["googleplus"] = $googleplusError = $this->secure($value);
                        break;
                    case ($fieldName === "linkedin"):
                        $slider["linkedin"] = $linkedinError = $this->secure($value);
                        break;
                    case ($fieldName === "github"):
                        $slider["github"] = $githubError = $this->secure($value);
                        break;
                }
            }
            
            //clear description if email field is empty and prepare slider elements for save
            foreach ($slider as $element => $properties) {
                switch ($element) {
                    case "contact": {
                        if (empty($properties["email"])) {
                            $properties["description"] = "";
                        }
                        $slider[$element] = json_encode($properties);
                    }
                }
            }
            
            //make modifications to the database
            if ($error === "") {
                foreach ($slider as $type => $content) {
                    Model::run("updateSlider", array("type" => $type, "content" => $content));
                }
                $message .= "Modifications done";
            }
        }
        
        //get currently set properties of contact elements
        $sliderContents = Model::run("getSliderContents");
        $this->contents = array();
        foreach ($sliderContents as $content) {
            switch ($content["type"]) {
                case "search":
                    $this->contents["search"] = $content["content"];
                    break;
                case "sidebox":
                    $this->contents["sidebox"] = $content["content"];
                    break;
                case "contact":
                    $contentArray = json_decode($content["content"], true);
                    $this->contents["emailAddress"] = $contentArray["email"];
                    $this->contents["emailDescription"] = $contentArray["description"];    
                    break;
                case "facebook":
                    $this->contents["facebook"] = $content["content"];    
                    break;
                case "twitter":
                    $this->contents["twitter"] = $content["content"];
                    break;
                case "youtube":
                    $this->contents["youtube"] = $content["content"];
                    break;
                case "googleplus":
                    $this->contents["googleplus"] = $content["content"];
                    break;
                case "linkedin":
                    $this->contents["linkedin"] = $content["content"];
                    break;
                case "github":
                    $this->contents["github"] = $content["content"];
                    break;
            }
        }        
        
        //add wrong inputed data for correction
        if (!empty($emailAddressError)) {
            $this->contents["search"] = !empty($searchbarError) ? $searchbarError : $this->contents["search"];
            $this->contents["sidebox"] = !empty($sideboxError) ? $sideboxError : $this->contents["sidebox"];
            $this->contents["emailAddress"] = !empty($emailAddressError) ? $emailAddressError : (
                !empty($emailAddress) ? $emailAddress : $this->contents["emailAddress"]
            );
            $this->contents["emailDescription"] = !empty($emailDescriptionError) ? $emailDescriptionError : $this->contents["emailDescription"];
            $this->contents["facebook"] = !empty($facebookError) ? $facebookError : $this->contents["facebook"];
            $this->contents["twitter"] = !empty($twitterError) ? $twitterError : $this->contents["twitter"];
            $this->contents["youtube"] = !empty($youtubeError) ? $youtubeError : $this->contents["youtube"];
            $this->contents["googleplus"] = !empty($googleplusError) ? $googleplusError : $this->contents["googleplus"];
            $this->contents["linkedin"] = !empty($linkedinError) ? $linkedinError : $this->contents["linkedin"];
            $this->contents["github"] = !empty($githubError) ? $githubError : $this->contents["github"];
        }
        
        //set messeges for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
    }
    
    protected function dictionaryAction()
    {
        $dictionary = new Dictionary();
        $message = "";
        $error = "";
        
        if (!empty($_POST)) {
            //check if dictionary form was submitted
            reset($_POST);
            $formName = substr(key($_POST), 0, strpos(key($_POST), "_"));
            if ($formName === "language") {
                foreach ($_POST as $field => $value) {
                    $fieldName = explode("_", $field)[1];
                    
                    //handle action for code or prepare string for translation
                    switch (true) {
                        case ($fieldName === "value"):
                            $language["code"] = $this->secure($value);
                            break;
                        case ($fieldName === "image"):
                            $language["image"] = $this->secure($value);
                            break;
                        case ($fieldName === "delete" && $value !== ""):
                            $dictionary->deleteCode($this->secure($value));
                            $message .= "Language " . $this->secure($value) . " has been deleted<br />";
                            break;
                        case ($fieldName === "clear"):
                            $dictionary->clearDictionary();
                            $message .= "Unused translations have been removed from dictionary<br />";
                            break;
                        case ($fieldName === "string" && $value !== ""):
                            $translation["base"] = $this->secure($value);
                            $codes = $dictionary->getCodes();
                            foreach ($codes as $code => $image) {
                                $string = Dictionary::get($translation["base"], $code);
                                if ($translation["base"] !== $string) {
                                    $translation["codes"][$code] = $string;
                                }
                            }
                    }
                }
                
                //save the code if it is composed of two lower case letters
                if (preg_match("/[a-z]{2}/", $language["code"]) && $language["code"]) {
                    $dictionary->addCode($language["code"], $language["image"]);
                    $message .= "Language " . $language["code"] . " has been added<br />";
                    if (!empty($language["image"])) {
                        $message .= "Image " . $language["image"] . " has been assigned to language  " . $language["code"] . "<br />";
                    }
                } elseif ($language["code"] !== "") {
                    $error .= "Language code has to contain 2 lower case letters<br />";
                }
                
            //add translation to the database  
            } elseif ($formName === "translate") {
                foreach ($_POST as $field => $value) {
                    $fieldName = explode("_", $field)[1];
                    if ($fieldName === "base") {
                        $translationAdd["base"] = $this->secure($value);
                    } elseif ($fieldName === "code") {
                        $translationAdd["code"] = $this->secure($value);
                    } elseif ($fieldName === "translation" && $value !== "") {
                        $translationAdd["translation"] = $this->secure($value);
                    }                    
                }  
                if (!empty($translationAdd["base"]) && !empty($translationAdd["code"]) && !empty($translationAdd["translation"])) {
                    $dictionary->addTranslation($translationAdd["base"], $translationAdd["code"], $translationAdd["translation"]);
                    $message .= "Translation has been added<br />";
                }
            }
        }
        
        //prepare data for forms
        $this->translation = !empty($translation) ? $translation : null;
        $this->strings = $dictionary->getStrings();
        $this->codes = $dictionary->getCodes();
        
        //get images from uploads for view
        $this->pictures = $this->getImages();
            
        //display message for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
    }
    
    protected function passwordAction()
    {
        //this action waits for result in corresponding Authenticator action
        $message = "";
        $error = "";
        
        //search for answer from authenticator
        if (!empty($_GET["correct"])) {
            switch ($_GET["correct"]) {
                case "true": {
                    $message .= "Password has been changed<br />";
                };break;
                case "false": {
                    $error .=
                        "Password has to contain at least 6 characters<br />" .
                        "Password must not contain \ character<br />"
                    ;
                };break;
            }
        }
        
        //display message for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
    }
    
    protected function usersAction()
    {
        //this action waits for result in corresponding Authenticator action
        $message = "";
        $error = "";
        $createResult = !empty($_GET["create"]) ? $this->secure($_GET["create"]) : null;
        $deleteResult = !empty($_GET["delete"]) ? $this->secure($_GET["delete"]) : null;        
        
        //search for answer from authenticator
        if ($createResult === "success") {
            $message .= "New account has been added, login and password has been sent on passed e-mail<br />";
        } elseif ($createResult === "incorrect") {
            $error .= "Could not create account because email address is incorrect<br />";
        } elseif ($createResult === "duplicated") {
            $error .= "Could not create account because email address already exitsts<br />";
        }
        
        if ($deleteResult === "success") {
            $message .= "Account has been deleted<br />";
        } elseif ($deleteResult === "incorrect") {
            $error .= "Could not delete account because email address is incorrect<br />";
        }
        
        //prepare users for selector
        $this->users = array();
        $users = Model::run("getUsers");
        if (!empty($users)) {
            $this->users = $users;
        }
        
        //display message for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
    }
    
    protected function generateAction()
    {
        //this action waits for result in corresponding Authenticator action
        $emailResult = !empty($_GET["email"]) ? $this->secure($_GET["email"]) : null;
        $message = "";
        $error = "";
        
        //search for answer from authenticator
        if ($emailResult === "correct") {
            $message .= "New login and password has been sent on passed e-mail";
        } elseif ($emailResult === "mismatch") {
            $error .= "Passed email is not authorized";
        } elseif ($emailResult === "incorrect") {
            $error .= "Wrong e-mail address";
        }
        
        //display message for user
        $this->message = !empty($message) ? $message : null;
        $this->error = !empty($error) ? $error : null;
    }
    
    protected function loginAction()
    {
        //this action waits for result in corresponding Authenticator action
        $error = "";
        
        //search for answer from authenticator
        if (!empty($_GET["correct"]) && $_GET["correct"] === 'false') {
            $error .= "Wrong username or password";
        }
       
        //display message for user        
        $this->loginError = !empty($error) ? $error : null;
    }
    
}
