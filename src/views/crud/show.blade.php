@extends('crud::layout')
@section('header')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Show {{Str::plural($modelName)}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route($routeRoot . '.index') }}"> Back</a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    @foreach($fields as $field)
        @php
            $value = $model->{$field->getColumnName()};
            $fieldColumnName = $field->getColumnName();
            $fieldDisplayName = $field->getDisplayName();
        @endphp

        @include(field_view($modelName, $field, 'show'))

    @endforeach
</div>
@endsection
