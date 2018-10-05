<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Tenement;
use DB;
use yajra\Datatables\Datatables;
use Auth;
use Response;
use \stdClass;
use LynX39;

class FileController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('monthlyfee.file');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($year_month) {
        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement_id = Auth::user()->tenement_id;
        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $fullPath = "{$path}/{$tenement_id}/{$year_month}/paybill";
        if (!file_exists( $fullPath )) {
            mkdir($fullPath, 0777, true);
        }

        $filelist = scandir($fullPath);

        $filelistTmp = array_diff(scandir($fullPath), array('.', '..'));

        $data = array();
        $rows = array();
        $i = 1;

        foreach($filelistTmp as $file){
            list($year_month, $flat_code, $type) = explode('_', $file);
            $rows = array();
            array_push($rows, $i, $year_month, $flat_code, $file);
            array_push($data, $rows);
            $i++;
        }

        return View('report.paybillfiles',[ 
            'year_month'=> $year_month,
            'tenement_id'=> $tenement_id,
            'filelist'=> $filelist,
            'json'=> json_encode($data),
            ]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function download_paybill($tenement_id, $year_month, $file_name) {
        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $file = "{$path}/{$tenement_id}/{$year_month}/paybill/$file_name";

        return Response::download($file);
    }
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview_paybill($tenement_id, $year_month, $file_name) {
        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $file = "{$path}/{$tenement_id}/{$year_month}/paybill/$file_name";

        return Response::make(file_get_contents($file), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$file_name.'"'        
            ]); 
    }    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_pnfiles($year_month) {
        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement_id = Auth::user()->tenement_id;
        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $fullPath = "{$path}/{$tenement_id}/{$year_month}/paymentnotice";
        if (!file_exists( $fullPath )) {
            mkdir($fullPath, 0777, true);
        }

        $filelistTmp = array_diff(scandir($fullPath), array('.', '..'));

        $data = array();
        $rows = array();
        $i = 1;

        foreach($filelistTmp as $file){
            list($year_month, $flat_code, $type) = explode('_', $file);
            $rows = array();
            array_push($rows, $i, $year_month, $flat_code, $file);
            array_push($data, $rows);
            $i++;
        }
        return View('report.paymentnoticefiles',[ 
            'year_month'=> $year_month,
            'tenement_id'=> $tenement_id,
            'json'=> json_encode($data),
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function exe_merge($year_month) {
        $tenement_id = Auth::user()->tenement_id;
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement = Tenement::where('id',$tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $fullPath = "{$path}/{$tenement_id}/{$year_month}/paymentnotice";
        if (!file_exists( $fullPath )) {
            mkdir($fullPath, 0777, true);
        }

        $filelistTmp = array_diff(scandir($fullPath), array('.', '..'));

        $data = array();
        $rows = array();
        $i = 1;

        foreach($filelistTmp as $file){
            list($year_month, $flat_code, $type) = explode('_', $file);
            $rows = array();
            array_push($rows, $i, $year_month, $flat_code, $file);
            array_push($data, $rows);
            $i++;

            if ("paymentnotice.pdf" == $type)
                $pdf->addPDF($fullPath . '/' . $file, 'all','P');
        }

        $pdf->merge('file', $fullPath . '/' . $year_month . '_' . $tenement[0]->tenement_code . '_' . 'paymentnotice.pdf', 'P');

        return redirect('report.paymentnoticefiles',[ 
            'year_month'=> $year_month,
            'tenement_id'=> $tenement_id,
            'json'=> json_encode($data),
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function exe_merge_paybill($year_month) {
        $tenement_id = Auth::user()->tenement_id;
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement = Tenement::where('id',$tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $fullPath = "{$path}/{$tenement_id}/{$year_month}/paybill";
        if (!file_exists( $fullPath )) {
            mkdir($fullPath, 0777, true);
        }

        $filelistTmp = array_diff(scandir($fullPath), array('.', '..'));

        $data = array();
        $rows = array();
        $i = 1;

        foreach($filelistTmp as $file){
            list($year_month, $flat_code, $type) = explode('_', $file);
            $rows = array();
            array_push($rows, $i, $year_month, $flat_code, $file);
            array_push($data, $rows);
            $i++;

            if ("paybill.pdf" == $type)
                $pdf->addPDF($fullPath . '/' . $file, 'all','L');
        }

        $pdf->merge('file', $fullPath . '/' . $year_month . '_' . $tenement[0]->tenement_code . '_' . 'paybill.pdf', 'L');

        return redirect('report.paybill',[ 
            'year_month'=> $year_month,
            'tenement_id'=> $tenement_id,
            'json'=> json_encode($data),
        ]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function download_paymentnotice($tenement_id, $year_month, $file_name) {
        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $file = "{$path}/{$tenement_id}/{$year_month}/paymentnotice/$file_name";

        return Response::download($file);
    }
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview_paymentnotice($tenement_id, $year_month, $file_name) {
        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $file = "{$path}/{$tenement_id}/{$year_month}/paymentnotice/$file_name";

        return Response::make(file_get_contents($file), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'. $file_name .'"'        
            ]); 
    }     
}