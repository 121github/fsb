<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareAcceptDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment on the accept environment';
    }

    public function run()
    {
        $commandList = array(
            'mv app/config/parameters.yml.accept app/config/parameters.yml',
            'mv web/app.php.accept web/app.php',
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}