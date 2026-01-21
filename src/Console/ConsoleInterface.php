<?php

namespace PhpFramework\Console;

interface ConsoleInterface
{
    public function handleCommand(ArgsInputs $argsInputs): void;
}
