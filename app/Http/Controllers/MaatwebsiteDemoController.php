<?php

namespace App\Http\Controllers;

use Input;

use App\Models\Item;
use DB;

use Excel;

class MaatwebsiteDemoController extends Controller

{

    public function importExport()

    {

        return view('importExport');

    }

    public function downloadExcel()

    {

        $data = Item::get()->toArray();

        return Excel::create('itsolutionstuff_example', function($excel) use ($data) {

            $excel->sheet('mySheet', function($sheet) use ($data)

            {

                $sheet->fromArray($data);

            });

        })->download("xls");

    }

    public function importExcel()

    {

        if(Input::hasFile('import_file')){

            $path = Input::file('import_file')->getRealPath();

            $data = Excel::load($path, function($reader) {

            })->get();

            if(!empty($data) && $data->count()){

                foreach ($data as $key => $value) {

                    $insert[] = ['title' => $value->title, 'description' => $value->description];

                }
                if(!empty($insert)){

                    DB::table('items')->insert($insert);

                    dd('Insert Record successfully.');

                }

            }

        }

        return back();

    }

}
