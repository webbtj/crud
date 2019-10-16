<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>{{$fieldDisplayName}}:</strong>

        <div class="input-group date" id="datetimepicker-{{$fieldColumnName}}">
            <input
                type="text"
                name="{{$fieldColumnName}}"
                value="{{$value}}"
                class="form-control"
                data-target="#datetimepicker-{{$fieldColumnName}}"
                data-toggle="datetimepicker"
                placeholder="{{$fieldDisplayName}}">

            <span class="input-group-addon">
                <span class="glyphicon glyphicon-time"></span>
            </span>
        </div>

    </div>
    <script type="text/javascript">
        $(function () {
            $('#datetimepicker-{{$fieldColumnName}}').datetimepicker({
                format: 'HH:mm:ss',
            });
        });
    </script>
</div>
