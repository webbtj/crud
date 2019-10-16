<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>{{$fieldDisplayName}}:</strong>

        <select name="{{$fieldColumnName}}" class="form-control">
            <option {{$value ? 'selected' : ''}} value="1">True</option>
            <option {{$value ? '' : 'selected'}} value="0">False</option>
        </select>
    </div>
</div>
