@extends('include.layout')

@section('style')
<style>    
.cImgPassport{
    max-width: 100%;
}

</style>
@endsection

@section('script')
{!! Html::script('js/ckeditor/ckeditor.js') !!}

{!! Html::script('js/manual_js/manual_click.js') !!}
<script>
    // Replace the <textarea id="address"> with a CKEditor
    // instance, using default configuration.
    // auto dissable success message.
    var settimmer = 0;
    $(function(){
        CKEDITOR.replace( 'address' );
        CKEDITOR.replace( 'note' );    

        //auto dissable success message
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);        
        
        /* The plugin will submit form and scroll to top*/
        $("#companySubmit").manualSubmit('frmCompany');

        /* The plugin will submit form and scroll to top*/
        $("#companyDelete").manualSubmit('frmCom');
                
        $("#companyRefresh").manualRefresh('frmCompany');
    });
    
</script>

@endsection

@section('content')

<?php 
/*
    =======================================
        CREATE BY HUNGNGUYEN
    =======================================
*/
?>


<div class="container-fluid time-table-no-margin">
    <div class="row">        
        <div class="col-md-8">
        <p class="pagetitle">Company detail page</p>
            <!-- begin .flash-message -->
            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('company-alert-' . $msg))
                <p class="alert alert-{!! $msg !!}">
                    {!! Session::get('company-alert-' . $msg) !!}<br>
                    The message will dissable with in <b id="show-time">5</b> seconds                            
                </p>
                @endif
                @endforeach
            </div> 
            <!-- end .flash-message -->
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id='frmCompany' action="{!! route('Company.store') !!}" method="POST" role="form" method="post">
                <table class="table table-striped table-bordered table-hover table-condensed">
                    {!! csrf_field() !!}
                    <tbody>
                        <tr>
                            <td class="col-md-3">Company name <em style='color:red'>(*)</em> </td>
                            <td class="col-md-9"><input type="text" id="companyName" name="companyName" value="{!! old('companyName') !!}" size="100"></td>
                        </tr>
                        <tr>
                            <td>Represent's Name</td>
                            <td><input type="text" id="contactName" name="contactName" value="{!! old('contactName') !!}" size="100"></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>                        
                                <textarea name="address" id="address" name="address" rows="10" cols="80">
                                </textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Tel</td><td><input type="text" id="tel" name="tel" value="{!! old('tel') !!}" size="100"></td>
                        </tr>
                        <tr>
                            <td>Fax</td><td><input type="text" id="fax" name="fax" value="{!! old('fax') !!}" size="100"></td>
                        </tr>
                        <tr>
                            <td>Contract rate</td>
                            <td>
                                <input type="radio" id="txt_specrate_no" value="0" name="contractRate"> <label for="txt_specrate_no">No</label> &nbsp;&nbsp;&nbsp;
                                <input type="radio" id="txt_specrate_yes" value="1" name="contractRate"> <label for="txt_specrate_yes">Yes</label>                              
                            </td>
                        </tr>
                        <tr>
                            <td>Branch</td>
                            <td>
                                @if(isset($areas))
                                    <select name="areaid">
                                        @foreach ( $areas as $area)
                                            <option value="{!! $area->id !!}" name="areaid">{!! $area->name !!}</option>
                                        @endforeach
                                    </select>
                                @endif                     
                            </td>
                        </tr>
                        <tr>
                            <td>Activation</td>
                            <td>
                                <input type="radio" name="activation" id="inputActivation_yes" value="1" checked="checked"> <label for="inputActivation_yes">Yes</label>
                                    &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="activation" id="inputActivation_no" value="0"> <label for="inputActivation_no">No</label>
                            </td>
                        </tr>
                        <tr>
                            <td>Special Note</td>
                            <td>
                                <textarea name="note" id="note" name="note" rows="10" cols="80">
                                </textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>    
        
        <div class="col-md-12 col-md-offset-2">
            </br>
            <button id='companySubmit' type="button" class="btn btn-primary">Create</button>            
            <button id='companyRefresh' type="button" class="btn btn-default">Refresh</button>
            <a href="{!! route('Company') !!}">Back To List</a>
        </div>
    </div>
</div>
@endsection