<?php

namespace App\Parsers;

use App\Models\Tracks\Tracks as Tracks;

class Parser
{
    private $track;

    protected function saveTrack(array $tracks, String $service): bool
    {
        foreach ($tracks as $track) {
            $track = new Tracks();

            $track->setName($track['name']);
            $track->setService($service);
            $track->setDownloadLink($track['download_link']);

            $track->save();
        }
    }

    protected function parse()
    {

    }
}