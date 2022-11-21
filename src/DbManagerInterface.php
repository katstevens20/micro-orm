<?php

namespace Kat\MicroORM;

interface DbManagerInterface
{
    public function addConnection(string $name, array $config): Void;

    public function getConnection(string $name): DbConnectionInterface;

    public function getConnections(): array;
}