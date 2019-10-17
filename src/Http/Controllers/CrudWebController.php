<?php

namespace Webbtj\Crud\Http\Controllers;

use Illuminate\Http\Request;

class CrudWebController extends CrudBaseController
{
    public function index()
    {
        $models = parent::index();
        return $this->view('crud::index', compact('models'));
    }

    public function create()
    {
        return $this->view('crud::create');
    }

    public function store(Request $request)
    {
        $model = parent::store($request);
        return redirect()->route($this->routeRoot . '.index')->with('message', 'Item created successfully.');
    }

    public function show($id)
    {
        $model = parent::show($id);
        return $this->view('crud::show', compact('model'));
    }

    public function edit($id)
    {
        $model = parent::edit($id);
        return $this->view('crud::edit', compact('model'));
    }

    public function update(Request $request, $id)
	{
        $model = parent::update($request, $id);
		return redirect()->route($this->routeRoot . '.index')->with('message', 'Item updated successfully.');
	}

    public function destroy($id)
	{
        $destroyed = parent::destroy($id);
		return redirect()->route($this->routeRoot . '.index')->with('message', 'Item deleted successfully.');
	}
}
