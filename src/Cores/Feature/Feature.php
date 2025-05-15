<?php

namespace Sdk\Cores\Feature;

use Sdk\Cores\Core;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class Feature extends Core
{
    public function run()
    {
        DB::beginTransaction();;
        try {
            parent::run();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            $this->exception($exception);
        }
        return $this;
    }

    protected function exception(Exception $exception)
    {
        $this->error($exception->getMessage());
    }

    protected function error($message = 'error', $data = [])
    {
        $this->setResponses([
            'success' => false,
            'errorMessage' => $message,
            'data' => $data,
            'errorCode' => 0,
            'showType' => 2,
        ]);
        return $this;
    }

    protected function success($data = [], $message = '')
    {
        $this->setResponses([
            'success' => true,
            'errorMessage' => $message,
            'data' => $data,
            'errorCode' => 0,
            'showType' => 0,
        ]);
        return $this;
    }

    protected function paginate(Builder $model, $callback = null)
    {
        $perPage = $this->getPerPage();
        $page = $this->getPage();
        $model = $model->paginate($perPage, ['*'], 'current', $page);
        $items = Collection::make($model->items())->toArray();
        if ($callback) {
            $items = call_user_func($callback, $items);
        }
        $this->setResponses([
            'data' => $items,
            'total' => $model->total(),
            'pageSize' => $model->perPage(),
            'current' => $model->currentPage(),
            "success" => true,
            "message" => "success",
        ]);
        return $this;
    }

    /**
     * @return int
     */
    protected function getPage()
    {
        return $this->data->getItem('current');
    }

    /**
     * @return int
     */
    protected function getPerPage()
    {
        return $this->data->getItem('page_size');
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

    protected function setResponses($data = [])
    {
        $data = $this->camel($data);
        $this->data->setItem('responses', $data);
    }

    public function getResponses()
    {
        return (array) $this->data->getItem('responses');
    }
}
