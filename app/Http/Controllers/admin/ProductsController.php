<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::latest('id')->with('product_images');
        //search in product list
        if (!empty($request->get('keyword'))) {
            $products = $products->where('title', 'like', '%' . $request->get('keyword') . '%');
        }
        $products = Product::latest('id')->paginate();
        $data['products'] = $products;
        return view('admin.product.list', $data);

    }

    public function create()
    {
        $data = [];
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brands::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.product.create', $data);
    }

    public function store(Request $request)
    {
        $rules = ['title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No'];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->shipping_returns = $request->shipping_returns;
            $product->short_description = $request->short_description;
            $product->related_products =
            (!empty($request->related_products))?implode(',',$request->related_products) :'';
            $product->save();
            if(!empty($request->image_array)){
                foreach ($request->image_array as $temp_image_id){
                    $tempImageInfo= TempImage::find($temp_image_id);
                    $extArray = explode('.',$tempImageInfo->name);
                    $ext = last($extArray); // png , jpg, gif

                    $productImage= new ProductImage();
                    $productImage->product_id=$product->id;
                    $productImage->image='NULL';
                    $productImage->save();

                    $imageName=$product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image=$imageName;
                    $productImage->save();

                    //generate thumb for product
                    //largeImages
                    $sourcePath = public_path() . '/temp/' . $tempImageInfo->name;
                    $destPath = public_path() . '/uploads/product/large/' .  $productImage->image;
                    $image = Image::read($sourcePath);
                    $image->resize(1400,null, function ($constraint){
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);
                    //smallImages
                    $destPath = public_path() . '/uploads/product/small/' .  $productImage->image;
                    $image = Image::read($sourcePath);
                    $image->resize(300,300);
                    $image->save($destPath);
                }
            }
            $request->session()->flash('success', 'product added successfuly');
            return response()->json(['status' => 'true', 'message' => 'product added successfuly']);
        } else {
            return response()->json(['status' => 'false', 'errors' => $validator->errors()]);
        }
    }

    public function edit($id, Request $request)
    {
        $product = Product::find($id);
        if (empty($product)) {
            return redirect()->route('products.index')->with('error', 'something is wrong , the product nor found ');
        }
        //fetch product images

        $productImages=ProductImage::where('product_id',$product->id)->get();
        $subcategories = SubCategory::where('category_id', $product->category_id)->get();
        //fettch related products
        $relatedProducts=[];
        if($product->related_products !=''){
            $productArray=explode(',',$product->related_products);
            $relatedProducts=Product::whereIn('id',$productArray);
        }
        $data=[];
        $data ['product'] = $product;
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brands::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['subcategories'] = $subcategories;
        $data['productImages'] = $productImages;
        $data['relatedProducts'] = $relatedProducts;
        return view('admin.product.edit',$data);
    }

    public function update($id, Request $request)
    {
        $product = Product::find($id);
        if (empty($product)) {
            return response()->json([
                'status' => false,
                'notfound' => true,
                'message' => 'product not found'
            ]);
        }

        $rules = ['title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No'];
        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->passes()) {
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->shipping_returns = $request->shipping_returns;
            $product->short_description = $request->short_description;
            $product->related_products =
            (!empty($request->related_products))?implode(',',$request->related_products) :'';
            $product->save();

            $request->session()->flash('success', 'product updated successfuly');
            return response()->json(['status' => 'true', 'message' => 'product updated successfuly']);
        } else {
            return response()->json(['status' => true, 'errors' => $validator->errors()]);

        }
    }


    public function destroy($id, Request $request)
    {

        $product = Product::find($id);

        if (empty($product)) {
            $request->session()->flash('error', 'product not found ');
            return response()->json(['status' => true, 'message' => 'product not found']);
        }
        $productsimages=ProductImage::where('product_id',$id)->get();
        if(!empty($productsimages)){
            foreach ($productsimages as $productsimage){
                File::delete(public_path() . '/uploads/category/thumb' . $productsimage->image);
                File::delete(public_path() . '/uploads/category' . $productsimage->image);
            }
            ProductImage::where('product_id',$id);

        }

        $product->delete();
        $request->session()->flash('success', 'product deleted successfuly');
        return response()->json(['status' => true, 'message' => 'product deleted successfuly']);
    }

    public function getProduct(Request $request)
    {
        $tempProduct = [];

        if ($request->term != "") {
            $products = Product::where('title', 'like', '%' . $request->term . '%')->get();
            if ($products != null) {
                foreach ($products as $product) {
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }
        return response()->Json(['tags' => $tempProduct, 'status' => true]);
    }
    public function productRatings()
    {
        $ratings=ProductRating::select('product_ratings.*','products.title as productTitle')->orderBy('product_ratings.created_at','DESC');
        $ratings=$ratings->leftJoin('products','products.id','product_ratings.product_id');
        $ratings=$ratings->paginate(10);
        return view('admin.product.ratings',[
            'ratings'=>$ratings
        ]);
    }
    public function changeRatingStatus(Request $request)
    {
        $productRating=ProductRating::find($request->id);
        $productRating->status=$request->status;
        $productRating->save();
        session()->flash('success','status changed successfully');
        return response()->json([
            'status'=>true,
        'message'=>'status changed successfully'
    ]);
    }
}
