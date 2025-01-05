<?php
/**
 * @author Tim Zapfe
 * @copyright Tim Zapfe
 * @date 23.12.2024
 */

namespace src\helpers;

use src\components\Entry;

class UserHelper extends BaseHelper
{
    /**
     * Returns the user given by their token or empty array.
     * @param string $token
     * @return array
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 23.12.2024
     */
    public static function getUserBySteam64ID(string $steam64ID): array
    {
        $entry = self::getUserQuery();

        $entry->where([
            'users' => [
                ['steam64ID', $steam64ID]
            ]
        ]);

        return $entry->one();
    }

    /**
     * Returns a user query with no where statement.
     * @return Entry
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 23.12.2024
     */
    public static function getUserQuery(): Entry
    {
        $entry = new Entry();

        $entry->columns([
            'users'            => [
                'id',
                'username',
                'snowflake',
                'steam64ID',
                'description',
                'deleted',
                'active',
                'updated_at',
                'created_at'
            ], 'user_settings' => [
                'language',
                'darkmode',
                'time_scale'
            ], 'roles'         => [
                "name AS 'role_name'",
                "color AS 'role_color'",
            ], 'images'        => [
                "src AS 'avatar'"
            ]
        ]);

        $entry->tables([
            'users',
            ['user_settings', 'users.id', 'user_settings.user_id', 'LEFT'],
            ['roles', 'users.role_id', 'roles.id', 'LEFT'],
            ['images', 'users.avatar_id', 'images.id', 'LEFT'],
        ]);

        return $entry;
    }

    /**
     * Returns a random string.
     * @param int $length
     * @return string
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public static function generateUserToken(int $length = 20): string
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    /**
     * Returns the snowflake for a user's username.
     * @param string $username
     * @return string
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public static function generateUserSnowflake(?string $username): string
    {
        if (empty($username)) {
            return ''; // empty string
        }

        // convert umlaute
        $umlaute = [
            'Ä' => 'Ae', 'ä' => 'ae',
            'Ö' => 'Oe', 'ö' => 'oe',
            'Ü' => 'Ue', 'ü' => 'ue',
            'ß' => 'ss'
        ];

        $username = strtr($username, $umlaute);

        // apply regex
        return preg_replace('/[^a-zA-Z0-9_.]/', '', $username);
    }

    /**
     * Returns boolean whether a username exists or not.
     * @param string $username
     * @return bool
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public static function checkIfUsernameExists(string $username): bool
    {
        $entry = self::getUserQuery();

        // check if username exists
        return $entry->where(['users' => [['username', $username]]])->exists();
    }
}