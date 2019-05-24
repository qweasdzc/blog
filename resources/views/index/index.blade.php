@extends('layouts.shop')
@section('title','野心的珠宝商城')


@section('content')
     <div class="head-top">
      <img src="{{asset('/index/images/head.jpg')}}" />
      <dl>
       <dt><a href="user.html"><img src="{{asset('/index/images/touxiang.jpg')}}" /></a></dt>
       <dd>
        <ul><h1 class="username">三级分销终身荣誉会员</h1></ul>

        <ul>
          <li><a href=""><strong>34</strong><p>全部商品</p></a></li>
          <li><a href="javascript:;"><span class="glyphicon glyphicon-star-empty"></span><p>收藏本店</p></a></li>
          <li style="background:none;"><a href="javascript:;"><span class="glyphicon glyphicon-picture"></span><p>二维码</p></a></li>
          <div class="clearfix"></div>
        </ul>
       </dd>
       <div class="clearfix"></div>
      </dl>
     </div><!--head-top/-->
     <form action="/zhubao/prolist" method="get"  class="search">
      <input type="text" class="seaText fl" name="goods_name" />
      <button  class="seaSub fr">搜索</button>
     </form><!--search/-->
     <ul class="reg-login-click">
      <li><a href="/zhubao/login">登录</a></li>
      <li><a href="/zhubao/reg" class="rlbg">注册</a></li>
      <div class="clearfix"></div>
     </ul><!--reg-login-click/-->
     <div id="sliderA" class="slider">
      @foreach($data as $k=>$v)
       <a href="/zhubao/proinfo/{{$v->goods_id}}"><img src="{{config('app.img_url')}}{{$v->goods_img}}" /></a>

    @endforeach
     </div><!--sliderA/-->
     <ul class="pronav">
      @foreach($res as $k=>$v)
      <li><a href="/zhubao/prolist/{{$v->cate_id}}">{{$v->cate_name}}</a></li>
      @endforeach
      <div class="clearfix"></div>
     </ul><!--pronav/-->


     <div class="index-pro1">
      @foreach($appt as $k=>$v)
      <div class="index-pro1-list">
       <dl>
        <dt><a href="/zhubao/proinfo/{{$v->goods_id}}"><img src="{{config('app.img_url')}}{{$v->goods_img}}" /></a></dt>
        <dd class="ip-text"><a href="/zhubao/proinfo/{{$v->goods_id}}">{{$v->goods_name}}</a><span>已售：488</span></dd>
        <dd class="ip-price"><strong>¥{{$v->shop_price}}</strong> <span>¥{{$v->market_price}}</span></dd>
       </dl>
      </div>
      @endforeach
     </div><!--index-pro1/-->

     <div class="joins"><a href="fenxiao.html"><img src="{{asset('/index/images/jrwm.jpg')}}" /></a></div>
     <div class="copyright">Copyright &copy; <span class="blue">这是就是三级分销底部信息</span></div>
     @include('public/footer');
     @endsection