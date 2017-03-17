<?php
/**
 * Prepare response for request
 */
class Responder
{   
    /**
     * Get requested language
     * @retrun string Requested language
     */
    public function getLanguage()
    {
        //get language from request or form cookie
        $lang = !empty($_GET["lang"]) ? $_GET["lang"] : null;
        if (!$lang && !empty($_COOKIE["language"])) {
            $lang = $_COOKIE["language"];
        }
        return $lang;
    }
    
    /**
     * Get requested response
     * @retrun string Requested response
     */
    public function getRequest()
    {
        return !empty($_GET["request"]) ? $_GET["request"] : null;
    }
    
    /********************************************************
    *********************** RESPONSES ***********************
    ********************************************************/
    
    /**
     * Regular page response
     */
    public function page()
    {
        //return cache if exists for passed query and if there are no post parameters
        $cacheFile = Config::getDirectory() . "cache/" . md5(Config::getCurrentQuery()) . ".html";
        if (empty($_POST) && file_exists($cacheFile) && (time() - Config::CACHE_TIME < filemtime($cacheFile))) {
            include($cacheFile);exit();
        }
        
        //set controller and model
        Controller::setController("page");
        Model::setModel("page");
        
        //include page layout
        include(Config::getDirectory() . "view/page.php");
        
        //cache response if there are no post parameters
        if (empty($_POST)) {
            $file = fopen($cacheFile, 'w');
            fwrite($file, ob_get_contents());
            fclose($file);
        }
    }
    
    /** 
     * Preview response simplified layout 
     */
    public function preview()
    {
        //if requested file exists show preview else redirect to homepage
        $preview = !empty($_GET["file"]) ? $_GET["file"] : null;
        if (!empty($preview) && file_exists(Config::getDirectory() . "uploads/max/" . $preview)){
            include(Config::getDirectory() . "view/preview.php");
        } else {
            header("Location:" . Config::getSite());exit();
        }
    }
    
    /** 
     * Admin response layout with authentication 
     */
    public function admin()
    {
        //redirect to default language if needed
        if (Config::getLanguage() !== Config::getDefaultLanguage()) {
            Config::setSite("?lang=" . Config::getDefaultLanguage());
            header("Location:" . Config::getSite() . "&request=admin");exit();
        }

        //set controller and model
        Controller::setController("admin");
        Model::setModel("admin");
        
        //get admin action and let authenticator handle it
        $action = !empty($_GET["action"]) ? $_GET["action"] : null;
        $authenticator = new Authenticator();
        $authenticator->handleAction($action);
        
        //pass information about user to view and include layout
        $loggedIn = $authenticator->isAuthenticated();
        unset($action, $authenticator);
        include(Config::getDirectory() . "view/admin.php");
    }
    
    /**
     * Browser request for downloading a file 
     */
    public function download()
    {
        //download file if exists else redirect
        if (!empty($_GET["file"])) {
            //search for the proper file
            $file = explode('/', $_GET["file"]);
            $file = Config::getDirectory() . "uploads/files/" . array_pop($file);
            if (file_exists($file)) {
                //download file
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit();
            } else {
                header("Location:" . Config::getSite());exit();
            }
        } else {
            header("Location:" . Config::getSite());exit();
        }
    }
    
    /**
     * Handle ajax requests
     */
    public function ajax()
    {
        //get proper ajax response
        switch (true) {
            case (array_key_exists("userEmail", $_POST)):
                Controller::setController("page");
                Controller::load("emailForm", array(
                    "email" => $_POST["recieverEmail"],
                    "description" => "",
                    "buttonId" => $_GET["article"]
                ));exit();
                break;
            case (array_key_exists("search", $_POST)):
                Controller::setController("page");
                Model::setModel("page");
                Controller::load("searchbarForm");exit();
                break;
        }
    }
    
}
