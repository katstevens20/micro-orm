<?php

namespace Kat\MicroORM;

use PDO;
use Exception;

class PgsqlConnector implements DbConnectionInterface
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

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    public function exec($query)
    {
        return $this->pdo->exec($query);
    }


    /**
     * Return rows
     *
     * @param $sql
     * @param $params
     * @return mixed
     */
    public function executeAndReturnRows($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Return count
     *
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function executeAndReturnCount($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Return count
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function executeAndReturnOne($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $resultArray = $stmt->fetchAll();
        return $resultArray ? $resultArray[0] : $resultArray;
    }

    /**
     * Execute insert or update sql
     *
     * @param $sql
     * @param array $params
     * @return bool
     */
    function executeInsertOrUpdate($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $param => $value) {
            $paramType = PDO::PARAM_STR;

            if (is_int($value)) {
                $paramType = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $paramType = PDO::PARAM_BOOL;
            } elseif (is_null($value)) {
                $paramType = PDO::PARAM_NULL;
            }

            $stmt->bindValue($param, $value, $paramType);
        }

        return $stmt->execute();
    }
}