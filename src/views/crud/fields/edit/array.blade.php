<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>{{$fieldDisplayName}}:</strong>

        @foreach($field->getOptions() as $option)
            <div>
                <input id="{{$fieldColumnName}}-{{$option}}" type="checkbox" name="{{$fieldColumnName}}[]" value="{{$option}}" {{in_csv($option, $value) ? 'checked' : ''}} />
                <label for="{{$fieldColumnName}}-{{$option}}">{{$option}}</label>
            </div>
        @endforeach

    </div>
</div>
