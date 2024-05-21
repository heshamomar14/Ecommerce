<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\BrandsController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DiscountCodeController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\ProductImageContorller;
use App\Http\Controllers\admin\ProductsController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartContoller;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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
//send email route
//Route::get('/test',function(){
//    orderEmail(11);
//});


//front routes
Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('/product/{Slug}', [ShopController::class, 'product'])->name('front.product');
//cart and checkout routes
Route::get('/cart', [CartContoller::class, 'cart'])->name('front.cart');
Route::post('/add-to-cart', [CartContoller::class, 'addToCart'])->name('front.addToCart');
Route::post('/update-cart', [CartContoller::class, 'updateCart'])->name('front.updateCart');
Route::post('/delete-item', [CartContoller::class, 'deleteItem'])->name('front.deleteItem');
Route::get('/checkout', [CartContoller::class, 'checkOut'])->name('front.checkout');
Route::post('/processCheckOut', [CartContoller::class, 'processCheckOut'])->name('front.processCheckOut');
Route::get('/thanks/{orderId}', [CartContoller::class, 'thankYou'])->name('front.thanks');
Route::post('/get-order-summery', [CartContoller::class, 'getOrderSummery'])->name('front.getOrderSummery');
Route::post('/apply-discount', [CartContoller::class, 'applyDiscount'])->name('front.applyDiscount');
Route::post('/remove-discount', [CartContoller::class, 'removeDiscount'])->name('front.removeDiscount');
//wishlist route
Route::post('/add-to-wishlists', [FrontController::class, 'addToWishlists'])->name('front.addToWishlists');
Route::get('/page/{Slug}', [FrontController::class, 'page'])->name('front.page');
Route::post('/send-contact-email', [FrontController::class, 'sendContactEmail'])->name('front.sendContactEmail');

//forget password
Route::get('/forgotPassword', [AuthController::class, 'forgotPassword'])->name('front.forgotPassword');
Route::post('/ProcessForgotPassword', [AuthController::class, 'ProcessForgotPassword'])->name('front.ProcessForgotPassword');
Route::get('/resetPassword/{token}', [AuthController::class, 'resetPassword'])->name('front.resetPassword');
Route::post('/ProcessResetPassword', [AuthController::class, 'ProcessResetPassword'])->name('front.ProcessResetPassword');
// save rating product route
Route::post('/save-rating/{productId}', [ShopController::class, 'saveRating'])->name('front.saveRating');


//  account register and login for user
Route::group(['prefix' => 'account'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/register', [AuthController::class, 'register'])->name('account.register');
        Route::post('/process-register', [AuthController::class, 'processRegister'])->name('account.processRegister');
        Route::get('/login', [AuthController::class, 'login'])->name('account.login');
        Route::post('/authinicate', [AuthController::class, 'authinicate'])->name('account.authinicate');
        Route::get('/login', [AuthController::class, 'login'])->name('account.login');

    });
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/profile', [AuthController::class, 'profile'])->name('account.profile');
        Route::post('/updateProfile', [AuthController::class, 'updateProfile'])->name('account.updateProfile');
        Route::post('/updateAddress', [AuthController::class, 'updateAddress'])->name('account.updateAddress');
        Route::get('/changePassword', [AuthController::class, 'showChangePassword'])->name('account.showChangePassword');
        Route::post('/process-changePassword', [AuthController::class, 'changePassword'])->name('account.changePassword');
        Route::get('/logout', [AuthController::class, 'logout'])->name('account.logout');
        Route::get('/my-orders', [AuthController::class, 'order'])->name('account.order');
        Route::get('/my-wishlist', [AuthController::class, 'wishlist'])->name('account.wishlist');
        Route::post('/remove-product-from-wishlist', [AuthController::class, 'removeProductFromWishlist'])->name('account.removeProductFromWishlist');
        Route::get('/order_details/{orderId}', [AuthController::class, 'orderDetails'])->name('account.orderDetails');


    });
});
///////////////////////////////////////////////////////////////////////
/// ///////////////////////////////////////////////////////////////////
/// ///////////////      ADMIN ROUTES        /////////////////////////
//////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
Route::group(['prefix' => 'admin'], function () {

    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AdminController::class, 'index'])->name('admin.login');
        Route::post('/authenicate', [AdminController::class, 'authenicate'])->name('admin.authenicate');


    });
    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');


        // Category Routes :
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');


        // sub-category routes

        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('subCategories.index');
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('subCategories.create');
        Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('subCategories.store');
        Route::get('/sub-categories/{subcategory}/edit', [SubCategoryController::class, 'edit'])->name('subCategories.edit');
        Route::put('/sub-categories/{subcategory}', [SubCategoryController::class, 'update'])->name('subCategories.update');
        Route::delete('/sub-categories/{subcategory}', [SubCategoryController::class, 'destroy'])->name('subCategories.delete');

        //Brands Routes
        Route::get('/brands', [BrandsController::class, 'index'])->name('brands.index');
        Route::get('/brands/create', [BrandsController::class, 'create'])->name('brands.create');
        Route::post('/brands', [BrandsController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit', [BrandsController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand}', [BrandsController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand}', [BrandsController::class, 'destroy'])->name('brands.delete');


        //products routes
        Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductsController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductsController::class, 'destroy'])->name('products.delete');
        //get products for related products
        Route::get('/get-products', [ProductsController::class, 'getProduct'])->name('products.getProduct');
        //rating blade
        Route::get('/ratings', [ProductsController::class, 'productRatings'])->name('products.productRatings');
        Route::get('/ratings-status', [ProductsController::class, 'changeRatingStatus'])->name('products.changeRatingStatus');


        //shippingCharge routes
        Route::get('/shipping/create', [ShippingController::class, 'create'])->name('shipping.create');
        Route::post('/shipping', [ShippingController::class, 'store'])->name('shipping.store');
        Route::get('/shipping/{id}', [ShippingController::class, 'edit'])->name('shipping.edit');
        Route::put('/shipping/{id}', [ShippingController::class, 'update'])->name('shipping.update');
        Route::delete('/shipping/{id}', [ShippingController::class, 'destroy'])->name('shipping.delete');

//discount coupon routes
        Route::get('/coupons', [DiscountCodeController::class, 'index'])->name('coupon.index');
        Route::get('/coupon/create', [DiscountCodeController::class, 'create'])->name('coupon.create');
        Route::post('/coupon', [DiscountCodeController::class, 'store'])->name('coupon.store');
        Route::get('/coupons/{coupon}/edit', [DiscountCodeController::class, 'edit'])->name('coupon.edit');
        Route::put('/coupons/{coupon}', [DiscountCodeController::class, 'update'])->name('coupon.update');
        Route::delete('/coupons/{coupon}', [DiscountCodeController::class, 'destroy'])->name('coupon.delete');

        //orders routes
        Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
        Route::get('/orders/{id}', [OrderController::class, 'detail'])->name('order.details');
        Route::post('/order/change-status/{id}', [OrderController::class, 'changeOrderStatus'])->name('order.changeOrderStatus');
        Route::post('/order/send-email/{id}', [OrderController::class, 'sendInvoiceEmail'])->name('order.sendInvoiceEmail');


        //users routes
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/user', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');

        // static pages routes
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.delete');


        //our controllers for my ajax method and addtional methods in  Admin pages

        Route::get('/products-subcategories', [ProductSubCategoryController::class, 'index'])->name('ProductSubCategory.index');
        Route::post('/products-updateImage', [ProductImageContorller::class, 'update'])->name('ProductImageUpdate.image');
        Route::delete('/products-deleteImage', [ProductImageContorller::class, 'destroy'])->name('ProductImageDelete.image');


        //upload img
        Route::post('/upload-temp-img', [TempImageController::class, 'create'])->name('temp.uploadImg');

        //setting routes
        Route::get('/showChangePassword', [SettingController::class, 'showChangePassword'])->name('admin.showChangePassword');
        Route::post('/ProcessChangePassword', [SettingController::class, 'ProcessChangePassword'])->name('admin.ProcessChangePassword');

// get slug route : make slug as  same as name
        Route::get('getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json(['status' => true, 'slug' => $slug]);
        })->name('getSlug');


    });
});
