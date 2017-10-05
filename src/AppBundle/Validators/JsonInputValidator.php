<?php
namespace AppBundle\Validators;

class JsonInputValidator
{

    private $input;
    private $state = [];

    public function __construct($input = null)
    {

        $this->setInput($input);

    }

    public function validate()
    {
        if (empty($this->input)) {
            $this->state = [0 => "No Error"];
            return true;
        }

        $decoded = json_decode($this->input);
        $this->setState(json_last_error(), json_last_error_msg());
        return $this->isValid();
    }

    public function isValid()
    {
        return $this->getErrorCode() == JSON_ERROR_NONE;
    }

    public function getErrorString()
    {
        return current($this->state);
    }

    public function getErrorCode()
    {
        return key($this->state);
    }

    public function setInput($input)
    {
        $this->input = $input;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function setState($code, $message)
    {

        $this->state = [$code => $message];

    }

}
