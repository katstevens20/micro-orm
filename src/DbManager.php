<?php

namespace Kat\MicroORM;

use Kat\MicroORM\Exceptions\DriverNotFoundException;
use Kat\MicroORM\Exceptions\InvalidConnectionNameException;
use Psr\Log\LoggerInterface;

class DbManager implements DbManagerInterface
{
    protected DbConnectionFactory $dbConnectionFactory;
    private LoggerInterface $logger;


    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->dbConnectionFactory = new DbConnectionFactory();
    }

    /**
     * @param string $name
     * @param array $config
     * @return DbConnectionInterface
     * @throws \Kat\MicroORM\Exceptions\DriverNotFoundException
     */
    public function addConnection(string $name, array $config): void
    {
        $this->logger->info("Connect to {$config['driver']} => {$config['db']} ");
        try {
            $this->dbConnectionFactory->makeConnection($name, $config);
        } catch (Exception | DriverNotFoundException $e) {
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