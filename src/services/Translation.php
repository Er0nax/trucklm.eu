<?php

namespace src\services;

use src\App;
use src\components\Entry;

/**
 * @author Tim Zapfe
 * @date 07.11.2024
 */
class Translation
{

    /**
     * @var array
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    private static array $translations = [];

    /**
     * @var Entry
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    private static Entry $entry;

    /**
     * Constructor which first sets all translations from db.
     */
    public function __construct()
    {
        // new entry for class
        self::$entry = new Entry();

        // get all
        $translations = self::$entry
            ->columns(['translations' => ['*']])
            ->tables(['translations'])
            ->where(['translations' => [['active', true]]])
            ->all();

        // set with correct key
        foreach ($translations as $translation) {
            // save as static array
            self::$translations[self::getKeyForTranslation($translation['value'])] = $translation;

            // also save in session
            $_SESSION['translations '] = self::$translations;
        }
    }

    /**
     * Returns a translated string with replaced values.
     * @param string|null $value
     * @param string $category
     * @param array $variables
     * @return string|null
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    public static function t(?string $value, string $category = 'site', array $variables = []): ?string
    {
        // value must be string!
        if (empty($value) || !is_string($value)) {
            return $value;
        }

        // translations allowed?
        if (!App::getConfig()->useTranslation) {
            return self::replaceVariables($value, $variables);
        }

        // get the valueAsKey for the key
        $valueAsKey = self::getKeyForTranslation($value);

        // check if a translation already exists in the translation array
        if (self::existInSavedTranslation($valueAsKey)) {

            // get the data
            $translation = self::$translations[$valueAsKey];

            // check if value exist for given language
            if (!empty($translation[$_SESSION['language']])) {

                // return the replaced value
                return self::replaceVariables($translation[$_SESSION['language']], $variables);
            }

            // return the default value
            return self::replaceVariables($value, $variables);
        }

        // add translation
        self::addTranslation($value, $category);

        // return the default value with replaces variables
        return self::replaceVariables($value, $variables);
    }

    /**
     * Insert a translation into the db and static array + updates session.
     * @param string $value
     * @param string $category
     * @return void
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    private static function addTranslation(string $value, string $category): void
    {
        // get the valueAsKey for the key
        $valueAsKey = self::getKeyForTranslation($value);

        // reset entry first
        self::$entry->reset();

        // insert into database
        $translationId = self::$entry->insert('translations', [
            'category' => $category,
            'value'    => $value,
        ]);

        // return int? (as success)
        if (is_numeric($translationId)) {

            // add to translations array
            self::$translations[$valueAsKey] = [
                'category' => $category,
                'value'    => $value,
            ];

            // update session
            $_SESSION['translations '] = self::$translations;
        }
    }

    /**
     * Returns the key for a translation.
     * @param string $value
     * @return string
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    private static function getKeyForTranslation(string $value): string
    {
        // first replace all
        // Remove any characters that do not match the allowed characters
        return trim(preg_replace('/[^a-zA-Z0-9_\-{},.]/', '-', strtolower($value)), '-');
    }

    /**
     * Returns boolean whether a translation exist in the saved translations array or not.
     * @param string $translationAsKey
     * @return bool
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    private static function existInSavedTranslation(string $translationAsKey): bool
    {
        $translationKeys = array_keys(self::$translations);

        return in_array($translationAsKey, $translationKeys);
    }

    /**
     * Returns a string with replaces variables.
     * @param string $value
     * @param array $variables
     * @return string
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    private static function replaceVariables(string $value, array $variables): string
    {
        foreach ($variables as $key => $_value) {
            $value = str_replace('{' . $key . '}', $_value, $value);
        }

        return $value;
    }
}