<?php

namespace Kat\MicroORM;

use PDO;
use Psr\Log\LoggerInterface;

class MysqlConnector implements DbConnectionInterface
{
    protected array $config;
    protected ?PdoLink $pdo = null;
    protected int $fetchMode = 0;//PDO::FETCH_DEFAULT for > php8.0
    protected ?LoggerInterface $logger = null;

    protected $timeout = 30;//timeout in seconds

    /**
     * setter for timeout
     */
    public function setTimeout(int $timeout): MysqlConnector
    {
        $this->timeout = $timeout;
        return $this;
    }
    public function setFetchMode(int $fetchMode): MysqlConnector
    {
        $this->fetchMode = $fetchMode;
        return $this;
    }

    //setter for logger interface
    public function setLogger(LoggerInterface $logger): MysqlConnector
    {
        $this->logger = $logger;
        return $this;
    }

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    /**
     * Close pdo connection
     */
    public function close(): void
    {
        $this->pdo = null;
    }


    /**
     * @throws \Exception
     */
    public function connect()
    {
        $this->pdo = new PdoLink($this->config['driver'] . ':host=' . $this->config['host'] . ';dbname=' . $this->config['db'], $this->config['user'], $this->config['password'], [PDO::ATTR_TIMEOUT => $this->timeout]);
    }

    public function executer_select($query, $index, &$result)
    {
        return $this->pdo->executer_select($query, $index, $result);
    }

    public function exec_params($query, $params = [])
    {
        return $this->pdo->exec_params($query, $params);
    }

    public function exec(string $query)
    {
        return $this->pdo->exec($query);
    }

    public function query(string $query)
    {
        return $this->pdo->query($query);
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    public function commit()
    {
        return $this->pdo->commit();
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
        //print ŝql for debug with params if there is any
        if ($this->logger) {
            $this->logger->debug($sql, $params);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll($this->fetchMode);
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
        if ($this->logger) {
            $this->logger->debug($sql, $params);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Return count
     *
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function executeAndReturnOne($sql, array $params = [])
    {
        if ($this->logger) {
            $this->logger->debug($sql, $params);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $resultArray = $stmt->fetchAll($this->fetchMode);
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
        if ($this->logger) {
            $this->logger->debug($sql, $params);
        }
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