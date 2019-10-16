<?php

use Illuminate\Support\Str;

if(!function_exists('snake_to_title')){
    function snake_to_title(String $string){
        return Str::title(str_replace('_', ' ', $string));
    }
}

if(!function_exists('in_csv')){
    function in_csv($needle, $haystack){
        return in_array($needle, explode(',', $haystack));
    }
}

if(!function_exists('field_view')){
    function field_view($model, $field, $view){
        if($field->getReadOnly()){
            $view = 'show';
        }

        $customView = sprintf('%s.fields.%s.%s', strtolower($model), $view, $field->getColumnName());
        if(view()->exists($customView)){
            return $customView;
        }else{
            return sprintf('crud::fields.%s.%s', $view, $field->getType());
        }
    }
}
