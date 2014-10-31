<?php

namespace Dmtx;

use Symfony\Component\Process\ProcessBuilder;

abstract class AbstractDmtx
{
    protected $options = array();
    protected $arguments = array();

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

    protected function getProcessBuilder($cmd, array $extras = array())
    {
        $builder = new ProcessBuilder();
        $builder->add($cmd);
        $builder->setTimeout($this->options['process-timeout']);

        foreach ($this->arguments as $argument) {
            try {
                $builder->add(
                    $this->getFormattedParameter(
                        $argument,
                        $this->getArgument($argument)
                    )
                );
            } catch (\InvalidArgumentException $ex) {
                //nothing here
            }
        }

        foreach ($extras as $key => $value) {
            try {
                $builder->add(
                    $this->getFormattedParameter(
                        $key,
                        $value
                    )
                );
            } catch (\InvalidArgumentException $ex) {
                //nothing here
            }
        }

        return $builder;
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

    protected function run($cmd, $input = null, array $extras = array())
    {
        $builder = $this->getProcessBuilder($cmd, $extras);

        if (!is_null($input)) {
            $builder->setInput(
                $input
            );
        }

        $process = $builder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
