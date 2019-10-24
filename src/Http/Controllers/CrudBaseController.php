<?php

namespace Webbtj\Crud\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use DB;
use Doctrine\DBAL\Types\Type;

class CrudBaseController extends Controller
{
    use \Webbtj\Crud\Traits\CrudControllerTrait;

    public function index()
    {
        $models = $this->model->paginate();
        return $models;
    }

    public function create()
    {
        return;
    }

    public function store(Request $request)
    {
		$inputs = $this->validInputs($request, 'store');
        $model = new $this->model;
        foreach($inputs as $k => $v){
            $model->$k = $v;
        }
        $model->save();
        return $model;
    }

    public function show($id)
    {
        $model = $this->model->findOrFail($id);
        return $model;
    }

    public function edit($id)
    {
        $model = $this->model->findOrFail($id);
        return $model;
    }

    public function update(Request $request, $id)
	{
        $inputs = $this->validInputs($request, 'update');
		$model = $this->model->findOrFail($id);
        foreach($inputs as $k => $v){
            $model->$k = $v;
        }
        $model->save();
        return $model;
	}

    public function destroy($id)
	{
		return $this->model->destroy($id);
	}


}
