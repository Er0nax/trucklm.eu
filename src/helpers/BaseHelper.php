<?php

namespace src\helpers;


/**
 * @author Tim Zapfe
 */
class BaseHelper
{
    /**
     * Returns the current full url.
     * @param bool $withRequest
     * @return string
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 15.10.2024
     */
    public static function getUrl(bool $withRequest = false): string
    {
        // Get the protocol (http or https)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';

        // Get the hostname (e.g., www.example.com)
        $host = $_SERVER['HTTP_HOST'];

        // Requests
        $request = (!$withRequest) ? $_ENV['PRIMARY_SITE_PATH'] : urldecode($_SERVER['REQUEST_URI']);

        return rtrim($protocol . $host . $request, '/') . '/';
    }

    /**
     * Returns the url with a ?t=<timestamp> at the end.
     * @param string $url
     * @return string
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public static function getUrlWithTimestamp(string $url): string
    {
        return $url . '?t=' . time();
    }

    public static function getSlug()
    {

    }

    /**
     * Creates a new cookie.
     * @param string $name
     * @param mixed $value
     * @param int $days
     * @return void
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public static function setCookie(string $name, mixed $value, int $days = 7): void
    {
        setcookie($name, $value,
            [
                'expires'  => time() + (86400 * $days),
                'path'     => '/',
                'samesite' => 'Lax', // oder 'None' für Drittanbieter-Kontexte
                'secure'   => true, // erforderlich bei 'SameSite=None'
                'httponly' => true, // optional, erhöht Sicherheit
            ]
        );
    }

    /**
     * Add custom character add the beginning of a string.
     * @param $string
     * @param string $character
     * @return mixed|string
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public static function ensureWithCharacter($string, string $start = null, string $end = null): mixed
    {
        // does start character exist and string does not already start with character?
        if (!empty($start) && !str_starts_with($string, $start)) {
            // Add character to the beginning of the string
            $string = $start . $string;
        }

        // does end character exist and string does not already end with character?
        if (!empty($end) && !str_ends_with($string, $end)) {
            // Add character to the beginning of the string
            $string = $string . $end;
        }

        return $string;
    }

    /**
     * Returns boolean whether a user is logged in or not.
     * @return bool
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 20.11.2024
     */
    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user']);
    }

    /**
     * Returns the current timestamp.
     * @return string
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public static function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}