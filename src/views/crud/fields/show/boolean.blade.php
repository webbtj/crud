<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>{{$fieldDisplayName}}:</strong>
        @if($value)
            <span class="no-btn btn btn-xs btn-info">True</span>
        @else
            <span class="no-btn btn btn-xs btn-danger">False</span>
        @endif
    </div>
</div>
