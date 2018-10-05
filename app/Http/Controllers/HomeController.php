<?php

namespace App\Http\Controllers;

use App\Jobs\ChangeLocale;
use Auth;
use App\Models\Tenement;
use App\Models\User;

class HomeController extends Controller
{

	/**
	 * Display the home page.
	 *
	 * @return Response
	 */
	public function index()
	{
		//$tenement_id = Auth::user()->tenement_id;

        //Check period user
        // if(Auth::user()->confirmed == 0 || $tenement_id == '') {
        //     return redirect("/home");
        // }

        // $tenement = Tenement::where('id', $tenement_id)->where('activation',1)->get();

        // $userDetailInfo = User::where('id', Auth::user()->id)->get();

		return redirect('/index');
	}

	/**
	 * Change language.
	 *
	 * @param  App\Jobs\ChangeLocaleCommand $changeLocale
	 * @param  String $lang
	 * @return Response
	 */
	public function language( $lang,
		ChangeLocale $changeLocale)
	{		
		$lang = in_array($lang, config('app.languages')) ? $lang : config('app.fallback_locale');
		$changeLocale->lang = $lang;
		$this->dispatch($changeLocale);

		return redirect()->back();
	}

}
