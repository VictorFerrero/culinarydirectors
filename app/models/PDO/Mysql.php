<?php

class Application_Model_PDO_Mysql
{
    private static $instance;
    
    public function __construct() {  }
    
    /**
     * Create a MYSQL singleton
     */
    public static function instance($parameters)
    {
        if(!isset(self::$instance)) {
            
            try {
                
                if(php_sapi_name() == 'cli'){
                    ini_set('mysql.connect_timeout', 300);
                    ini_set('default_socket_timeout', 300);
                }
                
                $db = Zend_Db::factory('Pdo_Mysql', $parameters);
                $db->getConnection();
                self::$instance = $db;
            } catch (Zend_Db_Adapter_Exception $e) {
                // perhaps a failed login credential, or perhaps the RDBMS is not running
                throw new Zend_Controller_Action_Exception('Database connection failed.', 404);  
            } catch (Zend_Exception $e) {
                // perhaps factory() failed to load the specified Adapter class
            }
        }
        
        return self::$instance;
    }
}

