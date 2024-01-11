<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $products = Product::with('product_images')->latest('id');
        if(!empty($request->keyword))
        {
            $products->where('title', 'LIKE', '%'.$request->keyword.'%');
        }
        $products = $products->paginate(10);
        return view('admin.products.list', compact('products'));
    }

    public function create()
    {
       // phpinfo();
        $data = [];
        $data['categories'] = Category::orderBy('name', 'asc')->get();
        $data['brands']     = Brand::orderBy('name', 'asc')->get();
        
        return view('admin.products.create', $data);
    }

    public function store(Request $request)
    {
        // dd($request->image_array);
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No'
        ];

        if(!empty($request->track_qty) && ($request->track_qty == 'Yes'))
        {
            $rules['qty'] = 'required|numeric';
        }
        $validator = Validator::make($request->all(), $rules);
        if($validator->passes()){
            $product            = new Product();
            $product->title     = $request->title;
            $product->slug      = $request->slug;
            $product->description = $request->description;
            $product->price     = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku       = $request->sku;
            $product->barcode   = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty       = $request->qty;
            $product->status    = $request->status;
            $product->category_id  = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id     = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->save();

            if(!empty($request->image_array)){
                foreach($request->image_array as $temp_image_id){
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.', $tempImageInfo->name);
                    $ext = last($extArray);

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->name = NULL;
                    $productImage->save();

                    $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage -> name = $imageName;
                    $productImage->save();
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath   = public_path().'/uploads/product/'.$imageName;
                    // Here should impliment resize images for large and thumb sizes no need for this copy
                    File::copy($sourcePath,$destPath);                 

                }
            }

            $request->session()->flash('success', 'Product added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Product added successfully'
            ]);

        }else{
            return response()->json([
                'status' => 'false',
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $product            = Product::find($id);
        if(empty($product))
        {
            return redirect()->route('products.index')->with('Product Not Found!');
        }
        $relatedProducts = [];
        if($product->related_products != null){
            $productArray = explode(',',$product->related_products);
            $relatedProducts = Product::whereIn('id',$productArray)->get();
        }
        $data               = [];
        $data['subCategories']  = SubCategory::where('category_id', $product->category_id)->get();
        $data['product']    = $product;
        $data['productImages'] = ProductImage::where('product_id', $product->id)->get();
        $data['categories'] = Category::orderBy('name', 'asc')->get();
        $data['brands']     = Brand::orderBy('name', 'asc')->get();
        $data['relatedProducts'] = $relatedProducts;
        return view('admin.products.edit', $data);
    }

    public function update($id, Request $request)
    {
        $product = product::find($id);
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No'
        ];

        if(!empty($request->track_qty) && ($request->track_qty == 'Yes'))
        {
            $rules['qty'] = 'required|numeric';
        }
        $validator = Validator::make($request->all(), $rules);
        if($validator->passes()){
            $product->title     = $request->title;
            $product->slug      = $request->slug;
            $product->description = $request->description;
            $product->price     = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku       = $request->sku;
            $product->barcode   = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty       = $request->qty;
            $product->status    = $request->status;
            $product->category_id  = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id     = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products)) ? implode(',', $request->related_products) : '';
            $product->save();

            $request->session()->flash('success', 'Product updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully'
            ]);

        }else{
            return response()->json([
                'status' => 'false',
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id, Request $request)
    {
        $product = Product::find($id);
        if(empty($product))
        {
            $request->session()->flash('error', 'Product not found!');
            return response()->json([
                'status' => false,
                'notFound' => true
            ]);
        }

        $productImages = ProductImage::where('product_id', $id);
        if(!empty($productImages)){
            foreach($productImages as $productImage){
                File::delete(public_path('uploads/product'.$productImage->name));
            }
        }
        $productImages->delete();
        $product->delete();
        $request->session()->flash('success', 'Product deleted successfully!');
        return response()->json([
            'status' => true,
            'success' => 'Product deleted successfully!'
        ]);
    }

    public function getProducts(Request $request)
    {
        $tempProducts = [];
        if($request->term != null){
            $products = Product::where('title', 'LIKE', '%'.$request->term.'%')->get();
        }

        if($products != null)
        {
            foreach($products as $product){
                $tempProducts[] = array("id" => $product->id, 'text' => $product->title);
            }
            return response()->json(['tags' => $tempProducts]);
        }
    }
}
