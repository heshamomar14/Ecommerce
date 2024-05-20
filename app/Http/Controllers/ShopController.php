<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = '';
        $subcategorySelected = '';
        $brandsArray = [];



        $categories = Category::orderBy('name', 'ASC')->with('subcategories')->where('status', 1)->get();
        $brands = Brands::orderBy('name', 'ASC')->get();
        $products = Product::where('status', 1);
        //apply filters
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }
        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subCategory->id);
            $subcategorySelected = $subCategory->id;
        }
        //brand filter
        if (!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandsArray);
        }
        //price filter
        if ($request->get('price_max') != '' && $request->get('price_min') != '') {

            if ($request->get('price_max') == 1000) {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 1000000]);
            } else {
                $products = $products->whereBetween(
                    'price',
                    [intval($request->get('price_min')), intval($request->get('price_max'))]
                );
            }
        }
        //search in all
        if (!empty($request->get('search'))){
            $products = $products->where('title' ,'like','%'.$request->get('search').'%');

        }
        // sort filter
        if ($request->get('sort') != '') {
            if ($request->get('sort') == 'latest') {
                $products = $products->orderBy('id', 'DESC');
            } else if ($request->get('sort') == 'price_asc') {
                $products = $products->orderBy('id', 'ASC');
            } else {
                $products = $products->orderBy('id', 'DESC');
            }
        } else {
            $products = $products->orderBy('id', 'DESC');

        }
        //send our data to blade
        $products = $products->paginate(6);
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subcategorySelected'] = $subcategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 1000 : $request->get('price_max');
        $data['priceMin'] = intval($request->get('price_min'));
        $data['sort'] = $request->get('sort');

        return view('front.shop', $data);
    }
    public function product($slug){
        $product= Product::where('slug', $slug)
            ->withCount('product_ratings')
            ->withSum('product_ratings','rating')
            ->with(['product_images','product_ratings'])
            ->first();
        if ($product ==null) {
            abort(404);

    }
    //fettch related products
    $relatedProducts=[];
    if($product->related_products !=''){
        $productArray=explode(',',$product->related_products);
        $relatedProducts=Product::whereIn('id',$productArray)->with('product_images')->where('status',1)->get();
    }
    $avgRating='0.00';
    $avgRatingPer='0.00';
    if ($product->product_ratings_count >0){
        $avgRating=number_format( $product->product_ratings_sum_rating/$product->product_ratings_count,2);
        $avgRatingPer=$avgRating*100/5;
    }
    $data['product']=$product;
    $data['avgRating']=$avgRating;
    $data['avgRatingPer']=$avgRatingPer;
    $data['relatedProducts'] = $relatedProducts;

    return view('front.product', $data);

    }

    public function saveRating($id,Request $request)
    {
        $validator = Validator::make($request->all(),
            ['username' => 'required|min:5',
                'email' => 'required|email',
                'comment' => 'required',
                'rating' => 'required',
            ]);
        if ($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
        $count=ProductRating::where('email',$request->email)->count();
        if ($count>0){
            session()->flash('error','you already rated this product');
            return response()->json([
                'status'=>false,
                'error'=>$validator->errors()
            ]);
        }
        $productRating=new ProductRating;
        $productRating->product_id=$id;
        $productRating->username=$request->username;
        $productRating->email=$request->email;
        $productRating->comment=$request->comment;
        $productRating->rating=$request->rating;
        $productRating->save();
        session()->flash('success','thanks for your rating');
        return response()->json([
            'status'=>true,
            'message'=>'thanks for your rating'
        ]);

    }
}
