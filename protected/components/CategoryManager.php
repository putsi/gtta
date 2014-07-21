<?php

/**
 * Category manager class
 */
class CategoryManager {
    private $_languages = array();

    /**
     * Constructor
     */
    public function __construct() {
        foreach (Language::model()->findAll() as $language) {
            $this->_languages[$language->code] = $language->id;
        }
    }

    /**
     * Prepare category sharing
     * @param CheckCategory $category
     * @param bool $recursive
     * @throws Exception
     */
    public function prepareSharing(CheckCategory $category, $recursive=false) {
        if (!$category->external_id) {
            $category->status = CheckCategory::STATUS_SHARE;
            $category->save();
        }

        if ($recursive) {
            $cm = new ControlManager();

            foreach ($category->controls as $control) {
                $cm->prepareSharing($control, $recursive);
            }
        }
    }

    /**
     * Serialize and share category
     * @param CheckCategory $category
     * @throws Exception
     */
    public function share(CheckCategory $category) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        $data = array(
            "name" => $category->name,
            "l10n" => array(),
        );

        foreach ($category->l10n as $l10n) {
            $data["l10n"][] = array(
                "code" => $l10n->language->code,
                "name" => $l10n->name,
            );
        }

        try {
            $api = new CommunityApiClient($system->integration_key);
            $category->external_id = $api->shareCategory(array("category" => $data))->id;
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
        }

        $category->status = CheckCategory::STATUS_INSTALLED;
        $category->save();
    }

    /**
     * Create category
     * @param $category
     * @return CheckCategory
     * @throws Exception
     */
    public function create($category) {
        /** @var System $system */
        $system = System::model()->findByPk(1);
        $api = new CommunityApiClient($system->integration_key);
        $category = $api->getCategory($category)->category;

        $id = $category->id;
        $existingCategory = CheckCategory::model()->findByAttributes(array("external_id" => $id));

        if ($existingCategory) {
            return $existingCategory;
        }

        $c = new CheckCategory();
        $c->external_id = $category->id;
        $c->name = $category->name;
        $c->status = CheckCategory::STATUS_INSTALLED;
        $c->save();

        foreach ($category->l10n as $l10n) {
            $l = new CheckCategoryL10n();
            $l->language_id = $this->_languages[$l10n->code];
            $l->check_category_id = $c->id;
            $l->name = $l10n->name;
            $l->save();
        }

        return $c;
    }
}