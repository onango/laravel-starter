<?php

namespace App\Repositories\Study;

/*
|--------------------------------------------------------------------------
| afyaPacs Dev
| Backend Developer : ibudirsan
| Email             : ibnudirsan@gmail.com
| Copyright © AfyaPacs 2022
|--------------------------------------------------------------------------
*/

interface StudyDesign
{
    public function datatable();
    public function create($param);
    public function edit($id);
    public function update($param, $id);
    public function trashedData($id);
    public function restore($id);
    public function deletePermanent($id);
}
