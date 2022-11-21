<?php

namespace Kat\MicroORM;

use Kat\MicroORM\Exceptions\DbConnectionNotFoundException;
use Kat\MicroORM\Exceptions\DriverNotFoundException;

class DbConnectionFactory
{
    protected array $connections = [];

    /**
     * @param array $config
     * @return DbConnection
     */
    public function makeConnection(string $name, array $config)
    {
        if (!array_key_exists($name, $this->connections)) {
            if ($config['driver'] === 'mysql') {
                $this->connections[$name] = new MysqlConnector($config);
            } elseif ($config['driver'] === 'pgsql') {
                $this->connections[$name] = new PgsqlConnector($config);
            }else{
                throw new DriverNotFoundException("DB driver not found!");
            }
        }
        return $this->connections[$name];
    }

    /**
     * @param string $name
     * @return mixed
     * @throws DbConnectionNotFoundException
     */
    public function getConnection(string $name) {

        if(!isset($this->connections[$name])) {
            throw new DbConnectionNotFoundException();
        }
        return $this->connections[$name];
    }

    /**
     * @return array
     */
    public function getConnections() {
        return $this->connections;
    }
}