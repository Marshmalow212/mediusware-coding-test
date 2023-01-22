<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\ProductVariantPrice;

class Product extends Model
{
    protected $fillable = [
        'id','title', 'sku', 'description'
    ];

    public function variant_price(){
        return $this->hasMany(ProductVariantPrice::class,'product_id','id')
        ->with('variant_one','variant_two','variant_three');
    }
    
    public function with_price_range($min,$max){
        return $this->hasMany(ProductVariantPrice::class,'product_id','id')
        ->with('variant_one','variant_two','variant_three')
        ->where('price','>=',$min)
        ->andWhere('price','<=',$max)
        ->orderBy('price');
        
    }

}
