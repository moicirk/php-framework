<?php

namespace PhpFramework\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMSetup;

class Database
{
    private EntityManager $entityManager;

    public function __construct()
    {
        $config = ORMSetup::createAttributeMetadataConfig(
            paths: [APP_ROOT . '/application/Entities'],
            isDevMode: true
        );
        $config->enableNativeLazyObjects(true);
        $dbConfig = require_once APP_ROOT . '/config/database.php';

        $connection = DriverManager::getConnection($dbConfig, $config);
        $this->entityManager = new EntityManager($connection, $config);
    }

    public function getRepository(string $className): EntityRepository
    {
        return $this->entityManager->getRepository($className);
    }
}
