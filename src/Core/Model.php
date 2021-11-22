<?php

namespace App\Core;

use Dotenv\Dotenv;

abstract class Model {
    protected Database $db;

    protected int $totalRecords;
    protected int $offset = 0;
    protected int $limit = 5;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(dirname(__DIR__)));
        $dotenv->load();

        $config = [
            'db' => [
                'host'=> $_ENV['DB_HOST'],
                'username'=> $_ENV['DB_USERNAME'],
                'password'=> $_ENV['DB_PASSWORD'],
                'database'=> $_ENV['DB_DATABASE']
            ]
        ];

        $this->db = Database::getInstance($config['db']);
    }

    protected function setTotalRecords($sql) {
        $statement = $this->db->run($sql);
        $this->totalRecords = $statement->fetchColumn();
    }

    protected function getCurrentPage() {
        return isset($_GET['page']) ? filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) : 1;
    }

    public function getTotalRecords() {
        return $this->totalRecords;
    }
}
