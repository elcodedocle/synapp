<?php

require_once dirname(__FILE__) . '/account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/account/' . SYNAPP_CONFIG_DIRNAME . '/database_host_and_credentials.php';
require_once dirname(__FILE__) . '/utils/http_response_code.php';

/**
 * returns $link to mysql server connection
 * if $select == true it also selects the database  
 * 
 * @param bool $select
 * @return \PDO
 */
function connect($select = true)
{
    try {
        $link = new PDO(
            "mysql:host=".SYNAPP_DB_HOST.
            ";port=".SYNAPP_DB_PORT.
            ($select ? ";dbname=" . SYNAPP_DB_NAME : '' ) .
            ";charset=utf8",
            SYNAPP_DB_USER,
            SYNAPP_DB_PASSWORD,
            array(
                PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
            )
        );
        $link->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        $link->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        error_log('Unable to connect to database');
        http_response_code(503);
        die ("Service temporarily unavailable.");
    }
    
    return $link;
    
}
