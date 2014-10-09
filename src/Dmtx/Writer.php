<?php

namespace Dmtx;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Process\ProcessBuilder;

class Writer
{
    private $messages;
    private $options;
    private $arguments = array(
        'encoding',
        'module',
        'symbol-size',
        'format',
        'resolution',
        'margin'
    );

    public function __construct(array $options = array())
    {
        $this->messages = array();

        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'encoding' => 'ascii',
            'module' => 5,
            'symbol-size' => 'square-auto',
            'format' => 'png',
            'message-separator' => ' ',
            'process-timeout' => 600
        ));

        $resolver->setOptional(array(
            'resolution',
            'margin'
        ));

        $resolver->setAllowedValues(array(
            'encoding' => array(
                'best',
                'fast',
                'ascii',
                'c40',
                'text',
                'x12',
                'edifact',
                '8base256'
            ),
            'format' => array(
                'png',
                'tif',
                'gif',
                'pdf'
            ),
            'symbol-size' => array(
                'square-auto',
                'rectangle-auto',
                '10x10',
                '24x24',
                '64x64'
            )
        ));

        $resolver->setAllowedTypes(array(
            'resolution' => 'integer',
            'module' => 'integer',
            'margin' => 'integer'
        ));
    }

    public function encode($message)
    {
        $this->messages[] = $message;
        return $this;
    }

    public function dump()
    {
        return $this->run();
    }

    private function getMessage()
    {
        return implode(
            $this->options['message-separator'],
            $this->messages
        );
    }

    private function run()
    {
        $builder = $this->getProcessBuilder();
        $builder->setInput(
            $this->getMessage()
        );

        $process = $builder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    private function getProcessBuilder()
    {
        $builder = new ProcessBuilder();
        $builder->add('dmtxwrite');
        $builder->setTimeout($this->options['process-timeout']);
        foreach ($this->arguments as $argument) {
            try {
                $builder->add(sprintf(
                    '--%s=%s',
                    $argument,
                    $this->getArgument($argument)
                ));
            } catch (\InvalidArgumentException $ex) {}
        }

        return $builder;
    }

    private function getArgument($argument)
    {
        if (!isset($this->options[$argument])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'No value defined into options for argument %s',
                    $argument
                )
            );
        }

        $value = $this->options[$argument];

        switch ($argument) {
            case 'encoding':
                return substr($value, 0, 1);
            case 'format':
                return strtoupper($value);
        }

        return $value;
    }
}
