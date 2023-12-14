<?php

use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NonAuthController;
use App\Http\Controllers\PercentageController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TermandConditionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Util\Percentage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/staff/list', [UserController::class, 'getStaffs']);
Route::post('/user/register', [RegisterController::class, 'store']);
Route::post('/login', [RegisterController::class, 'login']);
Route::post('/login/user', [RegisterController::class, 'userlogin']);

Route::get('/user/list/count', [UserController::class, 'getUserCount']);
Route::get('/relatedproducts/list/{userid}', [HomeController::class, 'relatedProducts']);

Route::get('/staff/list/count', [UserController::class, 'getStaffCount']);

Route::get('/product/list/pg', [ProductController::class, 'productList']);
Route::get('/product/list/pg/{productName}/{item_id}', [ProductController::class, 'productListSearch']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/product/create', [ProductController::class, 'store']);
    Route::get('/product/list', [ProductController::class, 'index']);

    # Cart
    Route::get('/cart', [CartController::class, 'cartItems']);
    Route::get('/cart/count', [CartController::class, 'itemCount']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/cart/add-quantity/{cart_session_id}', [CartController::class, 'addQuantityToCart']);
    Route::post('/cart/reduce-quantity/{cart_session_id}', [CartController::class, 'reduceQuantityToCart']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/cart/remove', [CartController::class, 'removeFromCart']);


    Route::get('/roleandpermission/lists', [RoleController::class, 'index']);
    Route::post('/addroleandpermision/', [RoleController::class, 'store']);
    Route::get('/permission/lists', [RoleController::class, 'index']);

    Route::post('/store/create', [StoreController::class, 'store']);
    Route::get('/store/list', [StoreController::class, 'index']);

    Route::post('/slider/create', [SliderController::class, 'store']);
    Route::get('/slider/list', [SliderController::class, 'index']);

    Route::post('/banner/create', [BannerController::class, 'store']);
    Route::get('/banner/list', [BannerController::class, 'index']);

    Route::post('/category/create', [CategoryController::class, 'store']);
    Route::get('/category/list', [CategoryController::class, 'index']);

    Route::post('/slider/update/{id}', [SliderController::class, 'update']);
    Route::delete('/slider/delete/{id}', [SliderController::class, 'destroy']);

    Route::post('/product/update/{id}', [ProductController::class, 'update']);
    Route::delete('/product/imagedelete/{id}', [ProductController::class, 'destroyImage']);
    Route::delete('/product/delete/{id}', [ProductController::class, 'destroy']);

    Route::post('/category/update/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/delete/{id}', [CategoryController::class, 'destroy']);

    Route::post('/subcategory/create', [SubCategoryController::class, 'store']);
    Route::post('/subcategory/update/{id}', [SubCategoryController::class, 'update']);
    Route::delete('/subcategory/delete/{id}', [SubCategoryController::class, 'destroy']);

    Route::post('/banner/update/{id}', [BannerController::class, 'update']);
    Route::delete('/banner/delete/{id}', [BannerController::class, 'destroy']);

    Route::post('/store/update/{id}', [StoreController::class, 'update']);
    Route::delete('/store/delete/{id}', [StoreController::class, 'destroy']);

    Route::post('/staff/update/{id}', [UserController::class, 'update']);
    Route::delete('/staff/delete/{id}', [UserController::class, 'destroy']);

    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy']);

});

//Category Protected Route
Route::get('/ztrade/index/{userId}', [HomeController::class, 'indexAuth']);

Route::get('/staffs', [UserController::class, 'index']);
Route::get('/customers', [UserController::class, 'getCustomers']);
Route::post('/adduser', [UserController::class, 'store']);

// Route::post('/category/create',[CategoryController::class,'store']);
// Route::post('/category/update/{id}',[CategoryController::class,'update']);
// Route::delete('/category/delete/{id}',[CategoryController::class,'destroy']);

//SubCategory Protected Route
Route::post('/subcategory/create', [SubCategoryController::class, 'store']);
Route::post('/subcategory/update/{id}', [SubCategoryController::class, 'update']);
Route::delete('/subcategory/delete/{id}', [SubCategoryController::class, 'destroy']);

//Percentage Protected Route
Route::post('/percentage/create', [PercentageController::class, 'store']);
Route::post('/percentage/update/{id}', [PercentageController::class, 'update']);
Route::get('/percentage/show/{id}', [PercentageController::class, 'show']);

//Store Protected Route
// Route::post('/store/create',[StoreController::class,'store']);


//Slider Protected Route

// Route::post('/slider/update/{id}',[SliderController::class,'update']);
// Route::delete('/slider/delete/{id}',[SliderController::class,'destroy']);

//Banner Protected Route
// Route::post('/banner/create',[BannerController::class,'store']);
// Route::post('/banner/update/{id}',[BannerController::class,'update']);
// Route::delete('/banner/delete/{id}',[BannerController::class,'destroy']);


//Product Protected Route
// Route::post('/product/create',[ProductController::class,'store']);
// Route::post('/product/update/{id}',[ProductController::class,'update']);
// Route::delete('/product/imagedelete/{id}',[ProductController::class,'destroyImage']);
// Route::delete('/product/delete/{id}',[ProductController::class,'destroy']);

//Privacy Policy Protected Route
Route::post('/privacypolicy/create', [PrivacyPolicyController::class, 'store']);
Route::post('/privacypolicy/update/{id}', [PrivacyPolicyController::class, 'update']);
Route::delete('/privacypolicy/delete/{id}', [PrivacyPolicyController::class, 'delete']);

//TermandCondition Protected Route
Route::post('/termandcondition/create', [TermandConditionController::class, 'store']);
Route::post('/termandcondition/update/{id}', [TermandConditionController::class, 'update']);
Route::delete('/termandcondition/delete/{id}', [TermandConditionController::class, 'delete']);

//AboutUs Protected Route
Route::post('/aboutus/create', [AboutUsController::class, 'store']);
Route::post('/aboutus/update/{id}', [AboutUsController::class, 'update']);
Route::delete('/aboutus/delete/{id}', [AboutUsController::class, 'delete']);

//Register



//Category
// Route::get('/category/list',[CategoryController::class,'index']);
Route::get('/category/show/{id}', [CategoryController::class, 'show']);

// SubCategory
Route::get('/subcategory/list', [SubCategoryController::class, 'index']);
Route::get('/subcategory/show/{id}', [SubCategoryController::class, 'show']);

//Store
// Route::get('/store/list',[StoreController::class,'index']);
Route::get('/store/show/{uniqueid}', [StoreController::class, 'show']);
Route::get('/store/showwithproduct/{id}', [StoreController::class, 'showproduct']);

//Slider
// Route::get('/slider/list',[SliderController::class,'index']);
Route::get('slider/show/{id}', [SliderController::class, 'show']);

//Banner
// Route::get('/banner/list',[BannerController::class,'index']);
Route::get('banner/show/{id}', [BannerController::class, 'show']);

//Product
// Route::get('/product/list',[ProductController::class,'index']);
Route::get('/product/show/{id}', [ProductController::class, 'show']);

//USer
Route::get('/user/show/{id}', [RegisterController::class, 'show']);
Route::post('/user/update/{id}', [RegisterController::class, 'update']);

//About Us
Route::get('/aboutus/list', [AboutUsController::class, 'index']);
Route::get('/aboutus/show/{id}', [AboutUsController::class, 'show']);

//Privacy Policy
Route::get('/privacypolicy/list', [PrivacyPolicyController::class, 'index']);
Route::get('/privacypolicy/show/{id}', [PrivacyPolicyController::class, 'show']);

//TermandCondition
Route::get('/termandcondition/list', [TermandConditionController::class, 'index']);
Route::get('/termandcondition/show/{id}', [TermandConditionController::class, 'show']);

//New Arrival
Route::get('/newarrival', [ProductController::class, 'newarrival']);

//Most Popular
Route::get('/mostpopular', [ProductController::class, 'mostpopular']);


//Top Selling
Route::get('/topselling', [ProductController::class, 'topselling']);
Route::get('/wishlist/{userid}', [NonAuthController::class, 'listofwishlist']);
Route::get('wishlist/list/{userId}', [NonAuthController::class, 'listofwishlist']);
//Search
Route::get('/search/{product_name}/{category_id}', [ProductController::class, 'search']);

//WishList
Route::post('/wishlist/{userid}/{product_id}', [NonAuthController::class, 'createWishList']);

Route::delete('wishlist/remove/{userid}/{product_id}', [NonAuthController::class, 'disableWishList']);

//Diable WishList

// WishList List
// Site Setting
Route::post('sitesetting/save', [SiteSettingController::class, 'upload']);
Route::post('sitesetting/update/{id}', [SiteSettingController::class, 'update']);
Route::get('sitesetting/list', [SiteSettingController::class, 'list']);

Route::get('test', function () {
    return "hello world";
});
