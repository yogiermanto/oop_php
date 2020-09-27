<?php
namespace Db;

use PDO;
use PDOStatement;

class Db 
{
    /**
      * @var string
      */
    private $_host = '127.0.0.1';

    /**
      * @var string
      */
    private $_dbname = 'ilkoom';

    /**
      * @var string
      */
    private $_username = 'root';

    /**
      * @var string
      */
    private $_password = '';

    /**
      * @var Db
      */
    private static $_instance = NULL;

    /**
      * @var \PDO
      */
    private $_pdo;

    /**
      * @var string
      */
    private $_columnName = "*";

    /**
      * @var string
      */
      private $_orderBy = "";

    private function __construct()
    {
        try {
            $this->_pdo = new PDO("mysql:host={$this->_host};dbname={$this->_dbname}",$this->_username,$this->_password);
            $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Throwable $th) {
            die("Koneksi / Query bermasalah {$th->getMessage()} ({$th->getCode()})");
        }
    }

    /**
     * Singletorn Pattern
     * 
     * Only instance once DB class 
     * 
     * @param void
     * 
     * @return Db
     */
    public static function getInstance() : Db
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new Db();
        }

        return self::$_instance;
    }

    /**
     * Function for execute pdo query
     * 
     * Function for execute pdo using query and bindvalue parameters and return PDOStatement Object
     * 
     * @param string $query
     * @param array $bindValue
     * 
     * @return PDOStatement
     */
    public function runQuery(string $query, array $bindValue = []) : PDOStatement
    {
        try {
            $stmt = $this->_pdo->prepare($query);
            $stmt->execute($bindValue);
        } catch (\Throwable $th) {
            die("Koneksi / Query bermasalah {$th->getMessage()} ({$th->getCode()})");
        }

        return $stmt;
    }

    /**
     * Execute query then get result
     * 
     * Return result of query invoke method runQuery.
     * Only use for select query
     * 
     * 
     * @param string $query
     * @param array $bindValue
     * 
     * @return Db
     */
    public function getQuery(string $query, array $bindValue = [])
    {
        return $this->runQuery($query, $bindValue)
                    ->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get
     * 
     * Run the select statemen based on the $tableName parameter
     * 
     * 
     * @param string $query
     * 
     * @return Db
     */
    public function get(string $tableName, string $condition = "", array $bindValue = [])
    {
        $query = "SELECT {$this->_columnName} FROM {$tableName} {$condition} {$this->_orderBy}";
        $this->_columnName = "*";
        return $this->getQuery($query, $bindValue);
    }

    /**
     * Select
     * 
     * Generate query select based on parameter $columnName
     * 
     * 
     * @param string $columnName
     * 
     * @return Db
     */
    public function select(string $columnName)
    {
        //for the first time call method select
        if ($this->_columnName !== "*") {
            $this->_columnName .= ", $columnName";
        //if not, concat columnName to previous $this->_columnName
        } else {            
            $this->_columnName = $columnName;
        }

        return $this;
    }

     /**
     * Order By
     * 
     * Generate query order by based on parameter $columnName and sortType
     * 
     * 
     * @param string $columnName
     * @param string $sortType
     * 
     * @return Db
     */
    public function orderBy(string $columnName, string $sortType = 'ASC')
    {
        $this->_orderBy = "ORDER BY {$columnName} {$sortType}";

        return $this;
    }
}