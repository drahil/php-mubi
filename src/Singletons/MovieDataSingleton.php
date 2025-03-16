<?php

namespace drahil\MubiStats\Singletons;

class MovieDataSingleton
{
    private static ?MovieDataSingleton $instance = null;
    private array $movieData;

    private function __construct()
    {
        $filePath = 'movies.json';

        if (file_exists($filePath)) {
            $jsonData = file_get_contents($filePath);
            $this->movieData = json_decode($jsonData, true) ?? [];
        } else {
            $this->movieData = [];
        }
    }

    public static function getInstance(): MovieDataSingleton
    {
        if (self::$instance === null) {
            self::$instance = new MovieDataSingleton();
        }

        return self::$instance;
    }

    public function getMovieData(): array
    {
        return $this->movieData;
    }

    public function setMovieData(array $movieData): void
    {
        $this->movieData = $movieData;
        file_put_contents('movies.json', json_encode($movieData, JSON_PRETTY_PRINT));
    }
}
