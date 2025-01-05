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
class Job extends Api
{
    /**
     * job cancelled
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionCancelled(): void
    {
        $this->requireUser();

        ResultHelper::render([
            'message' => 'Job Cancelled'
        ]);
    }

    /**
     * job delivered
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionDelivered(): void
    {
        $this->requireUser();

        ResultHelper::render([
            'message' => 'Job Delivered'
        ]);
    }

    /**
     * job started
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionStarted(): void
    {
        $this->requireUser();

        ResultHelper::render([
            'message' => 'Job started'
        ]);
    }
}