<?php

namespace Sdk\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function getParams()
    {
        return $this->request->all();
    }

    protected function response($data = [])
    {
        throw new HttpResponseException(response()->json($data));
    }
}
