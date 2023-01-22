<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title'=> 'required | string',
            'sku'=> 'required| string',
            'description'=> 'required | string',
            'product_image'=>'array',
            'proudct_variant'=>'array',
            'proudct_variant_prices'=>'array'

        ]);

        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()->all()],400);
        }
        try {
            DB::beginTransaction();
            $product = new Product($request->only('title','sku','description'));
            $product->save();
            $product_id = $product->id;
            $product_title = $product->title;
            $product_variant = $request->product_variant;
            $variantKeys = [];
            $dataVariant = ["product_id"=>$product_id];
            $dataInfo = ['product_id'=> $product_id];
            $product_variant_prices = $request->product_variant_prices;

            $variantKeys = $this->productVariant($product_variant, $dataVariant , $variantKeys);
            $this->productVariantPrice($product_variant_prices, $dataInfo, $variantKeys);
            DB::commit();
            return response()->json(['message'=>'Product Created!'],200);
            
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            Log::error('ProductController Store Method ',$th->getTrace());
            return response()->json(['message'=>'Something Wrong!'],400);
        }
        
        // $product_image = $request->product_image;
        
        // $product_id = 1;
        // $product_title = 'hello';
        // foreach ($product_image as $file) {
            //     if($file){
                //         $fileName= \Str::slug($product_id."_".$product_title);
                //         $fileExt = $file->getClientOriginalExtension();
                //         $path = $file->storeAs('public/'.$fileName.".".$fileExt);
                
                //     }
                // }
                
            }
            
    protected function productVariant($product_variant, $dataVariant, $variantKeys){
        foreach ($product_variant as $variant) {
            $dataVariant['variant_id'] = $variant['option'];
            foreach ($variant['tags'] as $tag ) {
                $dataVariant['variant'] = $tag;
                $res = ProductVariant::create($dataVariant);
                $variantKeys[$tag] = $res->id;
            }
        }
        
        return $variantKeys;        
    }
    
    protected function productVariantPrice($product_variant_prices, $dataInfo, $variantKeys){
        foreach ($product_variant_prices as $info) {
            $dataInfo['price'] = $info['price'];
            $dataInfo['stock'] = $info['stock'];
            $vars = explode('/',$info['title']);
            if($vars[0] && $variantKeys[$vars[0]]){
                $dataInfo['product_variant_one'] = $variantKeys[$vars[0]];
                if($vars[1] && $variantKeys[$vars[1]]){
                    $dataInfo['product_variant_two'] = $variantKeys[$vars[1]];
                    if($vars[2] && $variantKeys[$vars[2]]){
                        $dataInfo['product_variant_three'] = $variantKeys[$vars[2]];
                        
                    }
                    
                }
            }
            
            ProductVariantPrice::create($dataInfo);
        }
        
    }
    
    // ---------------------------------------
    // Get All Products
    // @param limit 
    // @param page 
    // ---------------------------------------
    public function getALL(Request $request){
        $limit = $request->limit ?? 5;
        $page = $request->page ?? 1;
        $offset = ($page - 1) * $limit;
        $product_count = Product::count();
        $products = Product::with('variant_price')
        ->offset($offset)
        ->limit($limit)
        ->get();
        $meta = [
            'total'=>$product_count,
            'limit'=>$limit,
            'page' => $page,
            'offset'=>$offset
        ];

        return response()->json(['data'=>$products,'meta'=>$meta]);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response\Json
     */
    public function filterProduct(Request $request)
    {
        
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
