<?php
/**
 * Project initializator and builder
 */
class Project
{
    /**
     * Include configuration file and cuase error if more than one Project object is created
     */
    public function __construct()
    {
        include("Config.class.php");
    }
    
    /**
     * Initialize project, set error handlers
     */
    public function initialize()
    {
        //set the absolute path to the project root directory
        Config::setDirectory();
        
        //send output only when finished (see ob_end_flush)
        ob_start();
        
        //handle errors for production environment only
        if (Config::IS_PRODUCTION_ENVIRONMENT) {
            register_shutdown_function(array($this, "shutDownHandler"));
            set_error_handler(array($this, "errorHandler"));
            
            //handle exceptions using errorHandler
            try {
                $this->build();
            } catch (Exception $e) {
                $this->errorHandler($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            }
        } else {
            $this->build();
        }
        
        //send the output
        ob_end_flush();
    }
    
    /**
     * Build response based on request
     */
    private function build()
    {
        //in case of error display the error page and exit
        $error = !empty($_GET["error"]) ? true : false;
        if ($error) {
            include(Config::getDirectory() . "view/error.php");exit();
        }
        
        //load php classes, get requested language, get requested response
        Config::getPhp();
        $responder = new Responder();
        $language = $responder->getLanguage();
        $language = (strlen($language) === 2 && preg_match("/[a-z]{2}/", $language)) ? $language : false;
        $request = $responder->getRequest();
        
        //execute response method setting proper language and request
        switch (true) {
            case (method_exists($responder, $request) && $language):
                Config::setSite("?lang=" . $language);
                Config::setLanguage($language);
                $responder->$request();
                break;
            case ($language):
                Config::setSite("?lang=" . $language);
                header("Location:" . Config::getSite() . "&request=page");exit();
            default:
                Config::setSite("?lang=" . Config::getDefaultLanguage());
                header("Location:" . Config::getSite() . "&request=page");exit();
        }
    }
    
    /**
     * Handle errors, create log record and redirect to error page
     * @param string $level Error level
     * @param string $message Error message
     * @param string $file File where error occured
     * @param string $line Line in file where error occured
     * @param string $context Error context
     */
    public function errorHandler($level = null, $message = null, $file = null, $line = null, $context = null)
    {
        //update or create a file for logging errors (one file per day)
        $log = Config::getDirectory() . "log/" . date("Y-m-d", time()) . "-error.log";
        $content = "";
        if (file_exists($log)) {
            $content = file_get_contents($log);
        }
        $content .=
            "Time: " . date("Y-m-d h:i:s", time()) .
            " | Level: " . $level .
            " | Message: " . $message .
            " | File: " . $file .
            " | Line: " . $line .
        "\n";
        file_put_contents($log, $content);
        
        //redirect to error page
        header("Location:" . Config::getDefaultSite() . "?error=true");exit();
    }
    
    /**
     * Handle fatal errors using errorHandler
     */
    public function shutDownHandler()
    {
        //get last error and pass it to errorHandler
        $error = error_get_last();
        if ($error) {
            $this->errorHandler($error["type"], $error["message"], $error["file"], $error["line"]);
        }
    }
    
}
