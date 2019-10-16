<?php

namespace Webbtj\Crud;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
// use Webbtj\Crud\Util;
// use Webbtj\Crud\Field;
// use Webbtj\Crud\CrudControllerTrait;
use Illuminate\Support\Facades\Schema;
use DB;
use Doctrine\DBAL\Types\Type;

class CrudController extends Controller
{
    use CrudControllerTrait;

    public function index()
    {
        $models = $this->model->paginate();
        return $this->view('crud::index', compact('models'));
    }

    public function create()
    {
        return $this->view('crud::create');
    }

    public function store(Request $request)
    {
		$inputs = $this->validInputs($request, 'store');
        $model = new $this->model;
        foreach($inputs as $k => $v){
            $model->$k = $v;
        }
        $model->save();

        return redirect()->route($this->routeRoot . '.index')->with('message', 'Item created successfully.');
    }

    public function show($id)
    {
        $model = $this->model->findOrFail($id);
        return $this->view('crud::show', compact('model'));
    }

    public function edit($id)
    {
        $model = $this->model->findOrFail($id);
        return $this->view('crud::edit', compact('model'));
    }

    public function update(Request $request, $id)
	{
        $inputs = $this->validInputs($request, 'update');
		$model = $this->model->findOrFail($id);
        foreach($inputs as $k => $v){
            $model->$k = $v;
        }
        $model->save();

		return redirect()->route($this->routeRoot . '.index')->with('message', 'Item updated successfully.');
	}

    public function destroy($id)
	{
		$this->model->destroy($id);
		return redirect()->route($this->routeRoot . '.index')->with('message', 'Item deleted successfully.');
	}


}
