<?php

/**
 * This is the model class for target check edit form.
 */
class TargetCheckEditForm extends CFormModel {
    const CUSTOM_SOLUTION_IDENTIFIER = "custom";

    /**
     * @var string override target.
     */
    public $overrideTarget;

    /**
     * @var string protocol.
     */
    public $protocol;

    /**
     * @var integer port.
     */
    public $port;

    /**
     * @var array (json) scripts
     */
    public $scripts;

    /**
     * @var array (json) script timeouts
     */
    public $timeouts;

	/**
     * @var string resultTitle.
     */
    public $resultTitle;

	/**
     * @var string result.
     */
    public $result;

    /**
     * @var boolean save solution.
     */
    public $saveResult;

    /**
     * @var string rating.
     */
    public $rating;

    /**
     * @var array solutions.
     */
    public $solutions;

    /**
     * @var array attachment_titles.
     */
    public $attachmentTitles;

    /**
     * @var string solution.
     */
    public $solution;

    /**
     * @var string solution title.
     */
    public $solutionTitle;

    /**
     * @var boolean save solution.
     */
    public $saveSolution;

    /**
     * @var array inputs.
     */
    public $inputs;

    /**
     * @var string poc.
     */
    public $poc;

    /**
     * @var string links.
     */
    public $links;

    /**
     * @var string table_result
     */
    public $tableResult;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("rating", "in", "range" => TargetCheck::getValidRatings()),
            array("port", "numerical", "integerOnly" => true, "min" => 0, "max" => 65536),
            array("protocol, solutionTitle, resultTitle", "length", "max" => 1000),
            array("overrideTarget", "checkOverrideTarget"),
            array("saveSolution, saveResult", "boolean"),
            array("inputs, result, solutions, solution, poc, links, attachmentTitles, tableResult, scripts, timeouts", "safe"),
		);
	}

    /**
     * Validate override target
     * @param $target
     * @return bool
     */
    public function checkOverrideTarget($attribute,$params) {
        $target  = trim($this->overrideTarget);
        $targets = explode("\n", $target);

        if (empty($targets)) {
            $this->addError('overrideTarget', Yii::t('app', 'Override target is not valid.'));
            return true;
        }

        foreach ($targets as $t) {
            if (!Utils::isIP($t) && !Utils::isIPRange($t) && !Utils::isDomain($t) && !Utils::isIPNetwork($t)) {
                $this->addError('overrideTarget', Yii::t('app', 'Override target is not valid.'));
                return false;
            }
        }

        $this->overrideTarget = $target;

        return true;
    }
}
