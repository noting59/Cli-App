<?php

use Phalcon\Cli\Task;

class ParseTask extends Task
{
    private $parser;

    public function mainAction()
    {
        echo 'No resource set, please select service for parsing' . PHP_EOL;
    }

    public function yandexAction(array $params)
    {
        if(!isset($params[0])) {
            echo 'No track name found' . PHP_EOL;
            return;
        };

        $this->di->get('parser')->parse('yandex', $params[0]);
    }
}