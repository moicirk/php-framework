<?php

namespace PhpFramework\Queues;

use PhpFramework\Console\LogTrait;

abstract class AbstractTask
{
    use LogTrait;

    public int|null $id = null;
}
