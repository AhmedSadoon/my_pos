<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categorires=Category::all();
        $products=Product::when($request->search,function($q)use($request){
            return $q->whereTranslationLike('name','%'.$request->search.'%');
        })->when($request->category_id,function ($q) use ($request)
        {
            return $q->where('category_id',$request->category_id);

        })->latest()->paginate(5);
        return view('dashboard.products.index',compact('categorires','products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorires=Category::all();
        return view('dashboard.products.create',compact('categorires'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules=[];

        foreach(config('translatable.locales')as $locale){


            $rules +=[$locale.'.title'=>['required',Role::unique('product_translations','name')]];
            $rules +=[$locale.'.description'=>['required',Role::unique('category_translations','name')]];

        }


        $rules+=[
            'pruchase_price'=>'required',
            'sale_price'=>'required',
            'stock'=>'required',
        ];

        $request->validate($rules);

        $request_data=$request->all();

        if($request->image){

            Image::make($request->image)->resize(300,null,function($constraint){
                $constraint->aspectRatio();
            })->save(public_path('uploads/product_images/'. $request->image->hashName()));

        }
        Product::create($request_data);
        session()->flash('success',('site.added.successfully'));

        return redirect()->route('dashboard.products.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $categorires=Category::all();

        return view('dashboard.products.edit',compact(['product','categorires']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $rules=[
            'category_id'=>'required'
        ];

        foreach(config('translatable.locales')as $locale){


            $rules +=[$locale.'.title'=>['required',Role::unique('product_translations','name')->ignore($product->id,'product_id')]];
            $rules +=[$locale.'.description'=>['required',Role::unique('category_translations','name')]];

        }


        $rules+=[
            'pruchase_price'=>'required',
            'sale_price'=>'required',
            'stock'=>'required',
        ];

        $request->validate($rules);

        $request_data=$request->all();

        if($request->image){

            if($product->image!='defualt.png'){

                Storage::disk('public_uploads')->delete('product_images/'.$product->image);

            }

            Image::make($request->image)->resize(300,null,function($constraint){
                $constraint->aspectRatio();
            })->save(public_path('uploads/product_images/'. $request->image->hashName()));

            $request_data['image']=$request->image->hashName();
        }

        $product->update($request_data);

        session()->flash('success',('site.updated.successfully'));

        return redirect()->route('dashboard.products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        session()->flash('success',('site.deleted.successfully'));

        return redirect()->route('dashboard.products.index');
    }
}
