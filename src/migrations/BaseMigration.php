<?php

namespace src\migrations;

use PDO;
use src\components\Entry;

/**
 * @author Tim Zapfe
 * @date 15.11.2024
 */
class BaseMigration
{
    /**
     * @var Entry
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    protected Entry $entry;

    /**
     * Constructor
     * @param PDO $con
     */
    public function __construct(PDO $con)
    {
        $this->entry = new Entry($con);
    }
}