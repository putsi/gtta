<?php

/**
 * This is the model class for target check edit form.
 */
class TargetCheckEditForm extends CFormModel {
    const CUSTOM_SOLUTION_IDENTIFIER = "custom";

	/**
     * @var string result.
     */
    public $result;

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
     * @var string table_result
     */
    public $tableResult;

    /**
     * @var array fields
     */
    public $fields;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("rating", "in", "range" => TargetCheck::getValidRatings()),
            array("saveSolution, saveResult", "boolean"),
            array("fields", "checkFields"),
            array("inputs, result, solutions, solution, attachmentTitles, tableResult, scripts, timeouts", "safe"),
		);
	}

    /**
     * Validate fields
     * @param $attribute
     * @param $params
     */
    public function checkFields($attribute, $params) {
        foreach ($this->{$attribute} as $name => $value) {
            if ($name == GlobalCheckField::FIELD_OVERRIDE_TARGET) {
                $this->{$attribute}[$name] = trim($value);
            }

            if ($name == GlobalCheckField::FIELD_PORT) {
                $value = (int) $value;

                if ($value < 0 || $value > 65536) {
                    $this->addError("fields", "Port must be between 0 and 65536");

                    return false;
                }

                $this->{$attribute}[$name] = $value;
            }
        }

        return true;
    }
}
