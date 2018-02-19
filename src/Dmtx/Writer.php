<?php

namespace Dmtx;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Writer extends AbstractDmtx
{
    protected $arguments = [
        'encoding',
        'module',
        'symbol-size',
        'format',
        'resolution',
        'margin',
    ];

    protected function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'encoding' => 'ascii',
            'module' => 5,
            'symbol-size' => 'square-auto',
            'format' => 'png',
            'message-separator' => ' ',
            'process-timeout' => 600,
            'command' => 'dmtxwrite',
        ]);

        $resolver->setDefined([
            'resolution',
            'margin',
        ]);

        $allowedValues = [
            'encoding' => [
                'best',
                'fast',
                'ascii',
                'c40',
                'text',
                'x12',
                'edifact',
                '8base256',
            ],
            'format' => [
                'png',
                'tif',
                'gif',
                'pdf',
            ],
            'symbol-size' => [
                'square-auto',
                'rectangle-auto',
                '10x10',
                '24x24',
                '64x64',
            ],
        ];
        foreach ($allowedValues as $option => $allowedValue) {
            $resolver->setAllowedValues($option, $allowedValue);
        }

        $allowedTypes = [
            'resolution' => 'integer',
            'module' => 'integer',
            'margin' => 'integer',
        ];
        foreach ($allowedTypes as $option => $allowedType) {
            $resolver->setAllowedTypes($option, $allowedTypes);
        }
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
