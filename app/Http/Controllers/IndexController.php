<?php

namespace App\Http\Controllers;

use App\Jobs\ChangeLocale;
use Auth;
use App\Models\Tenement;
use App\Models\User;
use App\Models\UserNote;
use Illuminate\Http\Request;
use DB;
use DateTime;
use Validator;

class IndexController extends Controller
{

	/**
	 * Display the home page.
	 *
	 * @return Response
	 */
	public function index()
	{
		$tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement = Tenement::where('id', $tenement_id)->where('activation',1)->get();

        $notes = UserNote::where('user_id', Auth::user()->id)->where('activation',1)->orderby("note_date","desc")->get();

        $userDetailInfo = User::where('id', Auth::user()->id)->get();

		return view('home',  [ 'tenement'=>$tenement[0], 'userDetailInfo'=>$userDetailInfo[0], 'notes'=>$notes ]);
	}
	
	/**
	 * Display the home page.
	 *
	 * @return Response
	 */
	public function destroy(Request $request)
	{
		// dd($request);
		//Check period user
        $id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        DB::beginTransaction();
        try {
            //check Tenement Id is exist or not.
            $userNote = UserNote::where('id',$request->id)->where('activation',1)->count();

            if($userNote == 0){
                return \Response::view('errors.404', array(), 404);
            }  

            $userNote = UserNote::find($request->id);
            $userNote->activation = 0;
            $userNote->updated_by = Auth::user()->id;
            $userNote->updated_at = date("Y-m-d H:i:s");
            $userNote->save();

            DB::commit();
            return redirect("/index");;
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }
	}

	public function store(Request $request)
    {
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        $messages = [
            'content.required' => 'Tiêu đề chưa nhập.',
            'note_date.required' => 'Ngày thực hiện chưa nhập.',
            'comment.required' => 'Mô tả chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'content' => 'required',
                    'note_date' => 'required',
                    'comment' => 'required',
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        }


        $note_date = DateTime::createFromFormat('d/m/Y', $request->note_date);
        $note_date = $note_date->format('Ymd');

        UserNote::create([
            'content'   =>  $request->input('content'),
            'note_date'   =>  $note_date,
            'comment' => $request->input('comment'),
            'user_id' => Auth::user()->id,
            'color' => $request->input('color_code'),
            'activation' => 1
        ]);

        return redirect()->route('home');
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
