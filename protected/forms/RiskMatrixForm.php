<?php

/**
 * This is the model class for risk matrix form.
 */
class RiskMatrixForm extends CFormModel {
    /**
     * @var string font size.
     */
    public $fontSize;

    /**
     * @var string font family.
     */
    public $fontFamily;

    /**
     * @var float page margin.
     */
    public $pageMargin;

    /**
     * @var float cell padding.
     */
    public $cellPadding;

    /**
     * @var integer template id.
     */
    public $templateId;

    /**
     * @var array target ids.
     */
    public $targetIds;

	/**
     * @var array matrix.
     */
    public $matrix;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
            ["fontSize, fontFamily, pageMargin, cellPadding", "required"],
            ["fontSize", "numerical", "integerOnly" => true, "min" => Yii::app()->params["reports"]["minFontSize"], "max" => Yii::app()->params["reports"]["maxFontSize"]],
            ["cellPadding", "numerical", "min" => Yii::app()->params["reports"]["minCellPadding"], "max" => Yii::app()->params["reports"]["maxCellPadding"]],
            ["pageMargin", "numerical", "min" => Yii::app()->params["reports"]["minPageMargin"], "max" => Yii::app()->params["reports"]["maxPageMargin"]],
            ["fontFamily", "in", "range" => Yii::app()->params["reports"]["fonts"]],
            ["templateId, matrix", "required"],
            ["templateId", "numerical", "integerOnly" => true],
            ["targetIds, matrix", "safe"],
		];
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return [
            "fontSize" => Yii::t("app", "Font Size"),
            "fontFamily" => Yii::t("app", "Font Family"),
            "pageMargin" => Yii::t("app", "Page Margin"),
            "cellPadding" => Yii::t("app", "Cell Padding"),
			"targetIds" => Yii::t("app", "Targets"),
		];
	}
}