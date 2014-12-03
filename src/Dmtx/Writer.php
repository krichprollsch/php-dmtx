<?php

namespace Dmtx;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Writer extends AbstractDmtx
{
    protected $arguments = array(
        'encoding',
        'module',
        'symbol-size',
        'format',
        'resolution',
        'margin'
    );

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'encoding' => 'ascii',
            'module' => 5,
            'symbol-size' => 'square-auto',
            'format' => 'png',
            'message-separator' => ' ',
            'process-timeout' => 600,
            'command' => 'dmtxwrite'
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
        if (is_array($message)) {
            $this->messages = $message;
        } else {
            $this->messages[] = $message;
        }

        return $this;
    }

    public function dump()
    {
        return $this->run(
            $this->getCmd(),
            $this->getMessage()
        );
    }

    public function saveAs($filename)
    {
        return $this->run(
            $this->getCmd(),
            $this->getMessage(),
            array(
                'output' => $filename
            )
        );
    }

    private function getMessage()
    {
        return implode(
            $this->options['message-separator'],
            $this->messages
        );
    }

    protected function getArgument($argument)
    {
        $value = parent::getArgument($argument);

        switch ($argument) {
            case 'encoding':
                return substr($value, 0, 1);
            case 'format':
                return strtoupper($value);
        }

        return $value;
    }
}
