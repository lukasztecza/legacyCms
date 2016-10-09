<?php
/**
 * Handle actions of corresponding view elements
 */
class ControllerPage extends Controller
{
    protected function headerAction()
    {
        //prepare language links and pass them to view
        $dictionary = new Dictionary();
        $codes = $dictionary->getCodes();
        $links = array();
        foreach ($codes as $code => $image) {
            $links[$code] = array(
                "url" => Config::getDefaultSite() . str_replace("?lang=" . Config::getLanguage(), "?lang=" . $code, Config::getCurrentQuery()),
                "image" => $image
            );
        }
        $this->links = $links;
    }
    
    protected function navAction()
    {
        //get main button
        $buttonId = !empty($_GET["article"]) ? $this->secure($_GET["article"]) : Model::run("getDefaultButton")["buttonId"];
        $mainButton = Model::run("getParentButtonById", array("buttonId" => $buttonId));
        
        //get buttons from database
        $buttons = Model::run("getButtons");
        if (!empty($buttons)) {
            $this->buttons = $buttons;
        } else {
            $this->buttons = array();
        }
        $this->activeButton = $mainButton["parentButtonId"];
    }
    
    protected function sectionAction()
    {
        //specify allowed page requests redirect to main page by default
        $article = !empty($_GET["article"]) ? $this->secure($_GET["article"]) : null;
        switch (true) {
            case is_numeric($article):
                $this->article = "article";
                break;
            case ($article === null):
                $this->article = "article";
                break;
            default:
                header("Location:" . Config::getSite() . "&request=page");exit();
        }
    }
    
    protected function articleAction()
    {
        //get which article is requested if none get default one
        $buttonId = !empty($_GET["article"]) ? $this->secure($_GET["article"]) : Model::run("getDefaultButton")["buttonId"];
        
        //get main button
        $mainButton = Model::run("getParentButtonById", array("buttonId" => $buttonId));
        
        //get submenu buttons from database for main button if exists (if Config::SUBMENU_VISIBLE = 0 do not show in subsites)
        if (Config::SUBMENU_VISIBLE || $mainButton["parentButtonId"] == $buttonId) {
            $menu = Model::run("getSubmenuById", array("buttonId" => $mainButton["parentButtonId"]));
        } else {
            $menu = array();
        }
        $this->buttons = !empty($menu) ? $menu : array();
        $this->activeButton = $buttonId;
        
        //get article from database for corresponding buttonId if is active if none is found pass empty array
        $dbArticle = Model::run("getArticleByButtonId", array("buttonId" => $buttonId));
        if (!empty($dbArticle)) {
            $article["title"] = $dbArticle["title"];
            $article["buttonId"] = $buttonId;
            $content = json_decode($dbArticle["content"], true);
            $article["elements"] = $content ? $content : null;
            $article["secured"] = $dbArticle["secured"];
            if ($article["secured"]) {
                //get page action and let authenticator handle it
                $action = !empty($_GET["action"]) ? $_GET["action"] : null;
                $authenticator = new Authenticator();
                $authenticator->handleAction($action);
                $this->loggedIn = $authenticator->isAuthenticated();
                $error = "";
                //search for answer from authenticator
                if (!empty($_GET["correct"]) && $_GET["correct"] === 'false') {
                    $error .= Dictionary::get(Dictionary::$texts[13]);
                }
                //display message for user        
                $this->loginError = !empty($error) ? $error : null;
            }
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
        } else {
            $this->article = array();
        }
    }
    
    protected function sliderAction()
    {
        //get which article is requested
        $buttonId = !empty($_GET["article"]) ? $this->secure($_GET["article"]) : Model::run("getDefaultButton")["buttonId"];
        
        //get slider elements
        $sliderElements = Model::run("getSliderElements");
        
        //prepare content and clear empty elements
        foreach ($sliderElements as $element => $content) {
            switch (true) {
                case ($content["type"] === "contact"):
                    $sliderElements[$element]["content"] = json_decode($content["content"], true);
                    if ($sliderElements[$element]["content"]["email"] === "") {
                        unset($sliderElements[$element]);
                    }
                    break;
                default:
                    if ($content["content"] === "") {
                        unset($sliderElements[$element]);
                    }
            }
        }
        
        //pass slider elements to the view
        $this->content = $sliderElements;
        $this->buttonId = $buttonId;
    }
    
    protected function emailFormAction($parameters = array())
    {
        //handle email form submission, if something is wrong display error and remember inputed data
        if (!empty($_POST)) {
            //check if email form was submitted testing first input name
            reset($_POST);
            $formName = key($_POST);
            if ($formName === "userEmail") {
                //get inputed data and check them
                $userEmail = !empty($_POST["userEmail"]) ? $_POST["userEmail"] : null;
                $userMessage = !empty($_POST["userMessage"]) ? $_POST["userMessage"]: null;
                $checked = $this->checkEmail($userEmail, $userMessage);
                $error = $checked["error"];
                $userEmail = $checked["email"];
                $userMessage = $checked["message"];
                
                //in case of no error send email
                if (!$error) {
                    //send message
                    $sendTo = $parameters["email"];
                    $title = "Email from page " . Config::getDefaultSite();
                    $this->sendEmail($sendTo, $userEmail, $title, $userMessage);
                    
                    //ajax response in case of success
                    if ($_GET["request"] === "ajax") {
                        header('Content-Type: application/json');
                        $success = Dictionary::get(Dictionary::$texts[7]);
                        echo json_encode(array(
                            "message" => $success,
                            "error" => 0
                        ));
                        exit();
                    }
                    
                    //prevent resending an email via refreshing the page
                    header("Location:" . Config::getSite() . "&request=page&article=" . $parameters["buttonId"] ."&emailSent=true");exit();
                }
                
                //ajax response in case of error
                if ($_GET["request"] === "ajax") {
                    header('Content-Type: application/json');
                    $error = Dictionary::get($error);
                    echo json_encode(array(
                        "message" => $error,
                        "error" => 1
                    ));
                    exit();
                }
            }
        }
        
        //pass variables to the view
        $this->description = $parameters["description"];
        $this->userEmail = !empty($userEmail) ? $userEmail : null;
        $this->userMessage = !empty($userMessage) ? $userMessage : null;
        $this->error = !empty($error) ? $error : null;
        $this->buttonId = $parameters["buttonId"];
        $this->recieverEmail = $parameters["email"];
        
        //if email was sent inform user about it
        $this->confirmSending = !empty($_GET["emailSent"]) ? Dictionary::$texts[7] : null;
    }
    
    protected function searchbarFormAction($parameters = array())
    {
        //get which article is requested
        $buttonId = !empty($_GET["article"]) ? $this->secure($_GET["article"]) : Model::run("getDefaultButton")["buttonId"];
    
        //handle searchbar form submission
        if (!empty($_POST)) {
            //check if searchbar form was submitted if input is longer than 3 execute search
            reset($_POST);
            $formName = key($_POST);
            if ($formName === "search") {
                $search = !empty($_POST["search"]) ? $this->secure($_POST["search"]) : null;
                if (strlen($search) >= 3) {
                    $results = Model::run("getArticlesByPattern", array("search" => $search));
                    if (empty($results)) {
                        $searchError = Dictionary::$texts[11];
                    }
                } else {
                    $searchError = Dictionary::$texts[12];
                }
            }
            
            //ajax response
            if ($_GET["request"] === "ajax") {
                header('Content-Type: application/json');
                if (empty($results)) {
                    $results = array();
                }
                if (empty($searchError)) {
                    $searchError = "";
                } else {
                    $searchError = Dictionary::get($searchError);
                }
                                
                //translate results tiltles and echo result
                foreach ($results as $key => $result) {
                    $results[$key]['title'] = Dictionary::get($result['title']);
                }
                //avoid associative indexes in ajax response
                $response = array();
                foreach ($results as $result) {
                    $response[] = $result;
                }
                echo json_encode(array(
                    "error" => $searchError,
                    "results" => $response,
                    "baseUrl" => Config::getSite() . "&request=page&article="
                ));
                exit();
            }
        }
        
        //pass variables to the view
        $this->description = $parameters["description"];
        $this->pattern = !empty($pattern) ? $pattern : "";
        $this->results = !empty($results) ? $results : array();
        $this->searchError = !empty($searchError) ? $searchError : "";
        $this->buttonId = $buttonId;
    }
    
    protected function footerAction()
    {
    }
    
}
