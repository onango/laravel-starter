<?php

namespace App\Http\Controllers\Admin\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Auth\Admin\AdminResponse;
use App\Repositories\Customer\CustomerResponse;

use Carbon\Carbon;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\customer\editRequest;
use App\Http\Requests\customer\customerRequest;
use App\Repositories\Study\StudyResponse;

/*
|--------------------------------------------------------------------------
| afyaPacs Dev
| Backend Developer : ibudirsan
| Email             : ibnudirsan@gmail.com
| Copyright © AfyaPacs 2022
|--------------------------------------------------------------------------
*/

class dashboardController extends Controller
{
    protected $CustomerResponse;
    protected $AdminResponse;
    protected $StudyResponse;


    public function __construct(StudyResponse  $StudyResponse, CustomerResponse $CustomerResponse, AdminResponse $AdminResponse)
    {
        $this->CustomerResponse = $CustomerResponse;
        $this->AdminResponse    = $AdminResponse;
        $this->StudyResponse  = $StudyResponse;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // $customer   = $this->CustomerResponse->datatable()->count();
        // $user       = $this->AdminResponse->datatable()->count();
        // return view('home', compact('customer', 'user'));
        if ($request->ajax()) {
            $result = $this->StudyResponse->datatable();
            return DataTables::eloquent($result)
                ->addIndexColumn(['address'])

                ->addColumn('action', function ($action) {

                    if (auth()->user()->can('Customer Trash')) {
                        $Delete =   '
                                                        <button type="button" class="btn btn-danger btn-sm btn-size"
                                                                onclick="isDelete(' . $action->id . ')">
                                                                Trash
                                                        </button>
                                                    ';
                    } else {
                        $Delete =   '';
                    }

                    if (auth()->user()->can('Customer Edit')) {
                        $Edit   =   '
                                                        <a href="' . url(route('customer.edit', $action->uuid)) . '" type="button" class="btn btn-success btn-sm btn-size">
                                                                    Edit
                                                        </a>
                                                    ';
                    } else {
                        $Edit   =   '';
                    }
                    return $Edit . " " . $Delete;
                })

                ->editColumn('age', function ($age) {
                    return $age->age . " Years";
                })

                ->editColumn('created_at', function ($created) {
                    $date = Carbon::create($created->created_at)->format('Y-m-d H:i:s');
                    return $date;
                })

                ->rawColumns(['action'])
                ->escapeColumns(['action'])
                ->smart(true)
                ->make();
        }
        return view('master.studies.index');
    }
}
