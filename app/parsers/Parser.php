<?php

namespace App\Parsers;

class Parser extends FactoryParser
{
    public $curlOptions = [
        CURLOPT_HEADER => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ];

    /**
     * Make curl requests with options
     *
     * @param $url
     * @return String
     * @throws \Error
     */
    public function curlRequest($url) : String
    {
        $curl = curl_init($url);

        curl_setopt_array($curl, $this->curlOptions);

        $result = curl_exec($curl);

        if(!$result)
        {
            throw new \Error('Can`t fetch page');
        }

        return $result;
    }
}