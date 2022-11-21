<?php

namespace Infra\Database;

use Kat\MicroORM\Exceptions\DbConnectionNotFoundException;
use Kat\MicroORM\Exceptions\DriverNotFoundException;
use Kat\MicroORM\Exceptions\InvalidConnectionNameException;
use Kat\MicroORM\DbManager;
use Kat\MicroORM\MysqlConnector;
use Kat\MicroORM\NoOpLogger;
use Kat\MicroORM\PgsqlConnector;
use PHPUnit\Framework\TestCase;

class DbManagerTest extends TestCase
{
    protected DbManager $dbManager;
    protected array $mysqlConfig;
    protected array $pgsqlConfig;

    /**
     * @throws DriverNotFoundException
     */
    public function setUp(): void
    {

        $this->mysqlConfig = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'db' => 'coyote',
            'user' => 'kat',
            'password' => ''
        ];

        $this->pgsqlConfig = [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'db' => 'coyote',
            'user' => 'kat',
            'password' => ''
        ];
        $this->dbManager = new DbManager(new NoOpLogger());
        $this->dbManager->addConnection('testMysql', $this->mysqlConfig);
        $this->dbManager->addConnection('testPgsql', $this->pgsqlConfig);
    }

    public function testDbManagerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(DbManager::class, $this->dbManager);
    }

    /**
     * @throws InvalidConnectionNameException
     */
    public function testDbManagerRaisingDbConnectionNotFoundException(): void
    {
        $this->expectException(DbConnectionNotFoundException::class);
        $this->dbManager->getConnection('mysql');
    }

    public function testMultipleDbConnectionsCreated(): void
    {
        $this->assertCount(2, $this->dbManager->getConnections());
    }

    public function testDbConnectionGetMysql(): void
    {
        $this->dbManager->addConnection('testMysql', $this->mysqlConfig);
        $dbConnection = $this->dbManager->getConnection('testMysql');
        $this->assertInstanceOf(MysqlConnector::class, $dbConnection);
    }

    public function testDbConnectionGetPgsql(): void
    {
        $this->dbManager->addConnection('testPgsql', $this->mysqlConfig);
        $dbConnection = $this->dbManager->getConnection('testPgsql');
        $this->assertInstanceOf(PgsqlConnector::class, $dbConnection);
    }

    public function testDbConnectionAdd(): void
    {
        $this->dbManager->addConnection('mysql', $this->mysqlConfig);
        $dbConnection = $this->dbManager->getConnection('mysql');
        $this->assertInstanceOf(MysqlConnector::class, $dbConnection);
    }
/*
    public function testDbManagerMysqlExecuteSelect(): void
    {
        $this->dbManager->addConnection('mysql', $this->mysqlConfig);
        $dbConnection = $this->dbManager->getConnection('mysql');
        $result = [];
        $dbConnection->executer_select('select * from COYOTE_CODIF_PAYS limit 3', '', $result);
        $this->assertCount(3, $result);
    }

    public function testDbManagerPgsqlExecuteSelect(): void
    {
        $this->dbManager->addConnection('pgsql', $this->pgsqlConfig);
        $dbConnection = $this->dbManager->getConnection('pgsql');
        $result = [];
        $dbConnection->executer_select('select * from pays limit 5', '', $result);
        $this->assertCount(5, $result);
    }*/

}