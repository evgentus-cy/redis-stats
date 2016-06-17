<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use \App\Stat;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Stat::addStat();
        
        $data['platforms'] = Stat::getStatByPlatforms();
        $data['browsers'] = Stat::getStatByBrowsers();
        $data['countries'] = Stat::getStatByCountries();
        $data['referers'] = Stat::getStatByReferers();
        
        return view('home')->with($data);
    }
}
