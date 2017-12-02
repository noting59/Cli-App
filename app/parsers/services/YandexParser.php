<?php

namespace App\Parsers\Services;

use App\Parsers\Contracts\ParserContract as ParserContract;
use App\Models\Tracks as Tracks;
use App\Parsers\Parser;

class YandexParser implements ParserContract
{
    private $parser;

    public function __construct(String $trackName, Parser $parser)
    {
        $this->parser = $parser;

        $tracks = $this->findTracks($trackName);
        $this->save($tracks);
    }

    public function findTracks(String $trackName) : array
    {
        $trackName = $this->removeQuotes($trackName);

        $content = $this->fetchPage($trackName);

        $tracks = $this->searchTracks($content);

        foreach ($tracks as $key => $value) {
            $tracks[$key]['track_download_url'] = $this->getDownloadLink($value);
        }

        return $tracks;
    }

    public function save(Array $tracks): bool
    {
        foreach ($tracks as $track) {
            $track = new Tracks;

            $track->setName($track['track_name']);
            $track->setDownloadLink($track['track_download_url']);
            $track->setService('yandex');

            $track->save();
        }

        return true;
    }

    private function removeQuotes(String $trackname): String
    {
        return str_replace(array('"', "'"), '', $trackname);
    }

    private function fetchPage(String $trackname): String
    {
        $result = $this->parser->curlRequest("https://music.yandex.ru/search?text=" . $trackname);

        return $result;
    }

    private function searchTracks(String $content): array
    {
        $tracks = [];

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($content);

        $searchNodesA = $dom->getElementsByTagName("a");

        foreach ($searchNodesA as $searchNodeA) {
            $classNameA = $searchNodeA->getAttribute('class');

            if (strpos($classNameA, 'd-track__title') !== false) {
                $tracks[] = [
                    'track_id' => basename($searchNodeA->getAttribute('href')), '
                    track_name' => $searchNodeA->nodeValue . ' - ' . $searchNodeA->getAttribute('title')
                ];
            }

        }

        if (empty($tracks)) {
            throw new \Error('No songs found');
        }

        return $tracks;
    }

    private function getDownloadLink(Array $track): string
    {
        $trackSrc = $this->findTrackSrc($track['track_id'] . '&format=json');

        $trackDetails = $this->getTrackDetails($trackSrc);

        $link = $this->buildDownloadUrl($trackDetails);

        return $link;
    }

    private function findTrackSrc(Int $track_id): String
    {
        $this->parser->curlOptions[CURLOPT_HTTPHEADER] = [
            'Content-Type: application/json',
            'X-Retpath-Y: ' . urlencode("https://music.yandex.ru/"),
            'Referer: https://music.yandex.ru/'
        ];

        $result = $this->parser->curlRequest("https://music.yandex.ru/api/v2.1/handlers/track/" . $track_id . "/track/download/m?hq=1");

        $result = json_decode($result, true);

        return $result['src'];
    }

    private function getTrackDetails(String $trackSrc) : array
    {
        $this->parser->curlOptions[CURLOPT_HTTPHEADER] = [
            'X-Retpath-Y: ' . urlencode("https://music.yandex.ru/")
        ];

        $result = $this->parser->curlRequest($trackSrc);

        return $result;
    }

    private function buildDownloadUrl(Array $trackDetails) : string
    {
        $salt = 'XGRlBW9FXlekgbPrRHuSiA';
        $hash = md5($salt . substr($trackDetails['path'], 1) . $trackDetails['s']);

        return 'https://' . $trackDetails['host'] . '/get-mp3/' . $hash . '/' . $trackDetails['ts'] . $trackDetails['path'];
    }
}