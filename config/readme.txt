This is tinyCms project which was developed for pages with minimal requirements such as presentation pages cards etc. 
It contains simplified authentication system without possibility to register new user but with possibility to 
add users by admin who can restrict some resources. It is strongly recommended to consider fully featured framework for 
larger projects which requires decent level of security as tinyCms uses most basic security methodologies 
(no tokens etc. -> see /model/Authenticator.class.php).
What is covered:
- Application split according to MVC pattern
- Admin panel for managing content of page
- Styles, scripts (readable and easy to change for your needs)
- Dictionary system, search system, caching system, error handling system, image managing system
- How to start guide which covers how to set config and database for page to make it ready to work
- Tutorial describing structure of the project
    
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), 
to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, 
distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, 
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.



##########################
### How to start guide ###
##########################
In guide I assume that you have access to the host directory ("/") and have domain pointing to this directory ("http://www.your_domain.com") 
and you have access to the database host "your_database_host" or with specified port "your_database_host:3306" 
and you have an email account "developers_email@a.com".
1) Create "your_directory" directory in your host and copy all tinyCms contents to it (or copy directly to main directory '/' if you want).
2) Create "your_database_name" database with "your_database_user" and "your_database_password" and execute following SQL in it:
    CREATE TABLE IF NOT EXISTS `user` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `login` VARCHAR(32) COLLATE utf8_general_ci NOT NULL,
        `email` VARCHAR(32) COLLATE utf8_general_ci NOT NULL,
        `password_hash` VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
        `salt` VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
        `group` VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        
    CREATE TABLE IF NOT EXISTS `code` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `code` VARCHAR(2) COLLATE utf8_general_ci NOT NULL,
        `image` VARCHAR(32) COLLATE utf8_general_ci NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

    CREATE TABLE IF NOT EXISTS `dictionary` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `base` TEXT COLLATE utf8_general_ci NOT NULL,
        `code` VARCHAR(2) COLLATE utf8_general_ci NOT NULL,
        `translation` TEXT COLLATE utf8_general_ci NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
    
    CREATE TABLE IF NOT EXISTS `menu` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `button_id` INT(11) NOT NULL,
        `parent_button_id` INT(11) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

    CREATE TABLE IF NOT EXISTS `button` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(32) COLLATE utf8_general_ci NOT NULL,
        `is_active` BIT(1) NOT NULL,
        `image_first` VARCHAR(32) COLLATE utf8_general_ci DEFAULT NULL,
        `image_second` VARCHAR(32) COLLATE utf8_general_ci DEFAULT NULL,
        `secured` BIT(1) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

    CREATE TABLE IF NOT EXISTS `article` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `button_id` INT(11) NOT NULL,
        `title` VARCHAR(32) COLLATE utf8_general_ci NOT NULL,
        `content` TEXT COLLATE utf8_general_ci NOT NULL,
        PRIMARY KEY (`id`),
        KEY `button_id` (`button_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

    CREATE TABLE IF NOT EXISTS `slider` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `type` VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
        `content` TEXT COLLATE utf8_general_ci NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
    INSERT INTO `slider` (`id`, `type`, `content`) 
    VALUES (1, 'search', '')
    ON DUPLICATE KEY UPDATE `id` = 1;
    INSERT INTO `slider` (`id`, `type`, `content`) 
    VALUES (2, 'contact', '{"email":"","description":""}')
    ON DUPLICATE KEY UPDATE `id` = 2;
    INSERT INTO `slider` (`id`, `type`, `content`)
    VALUES (3, 'sidebox', '')
    ON DUPLICATE KEY UPDATE `id` = 3;
    INSERT INTO `slider` (`id`, `type`, `content`)
    VALUES (4, 'facebook', '')
    ON DUPLICATE KEY UPDATE `id` = 4;
    INSERT INTO `slider` (`id`, `type`, `content`) 
    VALUES (5, 'twitter', '')
    ON DUPLICATE KEY UPDATE `id` = 5;
    INSERT INTO `slider` (`id`, `type`, `content`) 
    VALUES (6, 'youtube', '')
    ON DUPLICATE KEY UPDATE `id` = 6;
    INSERT INTO `slider` (`id`, `type`, `content`) 
    VALUES (7, 'googleplus', '')
    ON DUPLICATE KEY UPDATE `id` = 7;
    INSERT INTO `slider` (`id`, `type`, `content`) 
    VALUES (8, 'linkedin', '')
    ON DUPLICATE KEY UPDATE `id` = 8;
    INSERT INTO `slider` (`id`, `type`, `content`) 
    VALUES (9, 'github', '')
    ON DUPLICATE KEY UPDATE `id` = 9;

    CREATE TABLE IF NOT EXISTS `script` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `string` TEXT COLLATE utf8_general_ci NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        
3) Open "/your_directory/Config/Config.class.php" and set the following constants:
    const DB_PDO = "mysql:host=your_database_host:3306;dbname=your_database_name;charset=utf8";
    const DB_USER = "your_database_user";
    const DB_PASSWORD = "your_database_password";
    const DEV_EMAIL = "developers_email@a.com"; //he will be the sender of new passwords etc.
    const DIRECTORY = "your_directory/"; //or "/" if you copied files to main directory
    const SITE = "http://www.your_domain.com/your_directory/"; //or "http://www.your_domain.com/" if you copied files to main directory
     
4) In "/your_directory/Config/Config.class.php" change used styles and scripts commenting or uncommenting contents of arrays $cssFiles and $jsFiles:
    In $jsFiles array there are files which corresponds to $cssFiles array and files which are not related to it. 
    If you want to change appearance leave uncommented file in $cssFiles and comment the rest and do the same with corresponding files in $jsFiles
    If you have chosen to use some of $jsFiles which is related to $cssFiles (for instance default.js) do not forget to uncomment javascript hiding in view/page.php for chrome smooth unfading effect

5) In "/your_directory/view/preview.php" change in head included style (link tag) to corresponding preview style.
    For instance if you have chosen "style/default.css" then link should have src="style/defaultpreview.css"
    
6) In "/your_directory/view/page.php" change accordingly title and following meta tags:
    description, keywords, author
    
7) In "/your_directory/.htaccess" change corresponding lines:
    RedirectMatch 301 ^/mobile/.*$ http://www.your_domain.com/your_directory/
    RedirectMatch 301 ^/m/.*$ http://www.your_domain.com/your_directory/
  
8) Insert row to the user table in your database:
    INSERT INTO `user` (`id`, `login`, `email`, `password_hash`, `salt`, `group`)
    VALUES (1, 'developer', 'developers_email@a.com', '', '', 'admin')
    ON DUPLICATE KEY UPDATE `id` = 1;

9) Type "http://www.your_domain.com/your_directory/ in browser url bar. In right lower corner of the page there is invisible admin link 
    which redirects to admin panel (if you can not find it just type "http://www.your_domain.com/your_directory/?lang=en&request=admin")

11) Type your email in forget password form (new_password will be sent to your email)

12) Login to admin panel with login: developer password: new_password and click to section password to change it

13) Each section (Menu, Buttons, Articles, Images, Files, Sliders, Dictionary, Password, Users) has HINT area
    which will show info about how to use it. To start do the following:
    - Go to Buttons and add first_button filling its name and confirming
    - Add second_button
    - Go to Articles and click first_button
    - Add paragraph to its content "Hello article_1!" and change its title and confirm
    - Do the same for second_button
    - Go to Menu and add first_button and second_button as main buttons
    - Logout and go to see your page

14) If you have no errors viewing url "http://www.your_domain.com/your_directory/" then set the following in "/your_directory/config/Config.class.php":
    const IS_PRODUCTION_ENVIRONMENT = 1;
    const CACHE_TIME = 3600;



####################
##### Tutorial #####
####################
Contents:
part 1) structure
part 2) config
part 3) controller
part 4) model
part 5) view
part 6) dictionary
part 7) assets
part 8) authentication



part 1) structure
Project contains following directories: cache, config, controller, log, model, script, style, uploads, view and files .htaccess, index.php
In .htaccess you can set time for how long browser should store assets
File index.php includes "config/Project.class.php" and starts the project
Each directory contains .htaccess file which prohibits viewing its contents
Directory cache will store cached content of the page
Directory config contains "Project.class.php" and "Config.class.php" in which you set basic constants, variables and behaviour of the project
Directory controller contains "Controller.class.php", "Responder.class.php" and additional controllers for specific respond
Directory log will store error log files if in "config/Config.class.php" you will set const IS_PRODUCTION_ENVIRONMENT = 1;
Directory model contains "Model.class.php", "Authenticator.class.php", "Dictionary.class.php" and additional models for specific respond
Directory script contains javascript files
Directory style contains style files and subdirectories font, graphics (specific for project)
Directory uploads contains subdirectories files, min, med, max to store uploaded by user files and images (different sizes)
Directory view contains templates for different responses



part 2) config
This directory contains "Project.class.php" which is first to be created in the project. At the beginning in includes "Config.class.php" where
you can set database connections, files to include etc. and which comes with bunch of useful methods. In initialize method of "Porject.class.php"
an error handler is specified. If Config const IS_PRODUCTION_ENVIRONMENT = 1 then all generation of response is put into try-catch which creates
error log files (separate for each day) and redirects to the error page which template is in "view/error.php". If there was no error build method
of "Porject.class.php" is called which calls "controller/Responder.class.php" to get requested language and response. Requested language will be
used by "model/Dictionary.class.php" to get translations. If requested response is not found project is redirected to the default one.
For developing purposes it is better to have in "Config.class.php" following settings:
    const IS_PRODUCTION_ENVIRONMENT = 0; 
    const AUTO_AUTHENTICATE = 1; 
    const CACHE_TIME = 0;
If you expect large images to be uploaded add the following in /etc/php.ini:
    memory_limit = 32M
    upload_max_filesize = 10M
    post_max_size = 20M
In case you do not have access to in add in main app directory following .htaccess:
    php_value upload_max_filesize 10M
    php_value post_max_size 20M
    php_value memory_limit 32M

part 3) controller
First class to be called in this directory is "Responder.class.php" which has to contain proper method in responses section. For instance lets assume
that you want to create "myresponse" in project then you must do the following:
1) Create "myresponse" method in "Responder.class.php"
2) If you want its content to be cached copy "page" method of "Responder.class.php" to it
    First block of this method looks for cached content and returns it if it exists and cache time is not exceeded 
    (see "config/Config.class.php" const CACHE_TIME = 3600;).
    Second block of this method sets Controller and Model classes for this response lets set (it is not required):
        Controller::setController("myresponse"); //if you add it you have to create controller/ControllerMyresponse.class.php 
        Model::setModel("myresponse"); //if you add it you have to create model/ModelMyresponse.class.php
    Third block specifies templete:
        include(Config::getDirectory() . "view/myresponse.php");
    Fourth block of this method caches response if no $_POST parameters are present (we do not want to cache form submissions)
3) Create "view/myresponse.php" template in which you should contain layout of the "myresponse" 
    (however you do not have to specify it see "ajax" or "download" response)
4) If you have set controller in your response method create, "ControllerMyresponse.class.php" 
    ("Myresponse" is important part of name as "Controller.class.php" uses it to find proper file) and add first method to it:
        <?php
        class ControllerMyresponse extends Controller
        {
            protected function firstelementAction()
            {
                $this->text = "Hello, it is firstelement loaded in myresponse :)"; //will be accessible in template via $data->text;
            }
        }
5) Include added file in $phpFiles in "config/Config.class.php"
    "controller/ControllerMyresponse.class.php"
6) Create "view/myresponseElements/firstelement.php" ("firstelement" and "myresponse" are important parts of names 
    as "Controller.class.php" uses it to find proper files and directories) and add in it:
    <?php echo $data->text; ?>
7) Now in "view/myresponse.php" you can load "firstelement" using:
    <?php Controller::load("firstelement"); ?>
8) "ControllerMyresponse.class.php" extends Controller.class.php so it has access to the bunch of its method to secure strings, manage images etc.



part 4) model
1) If you have set model in your response method create, "modelMyresponse.class.php"
    ("Myresponse" is important part of name as "Model.class.php" uses it to find proper file) and add first method to it:
    <?php
    class ModelMyresponse extends Model
    {    
        protected function getItem($conn, $parameters = array())
        {
            $stmt = $conn->prepare("
                SELECT `name`
                FROM `item` 
                WHERE `id` = :id
            ");
            $stmt->execute(array(
                "id" => $parameters["id"]
            ));
            $return = array();
            while ($row = $stmt->fetch()) {
                $return[] = array(
                    "name" => $row["name"]
                );
            }
            return $return;
        }
    }
2) Include added file in $phpFiles in "config/Config.class.php":
    "model/ModelMyresponse.class.php"
3) Add "item" table to your database and put one record to it:
    CREATE TABLE IF NOT EXISTS `item` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
    INSERT INTO `item` (`id`, `name`) 
    VALUES (1, 'firstitem from database')
    ON DUPLICATE KEY UPDATE `id` = 1;
4) Now in "controller/ControllerMyresponse.class.php" you can call model method so change it to:
    class ControllerMyresponse extends Controller
    {
        protected function firstelementAction()
        {
            $this->text = "Hello, it is firstelement loaded in myresponse :)"; //will be accessible in template via $data->text;
            $this->items = Model::run("getItem", array("id" => 1));
        }
    }
5) And in "view/myresponseElements/firstelement.php" you can display it so add following:
    <?php foreach ($data->items as $item): ?> 
        <?php echo $item["name"]; ?>
    <?php endforeach; ?>
6) Remeber that if method of "ModelMyresponse.class.php" will return empty result then 
    "Model.class.php" will change it to null;



part 5) view
1) If you want to include file which does not need controller or model use include:
    Create "view/myresponseElements/templates/static.php"
        <p>Hello world!</p>
    Add it in "view/myresponse.php" using:
        <?php include(Config::getDirectory() . "view/myresponseElements/templates/static.php"); ?>
2) If you want to create link in your template to your page use:
        <a href="<?php Config::getSite() . "&request=page"; ?>">My response page</a>
    getSite will return url with default language of the project (if you want to get url without language use getDefaultSite)



part 6) dictionary
1) To use "model/Dictionary.class.php" simply use in your template or controller instead of:
        echo "text1"
    the following method:
        <?php Dictionary::get("text1"); ?>
2) If dictionary will not find translation for current language it will return "text1"
3) Use admin panel to add translations



part 7) assets
1) If you want to load assets specified by $cssFiles or $jsFiles in "config/Config.class.php" use in your template:
    <?php Config::getCss(); ?>
    <?php Config::getJs(); ?>
2) If you want to add css or js file create and include it in corresponding array ($cssFiles or $jsFiles)
    It will be included by above methods (you may also include path to the external source)



part 8) authentication
1) If you want to use authenticator create its instance in responder or controller and pass to its
    handle method action name (for instance login) got via submitted form $_GET["action]
2) For instance login method looks for $_POST("login") and $_POST("password") and if it finds 
    corresponding record in user table it redirects to the same page but without action get parameter in 
    query string and stores in session authenticated user which can be retrieved 
    by isAuthenticated method of authenticator class.
3) Some authenticator actions redirect to initial url with additional get parameters with info about result
    for instance $_GET("correct")
4) See admin method in "controller/Responder.class.php" or articleAction method 
    in "controller/ControllerPage.class.php" for authenticator usage examples
