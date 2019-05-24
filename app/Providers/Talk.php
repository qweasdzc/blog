<?php


namespace App;


use Illuminate\Database\Eloquent\Model;


class Talk extends Model
{
    protected $table = 'talk';
    protected $fillable = [
      'u_email',
        'level',
        'desc',
        'goods_id',
    ];
}