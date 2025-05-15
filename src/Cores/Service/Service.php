<?php

namespace Sdk\Cores\Service;

use Illuminate\Support\Str;
use Sdk\Cores\Core;
use Illuminate\Support\Facades\Http;

abstract class Service extends Core
{
    abstract protected function path();

    protected function host()
    {
        if (config('app.env') !== 'production') {
            return 'http://' . config('app.env') . '-apigateway.qcyyds.com/';
        }
        return 'http://apigateway.qcyyds.com/';
    }

    public function setBody($body = [])
    {
        $this->data->setItem('body', $body);
        return $this;
    }

    protected function execute()
    {
        $this->http();
    }

    protected function http()
    {
        $url = trim($this->host(), '/') . '/' . trim($this->path(), '/');
        $body = (array) $this->data->getItem('body');
        $http = Http::post($url, $body);
        $this->data->setItem('httpResponses', $http->json());
    }

    public function getHttpResponses($camel = true)
    {
        $data = (array) $this->data->getItem('httpResponses');
        if ($camel) {
            $data = $this->camel($data);
        }
        return $data;
    }

    final protected function camel($array)
    {
        $results = [];
        foreach ($array as $key => $value) {
            $camelKey = Str::camel($key);
            if (is_array($value) && !empty($value)) {
                $results[$camelKey] = $this->camel($value);
            } else {
                $results[$camelKey] = $value;
            }
        }
        return $results;
    }
}
