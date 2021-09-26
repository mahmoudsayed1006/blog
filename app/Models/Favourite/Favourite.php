<?php

namespace App\Models\Favourite;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use App\Models\User\User;
use App\Models\Post\Post;

class Favourite extends Model
{
    use HasFactory;
    protected $fillable = [
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
    protected $with = ['user','post'];
    public function getDeletedAttribute(){
        return $this->attributes['deleted']==0?false:true;
    }
    public function getPostAttribute(){
        return $this->attributes['post']=$this->attributes['post_id'];
    }
    public function user(){
        return $this->belongsTo(User::class)->select(['id', 'name','image','phone']);
    }
    public function Post(){
        return $this->belongsTo(Post::class)->select([
            'id', 
            'title_'.App::currentLocale().' as title',
            'description_'.App::currentLocale().' as description',
            'title_ar',
            'title_en',
            'description_ar',
            'description_en',
            'deleted',
            'user_id',
            'category_id',
        ]);
    }
}
