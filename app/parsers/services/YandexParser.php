<?php

namespace App\Parsers\Services;

use App\Parsers\Contracts\ParserContract as ParserContract;
use App\Models\Tracks as Tracks;

class YandexParser implements ParserContract
{
    private $trackName;

    public function __construct(String $trackName)
    {
        $this->trackName = $trackName;
        $tracks = $this->findTracks($this->trackName);
        $this->save($tracks);
    }

    public function findTracks(String $trackName): array
    {
        $trackName = $this->removeQoutes($trackName);

        $this->PhantomRequest($trackName);

        $this->searchTracks($this->PhantomRequest($trackName));

        return $tracks;
    }

    public function save(Array $tracks) : bool
    {
        foreach ($tracks as $item) {
            $track = new Tracks;

            $track->setName();
            $track->setDownloadLink();
            $track->setService('yandex');

            $track->save();
        }

        return true;
    }

    private function removeQoutes(String $trackname) : String
    {
        return str_replace(array('"', "'"), '', $trackname);
    }

    private function PhantomRequest(String $trackname) : string
    {
        $jsonString = '{
            "url": "https://music.yandex.ru/search?text=' . $trackname . '",
            "renderType": "plainText",
            "outputAsJson": true,
            "suppressJson": false,
            "requestSettings": {
              "clearCache": true,
              "userAgent": "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/534.34 (KHTML, like Gecko) Safari/534.34 PhantomJS/2.0.0 (PhantomJsCloud.com/2.0.1)",
            }
        }';


        $url = 'http://phantomjscloud.com/api/browser/v2/ak-pewrc-az5ah-zar40-f3crm-t00ah/';
        $options = array(
            "http" => array(
                "header"  => "Content-type: application/json",
                "method"  => "POST",
                "content" => $jsonString,
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $result = json_decode($result, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        return $result["pageResponses"][0]["frameData"]["content"];
    }

    private function searchTracks(String $content) : array
    {
        $tracks = [];

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($content);

        $searchNodesA = $dom->getElementsByTagName("a");

        foreach ($searchNodesA as $searchNodeA) {
            $classNameA = $searchNodeA->getAttribute('class');

            if(strpos($classNameA, 'd-track__title') !== false) {
                $tracks[] = $searchNodeA->getAttribute('href');
            }

        }

        var_dump($tracks);

        return $tracks;
    }
}