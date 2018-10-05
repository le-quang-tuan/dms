<?php
/*
 ***********************************
  Form Macros
  @author: Lê Trọng Lợi - 2016/10/25
  @version: 2.0
  @requires: 
    bootstrap - ver 3.3.5
    angularjs - ver 1.5.8
    angular-messages
    angular-password
    ng-file-upload (nếu dùng uploadImgField)

  @functions:
    textField
    hiddenField
    numberField
    passwordField
    emailField
    textareaField
    selectField
    multiSelectField
    selectTags
    dateField
    dateRangeField
    dateTimeField
    dateTimeRangeField
    checkboxField
    radioField
    uploadImgField
    inputWithSelectField
    
    submitField

    btnActionEditRecord
    btnActionDelRecord

  @use: Để bắt được validate của angular-messages, 
  phải để Fiels vào trong thẻ <form name="form">...</form>
  *name bắt buộc phải là "form"
    
 ***********************************
*/

Form::macro('errorField', function() 
{
    return <<<HTML
        <div class='errors'></div>
HTML;
});

Form::macro('successField', function() 
{
    return <<<HTML
        <div class='success'></div>
HTML;
});

Form::macro('textField', function($name, $label, $value=null, $attributes=array())
{
    if($value == null) {
        // Get old form input type field (Does not work on textarea, select)
        $value = Form::getValueAttribute($name);
    }
    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}='{$value}'";
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::text($name, $value, $attributes);

    return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);

//     return <<<HTML
//         <div class="form-group {$errorClass}" ng-class="{$ngClass}">
//             <label class="control-label" for="{$id}">{$label}</label>
//             {$input}
//             {$errorMessage}
//             {$ngMessages['messages']}
//         </div>
// HTML;
});

Form::macro('hiddenField', function($name, $value=null, $attributes=array())
{
    if($value == null) {
        // Get old form input type field (Does not work on textarea, select)
        $value = Form::getValueAttribute($name);
    }
    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}='{$value}'";

    $input = Form::hidden($name, $value, $attributes);

    return $input;
});

// {!! Form::numberField('interpreter.number_male', null, null, ['validate'=>'required', 'title'=>'Nam', 'value'=>'0', 'group-addon-right'=>'Nam']) !!}
Form::macro('numberField', function($name, $label=null, $value=null, $attributes=array())
{
    if($value === null) {
        // Get old form input type field (Does not work on textarea, select)
        $value = Form::getValueAttribute($name);
        if (is_null($value)) {
            $value = 0;
        }
    }
    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}={$value}";
    isset($attributes['validate']) ? ($attributes['validate'] .= '|number') : ($attributes['validate'] = 'number');

    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::input('number', $name, $value, $attributes);

    return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);
});

Form::macro('passwordField', function($name, $label, $attributes=array())
{
    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}=''";
    $attributes['autocomplete'] = 'off';
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::password($name, $attributes);

    return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);
});

Form::macro('emailField', function($name, $label, $value=null, $attributes=array())
{
    if($value == null) {
        // Get old form input type field (Does not work on textarea, select)
        $value = Form::getValueAttribute($name);
    }
    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}='{$value}'";
    isset($attributes['validate']) ? ($attributes['validate'] .= '|email') : ($attributes['validate'] = 'email');
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::email($name, $value, $attributes);

    return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);
});

// {!! Form::textField('user.first_name', 'Họ *', null, ['validate'=>'required']) !!}
Form::macro('textareaField', function($name, $label, $value=null, $attributes=array())
{
    if($value == null) {
        // Get old form input type field (Does not work on textarea, select)
        $value = Form::getValueAttribute($name);
    }
    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}='{$value}'";
    $attributes['size'] = isset($attributes['size']) ? $attributes['size'] : '100%x5';
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::textarea($name, $value, $attributes);

    return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);
});

// {!! Form::selectField('project.service', 'Ngôn ngữ', [], null, ['ng-options'=>'item as item.name for item in services']) !!}
Form::macro('selectField', function($name, $label, $options, $value=null, $attributes=array())
{
    if (is_null($options)) {
        $options = array();
    }
    $opts = array([0, ""]);
    array_push($opts, $options);

    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control dropdown-select' : 'form-control dropdown-select';
    $attributes['ng-model'] = $name;
    if (is_int($value)) {
        $attributes['ng-init'] = "{$name}={$value}";
    } else {
        $attributes['ng-init'] = "{$name}='{$value}'";
    }
    
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::select($name, $options, $value, $attributes);
    return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);
});

Form::macro('multiSelectField', function($name, $label, $options, $value=array(), $attributes=array())
{
    $id = 'input'.ucfirst(str_replace('.', '_', $name));
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control multiselect' : 'form-control multiselect';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}={$value}";
    $attributes['multiple'] = 'multiple';
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::select($name, $options, $value, $attributes);

    return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);

    /* Thêm đoạn javascript sau để chuyển thành dạng selectTags
     * Yêu cầu: 
     *  + https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js
    <script type="text/javascript">
        var placeholder_tag = {!! json_encode($selected_tags) !!};
        $.each(placeholder_tag, function(key, value){
            $("#tags").find('option[value="' + value + '"]').prop("selected", true);
        });

        $("#tags").select2({
            tags: true
        });
    </script>
    */
});

Form::macro('selectTags', function($name, $label, $value=null, $attributes=array())
{
    // Above
});

Form::macro('fileField', function($name, $label, $attributes=array())
{
    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}='{$value}'";
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::file($name, $attributes);
    return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);
});

// {!! Form::checkboxField('is_register', 'label') !!}
Form::macro('checkboxField', function($name, $label, $checked=false, $value=null, $attributes=array())
{
    // Trường hợp xác định checkbox trong list được check
    $typeofChecked = gettype($checked);
    if ($typeofChecked == 'object' || $typeofChecked == 'array') {
        $checked_list = $checked;
        $checked = false;
        foreach ($checked_list as $item_id) {
            if($value == $item_id) {
                $checked = true;
                break;
            }
        }
    }

    if($typeofChecked == 'string') {
        $checked = (strtolower($checked) == 'true' ? true : false);
    }

    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    // $attributes['class'] = '';
    $attributes['ng-model'] = $name;
    $checked_string = $checked ? 'true' : 'false';
    $attributes['ng-init'] = "{$name}={$checked_string}";
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::checkbox($name, $value, $checked, $attributes);
    // return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);

    return <<<HTML
    <div class="form-group {$errorClass} checkbox" ng-class="{$ngClass}">
        <label class="control-label" for="{$id}">
            {$input}
            {$label}
        </label>
        {$errorMessage}
        {$ngMessages['messages']}
    </div>
HTML;
});

Form::macro('radioField', function($name, $label, $checked=false, $attributes=array())
{
    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    // $attributes['class'] = '';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}='{$checked}'";
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $input = Form::radio($name, $label, $checked, $attributes);
    // return getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);
    return <<<HTML
    <div class="form-group {$errorClass} checkbox" ng-class="{$ngClass}">
        <label class="control-label" for="{$id}">
            {$input}
            {$label}
        </label>
        {$errorMessage}
        {$ngMessages['messages']}
    </div>
HTML;
});

Form::macro('uploadFileField', function($name, $label=null, $attributes=array(), $uploadFunction="upload")
{

    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = 'btn btn-default';
    $attributes['ng-model'] = $name;
    $attributes['name'] = $name;
    // $attributes['required'] = true; bỏ, vì sẽ dính đến validate toàn form mà nó thuộc
    $attributes['ngf-select'] = true;
    $attributes['ngf-pattern'] = "'image/*'";
    $attributes['ngf-accept'] = "'image/*'";
    // $attributes['ngf-max-size'] = "20MB";
    // $attributes['ngf-min-height'] = "100";
    // $attributes['ngf-resize'] = "{width: 200, height: 200}";
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $browse = Form::button("Browse...", $attributes);
    $labelHTML = isset($label) ? "<label class='control-label' for='{$id}'>{$label}</label>" : null;

    return <<<HTML
        <div class="form-group {$errorClass}" ng-class="{$ngClass}">
            {$labelHTML}
            <div class="input-group image-preview">
                <input type="text" class="form-control image-preview-filename" disabled="disabled"> <!-- don't give a name === doesn't send on POST/GET -->
                <span class="input-group-btn">
                    <!-- image-preview-clear button -->
                    <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                        <span class="glyphicon glyphicon-remove"></span> Clear
                    </button>
                    <!-- image-preview-input -->
                    <div class="btn btn-default image-preview-input">
                        <span class="glyphicon glyphicon-folder-open"></span>
                        <span class="image-preview-input-title">参照</span>
                        <input type="file" accept="*" name="input-file-preview"/> <!-- rename it -->
                    </div>
                </span>
            </div><!-- /input-group image-preview [TO HERE]--> 
            
            {$errorMessage}
            {$ngMessages['messages']}
        </div>
HTML;
});

Form::macro('uploadImgField', function($name, $label, $srcImage=null, $attributes=array(), $uploadFunction="upload")
{
    if ($srcImage == null) {
        $srcImage = asset('img/avatar.png');
    }

    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = 'btn btn-default';
    $attributes['ng-model'] = $name;
    $attributes['name'] = $name;
    // $attributes['required'] = true; bỏ, vì sẽ dính đến validate toàn form mà nó thuộc
    $attributes['ngf-select'] = true;
    $attributes['ngf-pattern'] = "'image/*'";
    $attributes['ngf-accept'] = "'image/*'";
    // $attributes['ngf-max-size'] = "20MB";
    // $attributes['ngf-min-height'] = "100";
    // $attributes['ngf-resize'] = "{width: 200, height: 200}";
    
    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    $browse = Form::button("Browse...", $attributes);
    $labelHTML = isset($label) ? "<label class='control-label' for='{$id}'>{$label}</label>" : null;

    return <<<HTML
        <div class="form-group {$errorClass}" ng-class="{$ngClass}">
            {$labelHTML}
            <div>
                <img ngf-src="{$name} || '{$srcImage}'" class="img-responsive img-thumbnail" style="width:100%;"/>
            </div>
            <div class="btn-group btn-group-justified" role="group">
                <div class="btn-group" role="group">
                    {$browse}
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" ng-click="{$uploadFunction}({$name})" ng-disabled="{$name}==null" ng-init="{$uploadFunction}.processing=false">
                        <i ng-show="!{$uploadFunction}.processing" class="glyphicon glyphicon-upload"></i> 
                        <i ng-show="{$uploadFunction}.processing" class="fa fa-refresh fa-spin"></i> 
                        Upload
                    </button>
                </div>
            </div>
            {$errorMessage}
            {$ngMessages['messages']}
        </div>
HTML;
});

Form::macro('dateField', function($name, $label, $value, $attributes=array() )
{
    if($value==null) {
        // Get old form input type field (Does not work on textarea, select)
        $value = Form::getValueAttribute($name);
    }

    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}='{$value}'";
    $attributes['format'] = isset($attributes['format']) ? $attributes['format'] : 'yyyy/mm/dd';;
    $attributes['placeHolder'] = getDatePlaceholder($attributes['format']);
    

    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);
    $attributes     = $ngMessages['attributes'];

    $input = Form::text($name, $value, $attributes);

    $labelHTML = isset($label) ? "<label class='control-label'>{$label}</label>" : null;

    return <<<HTML
        <div class="form-group dateField {$errorClass}" ng-class="{$ngClass}">
            {$labelHTML}
            <div class="input-group">
                {$input}
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-calendar"></i>
                </div>
            </div>
            {$errorMessage}
            {$ngMessages['messages']}
        </div>
HTML;
    /* Thêm đoạn javascript sau:
     * Yêu cầu: 
     *  + bootstrap-daterangepicker/moment.min.js
     *  + bootstrap-datepicker/bootstrap-datepicker.min.js
     *  + inputmask/jquery.inputmask.bundle.min.js
     *
    <script type="text/javascript">
        // TODO: Coi lại format inputmask, datepicker và placeholder đã đúng format chưa.
        $(".dateField input").inputmask("mm/dd/yyyy", { 
            placeholder: "__/__/____", 
            clearIncomplete: true
        });
        $('.dateField input').datepicker({
            format: "mm/dd/yyyy",
            autoclose: true,
            todayHighlight: true
        })
        .on('changeDate', function(e) {
            var date = e.date;
            // TODO: Coi lại xem đã phù hợp với format chưa.
            var value = (date.getMonth() + 1) + "/"+ date.getDate() +"/" + date.getFullYear();

            // Gán giá trị vào $scope của AngularJS (nếu có)
            // $scope.date = value;
        });
        
        // Mặc định giá trị là ngày hiện tại
        // $('.dateField input').datepicker('setDate', new Date());
        // $('.dateField input').datepicker('update');
    </script>
    */
});

Form::macro('dateRangeField', function($names=array(), $label, $values=array(), $attributes=array('format'=>'yyyy/mm/dd') )
{
    if(empty($values)) {
        // Get old form input type field (Does not work on textarea, select)
        $values = array();
        array_push($values, Form::getValueAttribute($names[0]));
        array_push($values, Form::getValueAttribute($names[1]));
    }

    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['placeHolder'] = getDatePlaceholder($attributes['format']);
    
    $id_StartDate = 'input'.ucfirst($names[0]);
    $attr_StartDate = $attributes;
    $attr_StartDate['id'] = $id_StartDate;
    $attr_StartDate['ng-model'] = $names[0];

    $errorClass_StartDate     = errorClass($names[0]);
    $errorMessage_StartDate   = errorMessage($names[0]);
    $ngMessages_StartDate     = ngMessages($label, $names[0], $attr_StartDate);
    $ngClass_StartDate        = ngClass($names[0]);
    $attr_StartDate           = $ngMessages_StartDate['attributes'];


    $id_EndDate = 'input'.ucfirst($names[1]);
    $attr_EndDate = $attributes;
    $attr_EndDate['id'] = $id_EndDate;
    $attr_EndDate['ng-model'] = $names[1];
    
    $errorClass_EndDate     = errorClass($names[1]);
    $errorMessage_EndDate   = errorMessage($names[1]);
    $ngMessages_EndDate     = ngMessages($label, $names[1], $attr_EndDate);
    $ngClass_EndDate        = ngClass($names[1]);
    $attr_EndDate           = $ngMessages_EndDate['attributes'];
    

    $input_StartDate = Form::text($names[0], $values[0], $attr_StartDate);
    $input_EndDate = Form::text($names[1], $values[1], $attr_EndDate);

    return <<<HTML
        <div class="form-group dateRangeField">
            <label class="control-label">{$label}</label>
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-2">Từ</div>
                    <div class="col-md-10">
                        <div class="input-group">
                            {$input_StartDate}
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="calendar" data="{$names[0]}"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-2">đến</div>
                    <div class="col-md-10">
                        <div class="input-group">
                            {$input_EndDate}
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="calendar" data="{$names[1]}"></div>
                    </div>
                </div>
            </div>
        </div>
HTML;
    /* Thêm đoạn javascript sau:
     * Yêu cầu: 
     *  + bootstrap-daterangepicker/moment.min.js
     *  + bootstrap-datepicker/bootstrap-datepicker.min.js
     *  + inputmask/jquery.inputmask.bundle.min.js
     * Lưu ý: Có thể chuyển thành dạng Inline bằng cách thay ".calendar" bởi "input".

    <script type="text/javascript">
        // TODO: Coi lại format inputmask, datepicker và placeholder đã đúng format chưa.
        $(".dateRangeField input").inputmask("mm/dd/yyyy", { 
            placeholder: "__/__/____", 
            clearIncomplete: true
        });
        $('.dateRangeField .calendar').datepicker({
            format: "mm/dd/yyyy",
            maxViewMode: 1,
            orientation: "bottom left",
            startDate: "0d",
            todayHighlight: true
        })
        .on('changeDate', function(e) {
            // TODO: Coi lại xem đã phù hợp với format chưa.
            var date = e.date;
            var value = (date.getMonth() + 1) + "/"+ date.getDate() +"/" + date.getFullYear();

            var input = $(this).attr('data');
            $(".dateRangeField input[name='"+ input +"']").val(value);

            // Gán giá trị vào $scope của AngularJS
            // if (input == "project.start_date") {
            //     $scope.project.start_date = value;
            // } else if (input == "project.end_date") {
            //     $scope.project.end_date = value;
            // }
        });
        
        // Mặc định giá trị là ngày hiện tại
        // $('.dateRangeField .calendar').datepicker('setDate', new Date());
        // $('.dateRangeField .calendar').datepicker('update');
    </script>
    */
});

Form::macro('dateTimeField', function($name, $label, $value, $attributes=array('format'=>'yyyy/mm/dd hh:mm xm') )
{
    if($value==null) {
        // Get old form input type field (Does not work on textarea, select)
        $value = Form::getValueAttribute($name);
    }

    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}='{$value}'";
    $attributes['placeHolder'] = getDatePlaceholder($attributes['format']);

    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);
    $attributes     = $ngMessages['attributes'];

    $input = Form::text($name, $value, $attributes);

    return <<<HTML
        <div class="form-group dateTimeField {$errorClass}" ng-class="{$ngClass}">
            <label class="control-label">{$label}</label>
            <div class="input-group">
                {$input}
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-calendar"></i>
                </div>
            </div>
            {$errorMessage}
            {$ngMessages['messages']}
        </div>
HTML;
    /* Thêm đoạn javascript sau:
     * Yêu cầu: 
     *  + bootstrap-daterangepicker/moment.min.js
     *  + bootstrap-datepicker/bootstrap-datepicker.min.js
     *  + inputmask/jquery.inputmask.bundle.min.js
     *
    <script type="text/javascript">
        // TODO: Coi lại format inputmask, datepicker và placeholder đã đúng format chưa.
        $(".dateTimeField input").inputmask("mm/dd/yyyy hh:mm xm", { 
            placeholder: "__/__/____ __:__ _m", 
            clearIncomplete: true
        });
        $('.dateTimeField input').datetimepicker({
            format: "MM/DD/YYYY hh:mm a",
        })
        .on('dp.change', function(e) {
            // Gán giá trị vào $scope của AngularJS
            // var value = $(this).val();
            // var input = $(this).attr('name');
            // if( input == 'date_time' ) {
            //  $scope.date_time = value;
            // }
        });
    </script>
    */
});

Form::macro('dateTimeRangeField', function($names=array(), $label, $values=array(), $attributes=array('format'=>'yyyy/mm/dd hh:mm xm') )
{
    if(empty($values)) {
        // Get old form input type field (Does not work on textarea, select)
        $values = array();
        array_push($values, Form::getValueAttribute($names[0]));
        array_push($values, Form::getValueAttribute($names[1]));
    }

    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['placeHolder'] = getDatePlaceholder($attributes['format']);
    
    $id_StartDate = 'input'.ucfirst($names[0]);
    $attr_StartDate = $attributes;
    $attr_StartDate['id'] = $id_StartDate;
    $attr_StartDate['ng-model'] = $names[0];

    $errorClass_StartDate     = errorClass($names[0]);
    $errorMessage_StartDate   = errorMessage($names[0]);
    $ngMessages_StartDate     = ngMessages($label, $names[0], $attr_StartDate);
    $ngClass_StartDate        = ngClass($names[0]);
    $attr_StartDate           = $ngMessages_StartDate['attributes'];


    $id_EndDate = 'input'.ucfirst($names[1]);
    $attr_EndDate = $attributes;
    $attr_EndDate['id'] = $id_EndDate;
    $attr_EndDate['ng-model'] = $names[1];
    
    $errorClass_EndDate     = errorClass($names[1]);
    $errorMessage_EndDate   = errorMessage($names[1]);
    $ngMessages_EndDate     = ngMessages($label, $names[1], $attr_EndDate);
    $ngClass_EndDate        = ngClass($names[1]);
    $attr_EndDate           = $ngMessages_EndDate['attributes'];
    

    $input_StartDate = Form::text($names[0], $values[0], $attr_StartDate);
    $input_EndDate = Form::text($names[1], $values[1], $attr_EndDate);

    return <<<HTML
        <div class="form-group dateTimeRangeField">
            <label class="control-label">{$label}</label>
            <div class="row">
                <div class="col-md-1">Từ</div>
                <div class="col-md-5">
                    <div class="input-group">
                        {$input_StartDate}
                        <div class="input-group-addon">
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">đến</div>
                <div class="col-md-5">
                    <div class="input-group">
                        {$input_EndDate}
                        <div class="input-group-addon">
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

HTML;
    /* Thêm đoạn javascript sau: (Có thể sửa lại format cho phù hợp)
     * Yêu cầu: 
     *  + bootstrap-daterangepicker/moment.min.js
     *  + bootstrap-datepicker/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js
     *  + inputmask/jquery.inputmask.bundle.min.js
     *
    <script type="text/javascript">
        // TODO: Coi lại format inputmask, datepicker và placeholder đã đúng format chưa.
        $(".dateTimeRangeField input").inputmask("mm/dd/yyyy hh:mm xm", { 
            placeholder: "__/__/____ __:__ _m", 
            clearIncomplete: true
        });
        $('.dateTimeRangeField input').datetimepicker({
            format: "MM/DD/YYYY hh:mm a",
        })
        .on('dp.change', function(e) {
            // Gán giá trị vào $scope của AngularJS
            var value = $(this).val();
            var index = $(this).attr('index');
            var name = $(this).attr('name');

            if (name == 'schedule.start_time') {
                $scope.scheduleList[index].start_time = value;
            } else if (name == 'schedule.end_time') {
                $scope.scheduleList[index].end_time = value;
            }
        });
    </script>
    */
});

Form::macro('inputWithSelectGroup', function($input_params, $select_params, $label=null, $attributes=array())
{
    // Struct:
    // $input_params = [
    //     'type'          => 'text|number|password|email',
    //     'name'          => null,
    //     'value'         => null
    // ];

    // $select_params = [
    //     'name'          => null,
    //     'value'         => null,
    //     'options'       => null
    //     'position'      => 'right'
    // ];


    // 1. XỬ LÝ SELECTBOX
    $name = isset($select_params['name']) ? $select_params['name'] : null;
    $value = isset($select_params['value']) ? $select_params['value'] : null;
    $options = isset($select_params['options']) ? $select_params['options'] : array();

    if($value == null) {
        // Get old form input type field (Does not work on textarea, select)
        $value = Form::getValueAttribute($name);
    }

    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}='{$value}'";

    $select = Form::select($name, $options, $value, $attributes);


    // 2. XỬ LÝ INPUT (INPUT là đối tượng chính để focus khi nhấp vào label và truyền attributes để validate)
    $name = isset($input_params['name']) ? $input_params['name'] : null;
    $value = isset($input_params['value']) ? $input_params['value'] : null;
    
    if($value === null) {
        // Get old form input type field (Does not work on textarea, select)
        $value = Form::getValueAttribute($name);
    }

    $id = 'input'.ucfirst($name);
    $attributes['id'] = $id;
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control' : 'form-control';
    $attributes['ng-model'] = $name;
    $attributes['ng-init'] = "{$name}={$value}";

    switch ($input_params['type']) {
        case 'text':
            break;

        case 'number':
            isset($attributes['validate']) ? ($attributes['validate'] .= '|number') : ($attributes['validate'] = 'number');
            break;

        case 'email':
            isset($attributes['validate']) ? ($attributes['validate'] .= '|email') : ($attributes['validate'] = 'email');
            break;

        case 'password':
            $attributes['autocomplete'] = 'off';
            break;
        
        default:
            break;
    }

    $errorClass     = errorClass($name);
    $errorMessage   = errorMessage($name);
    $ngMessages     = ngMessages($label, $name, $attributes);
    $ngClass        = ngClass($name);

    $attributes     = $ngMessages['attributes'];

    switch ($input_params['type']) {
        case 'text':
            $input = Form::text($name, $value, $attributes);
            break;

        case 'number':
            isset($attributes['validate']) ? ($attributes['validate'] .= '|number') : ($attributes['validate'] = 'number');
            $input = Form::input('number', $name, $value, $attributes);
            break;

        case 'email':
            $input = Form::email($name, $value, $attributes);
            break;

        case 'password':
            $input = Form::password($name, $value, $attributes);
            break;
        
        default:
            $input = Form::text($name, $value, $attributes);
            break;
    }

    
    // 3. SET VỊ TRÍ SELECT NẰM BÊN TRÁI HOẶC BÊN PHẢI
    // Bắt trường hợp group-addon Để tránh 2 class input-group lồng nhau khi chạy hàm getHTML
    if (isset($attributes['group-addon-left']) || isset($attributes['group-addon-right'])) {
        $group = ':group';
    } else {
        $group = '<div class="input-group" style="width: 100%;">:group</div>';
    }
    // Bắt trường hợp group-addon-center
    if (isset($attributes['group-addon-center'])) {
        $addon_center = ':group-addon-center';
    } else {
        $addon_center = '<div class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></div>';
    }
    
    switch ($select_params['position']) {
        case 'left':

            $group = str_replace(':group', $select . $addon_center . $input, $group);
            break;

        case 'right':
            $group = str_replace(':group', $input . $addon_center . $select, $group);
            break;
        
        default:
            $group = str_replace(':group', $select . $addon_center . $input, $group);
            break;
    }

    return getHtml($id, $label, $group, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages);

});

Form::macro('submitField', function($value = 'Submit', $btn_style = 'btn btn-primary')
{
    return <<<HTML
    <button type="submit" class="{$btn_style} btnSubmit">
        <i class="fa fa-check done"></i>
        <i class="fa fa-spin fa-refresh loading"></i>
        {$value}
    </button>
HTML;
});

Form::macro('btnActionEditRecord', function($data_id, $name="Sửa", $class="btnEdit_item", $modal="modal_e_item")
{
    return '<a class="'. $class .'" data-toggle="modal" href="#'. $modal .'" data-id="'. $data_id .'">
            <i class="fa fa-edit done"></i> 
            <i class="fa fa-spin fa-refresh loading"></i>
            '. $name .'
        </a>';
});

Form::macro('btnActionDelRecord', function($data_id, $name="Xóa", $class="btnDel_item", $modal="modal_d_item")
{
    return '<a class="'. $class .'" href="javascript:;;" data-token="'. Session::getToken() .'" data-id="'. $data_id .'">
            <i class="fa fa-edit done"></i> 
            <i class="fa fa-spin fa-refresh loading"></i>
            '. $name .'
        </a>';
});

if (! function_exists ( 'ngMessages' )) {
    function ngMessages($label, $name, $attributes)
    {
        $messages = '';
        $out = ['messages'=>$messages, 'attributes'=>$attributes];

        if (!isset($attributes['validate'])) {
            return $out;
        }

        if ($label == null && isset($attributes['title'])) {
            $label = $attributes['title'];
        }

        // Trường hợp ng-required (tức thỏa điều kiện mới required)
        if (isset($attributes['ng-required']) && !preg_match("/required/", $attributes['validate'])) {
            $messages .= '<p ng-message="required">'.trans('validation.required').'</p>';
        }

        $validArray = explode('|', $attributes['validate']);
        unset($attributes['validate']);
        foreach ($validArray as $valid) {
            switch ($valid) {
                case (preg_match("/required/", $valid) ? true : false):
                    $messages .= '<p ng-message="required">'.trans('validation.required').'</p>';
                    $attributes['required'] = 'required';
                    break;

                case (preg_match("/email/", $valid) ? true : false):
                    $messages .= '<p ng-message="email">'.trans('validation.email').'</p>';
                    break;

                case (preg_match("/min/", $valid) ? true : false):
                    $value = explode(':', $valid)[1];
                    $message = str_replace(':min', $value, trans('validation.min.numeric'));
                    $messages .= '<p ng-message="min">'.$message.'</p>';
                    $attributes['ng-min'] = $value;
                    break;

                case (preg_match("/max/", $valid) ? true : false):
                    $value = explode(':', $valid)[1];
                    $message = str_replace(':max', $value, trans('validation.max.numeric'));
                    $messages .= '<p ng-message="max">'.$message.'</p>';
                    $attributes['ng-max'] = $value;
                    break;

                case (preg_match("/minlength/", $valid) ? true : false):
                    $length = explode(':', $valid)[1];
                    $message = str_replace(':min', $length, trans('validation.min.string'));
                    $messages .= '<p ng-message="minlength">'.$message.'</p>';
                    $attributes['ng-minlength'] = $length;
                    break;

                case (preg_match("/maxlength/", $valid) ? true : false):
                    $length = explode(':', $valid)[1];
                    $message = str_replace(':max', $length, trans('validation.max.string'));
                    $messages .= '<p ng-message="maxlength">'.$message.'</p>';
                    $attributes['ng-maxlength'] = $length;
                    break;

                case (preg_match("/same/", $valid) ? true : false):
                    $elementSame = explode(':', $valid)[1];
                    $elementSameName = explode(',', $elementSame)[0];
                    $elementSameLabel = explode(',', $elementSame)[1];
                    $message = str_replace(':other', '"'.trim(strtolower($elementSameLabel)).'"', trans('validation.same'));
                    $messages .= '<div ng-message="passwordMatch">'.$message.'</div>';
                    $attributes['match-password'] = $elementSameName;
                    break;

                case (preg_match("/number/", $valid) ? true : false):
                    $messages .= '<p ng-message="number">'.trans('validation.numeric').'</p>';
                    break;

                // TODO: Bắt thêm valid.
                
                default:
                    # code...
                    break;
            }
        }

        $label = str_replace("*", "", $label);
        $label = strtolower($label);
        $label = trim($label);
        $messages = str_replace(':attribute', '"'.$label.'"', $messages);

        $out['messages'] = '<div class="help-block" ng-messages="form[\''.$name.'\'].$error" ng-if="form[\''.$name.'\'].$touched">' . $messages . '</div>';
        $out['attributes'] = $attributes;

        return $out;
    }
}

if (! function_exists ( 'ngClass' )) {
    function ngClass($name)
    {
        return '{ \'has-error\': form[\''.$name.'\'].$touched && form[\''.$name.'\'].$invalid }';
    }
}

if (! function_exists ( 'errorMessage' )) {
    function errorMessage($name) {
        // if ($errors = Session::get('errors' )) {
        //     return $errors->first($name, '<p class="help-block">:message</p>');
        // }

        // return "<p class='help-block' id='{$name}'></p>";
    }
}


if (! function_exists ( 'errorClass' )) {
    function errorClass($name) {
        $error = null;
        if ($errors = Session::get ( 'errors' )) {
            $error = $errors->first ( $name ) ? 'has-error' : null;
        }
        return $error;
    }
}

if (! function_exists ( 'getDatePlaceholder' )) {
    function getDatePlaceholder($format) {
        switch ($format) {
            case 'dd/mm/yyyy':
            case 'mm/dd/yyyy':
                return '__/__/____';
            case 'yyyy/mm/dd':
                return '____/__/__';
            case 'dd/mm/yyyy hh:mm xm':
            case 'mm/dd/yyyy hh:mm xm':
                return '__/__/____ __:__ _m';
            case 'yyyy/mm/dd hh:mm xm':
                return '____/__/__ __:__ _m';
            default:
                return '____/__/__';
        }
    }
}

// Trường hợp cho các input có thể có "input-group", và không có "label"
function getHtml($id, $label, $input, $attributes, $errorClass, $ngClass, $errorMessage, $ngMessages)
{
    $out = '';
    if (isset($attributes['group-addon-left']) || isset($attributes['group-addon-right']) || isset($attributes['group-addon-center'])) {
        $input_group_left = '';
        $input_group_right = '';
        $input_group_center = '';

        if (isset($attributes['group-addon-left'])) {
            $input_group_left = '<div class="input-group-addon">'. $attributes['group-addon-left'] .'</div>';
        }

        if (isset($attributes['group-addon-right'])) {
            $input_group_right = '<div class="input-group-addon">'. $attributes['group-addon-right'] .'</div>';
        }

        if (isset($attributes['group-addon-center'])) {
            $input_group_center = '<div class="input-group-addon">'. $attributes['group-addon-center'] .'</div>';
            $input = str_replace(':group-addon-center', $input_group_center, $input);
        }

        if ($label == null) {
            $out = <<<HTML
            <!-- <div class="form-group {$errorClass}" ng-class="{$ngClass}"> -->
                <div class="input-group">
                    {$input_group_left}
                    {$input}
                    {$input_group_right}
                </div>
                <!-- {$errorMessage} -->
                <!-- {$ngMessages['messages']} -->
            <!-- </div> -->
HTML;
        } else {
            $out = <<<HTML
            <div class="form-group {$errorClass}" ng-class="{$ngClass}">
                <label class="control-label" for="{$id}">{$label}</label>
                <div class="input-group">
                    {$input_group_left}
                    {$input}
                    {$input_group_right}
                </div>
                {$errorMessage}
                {$ngMessages['messages']}
            </div>
HTML;
        }
    } else {
        if ($label == null) {
            $out = <<<HTML
            <!-- <div class="form-group {$errorClass}" ng-class="{$ngClass}"> -->
                {$input}
            <!--     {$errorMessage} -->
            <!--     {$ngMessages['messages']} -->
            <!-- </div> -->
HTML;
        } else {
            $out = <<<HTML
            <div class="form-group {$errorClass}" ng-class="{$ngClass}">
                <label class="control-label" for="{$id}">{$label}</label>
                {$input}
                {$errorMessage}
                {$ngMessages['messages']}
            </div>
HTML;
        }
            
    }

    return $out;
}