<?php

namespace App\Http\Controllers;

use App\Models\Brand;
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
        
        $data               = [];
        $categories         = Category::with('subCategories')->orderBy('name', 'ASC')->where('status', 1)->get();
        $brands             = Brand::orderBy('name', 'ASC')->where('status', 1)->get();

        $products           = Product::where('status', 1);
        $brandsArray        = [];

        if(!empty($request->get('brands')))
        {
            $brandsArray = explode(',',$request->get('brands'));
            $products    = $products->whereIn('brand_id',$brandsArray);
        }
        
        if(!empty($categorySlug)){
            $category = Category::where('slug',$categorySlug)->first(); 
            $products = $products->where('category_id', $category->id); 
        }

        if(!empty($subCategorySlug)){
            $subCategory = SubCategory::where('slug',$subCategorySlug)->first();
            $products    = $products->where('sub_category_id', $subCategory->id);
        }

        if(($request->get('price_min')!=null) && ($request->get('price_max')!=null))
        {
            $products = $products->whereBetween('price',[intval($request->get('price_min')),intval($request->get('price_max'))]);
        }

        if($request->get('sort'))
        {
            if($request->get('sort') == 'latest'){
                $products = $products ->orderBy('id', 'DESC');
            }
            elseif($request->get('sort') == 'price_asc'){
                $products = $products->orderBy('price', 'ASC');
            }
            else{
                $products = $products->orderBy('price', 'DESC');
            }
        }else{
            $products = $products ->orderBy('id', 'DESC');
        }

        $products           = $products->paginate(6);

        $data['categories'] = $categories;
        $data['brands']     = $brands;
        $data['products']   = $products;
        $data['brandsArray'] = $brandsArray;
        $data['price_min']   = (intval($request->get('price_min')) != 0) ? intval($request->get('price_min')) : 1;
        $data['price_max']   = (intval($request->get('price_max')) != 0) ? intval($request->get('price_max')) : 5000;
        $data['sort']        = $request->get('sort');

        return view('front.shop',$data);
    }

    public function product($slug)
    {
        $product = Product::with('product_images')->where('slug',$slug)->first();
        
        if($product == null)
        {
            abort(404);
        }
        $relatedProducts = [];
        if($product->related_products != null){
            $productArray = explode(',',$product->related_products);
            $relatedProducts = Product::whereIn('id',$productArray)->get();
        }
        $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;
        return view('front.product', $data);
    }

    public function saveReview($productID, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'rating' => 'required',
            'comment' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $count = ProductRating::where('email', $request->email)->where('product_id', $productID)->count();
        if($count > 0){
            session()->flash('error', 'You already posted a review for this product');
            return response()->json([
                'status' => true
            ]);
        }
        $rating = new ProductRating();
        $rating->username = $request->name;
        $rating->email = $request->email;
        $rating->product_id = $productID;
        $rating->rating = $request->rating;
        $rating->comment = $request->comment;
        $rating->save();
        session()->flash('success', 'Your review posted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Your review posted successfully'
        ]);
    }
}
