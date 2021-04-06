<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class BasicCrudController extends Controller
{
    protected abstract function model(): string;
    protected abstract function rulesStore(): array;

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->model()::all();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, $this->rulesStore());
        $model = $this->model()::create($data);
        $model->refresh();
        return $model;
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $key = (new $model)->getRouteKeyName();
        return $this->model()::where($key, $id)->firstOrFail();
    }
}
