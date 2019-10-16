<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>{{$fieldDisplayName}}:</strong>

        <select name="{{$fieldColumnName}}" class="form-control">
            @foreach($field->getOptions() as $option)
                <option value="{{$option}}" {{$value == $option ? 'SELECTED' : ''}}>{{$option}}</option>
            @endforeach
        </select>

    </div>
</div>
