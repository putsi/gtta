<?php

/**
 * This is the model class for project vulnerabilities export form.
 */
class VulnsReportForm extends CFormModel
{
    /**
     * @var integer client id.
     */
    public $clientId;

    /**
     * @var integer project id.
     */
    public $projectId;

    /**
     * @var array target ids.
     */
    public $targetIds;

    /**
     * @var array ratings.
     */
    public $ratings;

    /**
     * @var array columns.
     */
    public $columns;

    /**
     * @var boolean include header.
     */
    public $header;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'ratings, columns', 'required' ),
            array( 'header', 'boolean' ),
            array( 'clientId, projectId, targetIds, ratings, columns', 'safe' ),
		);
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'clientId'  => Yii::t('app', 'Client'),
			'projectId' => Yii::t('app', 'Project'),
			'targetIds' => Yii::t('app', 'Targets'),
            'ratings'   => Yii::t('app', 'Ratings'),
			'columns'   => Yii::t('app', 'Columns'),
		);
	}
}