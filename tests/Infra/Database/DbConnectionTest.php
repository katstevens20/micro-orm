<?php

namespace Infra\Database;

use Kat\MicroORM\Exceptions\DriverNotFoundException;
use Kat\MicroORM\DbConnectionFactory;
use Kat\MicroORM\MysqlConnector;
use PHPUnit\Framework\TestCase;

class DbConnectionTest extends TestCase
{
    private $mysqlDriverConfig;
    public function setUp():void
    {
        $this->mysqlDriverConfig = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'db' => 'coyote',
            'user' => 'kat',
            'password' => ''
        ];
    }


    public function testMysqlDbConnectionCanBeInstantiated(): void
    {
        $dbConnectionFactory = new DbConnectionFactory();
        $mysqlConnector = $dbConnectionFactory->makeConnection('mysql', $this->mysqlDriverConfig);
        $this->assertInstanceOf(MysqlConnector::class, $mysqlConnector);
    }

    public function testMysqlDbConnectionRaisingDriverNotFoundException(): void
    {
        $this->expectException(DriverNotFoundException::class);
        $dbConnectionFactory = new DbConnectionFactory();
        $dbConnectionFactory->makeConnection('mysql', $this->mysqlDriverConfig);
    }
}