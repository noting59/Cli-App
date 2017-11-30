<?php

use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'Это задача по умолчанию и действие по умолчанию' . PHP_EOL;
    }
}