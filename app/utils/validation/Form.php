<?php

namespace App\Utils\Validation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Form
{

    private $messages = [];
    private $_ERRORS = [];
    private $request;
    private $requests = [];
    private $currentIndex = null;
    private $requestsData = [];
    private $val;
    private $tokenIndex = "_token";
    private $token;
    private $has_token;
    private $session;
    private $isValidationAllowed = true;
    protected $groupSection = [];

    const UNIMPORTANT_FIELD = "don'tCare[FIELD]";
    const UNIMPORTANT_GROUP = "don'tCare[GROUP]";

    function __call($name, $arguments)
    {
        if($this->isValidationAllowed) {
            if(!empty($arguments)) {
                array_unshift($arguments, $this->requestsData[$this->currentIndex]);
            } else {
                array_push($arguments, $this->requestsData[$this->currentIndex]);
            }
            try {
                call_user_func_array([$this->val, $name], $arguments);
            } catch (\Exception $e) {
                if ($e->getMessage() == self::UNIMPORTANT_GROUP) {
                    array_pop($this->groupSection);
                } else if ($e->getMessage() !== self::UNIMPORTANT_FIELD) {
                    $this->_ERRORS[$this->currentIndex] = $this->prepareMessage(
                        $e->getMessage()
                    );
                }
                unset($this->requestsData[$this->currentIndex]);
                $this->escapeValidation();
            }
        }
        return $this;
    }

    function __construct($token = true)
    {
        $this->val = new Validation();
        $this->has_token = $token;

        $this->getRequests();

    }

    private function isPostRequest()
    {
        return $_SERVER['REQUEST_METHOD'] == "POST";
    }

    private function hasGetRequest()
    {
        return !empty($_SERVER['QUERY_STRING']);
    }

    private function isJsonResult($req)
    {
        return empty($req->request->all());
    }

    public function inputStreamReader()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    private function getRequests()
    {
        $request = Request::createFromGlobals();
        $this->has_token ? $this->getTokenFromElement($request) ? null
            : die('token element not found') : null;
        if ($this->isPostRequest()) {
            $this->hasGetRequest() ? $this->requests = array_merge($this->requests, $request->query->all()) : null;

            $this->isJsonResult($request) ? $this->requests = array_merge($this->requests, $this->inputStreamReader()) : $this->requests = array_merge($this->requests, $request->request->all());
        } else {
            $this->requests = array_merge(
                $this->requests,
                $request->request->all()
            );
        }
    }

    private function getTokenFromElement($request)
    {
        $this->session = new Session();
        if (session_status() == PHP_SESSION_NONE) {
            $this->session->start();
        }
        if (!empty($request->request->get($this->tokenIndex))) {
            $this->token = $request->request->get($this->tokenIndex);

            return true;
        }

        return false;
    }

    public function setTokenIndexName($name)
    {
        $this->tokenIndex = $name;
    }

    public function request($pattern, $value = null)
    {
        $this->isValidationAllowed = true;
        if (!is_null($value)) {
            $this->requests = array_merge($this->requests, [$pattern => $value]);
        }
        $pattern = $this->preparePattern($pattern);
        try {
            $this->currentIndex = $pattern;
            $this->requestsData[$pattern] = $this->mineValueFromPattern(
                $pattern
            );
        } catch (\Exception $e) {
            $this->escapeValidation();
        }

        return $this;
    }

    public function group($name, $func)
    {
        $currentIndex = $name;
        array_push($this->groupSection, $name);
        $func($this);

        end($this->groupSection);

        if ($currentIndex == current($this->groupSection))
            array_pop($this->groupSection);

        return $this;
    }

    private function mineValueFromPattern($pattern)
    {
        $names = explode('.', $pattern);
        $data = $this->requests;
        foreach ($names as $value) {
            if (isset($data[$value])) {
                $data = $data[$value];
            } else {
                throw new \Exception("index not found");
            }
        }

        return $data;
    }

    private function preparePattern($name)
    {
        $sections = $this->groupSection;
        $sections[] = $name;

        return join(".", $sections);

    }

    private function convertResultToAssocArray(&$result)
    {
        $newRes = [];
        foreach ($result as $key => $value) {
            $temp = $value;
            $pattern = explode('.', $key);
            for ($i = count($pattern) - 1; $i >= 0; $i--) {
                $temp = [$pattern[$i] => $temp];
            }
            $newRes = array_merge_recursive($newRes, $temp);
        }

        return $newRes;
    }

    private function &except($options, &$req)
    {

        foreach ($options as $value) {
            foreach ($req as $k => $v) {
                if (strpos($k, $value . '.') !== false) {
                    $req[str_replace($value . '.', '', $k)] = $v;
                    unset($req[$k]);
                }
            }
        }
        return $req;
    }

    public function fetch($assoc = true, $opts = [])
    {

        if ($assoc && !empty($opts)) {
            if (isset($opts['except'])) {
                return $this->convertResultToAssocArray($this->except($opts['except'], $this->requestsData));
            }
        } else {
            return $assoc ? $this->convertResultToAssocArray($this->requestsData) : $this->requestsData;
        }
        return $this->requestsData;
    }

    public function getErrors($assoc = true, $opts = [])
    {
        if ($assoc && !empty($opts)) {
            if (isset($opts['except'])) {
                return $this->convertResultToAssocArray($this->except($opts['except'], $this->_ERRORS));
            }
        } else {
            return $assoc ? $this->convertResultToAssocArray($this->_ERRORS) : $this->_ERRORS;
        }
    }

    private function escapeValidation()
    {
        $this->isValidationAllowed = false;
    }

    public function val($rule)
    {
        if ($this->isValidationAllowed) {
            try {
                if (strpos($rule, "|") !== false) {
                    $rules = explode("|", $rule);
                    foreach ($rules as $value) {
                        if (strpos($value, ":") !== false) {
                            $val = explode(":", $value);
                            $method = $val[0];
                            $param = $val[1];
                            $this->val->{$method}(
                                $this->requestsData[$this->currentIndex],
                                $param
                            );
                        } else {
                            $method = $value;
                            $this->val->{$method}(
                                $this->requestsData[$this->currentIndex]
                            );
                        }
                    }
                } else {
                    if (strpos($rule, ":") !== false) {
                        $val = explode(":", $rule);
                        $method = $val[0];
                        $param = $val[1];
                        $this->val->{$method}(
                            $this->requestsData[$this->currentIndex],
                            $param
                        );
                    } else {
                        $method = $rule;
                        $this->val->{$method}(
                            $this->requestsData[$this->currentIndex]
                        );
                    }
                }
            } catch (\Exception $e) {
                if ($e->getMessage() == self::UNIMPORTANT_GROUP) {
                    array_pop($this->groupSection);
                } else if ($e->getMessage() !== self::UNIMPORTANT_FIELD) {
                    $this->_ERRORS[$this->currentIndex] = $this->prepareMessage(
                        $e->getMessage()
                    );
                }
                unset($this->requestsData[$this->currentIndex]);
            }
        }

        return $this;
    }

    private function prepareMessage($string)
    {
        $type = explode(':', $string)[0];
        $body = explode(':', $string)[1];

        if (key_exists($this->currentIndex, $this->messages)) {
            if (key_exists($type, $this->messages[$this->currentIndex])) {
                return $this->messages[$this->currentIndex][$type];
            } else if (key_exists($type, $this->messages)) {
                return $this->messages[$type];
            }
        }
        if (key_exists($type, $this->messages)) {
            return $this->messages[$type];
        }

        return $this->currentIndex . ' ' . $body;
    }

    public function setCustomMessage(array $messages)
    {
        $this->messages = $messages;
    }

    public function customRule($name, $func)
    {
        $this->val->setCustomMethod($name, $func);
    }

    private function checkToken()
    {
        if ($this->has_token) {
            if ($this->session->get($this->tokenIndex) !== $this->token) {
                return false;
            }
        }

        return true;
    }

    public function isValid()
    {
        return !empty($this->_ERRORS) ? false : $this->checkToken();
    }

    public function submit()
    {
        if (!empty($this->_ERRORS)) {
            Throw new \Exception("fix the following errors !");
        } elseif (!$this->checkToken()) {
            Throw new \Exception("incorrect Token");
        }

        return true;
    }
}
