<?php

namespace Sdk\Cores;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

abstract class Core
{
    protected $data = [];

    public function __construct()
    {
        $this->data = new Data();
    }

    public static function init()
    {
        return new static();
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function run()
    {
        try {
            $this->request();
            $this->execute();
            $this->response();
        } catch (Exception $exception) {
            Log::error(get_class($this) . '错误', [
                'error' => $exception->getMessage(),
                'data' => $this->data->all(),
            ]);
            throw $exception;
        }
        return $this;
    }

    abstract protected function execute();

    public function setRequests($requests)
    {
        $this->data->setItems($requests);
        return $this;
    }

    public function setRequest($key, $value)
    {
        $this->data->setItem($key, $value);
        return $this;
    }

    public function getResponses()
    {
        return $this->data->all();
    }

    public function getResponse($key, $default = null)
    {
        return $this->data->getItem($key, $default);
    }

    /**
     * @param $rules
     * @param array $messages
     * @throws Exception
     */
    public function validate($rules, $messages = [])
    {
        if (empty($rules)) {
            return;
        }
        $rules = $this->snake($rules);
        if (!empty($messages)) {
            $messages = $this->snake($messages);
            $messages = Arr::dot($messages);
        }
        $validate = Validator::make($this->data->all(), $rules, $messages);
        if ($validate->fails()) {
            throw new Exception($validate->errors()->first());
        }
    }

    final protected function snake($array)
    {
        $results = [];
        foreach ($array as $key => $value) {
            $camelKey = Str::snake($key);
            if (is_array($value) && !empty($value)) {
                $results[$camelKey] = $this->snake($value);
            } else {
                $results[$camelKey] = $value;
            }
        }
        return $results;
    }

    protected function request()
    {

    }

    protected function response()
    {

    }
}
