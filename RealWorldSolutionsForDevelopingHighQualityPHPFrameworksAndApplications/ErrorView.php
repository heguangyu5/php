<?php

require_once 'View.php';

class ErrorView extends View
{
    protected $errorMessage;

    public function __construct($viewScript, $errorMessage)
    {
        $this->errorMessage = $errorMessage;
        parent::__construct($viewScript);
    }
}
