<?php

namespace database;

class database{

    private $_database_name;
    private $_database_user;
    private $_database_password;
    public $connection;

    public function __construct(){
        $this->_database_name = $_ENV['DATABASE_NAME'];
        $this->_database_user = $_ENV['DATABASE_USER'];
        $this->_database_password = $_ENV['DATABASE_PASSWORD'];
    }

}