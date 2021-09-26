<?php

namespace App\Models\Rate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use App\Models\Post\Post;
class Rate extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment',
        'rate',
        'user_id',
        'post_id'
    ];
    protected $hidden = [
        'user_id',
        'post_id'
    ];
    protected $appends = [
        'post'
    ];
    protected $casts = [];
    protected $with = ['user'];
    public function getDeletedAttribute(){
        return $this->attributes['deleted']==0?false:true;
    }
    public function getPostAttribute(){
        return $this->attributes['post']=$this->attributes['post_id'];
    }
    public function user(){
        return $this->belongsTo(User::class)->select(['id', 'name','image','phone']);
    }
}
