<?php

namespace Kat\MicroORM;

use PDO;
use Psr\Log\LoggerInterface;
use Throwable;

class MysqlConnector implements DbConnectionInterface
{
    protected array $config;
    protected ?PdoLink $pdo = null;
    protected int $fetchMode = PDO::FETCH_DEFAULT;
    protected ?LoggerInterface $logger = null;


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
     * @throws \Exception
     */
    public function connect()
    {
        $delayMultiplier = isset($this->config['delaymultiplier']) && is_numeric($this->config['delaymultiplier']) ? $this->config['delaymultiplier'] : 2;
        $delay = isset($this->config['delay']) && is_numeric($this->config['delay']) ? $this->config['delay'] : 2;// Initial delay in seconds
        $retryCount = 0;
        $maxRetries = isset($this->config['retry']) && is_numeric($this->config['retry']) ? $this->config['retry'] : 1;
        while ($retryCount < $maxRetries && !$this->pdo) {
            try {
                $this->pdo = new PdoLink($this->config['driver'] . ':host=' . $this->config['host'] . ';dbname=' . $this->config['db'], $this->config['user'], $this->config['password']);
            } catch (throwable $e) {
                $retryCount++;
                if ($retryCount >= $maxRetries) {
                    throw new \Exception("Maximum number of retries exceeded:" . $e->getMessage());
                }
                sleep($delay);
                $delay *= $delayMultiplier;
            }
        }


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