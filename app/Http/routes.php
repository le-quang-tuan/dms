<?php

Route::group(['middleware' => ['web']], function () {

	// Home
	Route::get('/', [
		'uses' => 'Home\HomeController@index', 
		'as' => 'home'
	]);
    Route::get('/index', [
        'uses' => 'IndexController@index', 
        'as' => 'index'
    ]);

	Route::get('language/{lang}', 'HomeController@language')->where('lang', '[A-Za-z_-]+');


	// Admin
	Route::get('admin', [
		'uses' => 'AdminController@admin',
		'as' => 'admin',
		'middleware' => 'admin'
	]);

	Route::get('medias', [
		'uses' => 'AdminController@filemanager',
		'as' => 'medias',
		'middleware' => 'redac'
	]);

    //Note
    Route::group(['prefix' => 'note'], function () {
        Route::post('add', ['as' => 'Note.store', 'uses' => 'IndexController@store']);
        Route::post('destroy', ['as' => 'Note.destroy', 'uses' => 'IndexController@destroy']);
    });

	// Blog
	Route::get('blog/order', ['uses' => 'BlogController@indexOrder', 'as' => 'blog.order']);
	Route::get('articles', 'BlogController@indexFront');
	Route::get('blog/tag', 'BlogController@tag');
	Route::get('blog/search', 'BlogController@search');

	Route::put('postseen/{id}', 'BlogController@updateSeen');
	Route::put('postactive/{id}', 'BlogController@updateActive');

	Route::resource('blog', 'BlogController');

	// Comment
	Route::resource('comment', 'CommentController', [
		'except' => ['create', 'show']
	]);

	Route::put('commentseen/{id}', 'CommentController@updateSeen');
	Route::put('uservalid/{id}', 'CommentController@valid');


	// Contact
	Route::resource('contact', 'ContactController', [
		'except' => ['show', 'edit']
	]);

	// User
	Route::get('user/sort/{role}', 'UserController@indexSort');

	Route::get('user/roles', 'UserController@getRoles');
    Route::post('user/roles', 'UserController@postRoles');
	// Route::get('note/destroy', 'IndexController@destroy');
	Route::put('userseen/{user}', 'UserController@updateSeen');

	//Route::resource('user', 'UserController');

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', ['as' => 'User', 'uses' => 'User\UserController@getIndex']);
        Route::get('user/data', ['as' => 'User.data', 'uses' => 'User\UserController@anyData']);        
        Route::get('detail/{id}', ['as' => 'UserDetail.index', 'uses' => 'User\UserDetailController@index']);
        Route::post('detail/changepassword', ['as' => 'UserDetail.changepassword', 'uses' => 'User\UserDetailController@changepassword']);
        Route::post('detail/updatedetail', ['as' => 'UserDetail.updatedetail', 'uses' => 'User\UserDetailController@updatedetail']);
        Route::get('new', ['as' => 'UserNew.index', 'uses' => 'User\UserNewController@index']);
        Route::post('new/newUser', ['as' => 'UserNew.newUser', 'uses' => 'User\UserNewController@newUser']);
    });

	// Authentication routes...
	Route::get('auth/login', 'Auth\AuthController@getLogin');
	Route::post('auth/login', 'Auth\AuthController@postLogin');
	Route::get('auth/logout', 'Auth\AuthController@getLogout');
	Route::get('auth/confirm/{token}', 'Auth\AuthController@getConfirm');

	// Resend routes...
	Route::get('auth/resend', 'Auth\AuthController@getResend');

	// Registration routes...
	Route::get('auth/register', 'Auth\AuthController@getRegister');
	Route::post('auth/register', 'Auth\AuthController@postRegister');

	// Password reset link request routes...
	Route::get('password/email', 'Auth\PasswordController@getEmail');
	Route::post('password/email', 'Auth\PasswordController@postEmail');

	// Password reset routes...
	Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
	Route::post('password/reset', 'Auth\PasswordController@postReset');

	//Company
	Route::group(['prefix' => 'company'], function () {
        Route::get('/', ['as' => 'Company', 'uses' => 'CompanyController@getIndex']);
        Route::get('company/data', ['as' => 'Company.data', 'uses' => 'CompanyController@anyData']);        
        Route::get('detail/{id}', ['as' => 'CompanyDetail.index', 'uses' => 'CompanyDetailController@index']);
        Route::get('create', ['as'  =>  'Company.create',   'uses'  =>  'CompanyDetailController@create']); // ADD BY HUNGNGUYEN
        Route::post('create',  ['as'    =>  'Company.store',    'uses'  =>  'CompanyDetailController@store']); // ADD BY HUNGNGUYEN
        Route::post('detail/update', ['as' => 'CompanyDetail.update', 'uses' => 'CompanyDetailController@update']);
        Route::post('detail/destroy', ['as' => 'CompanyDetail.destroy', 'uses' => 'CompanyDetailController@destroy']);        
    });

    //Tenement
    Route::group(['prefix' => 'tenement'], function () {
        Route::get('/', ['as' => 'Tenement', 'uses' => 'Tenement\TenementController@getIndex']);
        Route::get('tenement/data', ['as' => 'Tenement.data', 'uses' => 'Tenement\TenementController@anyData']);        
        Route::get('detail', ['as' => 'TenementDetail.index', 'uses' => 'Tenement\DetailController@index']);
        
        Route::get('create', ['as'  =>  'Tenement.create',   'uses'  =>  'Tenement\DetailController@create']);
        Route::post('create',  ['as'    =>  'Tenement.store',    'uses'  =>  'Tenement\DetailController@store']);

        Route::post('detail/update', ['as' => 'TenementDetail.update', 'uses' => 'Tenement\DetailController@update']);
        Route::post('detail/destroy', ['as' => 'TenementDetail.destroy', 'uses' => 'Tenement\DetailController@destroy']);

        // elec  
        Route::get('tenement/elec', ['as' => 'TenementElec.data', 'uses' => 'Tenement\ElecController@anyData']);  
       	Route::get('elec/', ['as' => 'TenementElec', 'uses' => 'Tenement\ElecController@getIndex']);
       	Route::get('elec/create', ['as'  =>  'TenementElec.create',   'uses'  =>  'Tenement\ElecDetailController@create']);
       	Route::get('elec/{id}', ['as'  =>  'TenementElecDetail.index',   'uses'  =>  'Tenement\ElecDetailController@index']);
        Route::post('elec/create',  ['as'    =>  'TenementElec.store',    'uses'  =>  'Tenement\ElecDetailController@store']);
        Route::post('elec/update',  ['as'    =>  'TenementElec.update',    'uses'  =>  'Tenement\ElecDetailController@update']);
        Route::post('elec/destroy',  ['as'    =>  'TenementElec.destroy',    'uses'  =>  'Tenement\ElecDetailController@destroy']);
    
        // water
        Route::get('tenement/water', ['as' => 'TenementWater.data', 'uses' => 'Tenement\WaterController@anyData']);  
       	Route::get('water/', ['as' => 'TenementWater', 'uses' => 'Tenement\WaterController@getIndex']);
       	Route::get('water/create', ['as'  =>  'TenementWater.create',   'uses'  =>  'Tenement\WaterDetailController@create']);
       	Route::get('water/{id}', ['as'  =>  'TenementWaterDetail.index',   'uses'  =>  'Tenement\WaterDetailController@index']);
        Route::post('water/create',  ['as'    =>  'TenementWater.store',    'uses'  =>  'Tenement\WaterDetailController@store']);
        Route::post('water/update',  ['as'    =>  'TenementWater.update',    'uses'  =>  'Tenement\WaterDetailController@update']);
        Route::post('water/destroy',  ['as'    =>  'TenementWater.destroy',    'uses'  =>  'Tenement\WaterDetailController@destroy']);
        
        // Gas
        Route::get('tenement/gas', ['as' => 'TenementGas.data', 'uses' => 'Tenement\GasController@anyData']);  
       	Route::get('gas/', ['as' => 'TenementGas', 'uses' => 'Tenement\GasController@getIndex']);
       	Route::get('gas/create', ['as'  =>  'TenementGas.create',   'uses'  =>  'Tenement\GasDetailController@create']);
       	Route::get('gas/{id}', ['as'  =>  'TenementGasDetail.index',   'uses'  =>  'Tenement\GasDetailController@index']);
        Route::post('gas/create',  ['as'    =>  'TenementGas.store',    'uses'  =>  'Tenement\GasDetailController@store']);
        Route::post('gas/update',  ['as'    =>  'TenementGas.update',    'uses'  =>  'Tenement\GasDetailController@update']);
        Route::post('gas/destroy',  ['as'    =>  'TenementGas.destroy',    'uses'  =>  'Tenement\GasDetailController@destroy']);

        // Parking
        Route::get('tenement/parking', ['as' => 'TenementParking.data', 'uses' => 'Tenement\ParkingController@anyData']);  
       	Route::get('parking/', ['as' => 'TenementParking', 'uses' => 'Tenement\ParkingController@getIndex']);
       	Route::get('parking/create', ['as'  =>  'TenementParking.create',   'uses'  =>  'Tenement\ParkingDetailController@create']);
       	Route::get('parking/{id}', ['as'  =>  'TenementParkingDetail.index',   'uses'  =>  'Tenement\ParkingDetailController@index']);
        Route::post('parking/create',  ['as'    =>  'TenementParking.store',    'uses'  =>  'Tenement\ParkingDetailController@store']);
        Route::post('parking/update',  ['as'    =>  'TenementParking.update',    'uses'  =>  'Tenement\ParkingDetailController@update']);
        Route::post('parking/destroy',  ['as'    =>  'TenementParking.destroy',    'uses'  =>  'Tenement\ParkingDetailController@destroy']);
    });

	//Flat
    Route::group(['prefix' => 'flat'], function () {
        Route::get('/', ['as' => 'TenementFlat', 'uses' => 'Flat\FlatController@getIndex']);
        Route::get('/sample', ['as' => 'TenementFlat', 'uses' => 'Flat\FlatController@sample']);
        Route::get('flat/data', ['as' => 'TenementFlat.data', 'uses' => 'Flat\FlatController@anyData']);        
        Route::get('detail/{id}', ['as' => 'TenementFlatDetail.index', 'uses' => 'Flat\DetailController@index']);
        
        Route::get('detail/tfowner/{flat_id}', ['as' => 'TfResidentOwner.data', 'uses' => 'Flat\FlatController@tfOwnerAnyData']);    
        Route::get('detail/tfrent/{flat_id}', ['as' => 'TfResidentRent.data', 'uses' => 'Flat\FlatController@tfRentAnyData']);    

        Route::post('detail/resident/change', ['as' => 'TfResChange.create', 'uses' => 'Flat\FlatController@resChange']); 

        Route::post('detail/resident/destroy', ['as' => 'TfResDestroy.create', 'uses' => 'Flat\FlatController@resDestroy']); 

        Route::post('detail/resident/add', ['as' => 'TfResAdd.create', 'uses' => 'Flat\FlatController@resAdd']); 

        Route::get('detail/resident/anyData/{resident_id}', ['as' => 'TfRes.data', 'uses' => 'Flat\FlatController@resData']); 

        Route::get('create', ['as' => 'TenementFlatDetail.create', 'uses'  =>  'Flat\DetailController@create']);
        Route::post('create',  ['as' => 'TenementFlatDetail.store', 'uses'  =>  'Flat\DetailController@store']);

        Route::post('update', ['as' => 'TenementFlatDetail.update', 'uses' => 'Flat\DetailController@update']);
        Route::post('destroy', ['as' => 'TenementFlatDetail.destroy', 'uses' => 'Flat\DetailController@destroy']);

        //Route::get('/elec/{id}', ['as' => 'TfElecUsed', 'uses' => 'TenementFlatController@getIndex']);
        Route::get('elec/{id}/datatable', ['as' => 'TfElecUsed.data', 'uses' => 'Flat\ElecController@anyData']);        
        Route::get('elec/{id}', ['as' => 'TfElecUsed.index', 'uses' => 'Flat\ElecController@index']);
        Route::post('elec/{id}/store', ['as' => 'TfElecUsed.store', 'uses' => 'Flat\ElecController@store']);
        Route::get('elec/{id}/destroy', ['as' => 'TfElecUsed.destroy', 'uses' => 'Flat\ElecController@destroy']);

        //Elec hàng loạt
        Route::get('all/elec/{year_month}', ['as' => 'TfElecUsed.data', 'uses' => 'Flat\ElecController@allindex']);
        Route::get('all/elec/anyData/{year_month}', ['as' => 'TfElecUsed.data', 'uses' => 'Flat\ElecController@allFlatData']);
        Route::Post('elecchange', ['as' => 'Elec.exex_change', 'uses' => 'Flat\ElecController@exex_change']);
        
        //Vehicle hàng loạt
        Route::get('all/vehicle', ['as' => 'AllVehicle.data', 'uses' => 'Flat\VehicleController@allindex']);
        Route::get('all/vehicle/anyData', ['as' => 'AllVehicle.data', 'uses' => 'Flat\VehicleController@allFlatData']);
        Route::Post('vehiclechange', ['as' => 'AllVehicle.exex_change', 'uses' => 'Flat\VehicleController@exex_change']);
        Route::get('vehicle/{id}/show', ['as' => 'AllVehicle.show', 'uses' => 'Flat\VehicleController@show']);

        //water
        Route::get('water/{id}/datatable', ['as' => 'TfWaterUsed.data', 'uses' => 'Flat\WaterController@anyData']);
        Route::get('water/{id}', ['as' => 'TfWaterUsed.index', 'uses' => 'Flat\WaterController@index']);
        Route::post('water/{id}/store', ['as' => 'TfWaterUsed.store', 'uses' => 'Flat\WaterController@store']);
        Route::get('water/{id}/destroy', ['as' => 'TfWaterUsed.destroy', 'uses' => 'Flat\WaterController@destroy']);
        //Water hàng loạt
        Route::get('all/water/{year_month}', ['as' => 'TfWaterUsed.data', 'uses' => 'Flat\WaterController@allindex']);
        Route::get('all/water/anyData/{year_month}', ['as' => 'TfWaterUsed.data', 'uses' => 'Flat\WaterController@allFlatData']);
        Route::Post('waterchange', ['as' => 'Water.exex_change', 'uses' => 'Flat\WaterController@exex_change']);

        //gas
        Route::get('gas/{id}/datatable', ['as' => 'TfGasUsed.data', 'uses' => 'Flat\GasController@anyData']);
        Route::get('gas/{id}', ['as' => 'TfGasUsed.index', 'uses' => 'Flat\GasController@index']);
        Route::post('gas/{id}/store', ['as' => 'TfGasUsed.store', 'uses' => 'Flat\GasController@store']);
        Route::get('gas/{id}/destroy', ['as' => 'TfWaterUsed.destroy', 'uses' => 'Flat\GasController@destroy']);

        //Gas hàng loạt
        Route::get('all/gas/{year_month}', ['as' => 'TfGasUsed.data', 'uses' => 'Flat\GasController@allindex']);
        Route::get('all/gas/anyData/{year_month}', ['as' => 'TfGasUsed.data', 'uses' => 'Flat\GasController@allFlatData']);
        Route::Post('gaschange', ['as' => 'Gas.exex_change', 'uses' => 'Flat\GasController@exex_change']);
        //Service
        Route::get('service/{id}/datatable', ['as' => 'TfServiceUsed.data', 'uses' => 'Flat\ServiceController@anyData']);
        Route::get('service/{id}', ['as' => 'TfServiceUsed.index', 'uses' => 'Flat\ServiceController@index']);
        Route::post('service/{id}/store', ['as' => 'TfServiceUsed.store', 'uses' => 'Flat\ServiceController@store']);
        Route::get('service/{id}/destroy', ['as' => 'TfServiceUsed.destroy', 'uses' => 'Flat\ServiceController@destroy']);

        //Service hàng loạt
        Route::get('all/service/{year_month}', ['as' => 'AllService.data', 'uses' => 'Flat\ServiceController@allindex']);
        Route::get('all/service/anyData/{year_month}', ['as' => 'AllService.data', 'uses' => 'Flat\ServiceController@allFlatData']);
        Route::Post('servicechange', ['as' => 'AllService.exex_change', 'uses' => 'Flat\ServiceController@exex_change']);
        Route::get('service/{id}/show', ['as' => 'AllService.show', 'uses' => 'Flat\ServiceController@show']);

        //Parking
        Route::get('vehicle/{id}/datatable', ['as' => 'TfVehicle.data', 'uses' => 'Flat\VehicleController@anyData']);
        Route::get('vehicle/{id}', ['as' => 'TfVehicle.index', 'uses' => 'Flat\VehicleController@index']);
        Route::post('vehicle/{id}/store', ['as' => 'TfVehicle.store', 'uses' => 'Flat\VehicleController@store']);
        Route::get('vehicle/{id}/destroy', ['as' => 'TfVehicle.destroy', 'uses' => 'Flat\VehicleController@destroy']);
	});

    //Import
    Route::group(['prefix' => 'import'], function () {
        Route::get('importWater', 
            'Import\WaterController@index');
        Route::post('importWater/store', ['as' => 'ImportWater.store', 'uses' => 'Import\WaterController@store']);
        Route::post('importWater/download', ['as' => 'ImportWater.download', 'uses' => 'Import\WaterController@download']);
        Route::get('importWater/anyData', ['as' => 'ImportWater.anyData', 'uses' => 'Import\WaterController@anyData']);
        Route::post('importWater/save', ['as' => 'ImportWater.save', 'uses' => 'Import\WaterController@save']);

        Route::get('importGas', 
            'Import\GasController@index');
        Route::post('importGas/store', ['as' => 'ImportGas.store', 'uses' => 'Import\GasController@store']);
        Route::post('importGas/download', ['as' => 'ImportGas.download', 'uses' => 'Import\GasController@download']);
        Route::get('importGas/anyData', ['as' => 'ImportGas.anyData', 'uses' => 'Import\GasController@anyData']);
        Route::post('importGas/save', ['as' => 'ImportGas.save', 'uses' => 'Import\GasController@save']);

        Route::get('importElec', 
            'Import\ElecController@index');
        Route::post('importElec/store', ['as' => 'ImportElec.store', 'uses' => 'Import\ElecController@store']);
        Route::post('importElec/download', ['as' => 'ImportElec.download', 'uses' => 'Import\ElecController@download']);
        Route::get('importElec/anyData', ['as' => 'ImportElec.anyData', 'uses' => 'Import\ElecController@anyData']);
        Route::post('importElec/save', ['as' => 'ImportElec.save', 'uses' => 'Import\ElecController@save']);

        Route::get('importService', 
            'Import\ServiceController@index');
        Route::post('importService/store', ['as' => 'ImportService.store', 'uses' => 'Import\ServiceController@store']);
        Route::post('importService/download', ['as' => 'ImportService.download', 'uses' => 'Import\ServiceController@download']);
        Route::get('importService/anyData', ['as' => 'ImportService.anyData', 'uses' => 'Import\ServiceController@anyData']);
        Route::post('importService/save', ['as' => 'ImportService.save', 'uses' => 'Import\ServiceController@save']);

        Route::get('importVehicle', 
            'Import\VehicleController@index');
        Route::post('importVehicle/store', ['as' => 'ImportVehicle.store', 'uses' => 'Import\VehicleController@store']);
        Route::post('importVehicle/download', ['as' => 'ImportVehicle.download', 'uses' => 'Import\VehicleController@download']);
        Route::get('importVehicle/anyData', ['as' => 'ImportVehicle.anyData', 'uses' => 'Import\VehicleController@anyData']);
        Route::post('importVehicle/save', ['as' => 'ImportVehicle.save', 'uses' => 'Import\VehicleController@save']);

        Route::get('importFlat', 
            'Import\FlatController@index');
        Route::post('importFlat/store', ['as' => 'ImportFlat.store', 'uses' => 'Import\FlatController@store']);
        Route::post('importFlat/download', ['as' => 'ImportFlat.download', 'uses' => 'Import\FlatController@download']);
        Route::get('importFlat/anyData', ['as' => 'ImportFlat.anyData', 'uses' => 'Import\FlatController@anyData']);
        Route::post('importFlat/save', ['as' => 'ImportFlat.save', 'uses' => 'Import\FlatController@save']);
    });
    
    //monthlyfee
    Route::group(['prefix' => 'monthlyfee'], function () {
        Route::get('status', ['as' => 'MonthlyFeeFlat', 'uses' => 'MonthlyFee\FlatController@status']);

        Route::get('/', ['as' => 'MonthlyFeeFlat', 'uses' => 'MonthlyFee\FlatController@getIndex']);
        Route::get('anyData/{year_month}', ['as' => 'MonthlyFeeFlat.data', 'uses' => 'MonthlyFee\FlatController@anyData']);
        Route::get('index/{year_month}', ['as' => 'MonthlyFeeFlat.data', 'uses' => 'MonthlyFee\FlatController@index']);
        Route::Post('recalculate', ['as' => 'Paymonth.exex_recalculate', 'uses' => 'MonthlyFee\PaymonthController@exex_recalculate']);

        Route::get('paid/{flat_id}', 
            'MonthlyFee\PaymentFlatController@index');
        // danh sách tất cả phiếu đã thu
        Route::get('paid', 
            'MonthlyFee\PaymentFlatController@index_all');

        Route::get('paid/{flat_id}/anyData', ['as' => 'PaidFlat.anyData', 'uses' => 'MonthlyFee\PaymentFlatController@anyData']);
        Route::post('paid/save', ['as' => 'PaidFlat.save', 'uses' => 'MonthlyFee\PaymentFlatController@save']);

        Route::get('prepaid/{flat_id}', 
            'MonthlyFee\PrepaidFlatController@index');
        Route::get('prepaid/{flat_id}/anyData', ['as' => 'PrepaidFlat.anyData', 'uses' => 'MonthlyFee\PrepaidFlatController@anyData']);
        Route::post('prepaid/save', ['as' => 'PrepaidFlat.save', 'uses' => 'MonthlyFee\PrepaidFlatController@save']);

        // danh sách phiếu thu của căn hộ
        Route::get('paymonth/{flat_id}', 
            'MonthlyFee\PaymonthFlatController@index');

        // Tạo mới phiếu thu cho căn hộ
        Route::get('paymonth/{flat_id}/new', 
            'MonthlyFee\PaymonthFlatController@new_index');

        Route::get('status/{flat_id}', 
            'MonthlyFee\PaymonthFlatController@status');        
        Route::get('paymonth/{flat_id}/anyData', ['as' => 'PaymonthFlat.anyData', 'uses' => 'MonthlyFee\PaymonthFlatController@anyData']);
        Route::post('paymonth/save', ['as' => 'PaymonthFlat.save', 'uses' => 'MonthlyFee\PaymonthFlatController@save']);

        // DS phiếu ko thu
        Route::get('deptskip/{flat_id}', 
            'MonthlyFee\DeptSkipFlatController@index');

        Route::get('deptskip/{flat_id}/new', 
            'MonthlyFee\DeptSkipFlatController@new_index');

        Route::get('deptskip/{flat_id}/anyData', ['as' => 'DeptSkipFlat.anyData', 'uses' => 'MonthlyFee\DeptSkipFlatController@anyData']);
        Route::post('deptskip/save', ['as' => 'DeptSkipFlat.save', 'uses' => 'MonthlyFee\DeptSkipFlatController@save']);

        Route::get('deptskipdetail/{id}', 
            'MonthlyFee\DeptSkipDetailFlatController@index');
        Route::get('deptskipdetail/{id}/anyData', ['as' => 'DeptSkipDetailFlat.anyData', 'uses' => 'MonthlyFee\DeptSkipDetailFlatController@anyData']);
        Route::post('deptskipdetail/save', ['as' => 'DeptSkipDetailFlat.save', 'uses' => 'MonthlyFee\DeptSkipDetailFlatController@save']);
        Route::get('deptskipdetail/{id}/destroy', ['as' => 'DeptSkipDetailFlat.destroy', 'uses' => 'MonthlyFee\DeptSkipDetailFlatController@destroy']);

        Route::get('paidpayment/{paid_id}', 
            'MonthlyFee\PaymentFlatController@load_paid');
        Route::get('paidpayment/{paid_id}/anyData', ['as' => 'PaymonthFlat.anyData', 'uses' => 'MonthlyFee\PaymentFlatController@anyData']);
        Route::post('paidpayment/save', ['as' => 'PaymentFlat.modify', 'uses' => 'MonthlyFee\PaymentFlatController@modify']);

        Route::get('paiddetaillist', 
            'MonthlyFee\PaymentFlatController@index_paid_detail_list');
        Route::get('paiddetaillist/anyData', 
            'MonthlyFee\PaymentFlatController@anyDataDetail');

        Route::get('paiddetail/{id}', 
            'MonthlyFee\PaymentDetailFlatController@index');
        Route::get('paiddetail/{id}/anyData', ['as' => 'PaidFlat.anyData', 'uses' => 'MonthlyFee\PaymentDetailFlatController@anyData']);
        Route::post('paiddetail/save', ['as' => 'PaidFlat.save', 'uses' => 'MonthlyFee\PaymentDetailFlatController@save']);
        Route::get('paiddetail/{id}/destroy', ['as' => 'PaidFlat.destroy', 'uses' => 'MonthlyFee\PaymentDetailFlatController@destroy']);

        Route::get('exepaymonth', 
            'MonthlyFee\PaymonthController@index');
        Route::get('exepaymonth/anyData', ['as' => 'Paymonth.anyData', 'uses' => 'MonthlyFee\PaymonthController@anyData']);
        Route::post('exepaymonth/exex_store', ['as' => 'Paymonth.exex_store', 'uses' => 'MonthlyFee\PaymonthController@exex_store']);

        Route::get('flatpaid', 
            'MonthlyFee\FlatPaidController@index');
        Route::get('flatpaid/anyData', ['as' => 'FlatPaid.anyData', 'uses' => 'MonthlyFee\FlatPaidController@anyData']);

        // Hiển Thị danh sách Phiếu Thu Tạm và Chuyển Sang Thu Chính
        Route::get('paybillall/{year_month}', ['as' => 'ReportPayBill', 'uses' => 'MonthlyFee\PaybillFlatController@index_all']);

        Route::post('paybillall/download', ['as' => 'ReportPayBill.download', 'uses' => 'MonthlyFee\PaybillFlatController@download']);

        Route::get('paybillall/{year_month}/anyData', ['as' => 'ReportPayBill.anyData', 'uses' => 'MonthlyFee\PaybillFlatController@anyData']);

        Route::get('paybilldetail/{id}', 'MonthlyFee\PaybillFlatController@index');
        Route::post('paybilldetail/save', ['as' => 'PaybillFlat.save', 'uses' => 'MonthlyFee\PaybillFlatController@save']);
    });

    //report
    Route::group(['prefix' => 'report'], function () {
        Route::get('payment/{report_id}', ['as' => 'ReportPayment', 'uses' => 'Report\PaymentController@index']);

        // Route::get('paymentnotice/{flat_id}', ['as' => 'ReportPaymentNotice', 'uses' => 'Report\PaymentNoticeController@index']);

        Route::get('paymentnotice/{flat_id}/{year_month}', ['as' => 'ReportPaymentNotice', 'uses' => 'Report\PaymentNoticeController@indexpdf']);

        Route::get('paymentnotice/{year_month}', ['as' => 'ReportPaymentNotice', 'uses' => 'Report\PaymentNoticeController@index_tenement']);

        Route::get('paymentnotice_new/{year_month}', ['as' => 'ReportPaymentNotice', 'uses' => 'Report\PaymentNoticeController@index_tenement_new']);

        Route::get('deptnotice/{time}', ['as' => 'ReportPaymentNotice', 'uses' => 'Report\PaymentNoticeController@dept_notice']);

        Route::get('deptnoticefiles/{time}', ['as' => 'ReportPaymentNotice', 'uses' => 'Report\PaymentNoticeController@dept_notice_files']);

        Route::get('dl_deptnoticefiles/{time}/{file_name}', ['as' => 'ReportPaymentNotice', 'uses' => 'Report\PaymentNoticeController@download_deptnotice']);
        Route::get('pv_deptnoticefiles/{time}/{file_name}', ['as' => 'ReportPaymentNotice', 'uses' => 'Report\PaymentNoticeController@preview_deptnotice']);
        Route::post('deptnoticefiles/merge/{time}',['as' => 'File.dept_notices_merge', 'uses' => 'Report\PaymentNoticeController@dept_notices_merge']);

        Route::get('paybill/{year_month}/{flat_id}', ['as' => 'ReportPayBill', 'uses' => 'Report\PayBillController@index_tenement']);

        Route::get('paybillall/{year_month}/{flat_id}', ['as' => 'ReportPayBill', 'uses' => 'Report\PayBillController@index_tenement']);

        Route::post('paybillall/download', ['as' => 'ReportPayBill.download', 'uses' => 'Report\PayBillController@download']);

        Route::get('paybill/{year_month}',
            'Report\FileController@index');
        Route::get('dl_paybillfiles/{tenement_id}/{year_month}/{file_name}', ['as' => 'PayBill.download_paybill', 'uses' => 'Report\FileController@download_paybill']);
        Route::get('pv_paybillfiles/{tenement_id}/{year_month}/{file_name}', ['as' => 'PayBill.preview_paybill', 'uses' => 'Report\FileController@preview_paybill']);

        Route::get('paymentnoticefiles/{year_month}',
            'Report\FileController@index_pnfiles');

        Route::post('paymentnoticefiles/{year_month}/merge',['as' => 'File.exe_merge', 'uses' => 'Report\FileController@exe_merge']);

        Route::post('paybill/{year_month}/merge',['as' => 'File.exe_merge_paybill', 'uses' => 'Report\FileController@exe_merge_paybill']);

        Route::get('dl_paymentnoticefiles/{tenement_id}/{year_month}/{file_name}', ['as' => 'PayBill.download_paymentnotice', 'uses' => 'Report\FileController@download_paymentnotice']);
        Route::get('pv_paymentnoticefiles/{tenement_id}/{year_month}/{file_name}', ['as' => 'PayBill.preview_paymentnotice', 'uses' => 'Report\FileController@preview_paymentnotice']);

        Route::get('paidbill/{id}/{print_type}',
            'Report\PaymentController@paid_report');
        Route::get('dl_paidbill/{id}', ['as' => 'PaidBill.download_paidbill', 'uses' => 'Report\PaymentController@paid_report']);
        Route::get('pv_paidbill/{id}', ['as' => 'PaidBill.preview_paidbill', 'uses' => 'Report\PaymentController@paid_report']);
    });

    //report
    Route::group(['prefix' => 'home'], function () {
        Route::get('/', ['as' => 'Home', 'uses' => 'Home\HomeController@index']);
    });

    Route::get('/generate/models', '\\Jimbolino\\Laravel\\ModelBuilder\\ModelGenerator5@start');
    //Flat
    //monthlyfee
    Route::group(['prefix' => 'monthlyreport'], function () {
        Route::get('/monthdept', ['as' => 'MonthDept', 'uses' => 'MonthlyReport\MonthDeptController@getIndex']);
        Route::get('/monthdept/anyData/{year_month}', ['as' => 'MonthDept.data', 'uses' => 'MonthlyReport\MonthDeptController@anyData']);
        Route::get('monthdept/index/{year_month}', ['as' => 'MonthDept.data', 'uses' => 'MonthlyReport\MonthDeptController@index']);

        Route::get('/paid', ['as' => 'Paid', 'uses' => 'MonthlyReport\PaidController@index']);
        Route::get('/paid/anyData', ['as' => 'Paid.data', 'uses' => 'MonthlyReport\PaidController@anyData']);
        Route::post('paid/download', ['as' => 'Paid.download', 'uses' => 'MonthlyReport\PaidController@download']);

        Route::get('/feepaid', ['as' => 'FeePaid', 'uses' => 'MonthlyReport\FeePaidController@index']);
        Route::get('/feepaid/anyData', ['as' => 'FeePaid.data', 'uses' => 'MonthlyReport\FeePaidController@anyData']);
        Route::post('feepaid/download', ['as' => 'FeePaid.download', 'uses' => 'MonthlyReport\FeePaidController@download']);        

        Route::get('/alldept', ['as' => 'AllDept', 'uses' => 'MonthlyReport\AllDeptController@getIndex']);
        Route::get('/alldept/anyData', ['as' => 'AllDeptController.data', 'uses' => 'MonthlyReport\AllDeptController@anyData']);

        Route::get('/prepaid', ['as' => 'PrePaid', 'uses' => 'MonthlyReport\PrepaidController@getIndex']);
        Route::get('/prepaid/anyData/{year_month}', ['as' => 'PrePaid.data', 'uses' => 'MonthlyReport\PrepaidController@anyData']);
        Route::get('prepaid/index/{year_month}', ['as' => 'PrePaid.data', 'uses' => 'MonthlyReport\PrepaidController@index']);

        Route::get('/vehicle', ['as' => 'Vehicle', 'uses' => 'MonthlyReport\VehicleController@getIndex']);
        Route::post('/vehicle_fee/download', ['as' => 'VehicleFee.download', 'uses' => 'MonthlyReport\VehicleController@download']);
        Route::get('/vehicle_fee', ['as' => 'Vehicle', 'uses' => 'MonthlyReport\VehicleController@index']);

        Route::get('/vehicle/anyData/{year_month}', ['as' => 'Vehicle.data', 'uses' => 'MonthlyReport\VehicleController@anyData']);
        Route::get('vehicle/index/{year_month}', ['as' => 'Vehicle.data', 'uses' => 'MonthlyReport\VehicleController@index']);
    });

    //Kỹ Thuật
    Route::group(['prefix' => 'tech'], function () {
        Route::get('dailyactivity', ['as'  =>  'DailyActivity.index',   'uses'  =>  'Tech\DailyActivityController@index']);
        Route::post('dailyactivity/create',  ['as'    =>  'DailyActivity.store',    'uses'  =>  'Tech\DailyActivityController@store']);
        Route::post('dailyactivity/update',  ['as'    =>  'DailyActivity.update',    'uses'  =>  'Tech\DailyActivityController@update']);
        Route::post('dailyactivity/destroy',  ['as'    =>  'DailyActivity.destroy',    'uses'  =>  'Tech\DailyActivityController@destroy']);

        Route::get('dailyactivitycal', ['as' => 'DailyActivity', 'uses' => 
            'Tech\DailyActivityController@cal']);  
        Route::get('dailyactivity/events/{user_id}',
            'Tech\DailyActivityController@getDailyActivity');
        Route::post('dailyactivity/report',  ['as'    =>  'DailyActivity.report',    'uses'  =>  'Tech\DailyActivityController@report']);

        Route::get('schedulecal', ['as' => 'ScheduleCal', 'uses' => 
            'Tech\ScheduleController@cal']);  
        Route::get('schedulecal/events/{user_id}',
            'Tech\ScheduleController@getSchedule');
        
        Route::get('tech/schedule/{id}', ['as' => 'Schedule.data', 'uses' => 'Tech\ScheduleController@anyData']);  
        Route::get('schedule/{id}', ['as' => 'Schedule', 'uses' => 'Tech\ScheduleController@getIndex']);
        Route::get('schedule/detail/{id}', ['as' => 'Schedule.detail', 'uses' => 'Tech\ScheduleController@getScheduleDetail']);

        Route::get('importequipment', 
            'Tech\ImportEquipmentController@index');
        Route::post('importequipment/store', ['as' => 'ImportEquipment.store', 'uses' => 'Tech\ImportEquipmentController@store']);
        Route::post('importequipment/download', ['as' => 'ImportEquipment.download', 'uses' => 'Tech\ImportEquipmentController@download']);
        Route::get('importequipment/anyData', ['as' => 'ImportEquipment.anyData', 'uses' => 'Tech\ImportEquipmentController@anyData']);
        Route::post('importequipment/save', ['as' => 'ImportEquipment.save', 'uses' => 'Tech\ImportEquipmentController@save']);

        // Producer
        Route::get('tech/producer', ['as' => 'Producer.data', 'uses' => 'Tech\ProducerController@anyData']);  
        Route::get('producer/', ['as' => 'Producer', 'uses' => 'Tech\ProducerController@getIndex']);
        Route::get('producer/create', ['as'  =>  'Producer.create',   'uses'  =>  'Tech\ProducerDetailController@create']);
        Route::get('producer/{id}', ['as'  =>  'ProducerDetail.index',   'uses'  =>  'Tech\ProducerDetailController@index']);
        Route::post('producer/create',  ['as'    =>  'Producer.store',    'uses'  =>  'Tech\ProducerDetailController@store']);
        Route::post('producer/update',  ['as'    =>  'Producer.update',    'uses'  =>  'Tech\ProducerDetailController@update']);
        Route::post('producer/destroy',  ['as'    =>  'Producer.destroy',    'uses'  =>  'Tech\ProducerDetailController@destroy']);

        // Equipment Groups
        Route::get('tech/group', ['as' => 'Group.data', 'uses' => 'Tech\GroupController@anyData']);  
        Route::get('group/', ['as' => 'Group', 'uses' => 'Tech\GroupController@getIndex']);
        Route::get('group/create', ['as'  =>  'Group.create',   'uses'  =>  'Tech\GroupDetailController@create']);
        Route::get('group/{id}', ['as'  =>  'GroupDetail.index',   'uses'  =>  'Tech\GroupDetailController@index']);
        Route::post('group/create',  ['as'    =>  'Group.store',    'uses'  =>  'Tech\GroupDetailController@store']);
        Route::post('group/update',  ['as'    =>  'Group.update',    'uses'  =>  'Tech\GroupDetailController@update']);
        Route::post('group/destroy',  ['as'    =>  'Group.destroy',    'uses'  =>  'Tech\GroupDetailController@destroy']);

        // Equipment
        Route::get('tech/equipment', ['as' => 'Equipment.data', 'uses' => 'Tech\EquipmentController@anyData']);  
        Route::get('equipment/', ['as' => 'Equipment', 'uses' => 'Tech\EquipmentController@getIndex']);
        Route::get('equipment/create', ['as'  =>  'Equipment.create',   'uses'  =>  'Tech\EquipmentDetailController@create']);
        Route::get('equipment/{id}', ['as'  =>  'EquipmentDetail.index',   'uses'  =>  'Tech\EquipmentDetailController@index']);
        Route::post('equipment/create',  ['as'    =>  'Equipment.store',    'uses'  =>  'Tech\EquipmentDetailController@store']);
        Route::post('equipment/update',  ['as'    =>  'Equipment.update',    'uses'  =>  'Tech\EquipmentDetailController@update']);
        Route::post('equipment/destroy', ['as'    =>  'Equipment.destroy',    'uses'  =>  'Tech\EquipmentDetailController@destroy']);

        // Equipment
        Route::get('equipmainte/{id}', ['as'  =>  'EquipMainte.index',   'uses'  =>  'Tech\EquipmentMaintenanceController@index']);
        Route::post('equipmainte/create',  ['as'    =>  'EquipMainte.store',    'uses'  =>  'Tech\EquipmentMaintenanceController@store']);
        Route::post('equipmainte/update',  ['as'    =>  'EquipMainte.update',    'uses'  =>  'Tech\EquipmentMaintenanceController@update']);
        Route::post('equipmainte/destroy',  ['as'    =>  'EquipMainte.destroy',    'uses'  =>  'Tech\EquipmentMaintenanceController@destroy']);

        Route::post('equipmainte/report',  ['as'    =>  'EquipMainte.report',    'uses'  =>  'Tech\EquipmentMaintenanceController@report']);

        Route::get('importGas', 
            'Import\GasController@index');
        Route::post('importGas/store', ['as' => 'ImportGas.store', 'uses' => 'Import\GasController@store']);
        Route::post('importGas/download', ['as' => 'ImportGas.download', 'uses' => 'Import\GasController@download']);
        Route::get('importGas/anyData', ['as' => 'ImportGas.anyData', 'uses' => 'Import\GasController@anyData']);
        Route::post('importGas/save', ['as' => 'ImportGas.save', 'uses' => 'Import\GasController@save']);

        Route::get('importElec', 
            'Import\ElecController@index');
        Route::post('importElec/store', ['as' => 'ImportElec.store', 'uses' => 'Import\ElecController@store']);
        Route::post('importElec/download', ['as' => 'ImportElec.download', 'uses' => 'Import\ElecController@download']);
        Route::get('importElec/anyData', ['as' => 'ImportElec.anyData', 'uses' => 'Import\ElecController@anyData']);
        Route::post('importElec/save', ['as' => 'ImportElec.save', 'uses' => 'Import\ElecController@save']);

        Route::get('importService', 
            'Import\ServiceController@index');
        Route::post('importService/store', ['as' => 'ImportService.store', 'uses' => 'Import\ServiceController@store']);
        Route::post('importService/download', ['as' => 'ImportService.download', 'uses' => 'Import\ServiceController@download']);
        Route::get('importService/anyData', ['as' => 'ImportService.anyData', 'uses' => 'Import\ServiceController@anyData']);
        Route::post('importService/save', ['as' => 'ImportService.save', 'uses' => 'Import\ServiceController@save']);

        Route::get('importVehicle', 
            'Import\VehicleController@index');
        Route::post('importVehicle/store', ['as' => 'ImportVehicle.store', 'uses' => 'Import\VehicleController@store']);
        Route::post('importVehicle/download', ['as' => 'ImportVehicle.download', 'uses' => 'Import\VehicleController@download']);
        Route::get('importVehicle/anyData', ['as' => 'ImportVehicle.anyData', 'uses' => 'Import\VehicleController@anyData']);
        Route::post('importVehicle/save', ['as' => 'ImportVehicle.save', 'uses' => 'Import\VehicleController@save']);

        Route::get('importFlat', 
            'Import\FlatController@index');
        Route::post('importFlat/store', ['as' => 'ImportFlat.store', 'uses' => 'Import\FlatController@store']);
        Route::post('importFlat/download', ['as' => 'ImportFlat.download', 'uses' => 'Import\FlatController@download']);
        Route::get('importFlat/anyData', ['as' => 'ImportFlat.anyData', 'uses' => 'Import\FlatController@anyData']);
        Route::post('importFlat/save', ['as' => 'ImportFlat.save', 'uses' => 'Import\FlatController@save']);
    });
});