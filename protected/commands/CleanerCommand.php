<?php

/**
 * File cleaner class.
 */
class CleanerCommand extends CConsoleCommand
{
    /**
     * Tmp
     */
    private function _tmp()
    {
    }

    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args)
    {
        // one instance check
        $fp = fopen(Yii::app()->params['cleaner']['lockFile'], 'w');
        
        if (flock($fp, LOCK_EX))
        {
            $this->tmp();
            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
