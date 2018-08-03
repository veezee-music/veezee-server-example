<?php

namespace App\Utils\Validation;

class Validation
{
    private $funcs = [];
    const GROUP = "optionalGroup";

    function __call($name, $arguments)
    {
        if (key_exists($name, $this->funcs)) {
            if (!call_user_func_array($this->funcs[$name], $arguments)) {
                throw new \Exception($this->prepareMessage($name));
            }
        } else
            die ("$name doesn't exist ! ");
    }

    public function setCustomMethod($funcName, $func)
    {
        $this->funcs[$funcName] = $func;
    }

    private function prepareMessage($key)
    {
        return $key . ':' . ' wrong value ';
    }

    private function checkMin($data, $limit)
    {
        settype($limit, 'integer');
        if (trim($data) != "" && strlen($data) < $limit) {
            throw new \Exception($this->prepareMessage('min'));
        }
    }

    public function min($data, $limit)
    {
        if (is_array($data)) {
            foreach ($data as $value) {
                $this->checkMin($value, $limit);
            }
        } else {
            $this->checkMin($data, $limit);
        }
        return true;
    }

    private function checkRequire($data)
    {
        if (trim($data) === "" || empty($data) || !isset($data)) {
            throw new \Exception($this->prepareMessage('_require'));
        }
    }

    public function required($data)
    {
        if (is_array($data)) {
            foreach ($data as $value) {
                $this->checkRequire($value);
            }
        } else {
            $this->checkRequire($data);
        }
        return true;
    }

    private function checkMax($data, $limit)
    {
        settype($limit, 'integer');
        if (strlen($data) > $limit) {
            throw new \Exception($this->prepareMessage('max'));
        }
    }

    public function max($data, $limit)
    {
        if (is_array($data)) {
            foreach ($data as $value) {
                $this->checkMax($value, $limit);
            }
        } else {
            $this->checkMax($data, $limit);
        }
        return true;
    }

    private function checkEmail($data)
    {
        if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception($this->prepareMessage('email'));
        }
    }

    public function email($data)
    {
        if (is_array($data)) {
            foreach ($data as $value) {
                $this->checkEmail($value);
            }
        } else {
            $this->checkEmail($data);
        }
        return true;
    }

    private function checkNumeric($data)
    {
        if (!ctype_digit($data)) {
            throw new \Exception($this->prepareMessage('digit'));
        }
    }

    public function digit($data)
    {
        if (is_array($data)) {
            foreach ($data as $value) {
                $this->checkNumeric($value);
            }
        } else {
            $this->checkNumeric($data);
        }
        return true;
    }

    //add custom validation function here

    private function checkBool($data, $limit)
    {
        if ($limit == 'true') {
            if ($data != 'true')
                throw new \Exception($this->prepareMessage('bool'));
        } else {
            if ($data != 'false')
                throw new \Exception($this->prepareMessage('bool'));
        }
    }

    public function bool($data, $limit)
    {
        $this->checkBool($data, $limit);
    }

    private function checkAtLeastChar($data, $limit)
    {

    }

    public function atLeastChar($data, $limit)
    {
        $this->checkAtLeastChar($data, $limit);
    }

    private function checkAtLeastDigit($data, $limit)
    {
        settype($limit, 'integer');
        $onlyNumbers = filter_var($data, FILTER_SANITIZE_NUMBER_INT);

        if (strlen($onlyNumbers) < $limit)
            throw new \Exception($this->prepareMessage('atLeastDigit'));
    }

    public function atLeastDigit($data, $limit)
    {
        $this->checkAtLeastDigit($data, $limit);
    }


    private function checkMinInt($data, $limit)
    {
        settype($data, 'integer');
        settype($limit, 'integer');

        if ($data < $limit) {
            throw new \Exception($this->prepareMessage('minInt'));
        }
    }

    public function minInt($data, $limit)
    {
        $this->checkMinInt($data, $limit);
    }

    private function checkMaxInt($data, $limit)
    {
        settype($data, 'integer');
        settype($limit, 'integer');

        if ($data > $limit) {
            throw new \Exception($this->prepareMessage('maxInt'));
        }
    }

    public function maxInt($data, $limit)
    {
        $this->checkMaxInt($data, $limit);
    }


    private function checkUnChar($data, $limit)
    {

        if (strpos($data, $limit) !== false)
            throw new \Exception($this->prepareMessage('unChar'));

    }

    public function unChar($data, $limit)
    {
        if (strpos($limit, ',')) {
            $items = explode(',', $limit);
            foreach ($items as $item)
                $this->checkUnChar($data, $item);
        } else {
            $this->checkUnChar($data, $limit);
        }
    }

    private function checkUnimportant($data, array $limit, $type = null)
    {
        foreach ($limit as $value)
            if ($data == $value)
                if ($type == self::GROUP)
                    throw new \Exception("don'tCare[GROUP]");
                else
                    throw new \Exception("don'tCare[FIELD]");
    }

    public function optional($data, $limit)
    {
        $temp = [];
        if(!is_array($limit))
            $temp[] = $limit;
        else
            $temp = $limit;
        $this->checkUnimportant($data, $temp);
    }

    public function optionalGroup($data, $limit)
    {
        $temp = [];
        if(!is_array($limit))
            $temp[] = $limit;
        else
            $temp = $limit;
        $this->checkUnimportant($data, $temp,self::GROUP );
    }

}