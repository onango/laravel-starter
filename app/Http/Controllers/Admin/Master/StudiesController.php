<?php

namespace App\Http\Controllers\Admin\Master;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use App\Http\Controllers\Controller;
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

class StudiesController extends Controller
{

    protected $StudyResponse;
    public function __construct(StudyResponse  $StudyResponse)
    {
        $this->StudyResponse  = $StudyResponse;
        $this->middleware('permission:Customer Show',       ['only' => ['index']]);
        $this->middleware('permission:Customer Create',     ['only' => ['create', 'Store']]);
        $this->middleware('permission:Customer Edit',       ['only' => ['edit', 'update']]);
        $this->middleware('permission:Customer Trash',      ['only' => ['trashData', 'Trash']]);
        $this->middleware('permission:Customer Excel',      ['only' => ['downloadExcel']]);
        $this->middleware('permission:Customer Restore',    ['only' => ['RestoreData']]);
        $this->middleware('permission:Customer Delete',     ['only' => ['delete']]);
    }

    /**
     * List Data Customer
     */
    public function index(Request $request)
    {
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

    /**
     * Process moving data Trash
     */
    public function trashData($id)
    {
        try {
            $this->StudyResponse->trashedData($id);
            $success = true;
        } catch (\Exception $e) {
            $message = "Failed to moving data Trash";
            $success = false;
        }
        if ($success == true) {
            /**
             * Return response true
             */
            return response()->json([
                'success' => $success
            ]);
        } elseif ($success == false) {
            /**
             * Return response false
             */
            return response()->json([
                'success' => $success,
                'message' => $message,
            ]);
        }
    }

    /**
     * List Data Trash Customer.
     */
    public function Trash(Request $request)
    {
        if ($request->ajax()) {
            $result = $this->StudyResponse->datatable()
                ->onlyTrashed();
            return DataTables::of($result)
                ->addIndexColumn(['address'])

                ->addColumn('action', function ($action) {
                    if (auth()->user()->can('Customer Delete')) {
                        $Delete =   '
                                                <button type="button" class="btn btn-danger btn-sm btn-size"
                                                        onclick="isDelete(' . $action->id . ')">
                                                        Delete
                                                </button>
                                            ';
                    } else {
                        $Delete = '';
                    }

                    if (auth()->user()->can('Customer Restore')) {
                        $Restore    =   '
                                                    <button type="button" class="btn btn-success btn-sm btn-size"
                                                            onclick="isRestore(' . $action->id . ')">
                                                                Restore
                                                    </button>
                                                ';
                    } else {
                        $Restore    = '';
                    }
                    return $Delete . " " . $Restore;
                })

                ->editColumn('age', function ($age) {
                    return $age->age . " Years";
                })

                ->editColumn('deleted_at', function ($deleted) {
                    $date = Carbon::create($deleted->deleted_at)->format('Y-m-d H:i:s');
                    return $date;
                })

                ->rawColumns(['action'])
                ->escapeColumns(['action'])
                ->smart(true)
                ->make();
        }
        return view('master.studies.trash');
    }

    /**
     * View Create Data Customer.
     */
    public function create()
    {
        return view('master.studies.create');
    }

    /**
     * Process Create Data Customer.
     */
    public function Store(customerRequest $request)
    {
        try {
            $this->StudyResponse->create($request);
            $notification = [
                'message'      => 'Successfully created new customer.',
                'alert-type'  => 'primary',
                'gravity'     => 'bottom',
                'position'    => 'right'
            ];
            return redirect()->route('customer.index')->with($notification);
        } catch (\Exception $e) {

            $notification = [
                'message'     => 'Failed to created data new customer.',
                'alert-type'  => 'danger',
                'gravity'     => 'bottom',
                'position'    => 'right'
            ];
            return redirect()->route('customer.index')->with($notification);
        }
    }

    /**
     * View Edit Data Customer.
     */
    public function edit($id)
    {
        $result = $this->StudyResponse->edit($id);
        return view('master.studies.edit', compact('result'));
    }

    /**
     * Process Edit Data Customer.
     */
    public function update(editRequest $request, $id)
    {
        try {
            $this->StudyResponse->update($request, $id);
            $notification = [
                'message'      => 'Successfully updateed customer.',
                'alert-type'  => 'success',
                'gravity'     => 'bottom',
                'position'    => 'right'
            ];
            return redirect()->route('customer.index')->with($notification);
        } catch (\Exception $e) {
            $notification = [
                'message'     => 'Failed to updated data customer.',
                'alert-type'  => 'danger',
                'gravity'     => 'bottom',
                'position'    => 'right'
            ];
            return redirect()->route('customer.index')->with($notification);
        }
    }

    /**
     * Process moving Restore data customer.
     */
    public function RestoreData($id)
    {
        try {
            $this->StudyResponse->restore($id);
            $success = true;
        } catch (\Exception $e) {
            $message = "Failed to moving Restore data customer.";
            $success = false;
        }
        if ($success == true) {
            /**
             * Return response true
             */
            return response()->json([
                'success' => $success
            ]);
        } elseif ($success == false) {
            /**
             * Return response false
             */
            return response()->json([
                'success' => $success,
                'message' => $message,
            ]);
        }
    }

    /**
     * Process Delete Permanent Data Customer.
     */
    public function delete($id)
    {
        try {
            $this->StudyResponse->deletePermanent($id);
            $success = true;
        } catch (\Exception $e) {
            $message = "Failed to Delete Permanent data customer Trash";
            $success = false;
        }
        if ($success == true) {
            /**
             * Return response true
             */
            return response()->json([
                'success' => $success
            ]);
        } elseif ($success == false) {
            /**
             * Return response false
             */
            return response()->json([
                'success' => $success,
                'message' => $message,
            ]);
        }
    }

    /**
     * Process Report Data Excel Customer.
     */
    public function downloadExcel()
    {
        /** 
         * Maximum Time Setting 1800 seconds
         * (30 Minutes)
         * */
        ini_set('max_execution_time', 1800);
        date_default_timezone_set('Asia/Jakarta');
        $date       = date('Y-m-d-H-i-s');
        return Excel::download(new CustomersExport(), "Customers-$date.xlsx");
    }
}
