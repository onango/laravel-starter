<?php

namespace App\Repositories\Auth\Module;

use LaravelEasyRepository\Repository;

/*
|--------------------------------------------------------------------------
| afyaPacs Dev
| Backend Developer : ibudirsan
| Email             : ibnudirsan@gmail.com
| Copyright © AfyaPacs 2022
|--------------------------------------------------------------------------
*/

interface ModuleDesign extends Repository
{
    public function datatables();
    public function store($param);
    public function edit($id);
    public function update($param, $id);
    public function trash($id);
    public function trashedfirst($id);
    public function restore($id);
    public function delete($id);
}
