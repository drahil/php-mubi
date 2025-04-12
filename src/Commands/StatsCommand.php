<?php

namespace drahil\MubiStats\Commands;

use drahil\MubiStats\Services\MovieService;
use drahil\MubiStats\Services\MubiProfileService;
use drahil\MubiStats\Services\StatsService;
use drahil\MubiStats\Singletons\MovieDataSingleton;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class StatsCommand
{
    private MubiProfileService $profileService;
    private MovieService $movieService;
    private StatsService $statsService;


    public function __construct()
    {
        $this->profileService = new MubiProfileService();
        $this->movieService = new MovieService();
        $this->statsService = new StatsService();
    }

    /**
     * Run the command.
     * @throws GuzzleException
     * @throws Exception
     */
    public function run()
    {
        $moviesSaved = $this->getMoviesFromProfile();

        if (!$moviesSaved) {
            echo 'No movies saved.' . PHP_EOL;
            return 0;
        }

        $action = $this->choseAction();
        $this->handleAction($action);
    }

    /**
     * Save movies from Mubi profile.
     * @return bool
     * @throws GuzzleException
     * @throws Exception
     */
    private function getMoviesFromProfile(): bool
    {
        $url = $this->profileService->getProfileUrl();
        $profileId = $this->profileService->getProfileId($url);
        $movies = $this->movieService->getMovies($profileId);
        $this->statsService->setMoviesData(MovieDataSingleton::getInstance($profileId)->getMovieData());

        return $this->movieService->saveMovies($movies, $profileId);
    }

    /**
     * Print possible actions.
     */
    private function choseAction(): string
    {
        echo 'Possible actions:' . PHP_EOL;
        echo '1. Search movies by country' . PHP_EOL;
        echo '2. Search movies by director' . PHP_EOL;
        echo '3. Search movies by genre' . PHP_EOL;
        echo '4. Get stats' . PHP_EOL;

        echo 'Choose action: ';

        return trim(fgets(STDIN));
    }

    /**
     * Handle action.
     *
     * @param string $action
     */
    private function handleAction(string $action): void
    {
        switch ($action) {
            case '1':
                $this->statsService->getMoviesByCountry();
                break;
            case '2':
                $this->statsService->getMoviesByDirector();
                break;
            case '3':
                $this->statsService->getMoviesByGenre();
                break;
            case '4':
                $this->statsService->getStats();
                break;
            default:
                var_dump($action);
                echo 'Invalid action.' . PHP_EOL;
        }
    }
}