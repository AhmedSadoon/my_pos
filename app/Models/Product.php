<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $appends=['image_path','profit_percant'];

public $translatedAttributes = ['name','description'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function order()
    {
        return $this->belongsToMany(Order::class,'product_order');
    }

    public function getImagePathAttribute()
    {
        return asset('uploads/product_images/'.$this->image);
    }

    public function getProfitPercantAttribute()
    {
        $profit=$this->sale_price -$this->purchase_price;
        $profit_percant=$profit*100 /$this->purchase_price;
        return number_format($profit_percant,2);
    }


}
