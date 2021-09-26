<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category\Category;
use App\Models\User\User;
use App\Models\Rate\Rate;

use Illuminate\Support\Facades\App;

class Post extends Model
{
   // public $limit;
    //public $page;
    //public function __construct($details)
    //{
    //    $this->details = $details;
    //}
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'category_id',
        "rateCount",
        "rateNumbers",
    ];
    protected $cost =['deleted' => 'boolean'];
    protected $with = ['user','category'];
    protected $appends = ['title','description','isFavourite'];
    //isFavourite
    public function getIsFavouriteAttribute(){
        $fav = auth()->user()->favourites;
        if(!in_array ($this->attributes['id'], $fav)){
            return $this->attributes['isFavourite']=false;
        }else{
            return $this->attributes['isFavourite']=true;
        }
    }
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
    public function user(){
        return $this->belongsTo(User::class)->select(['id', 'name','image','phone']);
    }
    
    public function category(){
        return $this->belongsTo(Category::class)->select(['id','name_en','name_ar','img']);
    }
    public static function getAll($query=[]){
        return Post::where($query)->select([
            'title_'.App::currentLocale().' as title',
            'description_'.App::currentLocale().' as description',
            'category_id',
            'user_id',
            'id', 
            'deleted',
            'created_at',
            'updated_at',
        ])->get();
    }
    public static function getPagenation($limit,$page,$query=[]){
        return Post::where($query)->select([
            'title_'.App::currentLocale().' as title',
            'description_'.App::currentLocale().' as description',
            'category_id',
            'user_id',
            'id', 
            'deleted',
            'created_at',
            'updated_at',
        ])->paginate($limit,['*'],'page',$page);

    }
    public function rate(){
        return $this->hasMany(Rate::class);
    }
    

}
