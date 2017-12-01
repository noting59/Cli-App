<?php

namespace App\Parsers\Contracts;

interface ParserContract
{
    /**
     * Save tracks to db
     *
     * @param array $tracks
     * @return bool
     */
    public function save(Array $tracks) : bool;

    /**
     * Find tracks by its name
     *
     * @param String $trackName
     * @return array
     */
    public function findTracks(String $trackName) : array;
}