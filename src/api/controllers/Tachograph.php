<?php

namespace src\api\controllers;

use src\api\Api;
use src\App;
use src\helpers\BaseHelper;
use src\helpers\ResultHelper;
use src\helpers\TachoHelper;

/**
 * @author Tim Zapfe
 * @copyright Tim Zapfe
 * @date 28.12.2024
 */
class Tachograph extends Api
{
    /**
     * Update the tachograph for a user.
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionSet(): void
    {
        $user = $this->requireUser();

        // current tacho
        $currentTacho = $this->getTachograph($user['id']);

        // get values
        $speed = App::getRequest('speed');
        $mode = App::getRequest('mode');
        $distance = App::getRequest('distance');

        // create array with values
        $updates = [];

        // add speed
        if (is_numeric($speed)) {
            $updates['current_speed'] = $speed;
        }

        // add mode
        if (isset($mode) && ($mode === 'work' || $mode === 'pause')) {
            $updates['current_mode'] = $mode;
        }

        // add distance
        if (is_numeric($distance)) {
            $updates['current_distance'] = $distance;
        }

        // mode changed?
        if (isset($updates['current_mode']) && ($mode !== $currentTacho['current_mode'])) {
            // mode changed so update current_mode_started
            $updates['current_mode_started'] = BaseHelper::now();

            // mode was pause?
            if ($currentTacho['current_mode'] === 'pause') {

                // was current mode (pause) for at least 45 min?
                if (TachoHelper::secondsSinceTimestamp($currentTacho['current_mode_started'], $user['time_scale']) > 2600) {
                    // set 45 min to now
                    $updates['last_45_pause'] = BaseHelper::now();
                }

                // was current mode (pause) for at least 9 hours?
                if (TachoHelper::secondsSinceTimestamp($currentTacho['current_mode_started'], $user['time_scale']) > 32400) {
                    // set 45 min to now
                    $updates['last_9_pause'] = BaseHelper::now();
                }

                // get values
                $currentTotalSecondsPause = $currentTacho['total_seconds_pause'];
                $newTotalPauseSeconds = TachoHelper::secondsSinceTimestamp($currentTacho['current_mode_started'], $user['time_scale']);

                // add to total pause time
                $updates['total_seconds_pause'] = $currentTotalSecondsPause + ($newTotalPauseSeconds / $user['time_scale']);
            }

            // mode was work?
            if ($currentTacho['current_mode'] === 'work') {
                // get new values
                $current_total_seconds_work = $currentTacho['total_seconds_driven'];
                $newTotalSeconds = TachoHelper::secondsSinceTimestamp($currentTacho['current_mode_started'], $user['time_scale']);

                // add to total work time
                $updates['total_seconds_driven'] = $current_total_seconds_work + ($newTotalSeconds / $user['time_scale']);
            }
        }

        // updates empty?
        if (empty($updates)) {
            ResultHelper::render([
                'message' => 'Nothing to update.'
            ]);
        }

        // update tacho
        $updated = $this->entry->update('tachographs', $updates, [
            'user_id' => $user['id']
        ]);

        // success
        if ($updated) {
            ResultHelper::render([
                'message' => 'New values set for tachograph.'
            ]);
        }

        // error
        ResultHelper::render([
            'message' => 'Could not update values for tachograph.'
        ], 500);
    }

    /**
     * Returns all tachograph values for a user.
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    public function actionGet(): void
    {
        $user = $this->requireUser();

        // get the current tachograph.
        $tacho = $this->getTachograph($user['id']);

        // seconds in current mode with scale
        $seconds_in_mode = TachoHelper::secondsSinceTimestamp($tacho['current_mode_started']);

        $tacho['real'] = [
            'seconds_in_mode' => $seconds_in_mode,
            'minutes_in_mode' => round($seconds_in_mode / 60)
        ];

        $tacho['simulation'] = [
            'seconds_in_mode' => $seconds_in_mode * $user['time_scale'],
            'minutes_in_mode' => round(($seconds_in_mode * $user['time_scale']) / 60)
        ];

        // return all
        ResultHelper::render([
            'message' => 'Successfully retrieved tachograph values.',
            'tacho'   => $tacho
        ]);
    }

    /**
     * Returns the tachograph values for a user.
     * @param int $userID
     * @return array
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 05.01.2025
     */
    private function getTachograph(int $userID): array
    {
        $this->entry->reset();
        return $this->entry->columns(['tachographs' => ['*']])
            ->tables('tachographs')
            ->where(['tachographs' => [['user_id', $userID]]])
            ->one();
    }
}