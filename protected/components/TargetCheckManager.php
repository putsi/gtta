<?php
/**
 * Class TargetCheckManager
 */
class TargetCheckManager {
    /**
     * Target check create
     * @param Check $check
     * @param $data
     * @return TargetCheck
     * @throws Exception
     */
    public function create(Check $check, $data) {
        $targetCheck = new TargetCheck();

        try {
            $targetCheck->target_id = $data["target_id"];
            $targetCheck->check_id = $check->id;
            $targetCheck->user_id = $data["user_id"];
            $targetCheck->language_id = $data["language_id"];
            $targetCheck->status = isset($data["status"]) ? $data["status"] : TargetCheck::STATUS_OPEN;
            $targetCheck->rating = isset($data["rating"]) ? $data["rating"] : TargetCheck::RATING_NONE;

            if (isset($data["result"])) {
                $targetCheck->setFieldValue(
                    GlobalCheckField::FIELD_POC,
                    $data["result"]
                );
            }

            $targetCheck->save();
            $targetCheck->refresh();

            foreach ($check->scripts as $script) {
                $targetCheckScript = new TargetCheckScript();
                $targetCheckScript->check_script_id = $script->id;
                $targetCheckScript->target_check_id = $targetCheck->id;
                $targetCheckScript->save();
            }

            /** @var CheckField $field */
            foreach ($check->fields as $field) {
                $targetCheckField = new TargetCheckField();
                $targetCheckField->target_check_id = $targetCheck->id;
                $targetCheckField->check_field_id = $field->id;
                $targetCheckField->value = $field->getValue();
                $targetCheckField->hidden = $field->hidden;
                $targetCheckField->save();
            }

            $this->addEvidence($targetCheck);
        } catch (Exception $e) {
            throw new Exception("Can't create check.");
        }

        return $targetCheck;
    }

    /**
     * Start check
     * @param $id
     */
    public static function start($id, $chain=false) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            throw new Exception("Check not found.");
        }

        $now = new DateTime();

        $params = [
            "operation" => AutomationJob::OPERATION_START,
            "obj_id" => $targetCheck->id,
            "started" => $now->format(ISO_DATE_TIME),
        ];

        if ($chain) {
            $params["chain"] = true;
        }

        AutomationJob::enqueue($params);
    }

    /**
     * Stop check
     * @param $id
     * @throws Exception
     */
    public static function stop($id) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            throw new Exception("Check not found.");
        }

        if ($targetCheck->isRunning) {
            AutomationJob::enqueue(array(
                "operation" => AutomationJob::OPERATION_STOP,
                "obj_id" => $targetCheck->id,
            ));
        }
    }

    /**
     * Get running check ids
     * @param $id
     * @return array
     * @throws Exception
     */
    public static function getRunning() {
        $mask = JobManager::buildId(AutomationJob::ID_TEMPLATE, array(
            "operation" => "*",
            "obj_id" => "*",
        ));
        $mask .= ".pid";
        $keys = Resque::redis()->keys($mask);

        if (!is_array($keys)) {
            $keys = explode(" ", $keys);
        }

        $ids = array();

        foreach ($keys as $key) {
            if (preg_match("/check.start.\d+/", $key, $match)) {
                preg_match("/\d+/", $match[0], $id);
                $ids[] = $id[0];
            }
        }

        return $ids;
    }

    /**
     * Get check started time
     * @param $id
     * @return mixed
     */
    public static function getStartTime($id) {
        $job = JobManager::buildId(AutomationJob::ID_TEMPLATE, array(
            "operation" => AutomationJob::OPERATION_START,
            "obj_id" => $id,
        ));

        return JobManager::getVar($job, "started");
    }

    /**
     * Reindex target check fields
     * @param CheckField $checkField
     * @throws Exception
     */
    public static function reindexFields(CheckField $checkField) {
        foreach ($checkField->check->targetChecks as $targetCheck) {
            $targetCheckField = TargetCheckField::model()->findByAttributes([
                "check_field_id" => $checkField->id,
                "target_check_id" => $targetCheck->id
            ]);

            // if empty field -> update value
            if ($targetCheckField) {
                if (!$targetCheckField->value) {
                    $targetCheckField->value = $checkField->getValue();
                    $targetCheckField->save();
                }
            } else {
                $tcf = new TargetCheckField();
                $tcf->target_check_id = $targetCheck->id;
                $tcf->check_field_id = $checkField->id;
                $tcf->value = $checkField->getValue();
                $tcf->save();
            }
        }
    }

    /**	
     * Get check human readable data
     * @param TargetCheck $tc
     * @return array
     */
    public static function getData(TargetCheck $tc) {
        $renderController = new CController("RenderController");

        $attachmentList = array();
        $attachments = TargetCheckAttachment::model()->findAllByAttributes(array(
            "target_check_id" => $tc->id
        ));

        foreach ($attachments as $attachment) {
            $attachmentList[] = array(
                "name" => CHtml::encode($attachment->name),
                "path" => $attachment->path,
                "url" => Yii::app()->createUrl('project/attachment', array('path' => $attachment->path)),
            );
        }

        $table = null;

        if ($tc->table_result) {
            $table = new ResultTable();
            $table->parse($tc->table_result);
        }

        $time = TargetCheckManager::getStartTime($tc->id);
        $startedText = null;

        if ($time) {
            $started = new DateTime($time);
            $time = time() - strtotime($time);
            $user = $tc->user;

            if ($tc->status != TargetCheck::STATUS_FINISHED) {
                $startedText = Yii::t("app", "Started by {user} on {date} at {time}", array(
                    "{user}" => $user->name ? $user->name : $user->email,
                    "{date}" => $started->format("d.m.Y"),
                    "{time}" => $started->format("H:i:s"),
                ));
            }
        } else {
            $time = -1;
        }

        return [
            "id" => $tc->id,
            "overrideTarget" => $tc->overrideTarget,
            "result" => $tc->result,
            "poc" => $tc->poc,
            "tableResult" => $table ? $renderController->renderPartial("/project/target/check/tableresult", array("table" => $table, "check" => $tc), true) : "",
            "finished" => !$tc->isRunning,
            "time" => $time,
            "attachmentControlUrl" => Yii::app()->createUrl("project/controlattachment"),
            "attachments" => $attachmentList,
            "startedText" => $startedText,
        ];
    }

    /**
     * Add target check evidence
     * @param TargetCheck $targetCheck
     * @return IssueEvidence
     * @throws Exception
     */
    public function addEvidence(TargetCheck $targetCheck) {
        $target = $targetCheck->target;

        $issue = Issue::model()->findByAttributes([
            "project_id" => $target->project_id,
            "check_id" => $targetCheck->check_id,
        ]);

        if (!$issue) {
            $pm = new ProjectManager();
            $issue = $pm->addIssue($target->project, $targetCheck->check);
        }

        $evidence = IssueEvidence::model()->findByAttributes([
            "issue_id" => $issue->id,
            "target_check_id" => $targetCheck->id
        ]);

        if ($evidence) {
            return $evidence;
        }

        $evidence = new IssueEvidence();
        $evidence->issue_id = $issue->id;
        $evidence->target_check_id = $targetCheck->id;
        $evidence->save();

        return $evidence;
    }
}