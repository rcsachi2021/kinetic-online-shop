<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DiscountCodeController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/test', function () {
//     orderEmail(14);
// });

Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subcategorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('product/{slug}',[ShopController::class, 'product'])->name('front.product');
Route::get('/cart',[CartController::class, 'cart'])->name('front.cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('front.addToCart');
Route::post('/update-cart', [CartController::class, 'updateCart'])->name('front.updateCart');
Route::post('/delete-item', [CartController::class, 'deleteItem'])->name('front.deleteItem.cart');
Route::get('/checkout', [CartController::class, 'checkout'])->name('front.checkout');
Route::post('/process-checkout', [CartController::class, 'processCheckout'])->name('front.processCheckout');
Route::get('/thanks/{order}', [CartController::class, 'thankyou'])->name('front.thankyou');
Route::post('/get-order-summery', [CartController::class, 'getOrderSummery'])->name('front.getOrderSummery');
Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('front.applyDiscount');
Route::post('/remove-discount', [CartController::class, 'removeCoupon'])->name('front.removeCoupon');
Route::post('/add-to-wishlist', [FrontController::class, 'addToWishList'])->name('front.addToWishList');
Route::get('/page/{slug}', [FrontController::class, 'page'])->name('front.page');
Route::post('/send-contact-email', [FrontController::class, 'sendContactEmail'])->name('front.sendContactEmail');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('front.forgotPassword');
Route::post('/process-forgot-password', [AuthController::class, 'processForgotPassword'])->name('front.processForgotPassword');
Route::get('/reset-passeord/{token}', [AuthController::class, 'resetPassword'])->name('front.resetPassword');
Route::post('/process-reset-passeord', [AuthController::class, 'processResetPassword'])->name('front.processResetPassword');
Route::post('/save-review/{productID}', [ShopController::class, 'saveReview'])->name('front.saveReview');


Route::group(['prefix' => 'account'], function(){
    Route::group(['middleware' => 'guest'], function(){
        Route::get('/register', [AuthController::class, 'register'])->name('account.register');
        Route::get('/login', [AuthController::class, 'login'])->name('account.login');
        Route::post('/login', [AuthController::class, 'authenticate'])->name('account.authenticate');
        Route::post('/process-register', [AuthController::class, 'processRegister'])->name('account.processRegister');
    });

    Route::group(['middleware' => 'auth'], function(){
        Route::get('/profile', [AuthController::class, 'dashboard'])->name('account.profile');
        Route::post('/update-profile', [AuthController::class, 'updateProfile'])->name('account.updateProfile');
        Route::post('/update-address', [AuthController::class, 'updateAddress'])->name('account.updateAddress');
        Route::get('/my-orders', [AuthController::class, 'orders'])->name('account.orders');
        Route::get('/order-detail/{orderid}', [AuthController::class, 'ordersDetail'])->name('account.ordersDetail');
        Route::get('/wishlist', [AuthController::class, 'wishList'])->name('account.wishList');
        Route::post('/remove-product-from-wishlist', [AuthController::class, 'removeProductFromWishList'])->name('account.removeProductFromWishList');
        Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('account.showChangePassword');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('account.changePassword');
        Route::get('/logout', [AuthController::class, 'logout'])->name('account.logout');        
    });
});

Route::group(['prefix' => 'admin'], function(){
    Route::group(['middleware' => 'admin.guest'], function(){
        Route::get('login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('authenticate', [AdminLoginController::class,'authenticate'])->name('admin.authenticate');
    });

Route::group(['middleware' => 'auth.admin'], function(){
        Route::get('dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('logout', [HomeController::class, 'logout'])->name('admin.logout');

        //Category Routes
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');
       
        //temp-images.create
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');
        
        // Subcategory Routes
        Route::get('sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
        Route::post('sub-categories/store', [SubCategoryController::class, 'store'])->name('sub-categories.store');
        Route::get('sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.index');
        Route::get('sub-categories/{subcategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
        Route::put('sub-categories/{subcategory}', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        Route::delete('sub-categories/{subcategory}', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete');
        
        //Brand Routes
        Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('brands/store', [BrandController::class, 'store'])->name('brands.store');
        Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('brands/{brand}/edit',[BrandController::class,'edit'])->name('brands.edit');
        Route::put('brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('brands/{brand}', [BrandController::class, 'delete'])->name('brands.delete');

        //Products Routes
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
        Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index'])->name('product-subcategories.index');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.delete');
        Route::get('get-products', [ProductController::class, 'getProducts'])->name('products.getProducts');

        // Product Images Routes
        Route::post('product-images/{product}', [ProductImageController::class, 'save'])->name('product-images.save');
        Route::delete('product-images/{image}',[ProductImageController::class, 'destroy'])->name('product-images.delete');

        //Shipping Routes
        Route::get('shipping/create', [ShippingController::class, 'create'])->name('shipping.create');
        Route::post('shipping', [ShippingController::class, 'store'])->name('shipping.store');
        Route::get('shipping/{id}', [ShippingController::class, 'edit'])->name('shipping.edit');
        Route::post('shipping/{id}', [ShippingController::class, 'update'])->name('shipping.update');
        Route::get('shipping/delete/{id}', [ShippingController::class, 'delete'])->name('shipping.delete');        

        //Coupon codes Routes
        Route::get('coupons', [DiscountCodeController::class, 'index'])->name('coupons.index');
        Route::get('coupons/create', [DiscountCodeController::class, 'create'])->name('coupons.create');
        Route::post('coupons', [DiscountCodeController::class, 'store'])->name('coupons.store');
        Route::get('coupons/{coupon}/edit', [DiscountCodeController::class, 'edit'])->name('coupons.edit');
        Route::put('coupons/{coupon}', [DiscountCodeController::class, 'update'])->name('coupons.update');
        Route::delete('coupons/{coupon}', [DiscountCodeController::class, 'destroy'])->name('coupons.delete');
       
        //Order Routes
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}', [OrderController::class, 'detail'])->name('orders.detail');
        Route::post('order/change-status/{id}', [OrderController::class, 'changeOrderStatus'])->name('orders.changeOrderStatus');
        Route::post('order/send-email/{id}', [OrderController::class, 'sendInvoiceEmail'])->name('orders.sendInvoiceEmail');

        //Users Routes
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.delete');

        //Page Routes
        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('pages/{page}', [PageController::class, 'destroy'])->name('pages.delete');
       

        Route::get('/get-slug', function(Request $request){
            $slug = '';
            if(!empty($request->title))
            {
                $slug = Str::slug($request->title);
            }
            return response()->json(['status' => true, 'slug' => $slug]);
        })->name('get.slug');
        });

    
});
