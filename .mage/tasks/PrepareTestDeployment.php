<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareTestDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment on the test environment';
    }

    public function run()
    {
        $commandList = array(
            'mv app/config/parameters.yml.test app/config/parameters.yml',
            'mv web/app.php.test web/app.php',
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}