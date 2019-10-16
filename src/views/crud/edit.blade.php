@extends('crud::layout')
@section('header')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Edit {{Str::plural($modelName)}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route($routeRoot . '.index') }}"> Back</a>
        </div>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route($routeRoot . '.update', $model->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        @foreach($fields as $field)
            @php
                $value = $model->{$field->getColumnName()};
                $fieldColumnName = $field->getColumnName();
                $fieldDisplayName = $field->getDisplayName();
            @endphp

            @include(field_view($modelName, $field, 'edit'))

        @endforeach
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
@endsection
