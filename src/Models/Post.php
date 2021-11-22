<?php


namespace App\Models;


use App\Core\Model;
use Exception;
use \PDO;

class Post extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->limit = 2; // set limit to override default value
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getAllPublishedPosts(): array
    {
        $this->setTotalRecords("SELECT COUNT(*) FROM posts WHERE status=1");
        $this->offset = ($this->getCurrentPage() * $this->limit) - $this->limit;

        $sql = <<<SQL
                SELECT
                        posts.id,
                        posts.status,
                        posts.title,
                        posts.slug,
                        users.username as author,
                        posts.description,
                        posts.imagePath,
                        posts.updated_at
                FROM posts
                INNER JOIN users
                ON posts.user_id = users.id
                WHERE posts.status=1
                ORDER BY posts.id DESC
                LIMIT $this->offset, $this->limit
                SQL;

        $statement = $this->db->run($sql);
        if(!$statement){
            throw new Exception("Could not fetch posts");
        }

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $postData
     * @return mixed
     * @throws Exception
     */
    public function createPost(array $postData)
    {
        $sql = sprintf(
            "INSERT INTO posts(%s) VALUES(%s)",
            implode(', ', array_keys($postData)),
            ':'.implode(', :', array_keys($postData))
        );
        $statement = $this->db->run($sql, $postData);
        if(!$statement || $statement->rowCount() == 0)
        {
            throw new Exception("Could not create post");
        }

        return $this->getLatestPost();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getLatestPost()
    {
        $sql = <<<SQL
                SELECT
                        *
                FROM posts
                ORDER BY id DESC
                LIMIT 1;
                SQL;
        $statement = $this->db->run($sql);
        if(!$statement)
        {
            throw new Exception("Could not fetch post");
        }
        else if($statement->rowCount() > 0)
        {
            return $statement->fetch(PDO::FETCH_ASSOC);

        }
    }

    /**
     * @param array $postData
     * @return array
     * @throws Exception
     */
    public function getLoggedInUserPost(array $postData)
    {
        $this->setTotalRecords("SELECT COUNT(*) FROM posts WHERE user_id={$postData['id']}");
        $this->offset = ($this->getCurrentPage() * $this->limit) - $this->limit;

        $sql = sprintf(
            "SELECT
                posts.id,
                posts.status,
                posts.title,
                posts.slug,
                posts.description,
                posts.imagePath,
                posts.updated_at
            FROM posts
            WHERE posts.user_id=%s
            ORDER BY posts.id DESC
            LIMIT $this->offset, $this->limit",
            ':'.implode(', :', array_keys($postData))

        );
        $statement = $this->db->run($sql, $postData);
        if(!$statement)
        {
            throw new Exception('Could not fetch posts');
        }

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $postData
     * @return mixed
     * @throws Exception
     */
    public function getPost(array $postData) {
        $sql = sprintf(
            "SELECT posts.id, 
                    posts.status,
                    posts.title, 
                    posts.slug,
                    posts.description,
                    posts.imagePath, 
                    posts.updated_at, 
                    posts.user_id,
                    users.username as author
            FROM posts
            INNER JOIN users
            ON posts.user_id = users.id
            WHERE posts.id = %s",
            ':'.implode('', array_keys($postData))
        );

        $statement = $this->db->run($sql, $postData);
        if(!$statement)
        {
            throw new Exception('Could not fetch post');
        }

        else if($statement->rowCount() > 0)
        {
            return $statement->fetch(PDO::FETCH_ASSOC);
        }
    }

    /**
     * @param array $postData
     * @return mixed
     * @throws Exception
     */
    public function updatePost(array $postData)
    {
        $sql = "UPDATE posts 
                SET title=:title,
                    slug=:slug,
                    description=:description,
                    imagePath=:imagePath,
                    status=:status
                WHERE id=:id";

        $statement = $this->db->run($sql, $postData);
        if(!$statement)
        {
            throw new Exception('Could not update post');
        }
        else if($statement->rowCount() > 0)
        {
            return $this->getPost(['id' => $postData['id']]);
        }
    }

    /**
     * @param array $postData
     * @return mixed
     * @throws Exception
     */
    public function deletePost(array $postData) {
        $sql = sprintf(
            "DELETE FROM posts WHERE %s=%s",
            implode('', array_keys($postData)),
            ':'.implode('', array_keys($postData))
        );

        $statement = $this->db->run($sql, $postData);
        if(!$statement)
        {
            throw new Exception('Could not delete post');
        }
        else if($statement->rowCount() > 0)
        {
            return $statement->fetch(PDO::FETCH_ASSOC);
        }
    }
}
