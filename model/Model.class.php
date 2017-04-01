<?php
/**
 * Ceate connection available via $conn variable
 * Select proper model and execute passed method
 * Use Model::setModel to specify class and then Model::run
 */
class Model
{
    private static $modelClassName = null;
    private static $modelObject = null;
    protected static $conn = null;
    
    /**
     * Set model which is supposed to be accessed via Model::run
     * and create connection
     * @param string $className For instance to use ModelAdmin pass admin
     */
    public static function setModel($className = null)
    {
        //define model to use
        if ($className) {
            $modelClassName = static::$modelClassName = "Model" . ucfirst($className);
            static::$modelObject = new $modelClassName();
        }
        
        //connect to the database
        if (empty(static::$conn)) {
            $conn = new PDO(Config::DB_PDO, Config::DB_USER, Config::DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            static::$conn = $conn;
        }
    }
    
    /**
     * Execute the command if exists in extending class
     * @param string $methodName Extending class method name to be executed
     * @param array $parameters Optional will be passed to invoked method
     */
    public static function run($methodName, $parameters = array())
    {
        $modelClassName = static::$modelClassName;
        if (method_exists($modelClassName, $methodName)) {
            //call method and pass parameters if provided
            $modelObject = static::$modelObject;
            $conn = static::$conn;
            if (!empty($parameters)) {
                $modelResponse = $modelObject->$methodName($conn, $parameters);
            } else {
                $modelResponse = $modelObject->$methodName($conn);
            }
            
            //return response if exists
            return !empty($modelResponse) ? $modelResponse : null;
            
        //class is specified but method does not exists
        } elseif ($modelClassName) {
            throw new Exception("Model method " . $methodName . " does not exist in model/" . $modelClassName);
        
        //class is not specified (see setModel())
        } else {
            throw new Exception("Model is not specified for this request: " . Config::getCurrentQuery());
        }
    }
    
    /** 
     * Disallow construction of model objects without command run 
     */
    private function __construct()
    {
    }
    
}
