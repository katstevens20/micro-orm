<?php

namespace Kat\MicroORM;

class MysqlConnector implements DbConnectionInterface
{
    protected array $config;
    protected PdoLink $pdo;


    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }


    public function connect()
    {
        $this->pdo = new PdoLink($this->config['driver'] . ':host=' . $this->config['host'] . ';dbname=' . $this->config['db'], $this->config['user'], $this->config['password']);
    }

    public function executer_select($query, $index, &$result)
    {
        return $this->pdo->executer_select($query, $index, $result);
    }

    public function exec_params($query, $params)
    {
        return $this->pdo->exec_params($query, $params);
    }

    public function query(string $query) {
        return $this->pdo->query($query);
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }

    public function commit() {
        return $this->pdo->commit();
    }
}