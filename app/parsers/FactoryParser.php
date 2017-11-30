<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 01.12.17
 * Time: 0:09
 */

namespace App\Parsers;


abstract class FactoryParser
{
    public function parse(String $service):mixed
    {
        switch ($service) {
            case 'yandex':
                return new YandexParser($service);
            default:
                return false;
        }
    }
}