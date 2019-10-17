<?php

namespace Webbtj\Crud\Http\Controllers;

use Illuminate\Http\Request;

class CrudApiController extends CrudBaseController
{
    public function index()
    {
        $models = parent::index();
        return $models;
    }

    public function store(Request $request)
    {
        $model = parent::store($request);
        return $model;
    }

    public function show($id)
    {
        $model = parent::show($id);
        return $model;
    }

    public function update(Request $request, $id)
	{
        $model = parent::update($request, $id);
        return $model;
	}

    public function destroy($id)
	{
        $destroyed = parent::destroy($id);
        return $destroyed;
	}
}
