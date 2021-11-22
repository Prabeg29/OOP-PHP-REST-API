<?php


namespace App\Core;


use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private PDO $pdo;
    private static ?Database $instance = null;

    /**
     * @param array $dbConfig
     * @return Database
     */
    public static function getInstance(array $dbConfig)
    {
        if(is_null(self::$instance))
        {
            self::$instance = new Database($dbConfig);
        }
        return self::$instance;
    }

    private function __construct(array $dbConfig)
    {
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']}";
        $username = $dbConfig['username'] ?? '';
        $password = $dbConfig['password'] ?? '';

        try{
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $ex){
            die($ex->getMessage());
        }
    }

    /**
     * @param string $sql
     * @return bool|PDOStatement
     */
    protected function prepare(string $sql): PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    /**
     * @param $sql
     * @param array|null $params
     * @return bool | PDOStatement
     */
    public function run($sql, array $params = null)
    {
        $statement = $this->prepare($sql);

        if(!is_null($params)){
            foreach ($params as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
        }

        if(!$statement->execute($params)){
            return false;
        };

        return $statement;
    }
}
