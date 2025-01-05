<?php

namespace src\services;

use PDO;
use src\helpers\ResultHelper;
use PDOException;
use src\migrations\Install;

/**
 * @author Tim Zapfe
 * @copyright Tim Zapfe
 * @date 15.10.2024
 */
class Database
{
    /**
     * @var PDO
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public PDO $con;

    /**
     * Constructor
     */
    public function __construct()
    {
        $host = $_ENV['DBHOST'];
        $name = $_ENV['DBNAME'];
        $user = $_ENV['DBUSER'];
        $pass = $_ENV['DBPASS'];

        try {
            // connect
            $this->con = new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->con->exec('set names utf8mb4');
        } catch (PDOException $e) {

            // return error
            ResultHelper::render([
                'message' => $e->getMessage()
            ], 500);
        }

        if (!empty($this->con)) {
            $install = new install($this->con);
            $install->init();
        }
    }
}