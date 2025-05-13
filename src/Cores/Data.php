<?php

namespace App\Cores;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Data
{
    protected $data = [];

    public function getItem($key, $default = null)
    {
        return Arr::get($this->data, $this->snakeKey($key), $default);
    }

    /**
     * @param $key
     * @param null $message
     * @return mixed
     * @throws Exception
     */
    public function getItemRequired($key, $message = null)
    {
        $value = $this->getItem($key);
        if (!empty($value)) {
            return $value;
        }
        if (!$message) {
            $message = $key . '不能为空';
        }
        throw new Exception($message);
    }

    public function setItem($key, $value)
    {
        if (is_array($value)) {
            $value = Arr::dot($value, $key . '.');
            foreach ($value as $k => $v) {
                $this->setItem($k, $v);
            }
            return;
        }
        Arr::set($this->data, $this->snakeKey($key), $value);
    }

    public function addItem($key, $value)
    {
        Arr::add($this->data, $this->snakeKey($key), $value);
    }

    public function setItems(array $items)
    {
        $items = Arr::dot($items);
        foreach ($items as $key => $value) {
            $this->setItem($key, $value);
        }
    }

    public function hasItem($key)
    {
        return Arr::has($this->data, $this->snakeKey($key));
    }

    public function all()
    {
        return $this->data;
    }

    final private function snakeKey($key)
    {
        $keys = array_map(function ($item) {
            return Str::snake($item);
        }, explode('.', $key));
        return implode('.', $keys);
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
}
