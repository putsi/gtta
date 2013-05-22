<?php

/**
 * This is the model class for GT module check edit form.
 */
class GtModuleCheckEditForm extends LocalizedFormModel
{
    /**
     * @var string description.
     */
    public $description;

   /**
     * @var string target description.
     */
    public $targetDescription;

    /**
     * @var integer check id.
     */
    public $checkId;

    /**
     * @var integer sort order.
     */
    public $sortOrder;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('checkId, sortOrder', 'required'),
            array('checkId, sortOrder', 'numerical', 'integerOnly' => true, 'min' => 0),
            array('localizedItems, description, targetDescription', 'safe'),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'description' => Yii::t('app', 'Description'),
            'targetDescription' => Yii::t('app', 'Target Description'),
            'checkId' => Yii::t('app', 'Check'),
            'sortOrder' => Yii::t('app', 'Sort Order'),
		);
	}
}