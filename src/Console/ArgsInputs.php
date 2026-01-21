<?php

namespace PhpFramework\Console;

class ArgsInputs
{
    private array $tokens = [];

    /**
     * @return array
     * @throws ConsoleException
     */
    public function parse(): array
    {
        $inputs = $_SERVER['argv'] ?? [];
        if (!isset($inputs[1])) {
            throw new ConsoleException('There is no script that you want to use.');
        }

        $this->tokens = array_splice($inputs, 1);

        return $this->tokens;
    }
}
