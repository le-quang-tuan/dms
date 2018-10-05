<?php namespace App\Composers;
use App\Models\Tenement;
use App\Models\User;
use DB;
use yajra\Datatables\Datatables;
use Auth;

class HomeComposer
{
    public function compose($view)
    {
    	$tenement_id = Auth::user()->tenement_id;

        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement = Tenement::where('id', $tenement_id)->where('activation',1)->get();
        $userDetailInfo = User::where('id', Auth::user()->id)->get();

        //Add your variables
        $view->with('tenement', $tenement[0])
             ->with('userDetailInfo', $userDetailInfo[0]);
    }
}