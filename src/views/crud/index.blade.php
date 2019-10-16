@extends('crud::layout')
@section('header')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>All {{Str::plural($modelName)}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-success" href="{{ route($routeRoot . '.create') }}"> Create New {{$modelName}}</a>
        </div>
    </div>
</div>
@endsection

@section('content')
<table class="table table-bordered">
    <tr>
        @foreach($fields as $field)
            <th>{{ $field->getDisplayName() }}</th>
        @endforeach
        <th width="280px">Action</th>
    </tr>
    @foreach ($models as $model)
        <tr>
            @foreach($fields as $field)
                <td>
                    @php
                        $value = $model->{$field->getColumnName()};
                        $fieldColumnName = $field->getColumnName();
                        $fieldDisplayName = $field->getDisplayName();
                    @endphp

                    @include(field_view($modelName, $field, 'index'))
                </td>
            @endforeach
            <td>
                <form action="{{ route($routeRoot . '.destroy', $model->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route($routeRoot . '.show', $model->id) }}">Show</a>
                    <a class="btn btn-primary" href="{{ route($routeRoot . '.edit', $model->id) }}">Edit</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
