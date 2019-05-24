@extends('layouts.shop')
@section('title','野心的珠宝商城')

@section('content')

<body>
<div class="maincont">
    <header>
        <a href="javascript:history.back(-1)" class="back-off fl"><span class="glyphicon glyphicon-menu-left"></span></a>
        <div class="head-mid">
            <form action="" method="get" class="prosearch">
                <tr>
                    <td >
                        <input type="text" name="goods_name" />
                    </td>
                </tr>

            </form>
        </div>
    </header>
    <ul class="pro-select">
        <li class="pro-selCur">
            <a href="javascript:;" class="default" field="is_new"  a_type='1'>新品</a></li>
        <li><a href="javascript:;" class="default"  field="goods_num" a_type='2'>销量</a></li>
        <li><a href="javascript:;" class="default" field="self_price" a_type='3'>价格</a></li>
    </ul><!--pro-select/-->
    <div class="prolist" id="show">
            @foreach($res as $k=>$v)
                <dl>
                    <dt><a href="/zhubao/proinfo/{{$v->goods_id}}"><img src="{{config('app.img_url')}}{{$v->goods_img}}" width="100" height="100" /></a></dt>
                    <dd>
                        <h3><a href="/zhubao/proinfo/{{$v->goods_id}}">{{$v->goods_name}}</a></h3>
                        <div class="prolist-price"><strong>¥{{$v->shop_price}}</strong> <span>¥{{$v->market_price}}</span></div>
                        <div class="prolist-yishou"><span>5.0折</span> <em>已售：35</em></div>
                    </dd>
                    <div class="clearfix"></div>
                </dl>
            @endforeach
    </div>
 @include('public/footer')

    <script src="{{asset('/js/jquery.js')}}"></script>

    <script>
        $(function(){
            $(document).on('click','.default',function () {
                var _default=$(this).attr('a_type');
                var field='';
                if(_default==1){
                    field='is_new';
                    // field=1;
                }else if(_default==2){
                    // field=2;
                    field='is_hot';
                }else{
                    // field=3;
                    field='shop_price';
                }
                alert(field);
                $.post(
                    "/zhubao/prolist",
                    field,
                    function (res) {
                        // $('#show').html(res);
                    }
                );
            })
        })
$(function(){

})
    </script>

@endsection