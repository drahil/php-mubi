<?php

namespace drahil\MubiStats\Services;

use InvalidArgumentException;

class MubiProfileService
{
    /**
     * Get profile URL from user input.
     *
     * @return string
     */
    public function getProfileUrl(): string
    {
        echo 'Enter Mubi profile URL: ';
        return trim(fgets(STDIN));
    }

    /**
     * Extract profile ID from Mubi profile URL.
     *
     * @param string $url
     * @return string
     * @throws InvalidArgumentException If the URL is invalid.
     */
    public function getProfileId(string $url): string
    {
        if (empty($url)) {
            throw new InvalidArgumentException('URL cannot be empty.');
        }

        $parts = explode('/', trim($url, '/'));
        $profileId = end($parts);

        if (empty($profileId)) {
            throw new InvalidArgumentException('Invalid profile URL. Please provide a valid Mubi profile URL.');
        }

        return $profileId;
    }
}