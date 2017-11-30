<?php

use Phalcon\Cli\Task;
use App\Parsers\Parser as Parser;

class ParseTask extends Task
{
    public function mainAction()
    {
        echo 'No resource set, please select service for parsing' . PHP_EOL;
    }

    public function yandexAction(array $params)
    {
        if(!isset($params[1])) {
            echo 'No track name found' . PHP_EOL;
            return;
        }

        new Parser();
    }
}