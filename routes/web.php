<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('index/index');
// });

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/popay', 'Index\IndexController@popay');

Route::get('/mobilepay', 'Index\IndexController@mobilepay');
//首页
Route::get('/', 'Index\IndexController@index');

Route::prefix('/zhubao')->group(function(){
	//注册
	Route::any('/reg','Index\RegController@reg');
	//发送验证码
	Route::post('doadd', 'Index\RegController@doadd');
	//注册执行
	Route::post('doadddo', 'Index\RegController@doadddo');
	//登录
	Route::any('login','Index\RegController@login');
	//分类商品
    Route::any('/prolist/{goods_id}','Index\RegController@prolist');
    //所有商品
    Route::any('/prolist','Index\RegController@prolist');
    //商品详情
    Route::any('/proinfo/{goods_id}','Index\RegController@proinfo');
    //加入购物车
    Route::post('/cartt','Index\RegController@cartt');
    //购车展示
    Route::any('/car','Index\RegController@car');
    //购车小计
    Route::any('/xiaoji','Index\RegController@xiaoji');
	//更改购买数据
    Route::any('/chdckbuynumber','Index\RegController@chdckbuynumber');
	//获取总价
    Route::any('/counttotal','Index\RegController@counttotal');
    //删除商品
    Route::any('/delete','Index\RegController@delete');
    //结算前 判断是否登录
    Route::any('/check','Index\RegController@check');
	//购物车去结算
    Route::get('/carsubmit/{goods_id}','Index\RegController@pay');
    //个人中心
	Route::any('/user','Index\RegController@user');
    //收货地址
    Route::any('/address','Index\RegController@address');
	//新增收货地址
    Route::any('/addressdo','Index\RegController@addressdo');
	//二级联动
    Route::any('/att','Index\RegController@att');
	//添加收货地址
    Route::any('/addsubmit','Index\RegController@addsubmit');
	//修改收货地址
    Route::any('/addresss/{id}','Index\RegController@addresss');
	//修改执行
    Route::any('/addsubmitdo','Index\RegController@addsubmitdo');
    //提交订单
    Route::any('/successsubmit','Index\RegController@successsubmit');
	//订单详情页面
    Route::any('/success/{order_id}','Index\RegController@success');
    //评论
    Route::any('talkAdd','Index\RegController@talkAdd');

    
    
});

