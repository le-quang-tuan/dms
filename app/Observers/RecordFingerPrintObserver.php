<?php 

<?php 

namespace App\Observers;

class RecordFingerPrintObserver {

    $userID;

    public function __construct($userID){
        $user = Auth::user();
        $this->userID = $user->getId();
    }

    public function saving($model)
    {
        $model->updated_by = $this->userID;
    }

    public function saved($model)
    {
        $model->updated_by = $this->userID;
    }


    public function updating($model)
    {
        $model->updated_by = $this->userID;
    }

    public function updated($model)
    {
        $model->updated_by = $this->userID;
    }


    public function creating($model)
    {
        $model->created_by = $this->userID;
    }

    public function created($model)
    {
        $model->created_by = $this->userID;
    }


    public function removing($model)
    {
        $model->updated_by = $this->userID;
    }

    public function removed($model)
    {
        $model->updated_by = $this->userID;
    }
}