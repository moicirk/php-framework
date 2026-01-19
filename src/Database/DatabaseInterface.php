<?php

namespace PhpFramework\Database;

use Doctrine\ORM\EntityRepository;

interface DatabaseInterface
{
    public function getRepository(string $className): EntityRepository;
}
