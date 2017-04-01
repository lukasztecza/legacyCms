#!/usr/bin/env bash

# Set versions
APACHE_VERSION=2.4.7*
HOST=localhost
PORT=5678
MYSQL_VERSION=5.5
MYSQL_ROOT_PASSWORD=pass
MYSQL_USER=user
MYSQL_USER_PASSWORD=pass
MYSQL_HOST=localhost
MYSQL_DATABASE=tcms
PHP_VERSION=7.1

# Export variable to fix "dpkg-preconfigure: unable to re-open..." error
export DEBIAN_FRONTEND=noninteractive

# Add ondrej php repository
sudo add-apt-repository ppa:ondrej/php
apt-get update

# Install basic tools
apt-get install -y vim curl

# Install apache and create symlink pointing default apache web dir to /vagrant
apt-get install -y apache2="$APACHE_VERSION"

# Create symlink from default apache web dir to /vagrant
if ! [ -L /var/www/html ]; then
    rm -rf /var/www
    mkdir /var/www
    ln -fs /vagrant /var/www/html
fi

# Set ServerName to fix "AH00558: apache2: Could not reliably determine..." error
if ! fgrep ServerName /etc/apache2/apache2.conf; then
    echo "ServerName $HOST" | sudo tee -a /etc/apache2/apache2.conf
fi

# Set mysql answers and install mysql-server
debconf-set-selections <<< "mysql-server mysql-server/root_password password $MYSQL_ROOT_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $MYSQL_ROOT_PASSWORD"
apt-get install -y mysql-server-"$MYSQL_VERSION" mysql-client-"$MYSQL_VERSION"

# Set key_buffer_size to fix "Using unique option prefix key_buffer instead of key_buffer_size..." warning
if ! fgrep key_buffer_size /etc/mysql/my.cnf; then
    echo 'key_buffer_size = 16M' | sudo tee -a /etc/mysql/my.cnf
fi

# Install mysql-client
apt-get install -y mysql-client-"$MYSQL_VERSION"

# Install php and modules
apt-get install -y php"$PHP_VERSION" php-curl php-mysql php-gd

# Display all errors for php
sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/"$PHP_VERSION"/apache2/php.ini
sed -i "s/display_errors = .*/display_errors = On/" /etc/php/"$PHP_VERSION"/apache2/php.ini

# Allow usage of .htaccess files inside /var/www/html
cat <<EOL >> /etc/apache2/apache2.conf
<Directory /var/www/html>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
EOL

# Restart apache
service apache2 restart

# Change .htaccess file 
sed -i "s/www.your_domain.com\//$HOST/" /vagrant/.htaccess
sed -i "s/your_directory/\//" /vagrant/.htaccess

# Set up database (note no space after -p)
mysql -u root -p"$MYSQL_ROOT_PASSWORD" <<EOL
CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE;
GRANT ALL PRIVILEGES ON $MYSQL_DATABASE.* TO $MYSQL_USER@$MYSQL_HOST IDENTIFIED BY '$MYSQL_USER_PASSWORD';
FLUSH PRIVILEGES;
EOL

if [ $? != "0" ]; then
    echo "[Error]: Database creation failed. Aborting."
    exit 1
fi

# Create tables needed by tinyCms
mysql -u "$MYSQL_USER" -p"$MYSQL_USER_PASSWORD" -h $MYSQL_HOST $MYSQL_DATABASE <<"EOL"
CREATE TABLE IF NOT EXISTS `user` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `login` VARCHAR(32) COLLATE utf8_bin NOT NULL,
    `email` VARCHAR(32) COLLATE utf8_bin NOT NULL,
    `password_hash` VARCHAR(255) COLLATE utf8_bin NOT NULL,
    `salt` VARCHAR(255) COLLATE utf8_bin NOT NULL,
    `group` VARCHAR(255) COLLATE utf8_bin NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `user` (`login`, `email`, `password_hash`, `salt`, `group`)
VALUES ('developer', 'developers_email@a.com', '', '', 'admin')
ON DUPLICATE KEY UPDATE `id` = 1;

CREATE TABLE IF NOT EXISTS `code` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(2) COLLATE utf8_bin NOT NULL,
    `image` VARCHAR(32) COLLATE utf8_bin NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `dictionary` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `base` TEXT COLLATE utf8_general_ci NOT NULL,
    `code` VARCHAR(2) COLLATE utf8_bin NOT NULL,
    `translation` TEXT COLLATE utf8_general_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `menu` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `button_id` INT(11) NOT NULL,
    `parent_button_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `button` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(32) COLLATE utf8_general_ci NOT NULL,
    `is_active` BIT(1) NOT NULL,
    `image_first` VARCHAR(32) COLLATE utf8_bin DEFAULT NULL,
    `image_second` VARCHAR(32) COLLATE utf8_bin DEFAULT NULL,
    `secured` BIT(1) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `article` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `button_id` INT(11) NOT NULL,
    `title` VARCHAR(32) COLLATE utf8_general_ci NOT NULL,
    `content` TEXT COLLATE utf8_general_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `button_id` (`button_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `slider` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `type` VARCHAR(255) COLLATE utf8_bin NOT NULL,
    `content` TEXT COLLATE utf8_general_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `slider` (`id`, `type`, `content`) 
VALUES (1, 'search', '')
ON DUPLICATE KEY UPDATE `id` = 1;
INSERT INTO `slider` (`id`, `type`, `content`) 
VALUES (2, 'sidebox', '')
ON DUPLICATE KEY UPDATE `id` = 2;
INSERT INTO `slider` (`id`, `type`, `content`) 
VALUES (3, 'contact', '{"email":"","description":""}')
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
EOL

# Set config/Config.class.php file
sed -i "s/const AUTO_AUTHENTICATE = 0\;/const AUTO_AUTHENTICATE = 1\;/" /vagrant/config/Config.class.php
sed -i "s/your_database_host/$MYSQL_HOST/" /vagrant/config/Config.class.php
sed -i "s/your_database_name/$MYSQL_DATABASE/" /vagrant/config/Config.class.php
sed -i "s/your_database_user/$MYSQL_USER/" /vagrant/config/Config.class.php
sed -i "s/your_database_password/$MYSQL_USER_PASSWORD/" /vagrant/config/Config.class.php
sed -i "s/your_directory//" /vagrant/config/Config.class.php
sed -i "s/www\.your_domain\.com\//$HOST:$PORT/" /vagrant/config/Config.class.php
