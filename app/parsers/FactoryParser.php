<?php

namespace App\Parsers;

use App\Parsers\Services\YandexParser as YandexParser;

/**
 * Factory pattern for parsers
 *
 * Class FactoryParser
 * @package App\Parsers
 */
abstract class FactoryParser
{
    public function parse(String $service, String $searchText) : mixed
    {
        switch ($service) {
            case 'yandex':
                return new YandexParser($searchText);
            default:
                return false;
        }
    }
}