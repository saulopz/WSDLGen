<?php

class Operation
{
    public $name;
    public $parmIn;
    public $parmOut;
    public $description;
    public $encodingStyle;

    public function __construct($operation, $parmIn, $parmOut, $encodingStyle, $description)
    {
        $this->name = $operation;
        $this->parmIn = $parmIn;
        $this->parmOut = $parmOut;
        $this->description = $description;
        $this->encodingStyle = $encodingStyle;
    }
}
