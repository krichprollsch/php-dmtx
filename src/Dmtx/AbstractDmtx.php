<?php

namespace Dmtx;

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractDmtx
{
    protected $options = array();
    protected $arguments = array();
    protected $messages = array();

    public function __construct(array $options = array())
    {
        $this->messages = array();

        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
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

    protected function getProcessBuilder($cmd, array $extras = array(), $input = null)
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

        if (!is_null($input)) {
            $builder->setInput(
                $input
            );
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
        $process = $this
            ->getProcessBuilder($cmd, $extras, $input)
            ->getProcess();

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
