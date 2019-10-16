<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>{{$fieldDisplayName}}:</strong>

        <textarea
            class="json_text"
            name="{{$fieldColumnName}}"
            id="jsontext_{{$fieldColumnName}}"
            >{{$value}}</textarea>

        <div id="jsoneditor_{{$fieldColumnName}}" class="json_editor"></div>

        <script>
            (function(uid){
                container = document.getElementById("jsoneditor_" + uid)
                options = {
                    modes: ["code", "tree"],
                    onChangeText: function(jsonString){
                        document.getElementById("jsontext_" + uid).value = jsonString;
                    }
                }
                editor = new JSONEditor(this.container, this.options)
                initial = {!! $value ?? '{}' !!}
                editor.set(initial)
            })("{{$fieldColumnName}}");

        </script>
    </div>
</div>
