<?php

namespace drAhil\PhpMubi\Commands;

use drahil\PhpMubi\Services\MovieService;
use drahil\PhpMubi\Services\MubiProfileService;

class StatsCommand
{
    private MubiProfileService $profileService;
    private MovieService $movieService;

    public function __construct()
    {
        $this->profileService = new MubiProfileService();
        $this->movieService = new MovieService();
    }

    public function run()
    {
        $url = $this->profileService->getProfileUrl();
        $profileId = $this->profileService->getProfileId('https://mubi.com/en/users/12559036');
        $movies = $this->movieService->getMovies($profileId);
        $this->movieService->saveMovies($movies);
    }
}