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

    /**
     * Implements interface, search for tracks
     *
     * @param String $trackName
     * @return array
     */
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

    /**
     * Implements interface, save tracks to db
     *
     * @param array $tracks
     * @return bool
     */
    public function save(Array $tracks): bool
    {
        foreach ($tracks as $key => $value) {
            $track = new Tracks();

            //TODO Change model for not saving same tracks to db

            $track->setName($value['track_name']);
            $track->setDownloadLink($value['track_download_url']);
            $track->setService('yandex');

            $track->save();
        }

        return true;
    }

    /**
     * Remove quotes from search string passed from CLI arguments
     *
     * @param String $trackname
     * @return String
     */
    private function removeQuotes(String $trackname): String
    {
        return str_replace(array('"', "'"), '', $trackname);
    }

    /**
     * Fetch DOM as text from https://music.yandex.ru
     *
     * @param String $trackname
     * @return String
     */
    private function fetchPage(String $trackname): String
    {
        $result = $this->parser->curlRequest("https://music.yandex.ru/search?text=" . urlencode($trackname));

        return $result;
    }

    /**
     * Make search through DOM and find track id and track name
     *
     * @param String $content
     * @return array
     * @throws \Error
     */
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
                    'track_id' => basename($searchNodeA->getAttribute('href')),
                    'track_name' => $searchNodeA->nodeValue . ' - ' . $searchNodeA->getAttribute('title')
                ];
            }

        }

        if (empty($tracks)) {
            throw new \Error('No songs found');
        }

        return $tracks;
    }

    /**
     * Return download link for track
     *
     * @param array $track
     * @return string
     */
    private function getDownloadLink(Array $track): string
    {
        $trackSrc = $this->findTrackSrc($track['track_id']);

        $trackDetails = $this->getTrackDetails($trackSrc);

        $link = $this->buildDownloadUrl($trackDetails);

        return $link;
    }

    /**
     * Looking for track src and return it
     *
     * @param String $track_id
     * @return string
     */
    private function findTrackSrc(String $track_id): string
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

    /**
     * Get track details for download link
     *
     * @param String $trackSrc
     * @return array
     */
    private function getTrackDetails(String $trackSrc) : array
    {
        $this->parser->curlOptions[CURLOPT_HTTPHEADER] = [
            'X-Retpath-Y: ' . urlencode("https://music.yandex.ru/")
        ];

        $result = $this->parser->curlRequest($trackSrc . '&format=json');

        return json_decode($result, true);
    }

    /**
     * Build download link
     *
     * @param array $trackDetails
     * @return string
     */
    private function buildDownloadUrl(Array $trackDetails) : string
    {
        $salt = 'XGRlBW9FXlekgbPrRHuSiA';
        $hash = md5($salt . substr($trackDetails['path'], 1) . $trackDetails['s']);

        return 'https://' . $trackDetails['host'] . '/get-mp3/' . $hash . '/' . $trackDetails['ts'] . $trackDetails['path'];
    }
}