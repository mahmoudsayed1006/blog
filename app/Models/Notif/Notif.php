<?php

namespace App\Models\Notif;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use Illuminate\Support\Facades\App;

class Notif extends Model
{
    use HasFactory;
    protected $fillable = [
        'target_id',
        'resource_id',
        'title_en',
        'title_ar',
        'description_en',
        'description_ar',
        'type'
    ];
    protected $hidden = [
        'target_id',
        'resource_id',
    ];
    protected $casts = [];
    
    protected $with = ['target','resource'];
    protected $appends = ['title','description'];
    public function getTitleAttribute(){
        $title = 'title_'.App::currentLocale();
        return $this->attributes['title']=$this->attributes[$title];
    }
    public function getDescriptionAttribute(){
        $description = 'description_'.App::currentLocale();
        return $this->attributes['description']=$this->attributes[$description];
    }
    public function getDeletedAttribute(){
        return $this->attributes['deleted']==0?false:true;
    }
   
    public function target(){
        return $this->belongsTo(User::class)->select(['id', 'name','image','phone']);
    }
    public function resource(){
        return $this->belongsTo(User::class)->select(['id', 'name','image','phone']);
    }
    /*protected static function booted() {
        static::retrieved(function($item) {
            $title = 'name_'.App::currentLocale();
            $description = 'description_'.App::currentLocale();
            $item->title = $item->$title;
            $item->description = $description;
            return $item;
        });
    }*/
}
