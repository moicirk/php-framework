<?php

namespace PhpFramework\Databases;

use Doctrine\ORM\EntityRepository;

interface DatabaseInterface
{
    public function getRepository(string $className): EntityRepository;
}
