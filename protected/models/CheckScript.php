<?php

/**
 * This is the model class for table "check_scripts".
 *
 * The followings are the available columns in table "check_scripts":
 * @property integer $id
 * @property integer $check_id
 * @property integer $package_id
 * @property Package $package
 */
class CheckScript extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckScript the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return "check_scripts";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array("check_id, package_id", "required"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            "check" => array(self::BELONGS_TO, "Check", "check_id"),
            "package" => array(self::BELONGS_TO, "Package", "package_id"),
            "inputs" => array(self::HAS_MANY, "CheckInput", "check_script_id"),
		);
	}
}
