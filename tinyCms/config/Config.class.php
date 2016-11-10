<?php
// this is some test

/**
 * Project configuration file
 * Includes setting section an methods section
 */
class Config
{
    /*********************************************************
    ************************ SETTINGS ************************
    *********************************************************/
    
    /** When set to 0 errors are not handled */
    const IS_PRODUCTION_ENVIRONMENT = 0;
    
    /** When set to 1 user will be authenticated always */
    const AUTO_AUTHENTICATE = 0;
    
    /** PDO database settings */
    const DB_PDO = "mysql:host=your_database_host:3306;dbname=your_database_name;charset=utf8";
    
    /** Database user name */
    const DB_USER = "your_database_user";
    
    /** Database password */
    const DB_PASSWORD = "your_database_password";   
        
    /** Developer's email */
    const DEV_EMAIL = "developers_email@a.com";
    
    /** Directory where project is set relatively to server root */
    const DIRECTORY = "your_directory/";
    
    /** Default site of the the project including relative directory */
    const SITE = "http://www.your_domain.com/your_directory/";
    
    /** Default language of the site */
    const LANGUAGE = "en";
    
    /** Cache time in seconds */
    const CACHE_TIME = 0;
    
    /** Submenu visibility while in subsite */
    const SUBMENU_VISIBLE = 1;
    
    /** @var string Will store absolute project root directory path */
    private static $directory = null;
    
    /** @var string Will store url with selected language */
    private static $site = null;
    
    /** @var string Will store selected language */
    private static $language = null;
    
    /** Php logic files to include */
    private static $phpFiles = array(
        "controller/Controller.class.php",
        "controller/Responder.class.php",
        "model/Model.class.php",
        "controller/ControllerPage.class.php",
        "controller/ControllerAdmin.class.php",
        "model/Authenticator.class.php",
        "model/ModelPage.class.php",
        "model/ModelAdmin.class.php",
        "model/Dictionary.class.php"
    );
    
    /** Style files to include */
    private static $cssFiles = array(
//        "style/side.css",
//        "style/simple.css",
//        "style/win.css",
        "style/default.css",
//        "style/elegant.css"
    );
    
    /** Script files to include */
    private static $jsFiles = array(
        "script/tools.js",
        "script/cookie.js",
        "http://maps.googleapis.com/maps/api/js?key=AIzaSyB807bK54tL2b9XmwZcid40OvhQxK8bjaU",
        "script/map.js",
        "script/info.js",
//        "script/side.js",
//        "script/simple.js",
//        "script/win.js",
//        "script/default.js",
//        "script/elegant.js"
    );
    
    /********************************************************
    ************************ METHODS ************************
    ********************************************************/
    
    /**
     * Include logic files (php classes)
     */
    public static function getPhp()
    {
        foreach (static::$phpFiles as $file) {
            include(static::$directory . $file);
        }
    }
    
    /**
     * Build link tags to include css files
     * @return string Html link tags for css
     */
    public static function getCss()
    {
        $return = "";
        foreach (static::$cssFiles as $file) {
            if (strpos($file, "style/") === 0) {
                $return .= "<link href='" . static::SITE . $file . "' rel='stylesheet' type='text/css' />";
            } elseif (strpos($file, "http://") === 0 || strpos($file, "https://") === 0) {
                $return .= "<link href='" . $file . "' rel='stylesheet' type='text/css' >";
            }
        }
        return $return;
    }
    
    /**
     * Build script tags to include js files
     * @return string Html script tags for js
     */
    public static function getJs()
    {
        $return = "";
        foreach (static::$jsFiles as $file) {
            if (strpos($file, "script/") === 0) {
                $return .= "<script src='" . static::SITE . $file . "'></script>";
            } elseif (strpos($file, "http://") === 0 || strpos($file, "https://") === 0) {
                $return .= "<script src='" . $file . "'></script>";
            }
        }
        return $return;
    }
    
    /**
     * Set absolute path to project directory
     */
    public static function setDirectory()
    {
        static::$directory = $_SERVER["DOCUMENT_ROOT"] . static::DIRECTORY;
    }
    
    /**
     * Get absolute path to project directory
     * @return string Absolute path to the project directory
     */
    public static function getDirectory()
    {
        return static::$directory;
    }
    
    /**
     * Get default site language
     * @return string Default site language
     */
    public static function getDefaultLanguage()
    {
        return static::LANGUAGE;
    }
    
    /**
     * Set language of the site
     * @param string $language Site language
     */
    public static function setLanguage($language)
    {
        static::$language = $language;
    }
    
    /**
     * Get language of the site
     * @return string Site language
     */
    public static function getLanguage()
    {
        return static::$language ? static::$language : static::LANGUAGE;
    }
    
    /**
     * Get project default site
     * @return string Default site url
     */
    public static function getDefaultSite()
    {
        return static::SITE;
    }
    
    /**
     * Set project site (include lang for instance)
     * @param string $urlParameters Url parameters to be added to site
     */
    public static function setSite($urlParameters)
    {
        static::$site = static::SITE . $urlParameters;
    }
    
    /**
     * Get project site including parameters
     * @return string Site url
     */
    public static function getSite()
    {
        return !empty(static::$site) ? static::$site : static::SITE;
    }
    
    /**
     * Get previous site
     * @return string Previous site url
     */
    public static function getPreviousSite()
    {
        return !empty($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : static::$site;
    }
    
    /**
     * Get request parameters which led to current site
     * @return string Current site query string
     */
    public static function getCurrentQuery()
    {
        return !empty($_SERVER["QUERY_STRING"]) ? "?" . $_SERVER["QUERY_STRING"] : "";
    }
    
}
