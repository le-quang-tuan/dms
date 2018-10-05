<?php

// Line Break </br>
HTML::macro('br', function($count = 1)
{
    $br = str_repeat("</br>", $count);
    return $br;
});

// Table
HTML::macro('table', function($current_settings = array(), $fields = array())
{
    $table = '<div class="table-responsive"><table class="table table-bordered table-hover table-striped">';
    $table .='<tr>';
        foreach ($fields as $field)
        {
            $table .= '<th>' . Str::title($field) . '</th>';
        }
    $table .= '</tr>';
    $table .= '<tr>';
        if ( $current_settings == null){
            foreach($fields as $value) {
                $table .= '<td>Value</td>';
            }
        } else {
            foreach($current_settings as $value) {
                $table .= '<td>' .$value. '</td>';
            }
        }
    $table .= '</tr>';
    $table .= '</table></div>';
    return $table;
});

// Delete Modal
HTML::macro('deleteModal', function($modalID, $resource, $resource_name, $resource_id)
{
    $form_open = Form::open(['route' => [''.$resource.'.destroy', $resource_id], 'method' => 'DELETE']);
    $form_submit = Form::submitField("Delete", "btn btn-danger");
    $form_close =  Form::close();
    return '<div class="modal fade" id="'.$modalID.'" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="deleteModal">Delete '.$resource_name.'</h4>
                    </div>
                    <div class="modal-body">
                        <h4>Are you sure you want to permanently delete this '.$resource_name.'?</h4>
                        <br>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                        '.$form_open.'
                        '.$form_submit.'
                        '.$form_close.'
                    </div>
                </div>
            </div>
        </div>';
});


// Delete Modal
HTML::macro('deleteAccountModal', function($modalID)
{
    $form_open = Form::open(['action' => 'AccountController@deleteAccount']);
    $form_submit = Form::submitField("Delete Account", "btn btn-danger pull-left");
    $form_close =  Form::close();
    return '<div class="modal fade" id="'.$modalID.'" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="deleteModal">Permanently Delete Account</h4>
                    </div>
                    <div class="modal-body">
                        <h4>Are you sure you want to delete your account and all associated data?</h4>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                        '.$form_open.'
                        '.$form_submit.'
                        '.$form_close.'
                        <br><br>
                    </div>
                </div>
            </div>
        </div>';
});


// video item
HTML::macro('listVideoTemplete', function($video) 
{
    $link = url('video/'.$video->id);
    $thumbnail_src = Helper::image_url('video', $video->id, $video->thumbnail);

    // UPDATE - 2016.06.03 - LTLoi
    // Add icon [new] với film có điều kiện: now - created_at > 7 (tức những film tạo trong vòng 7 ngày trước thì được xem là mới)
    $created_at = date_create($video->created_at);
    $today = date_create(date("Y-m-d H:i:s"));
    $diff = date_diff($created_at, $today)->format("%a");
    $icon_new = '';
    if ($diff <= 7) {
        $icon_new = '<div style="position: absolute; top: -1px; right: 9px;"><img class="img-responsive" src="'.asset('assets/img/new_ribbon.png').'" style="max-width: 50px;"/></div>';
    }
    // END UPDATE

    return '<div class="item grid-group-item col-xs-6 col-sm-4 col-lg-3 p-l-10 p-r-10">
                <div class="thumbnail">
                    '.$icon_new.'
                    <a href="'.$link.'">
                        <img class="group list-group-image video_preview" src="'.$thumbnail_src.'" data-id="'.$video->id.'" data-preview='.$video->thumbnails.' />
                    </a>
                    <div class="caption">
                        <p class="group inner list-group-item-heading">
                            <a href="'.$link.'" title="'.$video->name.'">'.$video->name.'</a>
                        </p>
                        <div class="row">
                            <div class="hidden-xs">
                                <div class="col-sm-6 p-r-5">
                                    <i class="fa fa-eye"></i> '.number_format($video->views).'
                                </div>
                                <div class="col-sm-6 p-l-5">
                                    <span class="pull-right"><i class="fa fa-heart"></i> '.number_format($video->likes).'</span>
                                </div>
                            </div>
                            <div class="visible-xs">
                                <div class="col-xs-12">
                                    <i class="fa fa-eye"></i> '.number_format($video->views).'
                                </div>
                                <div class="col-xs-12">
                                    <i class="fa fa-heart"></i> '.number_format($video->likes).'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    /*      
    <div class="item grid-group-item col-xs-4 col-lg-3">
        <div class="thumbnail">
            <a href="#">
                <img class="group list-group-image" src="http://placehold.it/400x250/000/fff" alt="" />
            </a>
            <div class="caption">
                <h5 class="group inner list-group-item-heading">
                    <a href="#">Product title</a></h5>
                <p class="group inner list-group-item-text">
                    Product description... Lorem ipsum dolor sit amet, consectetuer adipiscing elit</p>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <i class="fa fa-eye text-info"></i> 93.329.322</span>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <span class="pull-right"><i class="fa fa-heart text-danger"></i> 93%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    */
});


HTML::macro('listAlbumTemplete', function($album)
{
    $link = url('album/'.$album->id);
    $thumbnail_src = Helper::image_url('album', $album->id, $album->thumbnail);

    // UPDATE - 2016.06.03 - LTLoi
    // Add icon [new] với film có điều kiện: now - created_at > 7 (tức những film tạo trong vòng 7 ngày trước thì được xem là mới)
    $created_at = date_create($album->created_at);
    $today = date_create(date("Y-m-d H:i:s"));
    $diff = date_diff($created_at, $today)->format("%a");
    $icon_new = '';
    if ($diff <= 7) {
        $icon_new = '<div style="position: absolute; top: -1px; right: 12px;"><img class="img-responsive" src="'.asset('assets/img/new_ribbon.png').'" style="max-width: 50px;"/></div>';
    }
    // END UPDATE

    return '<div class="item grid-group-item col-xs-6 col-md-4">
                <div class="thumbnail">
                    <a href="'.$link.'">
                        <img class="group list-group-image" src="'.$thumbnail_src.'" />
                    </a>
                    '.$icon_new.'
                    <div class="caption">
                        <h5 class="group inner list-group-item-heading">
                            <a href="'.$link.'">'.$album->name.'</a>
                        </h5>
                        <div class="row">
                            <div class="hidden-xs">
                                <div class="col-sm-6 p-r-5">
                                    <i class="fa fa-eye"></i> '.number_format($album->views).'
                                </div>
                                <div class="col-sm-6 p-l-5">
                                    <span class="pull-right"><i class="fa fa-heart"></i> '.number_format($album->likes).'</span>
                                </div>
                            </div>
                            <div class="visible-xs">
                                <div class="col-xs-12">
                                    <i class="fa fa-eye"></i> '.number_format($album->views).'
                                </div>
                                <div class="col-xs-12">
                                    <i class="fa fa-heart"></i> '.number_format($album->likes).'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';

    /*
    <div class="item grid-group-item col-xs-6 col-md-4">
        <div class="thumbnail">
            <a href="#">
                <img class="group list-group-image" src="http://placehold.it/360x420/000/fff" alt="" />
            </a>
            <div class="caption">
                <h4 class="group inner list-group-item-heading">
                    <a href="#">Product title</a></h4>
                <p class="group inner list-group-item-text">
                    Product description... Lorem ipsum dolor sit amet, consectetuer adipiscing elit</p>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <i class="fa fa-eye text-info"></i> 93.329.322</span>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <span class="pull-right"><i class="fa fa-heart text-danger"></i> 93%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    */
});