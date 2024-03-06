<?php

namespace Kat\MicroORM;

use Kat\MicroORM\Exceptions\DriverNotFoundException;
use Kat\MicroORM\Exceptions\InvalidConnectionNameException;
use Psr\Log\LoggerInterface;
use Throwable;

class DbManager implements DbManagerInterface
{
    protected DbConnectionFactory $dbConnectionFactory;
    private LoggerInterface $logger;
    private LoggerInterface $isLogActive;


    public function __construct(LoggerInterface $logger, bool $isLogActive = false)
    {
        $this->logger = $logger;
        $this->dbConnectionFactory = new DbConnectionFactory();
        $this->isLogActive = $isLogActive;
    }

    /**
     * @param string $name
     * @param array $config
     * @return DbConnectionInterface
     * @throws \Kat\MicroORM\Exceptions\DriverNotFoundException
     */
    public function addConnection(string $name, array $config): void
    {
        if($this->isLogActive) {
            $this->logger->info("Connect to {$config['driver']} => {$config['db']} ");
        }
        try {
            $this->dbConnectionFactory->makeConnection($name, $config);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    /**
     * @param string $name
     * @return DbConnectionInterface
     * @throws InvalidConnectionNameException
     * @throws \Kat\MicroORM\Exceptions\DbConnectionNotFoundException
     */
    public function getConnection(string $name): DbConnectionInterface
    {
        if (!$name) {
            throw new InvalidConnectionNameException();
        }
        //$this->logger->debug("Accessing $name in db connetions " . print_r($this->getConnections(), true));
        return $this->dbConnectionFactory->getConnection($name);
    }

    /**
     * @return array
     */
    public function getConnections(): array
    {
        return $this->dbConnectionFactory->getConnections();
    }
}