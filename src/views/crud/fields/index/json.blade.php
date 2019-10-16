@if(json_decode($value))
    <span class="no-btn btn btn-xs btn-info">JSON</span>
@else
    <span class="no-btn btn btn-xs btn-danger">empty</span>
@endif
