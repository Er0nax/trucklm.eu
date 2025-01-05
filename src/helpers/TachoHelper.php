<?php
/**
 * @author Tim Zapfe
 * @copyright Tim Zapfe
 * @date 05.01.2025
 */

namespace src\helpers;

use DateTime;
use Exception;

class TachoHelper extends BaseHelper
{
    /**
     * Returns the minutes since the last timestamp.
     * @param string $timestamp
     * @return int
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public static function secondsSinceTimestamp(string $timestamp, int $scale = 1): int
    {
        try {
            $givenTime = new DateTime($timestamp);
        } catch (Exception) {
            return 0;
        }

        // Get the current time as a DateTime object
        $currentTime = new DateTime();

        // Calculate the time difference
        $interval = $currentTime->diff($givenTime);

        // Convert the difference to minutes
        $seconds = ($interval->days * 24 * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;

        // Check if the given timestamp is in the future
        if ($givenTime > $currentTime) {
            $seconds = -$seconds; // Return a negative value for future timestamps
        }

        return $seconds * $scale;
    }
}