<?php
/**
 * Authentication module
 * Needs database tables: user(filled)
 * See config/readme.txt for details
 */
class Authenticator extends Model
{
    /** Specify actions for authenticated user */
    private $authenticatedActions = array(
        "logout",
        "password",
        "manage"
    );
    
    /** Specify actions for not authenticated user */
    private $unauthenticatedActions = array(
        "login",
        "generate"
    );
    
    /**
     * Start the session
     */
    public function __construct()
    {
        //check if connection is accessible
        if (!static::$conn) {
            Model::setModel();
        }
        $this->connection = static::$conn;
        
        //start the session
        session_start();
        session_regenerate_id(true);
    }
    
    /**
     * Execute action if it is allowed
     * @param string $action Name of the method to execute
     */
    public function handleAction($action)
    {
        //if action is not allowed redirect to deafault site
        switch (true) {
            case ($this->isAuthenticated() && in_array($action, $this->authenticatedActions)):
                $this->$action();
                break;
            case (!$this->isAuthenticated() && in_array($action, $this->unauthenticatedActions)):
                $this->$action();
                break;
            case ($action):
                $this->logout();
                header("Location: " . Config::getSite() . "&request=page");exit();
                break;
        }
    }
    
    /**
     * Check if user is authenticated
     * @return string Authenticated user or false
     */
    public function isAuthenticated()
    {
        return !empty($_SESSION["authenticated"]) ? $_SESSION["authenticated"] : false;
    }
    
    /**
     * Register action looks for $_POST email parameter and creates
     * new user generating new login and password for passed email
     */
    private function manage() {
        //prevent not admin from using this action 
        if ( $_SESSION["authenticated"]["group"] !== "admin") {
            $this->logout();
            header("Location: " . Config::getSite() . "&request=page");exit();
        }
    
        //prepare variables
        $conn = $this->connection;
        $createEmail = !empty($_POST["user_create"]) ? $_POST["user_create"] : null;
        $createResult = "";
        $deleteEmail = !empty($_POST["user_delete"]) ? $_POST["user_delete"] : null;
        $deleteResult = "";
        
        //if email to create has incorrect format note it in result
        if (!$createEmail) {
            $createResult = "empty";        
        } elseif (!filter_var($createEmail, FILTER_VALIDATE_EMAIL)) {
            $createResult = "incorrect";
        }
        
        //if email to delete has incorrect format note it in result
        if (!$deleteEmail) {
            $deleteResult = "empty";        
        } elseif (!filter_var($deleteEmail, FILTER_VALIDATE_EMAIL)) {
            $deleteResult = "incorrect";
        }
        
        //delete user
        if ($deleteResult === "") {
            $stmt = $conn->prepare("
                DELETE FROM `user`
                WHERE `email` = :email
                AND `group` = 'user'
            ");
            $stmt->execute(array("email" => $deleteEmail));
            $deleteResult = "success";
        }
        
        //if email already exists redirect
        if ($createResult === "") {
            $stmt = $conn->prepare("
                SELECT `email`
                FROM `user`
                WHERE `email` = :email
            ");
            $stmt->execute(array(
                "email" => $createEmail
            ));
            $existing = array();
            while ($row = $stmt->fetch()) {
                $existing = array(
                    "email" => $row["email"]
                );
            }
            if (!empty($existing["email"])) {
                $createResult = "duplicated";
            }
        }
        
        //if email is valid create an account and send email to user
        if ($createResult === "") {
            $login = substr($createEmail, 0, strpos($createEmail, "@"));
            $newPassword = $this->getRandomString();
            $salt = $this->getRandomString();
            $passwordHash = hash("sha256", $newPassword . $salt);
            $stmt = $conn->prepare("
                INSERT INTO `user` (`login`, `email`, `password_hash`, `salt`, `group`)
                VALUES (:login, :email, :passwordHash, :salt, :group)
            ");
            $stmt->execute(array(
                "login" => $login,
                "email" => $createEmail,
                "passwordHash" => $passwordHash,
                "salt" => $salt,
                "group" => 'user'
            ));
            $createResult = "success";
        }
        
        //send email to user and redirect
        if ($createResult === "success") {
            $headers = "From: <" . Config::DEV_EMAIL . ">\r\n";
            $headers .= "Content-Type: text/plain; charset=utf-8";
            mail(
                $createEmail,
                "New password",
                "Your new password is: " . $newPassword . " Your login is: " . $login,
                $headers
            );
        }
        header("Location: " . Config::getSite() . "&request=admin&extension=users&create=" . $createResult . "&delete=" . $deleteResult);exit();
    }
    
    
    /**
     * Login action looks for $_POST password and login parameters,
     * calls compare Password method and on success sets authenticated
     * $_SESSION parameter to array with keys: user, email, group
     */
    private function login()
    {
        $redirect = substr(Config::getDefaultSite() . Config::getCurrentQuery(), 0, strpos(Config::getDefaultSite() . Config::getCurrentQuery(), "&action=login"));
        //auto authenticate user (dev purposes only)
        if (Config::AUTO_AUTHENTICATE) {
            $_SESSION["authenticated"] = array("login" => "developer", "group" => "admin", "email" => Config::DEV_EMAIL);
            header("Location: " . $redirect);exit();
        }
        $password = !empty($_POST["password"]) ? $_POST["password"] : null;
        $login = !empty($_POST["login"]) ? $_POST["login"] : null;
        if ($data = $this->getAuthenticationData($login, $password)) {
            $_SESSION["authenticated"] = array(
                "login" => $data["login"],
                "email" => $data["email"],
                "group" => $data["group"]
            );
            //if group is not proper for admin request inform about error
            if ($_GET["request"] === "admin" && $_SESSION["authenticated"]["group"] !== "admin") {
                $this->logout();
                header("Location: " . $redirect . "&correct=false");exit();                
            }
            header("Location: " . $redirect);exit();
        } else {
            header("Location: " . $redirect . "&correct=false");exit();
        }
    }
    
    /**
     * Logout action clears $_SESSION parameters, destroys session and related cookie
     */
    private function logout()
    {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
           setcookie(session_name(), '', time() - 86400, '/');
        }
        session_destroy();
    }
    
    /**
     * Create new random password for passed correct email and sends message with new
     */
    private function generate()
    {
        $passedEmail = !empty($_POST["email"]) ? filter_var($_POST["email"], FILTER_SANITIZE_EMAIL) : "";
        
        //if email has incorrect format redirect
        if (!filter_var($passedEmail, FILTER_VALIDATE_EMAIL)) {
            header("Location: " . Config::getSite() . "&request=admin&email=incorrect");exit();
        }
        
        //if email does not match redirect
        $conn = $this->connection;
        $stmt = $conn->prepare("
            SELECT `login`, `email`
            FROM `user`
            WHERE `email` = :email
            AND `group` = 'admin'
        ");
        $stmt->execute(array(
            "email" => $passedEmail
        ));
        $confirmed = array();
        while ($row = $stmt->fetch()) {
            $confirmed = array(
                "login" => $row["login"],
                "email" => $row["email"]
            );
        }
        if (empty($confirmed["email"])) {
            header("Location: " . Config::getSite() . "&request=admin&email=mismatch");exit();
        }
        
        //user is confirmed so create new password, salt and password hash
        $newPassword = $this->getRandomString();
        $salt = $this->getRandomString();
        $passwordHash = hash("sha256", $newPassword . $salt);
        $this->generateNewPassword(array(
            "email" => $confirmed["email"],
            "passwordHash" => $passwordHash,
            "salt" => $salt
        ));
        
        //send email to user and redirect
        $headers = "From: <" . Config::DEV_EMAIL . ">\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8";
        mail(
            $confirmed["email"],
            "New password",
            "Your new password is: " . $newPassword . " Your login is: " . $confirmed["login"],
            $headers
        );
        header("Location: " . Config::getSite() . "&request=admin&email=correct");exit();
    }
    
    /**
     * Updates password for passed correct email and sends message with new
     */
    private function password()
    {
        //prevent not admin from using this action 
        if ( $_SESSION["authenticated"]["group"] !== "admin") {
            $this->logout();
            header("Location: " . Config::getSite() . "&request=page");exit();
        }
    
        //require password to be minimum 6 characters and not include \
        $newPassword = !empty($_POST["password"]) ? $_POST["password"] : "";
        if (strlen($newPassword) < 6 || strpos($newPassword, "\\") !== false) {
            header("Location: " . Config::getSite() . "&request=admin&extension=password&correct=false");exit();
        }
        
        //create salt and password hash
        $salt = $this->getRandomString();
        $passwordHash = hash("sha256", $newPassword . $salt);
        if ($_SESSION["authenticated"]["email"]) {
            $this->generateNewPassword(array(
                "email" => $_SESSION["authenticated"]["email"],
                "passwordHash" => $passwordHash,
                "salt" => $salt
            ));
        
            //send email to user and redirect
            $headers = "From: <" . Config::DEV_EMAIL . ">\r\n";
            $headers .= "Content-Type: text/plain; charset=utf-8";
            mail(
                $_SESSION["authenticated"]["email"],
                "New password",
                "Your new password is: " . $newPassword . " Your login is: " . $_SESSION["authenticated"]["login"],
                $headers
            );
            header("Location: " . Config::getSite() . "&request=admin&extension=password&correct=true");exit();
        }
    }
    
    /**
     * Check if password is correct for login
     * @param string $login Login
     * @param string $password Password
     * @retutn array User login data or false
     */
    private function getAuthenticationData($login, $password)
    {
        //get login data
        $conn = $this->connection;
        $stmt = $conn->prepare("
            SELECT `login`, `email`, `password_hash`, `salt`, `group`
            FROM `user`
            WHERE `login` = :login
        ");
        $stmt->execute(array(
            "login" => $login
        ));
        $loginData = array();
        while ($row = $stmt->fetch()) {
            $loginData = array(
                "login" => $row["login"],
                "email" => $row["email"],
                "passwordHash" => $row["password_hash"],
                "salt" => $row["salt"],
                "group" => $row["group"]
            );
        }

        //for correct login data return true else return false
        if (!empty($loginData) && hash("sha256", $password . $loginData["salt"]) === $loginData["passwordHash"]) {
            return $loginData;
        }
        return false;
    }
    
    /**
     * Generate new login data
     * @param array $parameters needs to contain keys: passwordHash, salt, email
     */
    private function generateNewPassword($parameters = array())
    {
        $conn = $this->connection;
        $stmt = $conn->prepare("
            UPDATE `user`
            SET `password_hash` = :passwordHash, `salt` = :salt
            WHERE `email` = :email AND `group` = 'admin'
        ");
        $stmt->execute(array(
            "passwordHash" => $parameters["passwordHash"],
            "salt" => $parameters["salt"],
            "email" => $parameters["email"]
        ));
    }
    
    private function getRandomString() {
        if (function_exists('mcrypt_create_iv')) {
            return md5(mcrypt_create_iv(10, MCRYPT_DEV_URANDOM));
        } elseif (function_exists('random_bytes')) {
            return md5(random_bytes(10));
        } else {
            return md5(rand(1,1000000));
        }
    }
}
