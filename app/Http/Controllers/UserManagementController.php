<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use DB;
use File;
use Image;
use Storage;
use Auth;
use \cbp\User;


class UserManagementController extends Controller
{


    public function index()
    {
        return view('layouts.admin'); 
    }

    public function allUsers(Request $r){

        

        $main_qry = DB::table('users')
            ->select('*');

        if ( $r->input('client') ) {
            return $main_qry->orderby('email')
                            ->get();
        }

        $columns = [
                'id',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
                'is_admin'
            ];


        $length = $r->input('length');
        $column = $r->input('column'); //Index
        $dir = $r->input('dir');
        $searchValue = $r->input('search');
        $searchStartDt = empty($r->input('dtStart'))? false:$r->input('dtStart');
        $searchEndDt = empty($r->input('dtEnd'))? false:$r->input('dtEnd');
        
        $query = $main_qry->orderBy($columns[$column], $dir);


        if ($searchValue && $searchStartDt && $searchEndDt) {
            $query->where(function($query) use ($searchValue, $searchStartDt, $searchEndDt) {

               $query->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), '>=', $searchStartDt)
                        ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), '<=', $searchEndDt)
                        ->where(function($query) use ($searchValue) {
                            $query->where('email', 'like', '%' . $searchValue . '%')
                                  ->orWhere('name', 'like', '%' . $searchValue . '%');
                        });
            });
        }
        else if ($searchValue && ($searchStartDt==false || $searchEndDt==false)) {
            $query->where(function($query) use ($searchValue) {
                 $query->where('name', 'like', '%' . $searchValue . '%')
                      ->orWhere('email', 'like', '%' . $searchValue . '%');
            });
        }
        else if (!$searchValue && ($searchStartDt==true && $searchEndDt==true)) {
            $query->where(function($query) use ($searchStartDt, $searchEndDt) {
                $query->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), '>=', $searchStartDt)
                      ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), '<=', $searchEndDt);
            });
        }


        $users = $query->paginate($length);
        //dd($shipment);
        return ['status' => true,'data' => $users, 'draw' => $r->input('draw')];

    }

}

