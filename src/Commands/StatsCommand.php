<?php

namespace drAhil\MubiStats\Commands;

use drahil\MubiStats\Services\MovieService;
use drahil\MubiStats\Services\MubiProfileService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class StatsCommand
{
    private MubiProfileService $profileService;
    private MovieService $movieService;


    public function __construct()
    {
        $this->profileService = new MubiProfileService();
        $this->movieService = new MovieService();
    }

    /**
     * Run the command.
     * @throws GuzzleException
     * @throws Exception
     */
    public function run()
    {
        $moviesSaved = $this->getMoviesFromProfile();

        if (! $moviesSaved) {
            echo 'No movies saved.' . PHP_EOL;
            return 0;
        }

        echo 'Movies saved successfully.' . PHP_EOL;
    }


    /**
     * Save movies from Mubi profile.
     * @return true
     * @throws GuzzleException
     * @throws Exception
     */
    private function getMoviesFromProfile(): true
    {
        $url = $this->profileService->getProfileUrl();
        $profileId = $this->profileService->getProfileId($url);
        $movies = $this->movieService->getMovies($profileId);

        return $this->movieService->saveMovies($movies);
    }
}