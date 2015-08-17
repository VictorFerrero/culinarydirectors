<?php

class Application_Model_PDO_Connector
{
    // DB accessor object
    protected $dbo; 
    
    // The DB name for the projects, will be set from the application.ini
    protected $project_db;
    
    // The DB name for the core data, will be set from by the application.ini
    protected $core_db;
    
    // The DB name for the magazine, will be set from by the application.ini
    protected $magazine_db;
    
    // The DB name for the comments, will be set from by the application.ini
    protected $comments_db;
    
    protected $app_config;
    
    /**
     * Create the DB connector class using ZF1 and PDO
     * @param array $dbo_confi public function __construct($db_config=false)g optional containing database configuration options
     */
    public function __construct($db_config=false)
    {
        $app_config = '';  

        // Get a local copy of the app config
        if(! $db_config) {
            $app_config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $resources = $app_config->getOption('resources');
            $db_config = $resources['db'];
        }
        
        $this->app_config = $app_config;

        // Set an PDO MySQL Driver options
        $driver_options = array(
           // PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
              PDO::MYSQL_ATTR_INIT_COMMAND  => 'SET NAMES utf8',
        );

        // Create the config array to pass to PDO
        $parameters = array(
            'host'           => $db_config['params']['host'],
            'username'       => $db_config['params']['username'],
            'password'       => $db_config['params']['password'],
            'dbname'         => $db_config['params']['dbname'],
            'driver_options' => $driver_options
        );
        
        // Set the Project and Core DB names based on the application.ini
        $this->project_db = $db_config['project_db'];
        $this->core_db = $db_config['params']['dbname'];
        $this->log_db = $db_config['log_db'];
        $this->magazine_db = $db_config['magazine_db'];
        $this->reporting_db = $db_config['reporting_db'];
        $this->comments_db = $db_config['comments_db'];
        
        /**
         * Try connecting and throw an error if it failed.
         * @TODO catch errors.
         */
        try {
            $db = Application_Model_PDO_Mysql::instance($parameters);
            $this->dbo = $db;
        } catch (Zend_Db_Adapter_Exception $e) {
            // perhaps a failed login credential, or perhaps the RDBMS is not running
            throw new Zend_Controller_Action_Exception('Database connection failed.', 404);  
        } catch (Zend_Exception $e) {
            // perhaps factory() failed to load the specified Adapter class
        }
    }
    
    public function shutdown()
    {
        $this->__destruct();
    }
    
    public function __destruct() {
       if($this->dbo) {
           // CLose DB connections
           $this->dbo->closeConnection();
       }
       
    }
}

