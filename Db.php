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

    /**
      * @var int
      */
      private $_count = 0;

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
     * @return object
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
     * @param string $tableName
     * @param string $condition
     * @param array $bindValue
     * @param string $limit
     * 
     * @return object
     */
    public function get(string $tableName, string $condition = "", array $bindValue = [], string $limit = "")
    {
        $query = "SELECT {$this->_columnName} FROM {$tableName} {$condition} {$this->_orderBy} {$limit}";
        $this->_columnName = "*";
        $this->_orderBy = "";
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

    /**
     * Get Where
     * 
     * Generate query get with where condition
     * 
     * 
     * @param string $tableName
     * @param array $condition
     * 
     * @return object
     */
    public function getWhere(string $tableName, array $condition)
    {
      $queryCondition = "WHERE {$condition[0]} {$condition[1]} ?";

      return $this->get($tableName, $queryCondition, [$condition[2]]);
    }

    /**
     * Get Where Once
     * 
     * Generate query get with where condition then only return single object
     * 
     * 
     * @param string $tableName
     * @param array $condition
     * 
     * @return object
     */
    public function getWhereOnce(string $tableName, array $condition)
    {
      $queryCondition = "WHERE {$condition[0]} {$condition[1]} ?";

      $result = $this->get($tableName, $queryCondition, [$condition[2]], 'LIMIT 1');

      if (empty($result)) {
        return false;
      }

      return $result[0];
    }

     /**
     * Get Like
     * 
     * Generate query get with where like condition
     * 
     * 
     * @param string $tableName
     * @param string $columnLike
     * @param string $search
     * 
     * @return object
     */
    public function getLike(string $tableName, string $columnLike, string $search)
    {
      $queryCondition = "WHERE {$columnLike} LIKE ?";
      
      return $this->get($tableName, $queryCondition, [$search]);
    }

     /**
     * Get Like
     * 
     * Generate query get with where like condition
     * 
     * 
     * @param string $tableName
     * @param string $columnLike
     * @param string $dataValues
     * 
     * @return int
     */
    public function check(string $tableName, string $columnName, string $dataValues)
    {
      $query = "SELECT {$columnName} FROM {$tableName} WHERE {$columnName} = {$dataValues}";
      
      return $this->runQuery($query)
                  ->rowCount();
    }

    /**
     * Insert
     * 
     * Generate query insert base on parameters
     * 
     * 
     * @param string $tableName
     * @param array $data
     * 
     * @return bool
     */
    public function insert(string $tableName, array $data)
    {
      $listColumn = "(".implode(', ', array_keys($data)).")";
      $placeHolder = "(".str_repeat('?, ', count($data)-1)."? )";

      $query = "INSERT INTO {$tableName} {$listColumn} VALUES {$placeHolder}";
      $this->_count = $this->runQuery($query, array_values($data))
                           ->rowCount();
      return true;
    }

    /**
     * Count affected rows
     * 
     * Getter method to count total affected rows
     * 
     * 
     * @param void
     * 
     * @return int
     */
    public function count()
    {
      return $this->_count;
    }

    /**
     * Update
     * 
     * Generate query update base on parameters
     * 
     * 
     * @param string $tableName
     * @param array $data
     * @param array $condition
     * 
     * @return bool
     */
    public function update(string $tableName, array $data, array $condition)
    {
		$query = "UPDATE {$tableName} SET ";
		foreach ($data as $key => $value) {
			$query .= "{$key} = ?, ";
		}

		$query = substr($query, 0, -2);
		$query .= " WHERE {$condition[0]} {$condition[1]} ?";

		$data_values = array_values($data);
		array_push($data_values, $condition[2]);
		
		$this->_count = $this->runQuery($query, $data_values)
							 ->rowCount();

		return true;
	}
	
	 /**
     * Delete
     * 
     * Generate query delete base on parameters
     * 
     * 
     * @param string $tableName
     * @param array $condition
     * 
     * @return bool
     */
    public function delete(string $tableName, array $condition)
    {
		$query = "DELETE FROM {$tableName} WHERE {$condition[0]} {$condition[1]} ?";
		$this->_count = $this->runQuery($query, [$condition[3]])
							 ->rowCount();

		return true;
    }






}