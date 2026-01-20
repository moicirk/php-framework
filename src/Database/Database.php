<?php

namespace PhpFramework\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMSetup;
use PhpFramework\Application;

class Database implements DatabaseInterface
{
    private EntityManager $entityManager;

    public function __construct()
    {
        $app = Application::instance();
        $config = ORMSetup::createAttributeMetadataConfig(
            paths: [$app->getBasePath() . '/application/Entities'],
            isDevMode: true
        );
        $config->enableNativeLazyObjects(true);
        $dbConfig = require $app->getBasePath() . '/config/database.php';

        $connection = DriverManager::getConnection($dbConfig, $config);
        $this->entityManager = new EntityManager($connection, $config);
    }

    public function getRepository(string $className): EntityRepository
    {
        return $this->entityManager->getRepository($className);
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
}
