<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Mail;
use \Log; 

class IndexController extends Controller
{
	//电脑支付
	public function popay(){
		
	}
	//手机支付
	public function mobilepay(){
		
	}

    	//    首页
    public function index(Request $request){

//        查询分类
        $res=DB::table('category')->where(['is_nav_show'=>1])->get();
//        dd($res);
//        首页展示
        $appt=DB::table('goods')->where(['is_new'=>1])->get();
//        轮播图
        $data=DB::table('goods')->where(['is_hot'=>1])->select('goods_img','goods_id')->orderBy('goods_id','desc')->limit(4)->get();
//        dd($data);

            return view('index/index',compact('res','appt','data'));
    }
     
}
