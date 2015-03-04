<?php

namespace Task;

use Mage\Task\AbstractTask;

class PermissionsConsole extends AbstractTask
{
    public function getName()
    {
        return 'Fixing app/console file permissions';
    }

    public function run()
    {
        $command = 'chmod +x app/console';
        $result = $this->runCommandRemote($command);

        return $result;
    }
}