<?php
use Composer\Console\Application;
use Composer\Command\UpdateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class ComposerCommandLine
{
    public function update()
    { 
        $input = new ArrayInput(array('command' => 'update'));
        $application = new Application();
        $output = new StreamOutput(fopen('log', 'w', false));
        @$application->doRun($input, $output);
        $log = file_get_contents('log');
        unlink('log');
        return $log;
    }
}
