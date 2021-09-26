<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post\Post;
use Illuminate\Support\Facades\App;

class Category extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name_ar',
        'name_en',
        'img'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];
    protected $appends = ['name'];
    public function getNameAttribute(){
        $name = 'name_'.App::currentLocale();
        return $this->attributes['name']=$this->attributes[$name];
    }
    public function getDeletedAttribute(){
        return $this->attributes['deleted']==0?false:true;
    }
    public function posts(){
        return $this->hasMany(Post::class);
    }
    /*protected static function booted() {
        static::retrieved(function($item) {
            $name = 'name_'.App::currentLocale();
            $item->name = $item->$name;
            //$item->img = $item->img;
            return $item;
        });
    }*/
}
