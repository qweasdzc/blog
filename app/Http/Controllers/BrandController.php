<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests\StoreBlogPost;
use App\Brand;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
class BrandController extends Controller
{

    
    

    /**
     * Display a listing of the resource.
     *展示
     * @return \Illuminate\Http\Response
     */
    
    public function sendemail()
    {
         $email=request()->email;
        $password=request()->password;
        if (Auth::attempt(['name' => $email, 'password' => $password])) {
               
            echo "ok";
           }else{
               echo "no";
           }
    
         //$this->send($email);
    }
    public function send($email)
    {
        \Mail::raw('hello' ,function($message)use($email){
            $message->subject('欢迎张英杰');
            $message->to($email);
            echo "成功";
        });
            
    }
    
    
    
    
    
    
    
    
    
    
    public function index()
    {
        
        //session
        
        // $res=session('key');
        // //dump($res);
        //  request()->session()->forget('key');
        //  $res1=session('key');
        //  dd($res1);
       
        //cookie

        // $name=Cookie::queue(Cookie::forget('n'));
        // $res1=Cookie::get('n');

        
       $n=request()->all();
      $where=[];
      if($n['n']??''){
          $where[]=['brand_name','like',"%$n[n]%"];
      }
      if($n['l']??''){
            $where['brand_url']=$n['l'];
    }
      
      $pagesize=config('app.PageSize');
        $data= Brand::where($where)->paginate($pagesize);
        //dump($n);
       return view('brand.list',['data'=>$data,'n'=>$n]);
    }

    /**
     * Show the form for creating a new resource.
     *添加
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$data=request()->session()->put('key','牛俊波');
        //Cookie::queue('n','张晨',6);
        //  request()->session()->put('uid','66');
        //request()->session()->forget('uid');
        return view('brand.add');
    }

    //public function doadd(StoreBlogPost $request)//第二种验证方法
    public function doadd(Request $request)
    {
        
        $data=$request->except(['_token']); 
      
        //第一种验证
        // $validatedData = $request->validate([
        //     'brand_name' => 'required|unique:brand|max:10',
        //     'brand_logo' => 'required',
        //     'brand_url' => 'required',
        //     'brand_desc' => 'required',
        // ],[
        //     'brand_name.required'=>'品牌名不能为空',
        //     'brand_name.unique'=>'品牌名已经拥有',
        //     'brand_name.max'=>'品牌名最大长度为10',
        //     'brand_logo.required'=>'logo不能为空',
        //     'brand_url.required'=>'品牌网址不能为空',
        //     'brand_desc.required'=>'品牌介绍不能为空',
        // ]);

        //第三种手动验证方法
        $validator = \Validator::make($request->all(), [
                'brand_name' => 'required|unique:brand|max:10',
                'brand_logo' => 'required',
                'brand_url' => 'required',
                'brand_desc' => 'required',
            ],[
                'brand_name.required'=>'品牌名不能为空',
                'brand_name.unique'=>'品牌名已经拥有',
                'brand_name.max'=>'品牌名最大长度为10',
                'brand_logo.required'=>'logo不能为空',
                'brand_url.required'=>'品牌网址不能为空',
                'brand_desc.required'=>'品牌介绍不能为空',
                ]);
            if ($validator->fails()) {
            return redirect('brand/add')->withErrors($validator)->withInput();
            }



        if($request->hasFile('brand_logo')){
            $res= $this->upload('brand_logo');
            if($res['code']){
                $data['brand_logo']=$res['imgurl'];
            }else{
                return $res['message'];
            }
        }
        
        // $model=new Brand;
        // $res=$model->insert($data);
        //$res=Brand::create($data);
        
        // dd($res);
        $status=DB::table('brand')->insert($data);
        if($status){
            return redirect('/brand/list');
        }
        
    }


    public function show(){
        echo $id;
    }


    public function upload($file)
    {
        if( request()->file($file)->isValid() ){
            $photo=request()->file($file);
            $data=$photo->store(date('Ymd'));
            return ['code'=>1,'imgurl'=>$data];
        }else{
            return ['code'=>2,'message'=>'失败'];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *修改视图
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function edit()
    {
        $data=request()->input('brand_id');
        //dd($data);
        $res=DB::table('brand')->where('brand_id',$data)->get();
        foreach($res as $v){
        }
        return view('brand.upd',['v'=>$v]);
    }
    
    //执行修改
    public function doupd()
    {
        $data=request()->except(['_token','brand_logo']);
        $res=DB::table('brand')->where('brand_id',$data['brand_id'])->update($data);
        if($res){
            return redirect('/brand/list');
        }
    }


    /**
     * Remove the specified resource from storage.
     *删除
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $data=request()->input();
        $res=DB::table('brand')->where(['brand_id'=>$data])->delete();
        if($res){
            return redirect('/brand/list');
        }
    }   
}
