<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
class userController extends Controller
{
    //
    protected $data = array(
        'title' => 'User',
        'module' => 'User'
    );
    public function index()
    {
        # code...
        $data = $this->data;
        $data['data'] = User::all();
        return view('admin.users.index',$data);
    }public function create()
    {
        # code...

    }
}
