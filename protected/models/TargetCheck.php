<?php

/**
 * This is the model class for table "target_checks".
 *
 * The followings are the available columns in table "target_checks":
 * @property integer $id
 * @property integer $target_id
 * @property integer $check_id
 * @property string $result
 * @property string $rating
 * @property string $status
 * @property string $target_file
 * @property string $result_file
 * @property string $started
 * @property integer $pid
 * @property integer $language_id
 * @property string $protocol
 * @property integer $port
 * @property string $override_target
 * @property integer $user_id
 * @property string $table_result
 * @property string $solution
 * @property string $solution_title
 * @property string $poc
 * @property string $links
 * @property User $user
 * @property Check $check
 * @property Target $target
 * @property TargetCheckInput[] $inputs
 * @property TargetCheckAttachment[] $attachments
 * @property TargetCheckSolution[] $solutions
 * @property TargetCheckVuln $vuln
 */
class TargetCheck extends ActiveRecord implements IVariableScopeObject {
    /**
     * Check statuses.
     */
    const STATUS_OPEN = 0;
    const STATUS_IN_PROGRESS = 10;
    const STATUS_STOP = 50;
    const STATUS_FINISHED = 100;

    /**
     * Result ratings.
     */
    const RATING_NONE = 0;
    const RATING_NO_VULNERABILITY = 10;
    const RATING_HIDDEN = 20;
    const RATING_INFO = 50;
    const RATING_LOW_RISK = 100;
    const RATING_MED_RISK = 200;
    const RATING_HIGH_RISK = 500;

    /**
     * Export columns.
     */
    const COLUMN_TARGET = "target";
    const COLUMN_NAME = "name";
    const COLUMN_REFERENCE = "reference";
    const COLUMN_BACKGROUND_INFO = "background";
    const COLUMN_QUESTION = "question";
    const COLUMN_RESULT = "result";
    const COLUMN_SOLUTION = "solution";
    const COLUMN_RATING = "rating";
    const COLUMN_ASSIGNED_USER = "assigned_user";
    const COLUMN_DEADLINE = "deadline";
    const COLUMN_STATUS = "status";

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCheck the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "target_checks";
	}

    /**
     * Get valid rating values
     * @return array
     */
    public static function getValidRatings() {
        return array(
            self::RATING_NONE,
            self::RATING_NO_VULNERABILITY,
            self::RATING_HIDDEN,
            self::RATING_INFO,
            self::RATING_LOW_RISK,
            self::RATING_MED_RISK,
            self::RATING_HIGH_RISK,
        );
    }

    /**
     * Get rating names
     * @return array
     */
    public static function getRatingNames() {
        return array(
            self::RATING_NONE => Yii::t("app", "No Test Done"),
            self::RATING_NO_VULNERABILITY => Yii::t("app", "No Vulnerability"),
            self::RATING_HIDDEN => Yii::t("app", "Hidden"),
            self::RATING_INFO =>  Yii::t("app", "Info"),
            self::RATING_LOW_RISK => Yii::t("app", "Low Risk"),
            self::RATING_MED_RISK => Yii::t("app", "Med Risk"),
            self::RATING_HIGH_RISK => Yii::t("app", "High Risk"),
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("target_id, check_id", "required"),
            array("target_id, check_id, pid, port, language_id, user_id", "numerical", "integerOnly" => true),
            array("target_file, result_file, protocol, override_target", "length", "max" => 1000),
            array("status", "in", "range" => array(self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_STOP, self::STATUS_FINISHED)),
            array("rating", "in", "range" => self::getValidRatings()),
            array("result, started, table_result, solution, solution_title, poc, links", "safe"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "target" => array(self::BELONGS_TO, "Target", "target_id"),
            "check" => array(self::BELONGS_TO, "Check", "check_id"),
            "language" => array(self::BELONGS_TO, "Language", "language_id"),
            "user" => array(self::BELONGS_TO, "User", "user_id"),
            "vuln" => array(self::HAS_ONE, "TargetCheckVuln", "target_check_id"),
            "inputs" => array(self::HAS_MANY, "TargetCheckInput", "target_check_id"),
            "solutions" => array(self::HAS_MANY, "TargetCheckSolution", "target_check_id"),
            "attachments" => array(self::HAS_MANY, "TargetCheckAttachment", "target_check_id"),
		);
	}

    /**
     * Set automation error.
     */
    public function automationError($error) {
        $uniqueHash = strtoupper(substr(hash("sha256", time() . rand() . $error), 0, 16));

        Yii::log($uniqueHash . " " . $error, "error");
        Yii::getLogger()->flush(true);

        $message = Yii::t("app", "Internal server error. Please send this error code to the administrator - {code}.", array(
            "{code}" => $uniqueHash
        ));

        if (!$this->result) {
            $this->result = "";
        } else {
            $this->result .= "\n";
        }

        $this->result .= $message;
        $this->status = TargetCheck::STATUS_FINISHED;
        $this->save();
    }

    /**
     * Check if check is running
     * @return boolean is running.
     */
    public function getIsRunning() {
        return in_array($this->status, array(self::STATUS_IN_PROGRESS, self::STATUS_STOP));
    }


    /**
     * Get variable value
     * @param $name
     * @param VariableScope $scope
     * @return mixed
     * @throws Exception
     */
    public function getVariable($name, VariableScope $scope) {
        $check = $this->check;
        $names = $this->getRatingNames();
        $abbreviations = array(
            self::RATING_NONE => "none",
            self::RATING_NO_VULNERABILITY => "no_vuln",
            self::RATING_HIDDEN => "hidden",
            self::RATING_INFO =>  "info",
            self::RATING_LOW_RISK => "low",
            self::RATING_MED_RISK => "med",
            self::RATING_HIGH_RISK => "high",
        );

        $checkData = array(
            "name" => $check->getLocalizedName(),
            "background_info" => $check->getLocalizedBackgroundInfo(),
            "hints" => $check->getLocalizedHints(),
            "question" => $check->getLocalizedQuestion(),
            "rating" => $abbreviations[$this->rating],
            "rating_name" => $names[$this->rating],
            "target" => $this->override_target ? $this->override_target : $this->target->host,
            "links" => $this->links,
            "poc" => $this->poc,
            "result" => $this->result,
            "reference" => $check->_reference->name . ($check->reference_code ? "-" . $check->reference_code : ""),
            "solution" => array(),
            "attachments" => array(),
        );

        if ($this->solution) {
            $checkData["solution"][] = $this->solution;
        }

        foreach ($this->solutions as $solution) {
            $checkData["solution"][] = $solution->solution->localizedSolution;
        }

        $checkData["solution"] = implode("<br><br>", $checkData["solution"]);

        foreach ($this->attachments as $attachment) {
            if (in_array($attachment->type, array("image/jpeg", "image/png", "image/gif", "image/pjpeg"))) {
                $checkData["attachments"][] = array(
                    "name" => $attachment->name,
                    "file" => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path,
                    "type" => $attachment->type,
                );
            }
        }

        if (!in_array($name, array_keys($checkData))) {
            throw new Exception(Yii::t("app", "Invalid variable: {var}.", array("{var}" => $name)));
        }

        return $checkData[$name];
    }

    /**
     * Get list
     * @param $name
     * @param $filters
     * @param VariableScope $scope
     * @return array
     * @throws Exception
     */
    public function getList($name, $filters, VariableScope $scope) {
        throw new Exception(Yii::t("app", "Invalid list: {list}.", array("{list}" => $name)));
    }
}
