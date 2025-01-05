<?php

namespace src\api\controllers;

use src\api\Api;
use src\App;
use src\controllers\UserController;
use src\helpers\ResultHelper;
use src\helpers\UserHelper;

/**
 * @author Tim Zapfe
 * @copyright Tim Zapfe
 * @date 28.12.2024
 */
class Log extends Api
{
    /**
     * log all data for a user.
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionData(): void
    {
        $this->requireUser();

        ResultHelper::render([
            'message' => 'data logged'
        ]);
    }

    /**
     * Log fined event.
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionFined(): void
    {
        $this->requireUser();

        ResultHelper::render([
            'message' => 'Fined'
        ]);
    }

    /**
     * log ferry event
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionFerry(): void
    {
        $this->requireUser();

        ResultHelper::render([
            'message' => 'Ferry used'
        ]);
    }

    /**
     * log refueled event
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionRefueled(): void
    {
        $this->requireUser();

        ResultHelper::render([
            'message' => 'Refueled'
        ]);
    }

    /**
     * log tollgate event
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionTollgate(): void
    {
        $this->requireUser();

        ResultHelper::render([
            'message' => 'Tollgate used'
        ]);
    }

    /**
     * log train event
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionTrain(): void
    {
        $this->requireUser();

        ResultHelper::render([
            'message' => 'Train used'
        ]);
    }
}