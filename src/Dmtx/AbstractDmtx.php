<?php

namespace Dmtx;

use Symfony\Component\Process\Process;
use Symfony\Component\OptionsResolver\OptionsResolver;


abstract class AbstractDmtx
{
    protected $options = [];
    protected $arguments = [];
    protected $messages = [];

    public function __construct(array $options = [])
    {
        $this->messages = [];

        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('command');
    }

    protected function getArgument($argument)
    {
        if (!isset($this->options[$argument])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'No value defined into options for argument %s',
                    $argument
                )
            );
        }

        return $this->options[$argument];
    }

    protected function getProcess($cmd, array $extras = [], $input = null)
    {
        $cmdArguments = [$cmd];

        foreach ($this->arguments as $argument) {
            try {
                $cmdArguments[] = $this->getFormattedParameter(
                    $argument,
                    $this->getArgument($argument)
                );
            } catch (\InvalidArgumentException $ex) {
                //nothing here
            }
        }

        foreach ($extras as $key => $value) {
            try {
                $cmdArguments[] = $this->getFormattedParameter($key, $value);
            } catch (\InvalidArgumentException $ex) {
                //nothing here
            }
        }

        $process = new Process($cmdArguments);
        $process->setTimeout($this->options['process-timeout']);

        if (!is_null($input)) {
            $process->setInput(
                $input
            );
        }

        return $process;
    }

    private function getFormattedParameter($key, $value)
    {
        if ($value === true) {

            return sprintf('--%s', $key);
        }

        if (!is_bool($value)) {
            
            return sprintf(
                '--%s=%s',
                $key,
                $value
            );
        }

        return null;
    }

    protected function run($cmd, $input = null, array $extras = [])
    {
        $process = $this->getProcess($cmd, $extras, $input);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    protected function getCmd()
    {
        return $this->options['command'];
    }
}
