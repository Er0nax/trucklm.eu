<?php
/**
 * @author Tim Zapfe
 * @date 15.11.2024
 */

namespace src\migrations;

use src\App;
use src\helpers\BaseHelper;
use src\helpers\ResultHelper;

/**
 * @author Tim Zapfe
 * @date 15.11.2024
 */
class Install extends BaseMigration
{
    /**
     * make sure all tables are installed.
     * @return void
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public function init(): void
    {
        // not environment?
        if ($_ENV['ENVIRONMENT'] !== 'dev') {
            return;
        }

        // install is set to true?
        if ($_ENV['INSTALL_APP'] !== 'true') {
            return;
        }

        // key given and same as security key in env?
        if (empty($_GET['key']) || $_ENV['SECURITY_KEY'] !== $_GET['key']) {
            ResultHelper::render([
                'message' => 'Are you sure to continue? If yes, press down below.',
                'link'    => BaseHelper::getUrl(true) . '?key=' . $_ENV['SECURITY_KEY'],
            ], 500, [
                'translate' => false
            ]);
        }

        ResultHelper::switchView(true);
        ResultHelper::log('Loading database tables from config...', 'warn');

        // get tables
        $tables = App::getConfig()->tables;

        ResultHelper::log('Found ' . count($tables) . ' tables.', 'warn');

        // loop through each table
        foreach ($tables as $tableName => $definition) {
            // Extract columns, options, and defaults
            $columns = $definition['columns'] ?? [];
            $options = $definition['options'] ?? [];
            $defaults = $definition['defaults'] ?? [];

            ResultHelper::log('Creating table ' . $tableName . '...');

            // check if the table exists
            $this->entry->createTable($tableName, $columns, $options, $defaults);
        }

        ResultHelper::log('Finished installing database.', 'success');

        ResultHelper::switchView();
    }
}