Palantir
========
# Development of web platform for remote role games

## Main technologies used:
 - PHP
 - Symfony
 - Composer
 - Twig
 - YAML
 - WAMP (Web Application Messaging Protocol)
 - MySQL
 - HTML
 - JavaScript
 - jQuery
 - jQuery UI
 - CSS
 - Bootstrap
 - JSON


## Develop deployment
1. Clone repository

2. Install mysql and create symfony database
    ```bash
    # As example:
    sudo apt install mysql-server
    sudo mysql
    ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '!YourPassw0rd';
    FLUSH PRIVILEGES;
    EXIT
    mysql -u root -p
    CREATE DATABASE symfony;
    ```

3. Add tables
    ```bash
     php bin/console doctrine:schema:update --force
    ```
    ```mysql
    CREATE TABLE `sessions` (
        `sess_id` VARCHAR(128) NOT NULL PRIMARY KEY,
        `sess_data` BLOB NOT NULL,
        `sess_time` INTEGER UNSIGNED NOT NULL,
        `sess_lifetime` INTEGER UNSIGNED NOT NULL
    ) COLLATE utf8mb4_bin, ENGINE = InnoDB;
    ```

4. Install assets
    ```
     php bin/console assetic:dump
    ```

5. Run it
    ```
     php bin/console server:start
    ```

6. And access to
    ```
     http://127.0.0.1:8000/es_ES/
    ```

7. Register a user and set in DB SUPER_ADMIN role
    ```
        a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}
    ```