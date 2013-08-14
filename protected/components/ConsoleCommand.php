<?php

/**
 * Base class for all application's commands.
 */
class ConsoleCommand extends CConsoleCommand
{
    /**
     * Init a command object.
     */
    public function init() {
        $system = System::model()->findByPk(1);

        if (!$system->timezone) {
            $system->timezone = "Europe/Zurich";
        }

        date_default_timezone_set($system->timezone);
    }

    /**
     * Renders a template.
     */
    protected function render($template, $data = array())
    {
        $path = Yii::getPathOfAlias($template).'.php';

        if (!file_exists($path))
            throw new Exception(Yii::t('app', 'Template {template} does not exist.', array(
                '{template}' => $path
            )));

        return $this->renderFile($path, $data, true);
    }
}
