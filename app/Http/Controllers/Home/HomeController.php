<?php

namespace App\Http\Controllers\Home;
use App\Http\Controllers\Controller;
use App\Jobs\ChangeLocale;

class HomeController extends Controller
{
	/**
	 * Display the home page.
	 *
	 * @return Response
	 */
	public function index()
	{	
		return view('home.index');
	}
}
