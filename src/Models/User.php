<?php


namespace App\Models;


use App\Core\Model;
use Exception;
use PDO;
use PDOStatement;

class User extends Model
{
    protected const TABLE = "users";

    /**
     * @param array $userData
     * @return bool|mixed|PDOStatement
     * @throws \Exception
     */
    public function createUser(array $userData){
        $sql = sprintf(
            "INSERT INTO ".self::TABLE."(%s) VALUES(%s)",
            implode(', ', array_keys($userData)),
            ':'.implode(', :', array_keys($userData))
        );

        $statement = $this->db->run($sql, $userData);
        if(!$statement){
            throw new \Exception("Could not register user");
        }
        return $this->getLatestRegisteredUser() ;
    }

    /**
     * @return mixed
     */
    private function getLatestRegisteredUser()
    {
        $sql = <<<SQL
                SELECT
                        id,
                        username,
                        email,
                        created_at
                FROM users
                ORDER BY id DESC
                LIMIT 1;
                SQL;
        $statement = $this->db->run($sql);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $where
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function getUserWhere(array $where)
    {
        $sql = sprintf(
            "SELECT 
                *
                FROM users
                WHERE %s=%s;",
            implode(', ', array_keys($where)),
            ':'.implode(', :', array_keys($where))
        );

        $statement = $this->db->run($sql, $where);

        if(!$statement || $statement->rowCount() == 0){
            throw new Exception("Could not fetch user");
        }

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}
