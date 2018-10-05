@extends('front.layout.master')

@section('styles')
	<style type="text/css">
		.table .header {
			background-color: #3d88d1;
			color: #ffffff;
		}

		.table .odd {
			background-color: #87b1da;
		}

		.table .even {
			background-color: #ceddec;
		}

		.table .numeric-negative {
			color: red;
		}

		.table .numeric-postive {
			color: green;
		}

		.table td:nth-of-type(5), .table td:nth-of-type(8) {
			text-align: right;
		}

		.table td:nth-of-type(6), .table td:nth-of-type(7) {
			text-align: center;
		}
	</style>
@stop

@section('content-fullwidth')

<div ng-app="UserPage" ng-controller="UserController">
	<h1>社員情報</h1>

	<form name="form" id="user-search-condition">
		{!! Form::hiddenField('editRowIndex', null) !!}
		<div class="show-error-wrap">
			<span class="show-error-message" ng-init="act.error=''" ng-show="act.error!=''" ng-bind="act.error"></span>
		</div>
		<div class="row" ng->
			<div class="col-md-2">
				{!! Form::textField('params.id', '担当者コード', null, ['ng-disabled'=>'editUserMode.processing']) !!}
			</div>
			<div class="col-md-2">
				{!! Form::textField('params.email', 'メールアドレス', null) !!}
			</div>
			<div class="col-md-2">
				{!! Form::textField('params.name', '担当者名') !!}
			</div>
			<div class="col-md-2">
				{!! Form::textField('params.nick_name', '担当者名カナ') !!}
			</div>
			<div class="col-xs-8 col-md-2">
				{!! Form::selectField('params.occupation_id', '支店（自社）', null, 0, ['ng-options'=>'item.id as item.name for item in occupationList']) !!}
			</div>
			<div class="col-xs-4 col-md-2">
				<label></label>
				{!! Form::checkboxField('params.inactive', '削除含む', false) !!}
			</div>
		</div>
		<div class="row" ng-show="addUserMode.processing || editUserMode.processing">
			<div class="col-sm-6 col-md-3">
				{!! Form::passwordField('params.password', 'パスワード', ['ng-disabled'=>'(editUserMode.processing && !params.is_change_pass)']) !!}
			</div>
			<div class="col-sm-6 col-md-3">
				{!! Form::passwordField('params.password_confirmation', 'パスワード確認', ['ng-disabled'=>'(editUserMode.processing && !params.is_change_pass)']) !!}
			</div>
			<div class="col-sm-6 col-md-4" ng-show="editUserMode.processing">
				<label>パスワード変更</label>
				{!! Form::checkboxField('params.is_change_pass', 'パスワード変更ためにクリックしてください。', false) !!}
			</div>
			<div class="col-sm-6 col-md-2">
				{!! Form::selectField('params.role_id', '権限', null, 3, ['ng-options'=>'item.id as item.name for item in roleList']) !!}
			</div>
		</div>
		<div class="row">
			<div class="col-xs-4 col-sm-4" align="left">
				<button type="button" class="btn btn-default pull-left" ng-click="clearForm()" ng-hide="editUserMode.processing">
					<span class="fa fa-refresh"></span>
					クリア
				</button>
			</div>
			<div class="col-xs-4 col-sm-4" align="center">
				<button type="button" class="btn btn-default" ng-click="doSearch()" ng-init="doSearch.processing=false" ng-hide="addUserMode.processing || editUserMode.processing">
					<span ng-show="!doSearch.processing" class="fa fa-search"></span> 
                	<span ng-show="doSearch.processing" class="fa fa-refresh fa-spin"></span>
					検索
				</button>
			</div>
			<div class="col-xs-4 col-sm-4" align="right">
				<button type="button" class="btn btn-default" ng-click="addUserMode()" ng-init="addUserMode.processing=false" ng-disabled="addUserMode.processing || editUserMode.processing">
					<span ng-show="!addUserMode.processing" class="fa fa-plus"></span> 
                	<span ng-show="addUserMode.processing" class="fa fa-refresh fa-spin"></span>
					新規
				</button>
			</div>
			<div class="clearfix"></div>
			<br>

			<div class="col-xs-12 col-sm-12" align="center">
				<button type="button" class="btn btn-primary" ng-click="actOK()" ng-init="actOK.processing=false" ng-show="addUserMode.processing || editUserMode.processing">
                	<span ng-show="!actOK.processing" class="fa fa-check"></span>
                	<span ng-show="actOK.processing" class="fa fa-refresh fa-spin"></span>
					はい
				</button>

				<button type="button" class="btn btn-danger" ng-click="actCancel(params.id)" ng-init="actCancel.processing=false" ng-show="addUserMode.processing || editUserMode.processing">
                	<span ng-show="!actCancel.processing" class="fa fa-remove"></span>
                	<span ng-show="actCancel.processing" class="fa fa-refresh fa-spin"></span>
					キャンセル
				</button>
			</div>
		</div>

	</form>
	<hr>


	<div class="row">

		<div class="table-responsive">
			<table class="table table-bordered" datatable="ng" dt-options="dtOptions" ng-init="initTable()">
				<thead>
					<tr class="header">
						<th>担当者コード</th><!-- ID -->
						<th>メールアドレス</th><!-- Email -->
						<th>担当者名</th><!-- Name -->
						<th>担当者名カナ</th><!-- Nick Name -->
						<th>支店コード</th><!-- Occupation -->
						<th>状態</th><!-- Status -->
						<th>詳細</th><!-- Detail -->
						<th>権限</th><!-- Role -->
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="row in dataSet" ng-class-odd="'odd'" ng-class-even="'even'">
						<td ng-bind="row.id"></td>
						<td ng-bind="row.email"></td>
						<td ng-bind="row.name"></td>
						<td ng-bind="row.nick_name"></td>
						<td ng-bind="row.occupation_name"></td>
						<td ng-bind-html="statusTemplate(row.activated)"></td>
						<td>
							<span class="badge"><span ng-bind="row.role_name"></span></span>
						</td>
						<td>
							<button type="button" class="btn btn-xs btn-default" ng-click="editUserMode(row, $index)" ng-init="editUserRows[row.id].processing=false" ng-disabled="addUserMode.processing || editUserMode.processing">
								<span ng-show="!editUserRows[row.id].processing" class="fa fa-pencil"></span> 
                				<span ng-show="editUserRows[row.id].processing" class="fa fa-refresh fa-spin"></span>
                				編集
							</button>
							<button type="button" class="btn btn-xs btn-danger" ng-click="deleteUser(row.id, $index)" ng-init="deleteUserRows[row.id].processing=false" ng-disabled="addUserMode.processing || editUserMode.processing">
								<span ng-show="!deleteUserRows[row.id].processing" class="fa fa-remove"></span> 
                				<span ng-show="deleteUserRows[row.id].processing" class="fa fa-refresh fa-spin"></span>
								削除
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

	</div>
</div>
	
@stop

@section('scripts')
	<script type="text/javascript">
		var MESSAGE_DELETE_USER = "{!! trans('messages.delete_user') !!}";
	</script>

	<script type="text/javascript">
	/**
	* CustomerPage Module
	*
	* Description
	*/
	var userPage = angular.module('UserPage', ['ngMessages', 'ngPassword', 'datatables', 'datatables.scroller']);

	userPage.run(function($rootScope, DTOptionsBuilder) {
		$rootScope.occupationList = {!! json_encode($occupationList) !!};
		$rootScope.roleList = {!! json_encode($roleList) !!};

		$rootScope.dtOptions = DTOptionsBuilder.newOptions()
								.withLanguageSource('{!! asset("js/plugins/angular-datatables/i18n/Japanese.json") !!}')
								.withScroller()
							    .withOption('deferRender', true)
							    // Do not forget to add the scorllY option!!!
							    .withOption('scrollY', 200)
								.withDisplayLength(999999)
        						.withDOM('');

        $rootScope.showAlert = false;

        $rootScope.initTable = function() {
        	$rootScope.editUserMode = function(){};
        	$rootScope.deleteUser = function(){};
        	$rootScope.editUserMode.processing=false;
        	$rootScope.deleteUser.processing=false;

        	$rootScope.editUserRows = [];
        	$rootScope.deleteUserRows = [];
        }
	});

	userPage.controller('UserController', function($scope, $http, $sce) {
		$scope.doSearch = function() {
			$scope.doSearch.processing = true;

				$http({
                    method: 'POST',
                    url: _URL + '/user/filter',
                    data : $scope.params 
                })
                .success(function (res) {
                    $scope.doSearch.processing = false;

                    if (res.success ==  true) {
						$scope.dataSet = res.dataSet;
                    }
                })
                .error(function(response, status_code) {
                    $scope.doSearch.processing = false;
                    if (status_code == 401) {
                    	toastr.error(response.message);
                    };
                });
		}

		$scope.addUserMode = function() {
			$scope.addUserMode.processing = true;
		}

		$scope.editUserMode = function(user, index) {
			$scope.editUserMode.processing = true;
			$scope.editUserRows[user.id].processing = true;
			$scope.editRowIndex = index;


			$scope.params.id = user.id;
			$scope.params.email = user.email;
			$scope.params.name = user.name;
			$scope.params.nick_name = user.nick_name;
			$scope.params.occupation_id = user.occupation_id;
			$scope.params.inactive = user.activated == 0 ? true : false;
			$scope.params.is_change_pass = false;
			$scope.params.password = '';
			$scope.params.password_confirmation = '';
			$scope.params.role_id = user.role_id;
		}

		$scope.createUser = function() {
			$scope.actOK.processing = true;
			$scope.act.error = '';
			clearInputError();

			$http({
                method: 'POST',
                url: _URL + '/user/create',
                data: {
                	'id' : $scope.params.id,
                	'email' : $scope.params.email,
                	'name' : $scope.params.name,
                	'nick_name' : $scope.params.nick_name,
                	'occupation_id' : $scope.params.occupation_id,
                	'inactive' : $scope.params.inactive,
                	'password' : $scope.params.password,
                	'password_confirmation' : $scope.params.password_confirmation,
                	'role_id' : $scope.params.role_id,
                }
            })
            .success(function (response) {
                $scope.actOK.processing = false;

                if (response.success) {
                	$scope.addUserMode.processing = false;
                	toastr.success(response.message);
                } else {
                	if (response.message) {
                		toastr.error(response.message);
                	};
                	if (response.errors) {
                    	$scope.act.error = showInputError(response.errors);
                    };
                }
            })
            .error(function(response, status_code) {
                $scope.actOK.processing = false;
                if (status_code == 401) {
                	toastr.error(response.message);
                };
            });
		}

		$scope.updateUser = function() {
			clearInputError();
			$scope.act.error = '';
			$scope.actOK.processing = true;
			var index = $scope.editRowIndex;

			$http({
                method: 'POST',
                url: _URL + '/user/update/' + $scope.params.id,
                data: {
                	'id' : $scope.params.id,
                	'email' : $scope.params.email,
                	'name' : $scope.params.name,
                	'nick_name' : $scope.params.nick_name,
                	'occupation_id' : $scope.params.occupation_id,
                	'inactive' : $scope.params.inactive,
                	'is_change_pass' : $scope.params.is_change_pass,
                	'password' : $scope.params.password,
                	'password_confirmation' : $scope.params.password_confirmation,
                	'role_id' : $scope.params.role_id,
                }
            })
            .success(function (response) {
                $scope.actOK.processing = false;
                // $scope.editUserRows[$scope.params.id].processing = false;

                if (response.success) {
                	// $scope.editUserMode.processing = false;

                	$scope.dataSet[index].id = response.user.id;
					$scope.dataSet[index].code = response.user.code;
					$scope.dataSet[index].email = response.user.email;
					$scope.dataSet[index].name = response.user.name;
					$scope.dataSet[index].nick_name = response.user.nick_name;
					$scope.dataSet[index].occupation_id = response.user.occupation_id;
					$scope.dataSet[index].occupation_name = response.user.occupation_name;
					$scope.dataSet[index].activated = response.user.activated;
					$scope.dataSet[index].role_id = response.user.role_id;
					$scope.dataSet[index].role_name = response.user.role_name;

					toastr.success(response.message);
                } else {
                	if (response.message) {
                		toastr.error(response.message);
                	};
                	if (response.errors) {
                    	$scope.act.error = showInputError(response.errors);
                    };
                }
            })
            .error(function(response, status_code) {
                $scope.actOK.processing = false;
                // $scope.editUserRows[$scope.params.id].processing = false;
                if (status_code == 401) {
                	toastr.error(response.message);
                };
            });
		}

		$scope.deleteUser = function(id, rowIndex) {
			$.confirm({
			    content: MESSAGE_DELETE_USER,
		        confirm: function () {
		        	$scope.deleteUser.processing = true;
					$scope.deleteUserRows[id].processing = true;

					$http({
		                method: 'POST',
		                url: _URL + '/user/destroy/' + id
		            })
		            .success(function (response) {
		                $scope.deleteUser.processing = false;
		                $scope.deleteUserRows[id].processing = false;

		                if (response.success) {
		                	$scope.dataSet.splice(rowIndex, 1);
		                	toastr.success(response.message);
		                }
		            })
		            .error(function(response, status_code) {
		                $scope.deleteUser.processing = false;
		                $scope.deleteUserRows[id].processing = false;
		                if (status_code == 401) {
		                	toastr.error(response.message);
		                };
		            });
		        },
		        cancel: function () {
            		return;
		        }
			    
			});

			
		}

		$scope.actOK = function() {
			$scope.showAlert = false;
			$scope.errors = null;

			if ($scope.addUserMode.processing) {
				$scope.createUser();
			} else if ($scope.editUserMode.processing) {
				$scope.updateUser();
			} else {
				// Nothing
			}
		}

		$scope.actCancel = function(user_id) {
			clearInputError();
			$scope.act.error = '';
			$scope.addUserMode.processing = false;
			$scope.editUserMode.processing = false;
			if ($scope.editUserRows[user_id]) {
				$scope.editUserRows[user_id].processing = false;
			};
		}

		$scope.statusTemplate = function(activated) {
			var html = '';
			if (activated == 1) {
				html = '<span class="label label-success">有効</span>';
			} else {
				html = '<span class="label label-danger">無効</span>';
			}

			return $sce.trustAsHtml(html);
		}

		$scope.clearForm = function() {
			$scope.params.id = '';
			$scope.params.email = '';
			$scope.params.name = '';
			$scope.params.nick_name = '';
			$scope.params.occupation_id = 0;
			$scope.params.inactive = false;
			$scope.params.password = ''
			$scope.params.password_confirmation = '';
		}
	});

	</script>
@stop