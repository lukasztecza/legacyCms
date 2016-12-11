<?php
/**
 * Controller class selects proper controller in creator section
 * and provides usefull methods in tools section
 * Use Controller::setController to specify class and then Controller::load
 */
class Controller
{
    /********************************************************
    ************************ CREATOR ************************
    ********************************************************/
    
    /** @var string Will store set controller class name */
    private static $controllerClassName = null;
    
    /** @var type Will store directory name for view elements */
    private static $directoryName = null;
    
    /**
     * Set controller which is supposed to be accessed via Controller::load
     * and directory where view files will be stored
     * @param string $className For instance to use directory adminElements and class ControllerAdmin pass admin
     */
    public static function setController($className = null)
    {
        if ($className) {
            static::$controllerClassName = "Controller" . ucfirst($className);
            static::$directoryName = lcfirst($className) . "Elements";
        }
    }
    
    /**
     * Include element with corresponding controller object
     * gives access to corresponding action via (object) $data
     * @param string $element View element in corresponding view direcotry
     * @param array $parameters Optional will be passed to corresponding controller method
     */
    public static function load($element, $parameters = array())
    {
        //if controller is not specified throw exception
        if (empty(static::$controllerClassName)) {
            throw new Exception("Controller is not specified for this request: " . Config::getCurrentQuery());
        }
        
        //get view file name
        $viewFileName = Config::getDirectory() . "view/" . static::$directoryName . "/" . $element . ".php";
        //throw exception if view file does not exist
        if (!file_exists($viewFileName)) {
            throw new Exception("File " . $viewFileName . " does not exist");
        }
        
        //choose controller and method name
        $controllerClassName = static::$controllerClassName;
        $methodName = $element . "Action";
        
        //if method exists create controller object and include view file else throw exception
        if (method_exists($controllerClassName, $methodName)) {
            $data = new $controllerClassName($methodName, $parameters);
            include($viewFileName);
        } else {
            throw new Exception("Controller method " . $methodName . " does not exist in controller/" . $controllerClassName);
        }
    }
    
    /**
     * Proper controller object is created via static method load,
     * executes selected method in proper controller and passes (array)$parameters if provided
     * @param string $methodName Extending class method name to be executed
     * @param array $parameters Optional will be passed to invoked method
     */
    protected function __construct($methodName, $parameters = array())
    {
        if (!empty($parameters)) {
            $this->$methodName($parameters);
        } else {
            $this->$methodName();
        }
    }
    
    /********************************************************
    ************************* TOOLS *************************
    ********************************************************/
    
    /** @var array Images extensions and mime types */
    protected $imageCheck = array(
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "png" => "image/png",
        "gif" => "image/gif"
    );
    
    /** @var array Files extensions and mime types */
    protected $fileCheck = array(
        "txt" => "text/plain",
        "pdf" => "application/pdf",
        "odt" => "application/vnd.oasis.opendocument.text",
        "ods" => "application/vnd.oasis.opendocument.spreadsheet",
        "odp" => "application/vnd.oasis.opendocument.presentation",
        "doc" => "application/msword",
        "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "xls" => "application/vnd.ms-excel",
        "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "ppt" => "application/vnd.ms-powerpoint",
        "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        "mp3" => "audio/mpeg"
    );
    
    /**
     * Secure $data in order to avoid XSS accepts int, float, string
     * @param mixed $data Variable to be secured
     * @return mixed Secured string, int or float
     */
    protected function secure($data)
    {
        if (is_string($data)) {
            $data = trim($data);
            $data = implode("", explode("\\", $data));
            $data = htmlspecialchars($data);
            return $data;
        } elseif (is_int($data)) {
            return (int)$data;
        } elseif (is_numeric($data)) {
            return (float)$data;
        }
    }
    
    /**
     * Create valid slug lowercassing and replacing all non letters, digits or dots by bars 
     * @param string $data Invalid slug
     * @return string Valid slug
     */
    protected function slugify($data)
    {
        if (is_string($data)) {
            $data = preg_replace('/[^0-9A-Za-z.]/', '-', $data);
            $data = strtolower(trim($data, '-'));
            return $data;
        }
    }
    
    /**
     * Move single letters to the new line if occurs in the end of a line
     * @param string $text String to be converted
     * @return string String with all spaces after single letter converted to &nbsp
     */
    protected function switchSpaces($text)
    {
        return preg_replace(array('/ ([a-zA-Z]) /', '/^([a-zA-Z]) /'), array(' $1&nbsp;', '$1&nbsp;'), $text);
    }
    
    /**
     * Get images from uploads directory
     * @return array Array of image names
     */
    protected function getImages()
    {
        $return = array();
        foreach (glob(Config::getDirectory() . "uploads/min/*.*") as $filename) {
            $fileExtension = substr($filename, strrpos($filename, ".") + 1);
            if (getimagesize($filename) && array_key_exists(strtolower($fileExtension), $this->imageCheck)) {
                $return[] = substr($filename, strpos($filename, "uploads/min/") + 12);
            }
        }
        return $return;
    }
    
    /**
     * Get files from uploads directory
     * @return array Array of file names
     */
    protected function getFiles()
    {
        $return = array();
        foreach (glob(Config::getDirectory() . "uploads/files/*.*") as $filename) {
            $fileExtension = substr($filename, strrpos($filename, ".") + 1);
            if (array_key_exists(strtolower($fileExtension), $this->fileCheck)) {
                $return[] = substr($filename, strpos($filename, "uploads/files/") + 14);
            }
        }
        return $return;
    }
    
    /**
     * Check if email data is correct
     * @param string $userEmail User email
     * @param string $userMessage User message
     * @return array Contains keys: email, message, error
     */
    protected function checkEmail($userEmail, $userMessage)
    {
        $return = array("email" => "", "message" => "", "error" => "");
        $return["email"] = filter_var($userEmail, FILTER_SANITIZE_EMAIL);
        $return["message"] = $this->secure($userMessage);
        
        //validate inputed email
        if (empty($return["email"])) {
            $return["error"] .= Dictionary::$texts[4];
        } elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $return["error"] .= Dictionary::$texts[5];
            
        //validate inputed message
        } elseif (empty($return["message"])) {
            $return["error"] .= Dictionary::$texts[6];
        }
        
        return $return;
    }
    
    /**
     * Send message to the specified email
     * @param string $sendTo Email where messege is supposed to be sent
     * @param string $userEmail Sender email
     * @param string $title Email title
     * @param string $userMessage Email content
     */
    protected function sendEmail($sendTo, $userEmail, $title, $userMessage)
    {
        $headers = "From: <" . $userEmail . ">\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8";
        mail($sendTo, $title, $userMessage, $headers);
    }
    
    /**
     * Clear cache files
     */
    protected function clearCache()
    {
        $cacheFiles = glob(Config::getDirectory() . "cache/*.*");
        foreach ($cacheFiles as $filename) {
            unlink($filename);
        }
    }
    
    /**
     * Upload image so it always has min, med, max sizes versions available preserving transparency
     * If size (min, med, max) is larger than image width then image will not be enlarged
     */
    protected function uploadImage($uploadedFile)
    {
        $sizes = array("min" => array("width" => 100), "med" => array("width" => 400), "max" => array("width" => 1000));
        $imageSize = getimagesize($uploadedFile["tmp_name"]);
        foreach ($sizes as $size => $value) {
            $proportion = $sizes[$size]["width"] / $imageSize[0];
            //do not enlarge small images
            if ($proportion > 1) {
                $sizes[$size]["width"] = $imageSize[0];
                $sizes[$size]["height"] = $imageSize[1];
            } else {
                $sizes[$size]["height"] = (int)($imageSize[1] * $proportion);
            }
        }

        //create image according to mime type
        switch ($uploadedFile["type"]) {
                case $this->imageCheck["jpg"]:
                $image = imagecreatefromjpeg($uploadedFile["tmp_name"]);
                break;
            case $this->imageCheck["png"]:
                $image = imagecreatefrompng($uploadedFile["tmp_name"]);
                break;
            case $this->imageCheck["gif"]:
                $image = imagecreatefromgif($uploadedFile["tmp_name"]);
                break;
        }
        
        //save all versions of image
        foreach ($sizes as $size => $values) {
            //create image according to mime type
            switch ($uploadedFile["type"]) {
                case $this->imageCheck["jpg"]:
                    $image = imagecreatefromjpeg($uploadedFile["tmp_name"]); 
                    break;
                case $this->imageCheck["png"]:
                    $image = imagecreatefrompng($uploadedFile["tmp_name"]); 
                    break;
                case $this->imageCheck["gif"]:
                    $image = imagecreatefromgif($uploadedFile["tmp_name"]); 
                    break;
            }
       
            //create resized images (preserve transparency) 
            $resizedImage = imagecreatetruecolor($values["width"], $values["height"]);
            $transparentIndex = imagecolortransparent($image);
            if ($uploadedFile["type"] === $this->imageCheck["gif"] && $transparentIndex < imagecolorstotal($image)) {
                $transparentColor = imagecolorsforindex($image, $transparentIndex);
                $transparencyId = imagecolorallocate($resizedImage, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                imagefill($resizedImage, 0, 0, $transparencyId);
                imagecolortransparent($resizedImage, $transparencyId);
            } elseif ($uploadedFile["type"] === $this->imageCheck["png"]) {
                imagealphablending($resizedImage, false);
                $transparencyId = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
                imagefill($resizedImage, 0, 0, $transparencyId);
                imagesavealpha($resizedImage, true);            
            }
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $values["width"], $values["height"], $imageSize[0], $imageSize[1]);
            
            //save image
            switch ($uploadedFile["type"]) {
                case $this->imageCheck["jpg"]:
                    imagejpeg($resizedImage, Config::getDirectory() . "uploads/" . $size . "/" . $this->slugify($uploadedFile["name"]), 100);
                    break;
                case $this->imageCheck["png"]:
                    imagepng($resizedImage, Config::getDirectory() . "uploads/" . $size . "/" . $this->slugify($uploadedFile["name"]), 0); 
                    break;
                case $this->imageCheck["gif"]:
                    imagegif($resizedImage, Config::getDirectory() . "uploads/" . $size . "/" . $this->slugify($uploadedFile["name"]));
                    break;
            }
        }
    }   
    
}
