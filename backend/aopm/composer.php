<?php
use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class ComposerCommandLine
{
    public function update()
    { 
        $input = new ArrayInput(array('command' => 'update'));
        $application = new Application();
        $out = "php://output";
        if (php_sapi_name() === 'cli') $out = 'php://stdout';
        $output = new StreamOutput(fopen($out, 'w'));
        @$application->doRun($input, $output);
    }
}
