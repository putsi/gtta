<?php

/**
 * This is the model class for target custom check edit form.
 */
class TargetCustomCheckEditForm extends CFormModel {
    /**
     * @var string name.
     */
    public $name;

    /**
     * @var string background info.
     */
    public $backgroundInfo;

    /**
     * @var string question.
     */
    public $question;

	/**
     * @var string result.
     */
    public $result;

    /**
     * @var string rating.
     */
    public $rating;

    /**
     * @var string solution.
     */
    public $solution;

    /**
     * @var string solution title.
     */
    public $solutionTitle;

    /**
     * @var boolean create check.
     */
    public $createCheck;

    /**
     * @var boolean control id.
     */
    public $controlId;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("rating", "in", "range" => TargetCheck::getValidRatings()),
            array("name, solutionTitle", "length", "max" => 1000),
            array("controlId", "numerical", "integerOnly" => true),
            array("createCheck", "boolean"),
            array("backgroundInfo, question, result, solution", "safe"),
		);
	}
}