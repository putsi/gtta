<?php

/**
 * Class SystemManager
 */
class SystemManager {
    /**
     * Update system status (atomic)
     * @param $newStatus
     * @param $allowedStatuses
     * @throws Exception
     */
    public static function updateStatus($newStatus, $allowedStatuses=null) {
        $error = false;

        if ($allowedStatuses !== null && !is_array($allowedStatuses)) {
            $allowedStatuses = array($allowedStatuses);
        }

        $fp = fopen(Yii::app()->params["systemStatusLock"], "w");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            $system = System::model()->findByPk(1);

            if ($allowedStatuses !== null && !in_array($system->status, $allowedStatuses)) {
                $error = true;
            }

            if (!$error) {
                $system->status = $newStatus;
                $system->update_pid = null;
                $system->save();
            }

            flock($fp, LOCK_UN);
        } else {
            $error = true;
        }

        fclose($fp);

        if ($error) {
            throw new Exception("Error changing system status.");
        }
    }
} 