<?php

/**
 * Class RegenerateJob
 */
class RegenerateJob extends BackgroundJob {
    /**
     * Job id
     */
    const ID_TEMPLATE = "gtta.sandbox.regenerate";

    /**
     * Perform
     */
    public function perform() {
        try {
            $vm = new VMManager();
            $vm->regenerate(false);
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}