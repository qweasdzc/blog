<?php

namespace App\Http\Controllers\index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Brand;
use Mail;
use App\Goods;
use App\Talk;


class RegController extends Controller
{
//注册
    public function reg()
    {
        return view('reg.add');
    }


//登录
    public  function login(Request $request){
        if($request->Post()){
           $user_email=$request->user_email;
           $user_pwd=$request->user_pwd;

//账号非空
            if($user_pwd==''){
                return [
                    'msg'=>'密码不能为空',
                    'code'=>5
                ];
            }

//密码非空
            if($user_email==''){
                return [
                    'msg'=>'账号不能为空',
                    'code'=>5
                ];
            }
//邮箱
            $where2=[
                'u_email'=>$user_email
            ];
            $res=DB::table('user')->where($where2)->first();
            // dd($res);
            if(!$res){
                return [
                    'msg'=>'账号或密码有误',
                    'code'=>5
                ];
            }else{
                if(md5($user_pwd)==$res->u_pwd){
                //dump(11);
                    $user=[
                        'user'=>$user_email,
                        'u_id'=>$res->u_id
                    ];
                    $request->session()->put('userlogin',$user);
                    return [
                        'msg'=>'登录成功',
                        'code'=>6
                    ];
                }else{
                    return [
                        'msg'=>'登录失败',
                        'code'=>2
                    ];
                }
            }
        }else{
            return view('reg.login');
        }

    }


//个人中心
    public function user()
    {
        return view('user.user');
    }


//发送验证码
     public function doadd(Request $request)
    {
//邮箱发送验证码
        $user_email=$request->u_email;
        //echo $user_email;die;
        if(!$user_email){
            $this->error('请填写邮箱');die;
        }
    $str=DB::table('user')->where(['u_email'=>$user_email])->first();
        if($str){
                return [
                    'msg'=>'邮箱已注册',
                    'code'=>5
                ];
        }else{
//生成随机数
            $code = rand(100000, 999999);
            $res=Mail::send('reg/shitu',['code'=>$code], function ($message) use ($user_email) {
//设置主题
                $message->subject("珠宝微商城");
//设置接受方
                $message->to($user_email);
            });
            if(!$res){
                //先清除session
                $request->session()->forget('Email');
                //存session
                $request->session()->put('Email',['code'=>$code,'u_email'=>$user_email]);
                return [
                    'msg'=>'发送成功',
                    'code'=>6
                ];
            }
        }
    }


//注册执行
    public function doadddo (Request $request)
    {
           $user_email=$request->user_email;
            $user_pwd=$request->user_pwd;
            $code=$request->code;
            $type=$request->type;
            if($type==2){
            //取出session
                $session=$request->session()->get('Email');
                // dd($session);
                if($code!=$session['code']){
                  return [
                      'msg'=>'验证码不正确',
                      'code'=>5
                  ];
                }
                $res=DB::table('user')->insert(
                    ['u_email' => $user_email, 'u_pwd'=> md5($user_pwd)]
                );
                if($res){
                    return [
                        'msg'=>'注册成功',
                        'code'=>6
                    ];
                }

            }else{
                //取出session
                $session=$request->session()->get('Tel');
                if($code!=$session['code']){
                    return [
                        'msg'=>'验证码不正确',
                        'code'=>5
                    ];
                }
                $res=DB::table('user')->insert(
                    ['u_email' => $user_email, 'u_pwd' => md5($user_pwd)]
                );
                if($res){
                    return [
                        'msg'=>'注册成功',
                        'code'=>6
                    ];
                }
            }

    }

//全部商品
    public function prolist($goods_id=0){
        // echo 111; die;
        $goods_name=\request()->goods_name;
        $cate_id=\request()->id;
        if($cate_id==0){
            $res=cache('res_'.$cate_id);
            $res=cache('ress'.$cate_id);
            //dump(8);
            if(!$res) {
                //dump(3);
//搜索
                if ($goods_name) {
                    //dump(1);
                    $where = [
                        ['goods_name', 'like', "%$goods_name%"]
                    ];
                    $res = DB::table('goods')->where($where)->get();
                    cache(['ress'.$cate_id => $res], 1);
                } else {
                    //dump(2);
                    $res = DB::table('goods')->get();

                }
                cache(['res_' . $cate_id => $res], 1);
            }
                return view('prolist.prolist', compact('res'));

       }else {
            $res = cache('res_' . $cate_id);
            if (!$res) {
                $cateInfo = DB::table('category')->get();
//获取所有子类id--递归
                $ppt = $this->getCateId($cateInfo, $cate_id);
                $res = DB::table('goods')
                    ->whereIn('cate_id', $ppt)
                    ->get();
                cache(['res_' . $cate_id => $res], 60 * 24);
            }
            return view('prolist.prolist', compact('res'));
        }

    }
//商品详情
    public  function proinfo ($id){
        
        if($id){
            //echo 111;die;
            $ress=DB::table('goods')->where('goods_id',$id)->first();
            $imgs=explode('|',rtrim($ress->goods_img,'|'));
            return view('prolist/proinfo',compact('ress','imgs'));
        }
    }
    
//加入购物车
    public function cartt(Request $request){
            $goods_id=$request->goods_id;
            $buy_number=$request->byu_num;

        //取session
        $session= $request->session()->get('userlogin');
        $user_id=$session['u_id'];
        //判断是否登录
        //if(empty($session)) {
            //return [
                //'msg' => '请先登录',
                //'code' => 5
            //];
        //}
//商品id非空
            if(empty($goods_id)){
                return  [
                    'msg'=>'请选择一件商品',
                    'code'=>2
                ];
            }
//购买数量非空
        if(empty($buy_number)){
            return  [
                'msg'=>'购买数量能为空',
                'code'=>2
            ];
        }

        $whereinfo=[
                'goods_id'=>$goods_id,
                'u_id'=>$user_id
        ];
        $ress=DB::table('cart')->where($whereinfo)->first();
//查询到做累加
            if($ress){
//检测库存
                $appt=$this->checkGoodsNum($goods_id,$ress->buy_number,$buy_number);
                if($appt==true){
                    $where=[
                        'buy_number'=>$buy_number+$ress->buy_number,
                        'update_time'=>time()
                    ];

                    $attr=DB::table('cart')
                        ->where($whereinfo)
                        ->update($where);
                    if($attr){
                        return[
                            'msg'=>'加入购车成功',
                            'code'=>6
                        ];
                    }else{
                        return[
                            'msg'=>'加入购车失败',
                            'code'=>5
                        ];
                    }
                }
            }else{
                $where=[
                    'goods_id'=>$goods_id,
                    'u_id'=>$user_id,
                    'buy_number'=>$buy_number,
                    'create_time'=>time(),
                    'update_time'=>time()
                ];
                $resss=DB::table('cart')->insert($where);
                   if($resss){
                       return[
                           'msg'=>'加入购车成功',
                           'code'=>6
                       ];
                   }else{
                       return[
                           'msg'=>'加入购车失败',
                           'code'=>5
                       ];
                   }
            }
        }

//购车展示
    public function car(Request $request){
        //取session
        $session= $request->session()->get('userlogin');
        $user_id=$session['u_id'];
        $where=[
            'u_id'=>$user_id,
        ];
        $res=DB::table('goods as g')
            ->join("cart as c",'g.goods_id','=','c.goods_id')
            ->where($where)
            ->get();
        //$res=array_reverse($res,desc);
       $data= DB::table('cart')->where($where)->count();
        if($res){
            return view('car/car',compact('res','data'));
        }

    }

//检测库存
    public function checkGoodsNum($goods_id,$num,$buy_number){
        //dump(111);die;
        $goodsWhere=[
            'goods_id'=>$goods_id
        ];
        $arr=DB::table('goods')->where($goodsWhere)->value('goods_number');
        //dump($arr);die;
        if(($buy_number+$num)>$arr){
            $n=$buy_number-$num;
            echo ( "购买的数量超过库存，您还可以购买'.$n.'件");
            return false;
        }else{
            return true;
        }
    }

//购物车小计
        public function xiaoji(Request $request){
        $goods_id=$request->all();
            if(empty($goods_id)){
                echo 0;
            }
        }

//更改购买数据
    public function chdckbuynumber(Request $request){
        $goods_id=$request->goods_id;
        $buy_number=$request->buy_number;
        //取session
        $session= $request->session()->get('userlogin');
        $user_id=$session['u_id'];
        $appt=$this->checkGoodsNum($goods_id,$buy_number,0);
        if($appt==true){
            $where=[
                'goods_id'=>$goods_id,
                'u_id'=>$user_id
            ];
            $up=[
                'buy_number'=>$buy_number,
                'update_time'=>time()
            ];
            $res=DB::table('cart')
                ->where($where)
                ->update($up);
        }else{
            return[
                'msg'=>'购买数量超出库存',
                'code'=>5
            ];
        }
    }

//获取总价
    public function counttotal(){
        $goods_id=request()->goods_id;
        //取session
        $session= request()->session()->get('userlogin');
        $user_id=$session['u_id'];
        $where=[
            'u_id'=>$user_id
        ];
        $goods_id=explode(',',$goods_id);
        $ress=DB::table('cart as c')
            ->select('buy_number','shop_price','c.goods_id')
            ->join('goods as g','c.goods_id','=','g.goods_id')
            ->where($where)
            ->get();
        $count=0;
        foreach ($ress as $k=>$v){
            foreach ($goods_id as $key=>$val){
                if($v->goods_id==$val){
        //dump($v);
                    $count+=$v->buy_number*$v->shop_price;
                }
            }

        }
        return $count;
    }


//删除购物车商品
    public function delete(){
        $goods_id=request()->goods_id;
        $session= request()->session()->get('userlogin');
        $user_id=$session['u_id'];
        $goods_id=explode(',',$goods_id);
        //dd($goods_id);
        $where=[
            'u_id'=>$user_id,
        ];
        //dd($where);
        $ress=DB::table('cart')
            ->where($where)->whereIn('goods_id',$goods_id);
        //dd($ress);
        if($ress){
            return [
                'msg'=>'删除成功',
                'code'=>6
            ];
        }else{
            return [
                'msg'=>'删除失败',
                'code'=>5
            ];
        }
    }

//结算前 判断是否登录
    public  function check(){
        $session=\request()->session()->get('userlogin');
        $user_id=$session['u_id'];
        if($user_id){
            echo 1;
        }else{
            echo 2;
        }
    }


//购物车结算
    public function paysubmit(){
        $goods_id=\request()->goods_id;
        //取session
        $session= request()->session()->get('userlogin');
        $user_id=$session['user_id'];

        if(empty($goods_id)){
            return[
                'msg'=>'请选择一件商品',
                'code'=>5
            ];
        }
    }


//收货地址
    public function address(){
        $session=\request()->session()->get('userlogin');
        $user_id=$session['u_id'];
        $where=[
            'u_id'=>$user_id
        ];
        $ress=DB::table('address')->where($where)->get();
        $arr = json_decode(json_encode($ress),true);
        if(!empty($arr)){
            foreach ($arr as $k=>$v){
                $arr[$k]['province']=DB::table('area')->where(['id'=>$v['province']])->value('name');
                $arr[$k]['city']=DB::table('area')->where(['id'=>$v['city']])->value('name');
                $arr[$k]['area']=DB::table('area')->where(['id'=>$v['area']])->value('name');
            }
                //dump($arr);
            $arr = json_decode(json_encode($arr));
        }
        return view('add.address',compact('arr'));
    }


//修改收货地址
    public function addresss($id){
        if(empty($id)){
            return [
                'msg'=>'请选择修改的地址',
                'code'=>5
            ];
        }
        $ress=DB::table('address')->where( 'address_id',$id)->first();
//三级联动
        $cartInfo=$this->getAreaInfo(0);
//市
        $city=$this->getAreaInfo($ress->province);
//区县
        $area=$this->getAreaInfo($ress->city);
        return view('add/addresss',compact('cartInfo','ress','city','area'));
    }


//修改地址执行
    public function addsubmitdo(){
        $ress=\request()->all();
        //取session
        $session= \request()->session()->get('userlogin');
        $user_id=$session['u_id'];
        if($ress['is_default']==1){
            $res=DB::table('address')->where('address_id',$ress['address_id'])->update($ress);
            if($res){
                $resss=DB::table('address')->where('u_id',$user_id)->where('address_id','!=',$ress['address_id'])->update(['is_default'=>2]);
                return [
                    'msg'=>'修改成功',
                    'code'=>6
                ];
            }

        }else{
            $res=DB::table('address')->where('address_id',$ress['address_id'])->update($ress);
            if($res){
                return [
                    'msg'=>'修改成功',
                    'code'=>6
                ];
            }else{
                return [
                    'msg'=>'修改失败',
                    'code'=>5
                ];
            }
        }
    }


//新增收货地址
    public function addressdo(){
//三级联动 省
        $cartInfo=$this->getAreaInfo(0);
        return view('add.addressdo',compact('cartInfo'));
    }


//三级联动
    public function getAreaInfo($pid){
        $where=[
                'pid'=>$pid
        ];
        $ress=DB::table('area')->where($where)->get();
        if($ress){
            return $ress;
        }else{
            return false;
        }
    }


//二级联动
    public function att(){
        $id=\request()->id;
        if(empty($id)){
            return [
                'msg'=>'选择一个',
                'code'=>5
            ];
        }
        $where=[
            'pid'=>$id
        ];
       $attp=$this->getAreaInfo($id);
        return $attp;
    }


//添加收货地址
    public  function addsubmit(){
        $res=\request()->all();
        //取session
        $session= \request()->session()->get('userlogin');
        $res['u_id']=$session['u_id'];
        $res['create_time']=time();
        if(empty($res['u_id'])){
            return [
                'msg'=>'请先登录',
                'code'=>2
            ];
        }
        $where=[
            'u_id'=>$res['u_id']
        ];
        //dd($res);
//判断有没有此用户
       $resss= DB::table('address')->where($where)->first();
       if($resss !=''){
           if($res['is_default']==1){
               DB::table('address')->where('u_id',$res['u_id'])->update(['is_default'=>2]);
           }
           $app=DB::table('address')->insert($res);

           if($app){
               return [
                   'msg'=>'添加成功',
                   'code'=>6
               ];
           }else{
               return [
                   'msg'=>'添加失败',
                   'code'=>5
               ];
           }
       }else{
           $app=DB::table('address')->insert($res);
           if($app){
               return [
                   'msg'=>'添加成功',
                   'code'=>6
               ];
           }else{
               return [
                   'msg'=>'添加失败',
                   'code'=>5
               ];
           }
       }
    }


//去结算
    public function pay(){
        $goods_id=\request()->goods_id;
        //取session
        $session= request()->session()->get('userlogin');
        $user_id=$session['u_id'];
        $goods_id=explode(',',$goods_id);
        $where=[
            'u_id'=>$user_id,
        ];
        //DB::table('shop_cart')->whereIn()
       $app= DB::table('cart as c')
            ->join("goods as g",'c.goods_id','=','g.goods_id')
            ->where($where)
           ->whereIn('c.goods_id',$goods_id)
            ->get();
       $count=0;
       foreach ($app as $k=>$v){
           foreach ($goods_id as $key=>$val){
               if($v->goods_id==$val){
                   $count+=$v->buy_number*$v->shop_price;
               }
           }
       }
       $where1=[
           'u_id'=>$user_id
           ,'is_default'=>1
       ];
            //$data=DB::table('shop_address')->where($where1)->first();
            //$data=json_decode(json_encode($data),true);
               //dump($data);
            //三级联动
            //$data['province']=$this->getAreaInfo(0);
            //市
            //$data['city']=$this->getAreaInfo($data->province);
            //区县
            //data['area']=$this->getAreaInfo($data->city);
                //dd($data);
        return view('pay.pay',compact('app','count'));
    }


//订单号生成
    public function createOrderNo(){
        //取session
        $session= request()->session()->get('userlogin');
        $user_id=$session['u_id'];
        return time('Ymd').rand(1000,9999).$user_id;
    }


//订单总金额
    public function getOrderAmount($goods_id){
        //取session
        $session= request()->session()->get('userlogin');
        $user_id=$session['u_id'];
        $goods_id=explode(',',$goods_id);
        $where=[
            'u_id'=>$user_id
        ];
        //DB::table('shop_cart')->whereIn()
        $app= DB::table('cart as c')
            ->join("goods as g",'c.goods_id','=','g.goods_id')
            ->where($where)
            ->whereIn('c.goods_id',$goods_id)
            ->get();
        $count=0;
        foreach ($app as $k=>$v){
            foreach ($goods_id as $key=>$val){
                if($v->goods_id==$val){
                    $count+=$v->buy_number*$v->shop_price;
                }
            }
        }
        return $count;
    }


//提交订单
    public function successsubmit(){
        $goods_id=\request()->goods_id;
        //取session
        $session= request()->session()->get('userlogin');
        $user_id=$session['u_id'];
        if(empty($user_id)){
                return [
                    'msg'=>'请先登录',
                    'code'=>5
                ];
        }
        if(empty($goods_id)){
            return [
                'msg'=>'请选择下单的商品',
                'code'=>2
            ];
        }

//启动事务
        DB::beginTransaction();
        try{
//订单号
            $order_no=$this->createOrderNo();    //    订单号生成
            //dd($order_no);
            $order_amount=$this->getOrderAmount($goods_id);//总金额
            $cartInfo['order_no']=$order_no;
            $cartInfo['order_acount']=$order_amount;
            //dd($cartInfo['order_acount']);
            $cartInfo['u_id']=$user_id;
            $cartInfo['create_time']=time();
            $cartInfo['update_time']=time();
            //dd($cartInfo);
            $res1=DB::table('order')->insertGetId($cartInfo);
            if(empty($res1)){
                throw new \Exception('订单详情信息写入失败');
            }

//订单详情
//订单详情表添加 订单id（获取刚刚添加的自增id）
//获取刚刚订单表添加的id
            $order_id=$res1;
            $where=[
                'is_default'=>1,
                'u_id'=>$user_id
            ];
            $order_address=DB::table('address')
                ->where($where)
                ->select('address_name','address_tel','address_detail','province','city','area')
                ->first();
            $order_address->order_id=$order_id;
            $order_address->u_id=$user_id;
            $order_address->create_time=time();
            $order_address->update_time=time();
            $order_address=json_decode(json_encode($order_address),true);
            $res2=DB::table('order_address')->insertGetId($order_address);
            if(empty($res2)){
                throw new \Exception('订单收货地址写入失败');
            }

//订单商品详情
            $goods_id=explode(',',$goods_id);
            $where=[
                'u_id'=>$user_id
            ];
            $data=DB::table('cart as c')
                ->select('buy_number','g.goods_id','shop_price','goods_name','goods_img')
                ->join("goods as g",'c.goods_id','=','g.goods_id')
                ->where($where)
                ->get();

//添加字段
            $date=[];
                foreach ($data as $k=>$v){
                    foreach ($goods_id as $key=>$val){
                        if($v->goods_id==$val){
                            $v->order_id=$order_id;
                            $v->create_time=time();
                            $v->update_time=time();
                            $v->u_id=$user_id;
                             $date[]=$v;
                        }
                    }
                }

//对象转换成数组
                $date=json_decode(json_encode($date),true);
                //dd($date);
                $res3=DB::table('order_detail')->insertGetId($date);
            if(!$res3){
                throw new \Exception('订单商品详情写入失败');
            }

//减少商品购买的数量
            //取session
            $session= request()->session()->get('userlogin');
            $user_id=$session['u_id'];
            $ress=DB::table('cart')
                ->select('cart.buy_number','cart.goods_id','goods.goods_number')
                ->join('goods','cart.goods_id','=','goods.goods_id')
                ->where(['u_id'=>$user_id])
                ->get();
            foreach ($ress as $k=>$v){
                foreach($goods_id as $key=>$val){
                    if($v->goods_id ==$val){
                        $v->goods_number =$v->goods_number - $v->buy_number;
                        $res = DB::table('goods')->where('goods_id',$val)->update(['goods_number'=>$v->goods_number]);
                    }
                }
            }

//删除购物车数据
            $goods_id=\request()->goods_id;
            //取session
            $session= request()->session()->get('userlogin');
            $user_id=$session['u_id'];
            $s_id=explode(',',$goods_id);
           // $where=[
           //     'goods_id'=>$s_id,
           //     'u_id'=>$user_id
           // ];
            if(strpos($goods_id,',')==false){
                $res4=DB::table('cart')->where('goods_id',$goods_id);
            }else{
                $res4=DB::table('cart')->whereIn( 'goods_id',$s_id);
            }
            if($res4==0){
                throw new \Exception('购物车删除失败'); 
            }

//提交事务
             DB::rollback();
            return [
                'code'=>5,
                'font'=>'下单失败'
            ];       
        }catch (\Exception $e) {
        //dump($e->getMessage());
           DB::commit();
            return [
                'code'=>6, 
                'font'=>'下单成功',
                'order_id'=>$order_id
            ];
        }
    }

//订单展示页面
    public  function success($id){
            $res=DB::table('order')->where('order_id',$id)->first();
        return view('pay.success',compact('res'));
    }

}
