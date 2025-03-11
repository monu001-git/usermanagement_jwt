<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\authController;
use App\Http\Controllers\admin\bannerController;
use App\Http\Controllers\admin\pageController;
use App\Http\Controllers\admin\menuController;
use App\Http\Controllers\admin\userController;
use App\Http\Controllers\admin\roleController;
use App\Http\Controllers\admin\orgMemberController;
use App\Http\Controllers\admin\orgStructureController;
use App\Http\Controllers\admin\commonController;
use App\Http\Controllers\admin\masterController;
use App\Http\Controllers\admin\noticeBoardController;
use App\Http\Controllers\admin\testimonialController;
use App\Http\Controllers\homeController;


    Route::post('/register', [authController::class, 'register']);
    Route::post('/login', [authController::class, 'login']);
   
    // Route::middleware('auth:api')->group( function () {
    //     Route::middleware([logMiddleware::class])->group(function () {

        Route::resource('users',userController::class);
        Route::resource('roles',roleController::class);
        Route::resource('banners',bannerController::class);
        Route::resource('menus',menuController::class);
        Route::resource('orgs',orgStructureController::class);
        Route::resource('notice-boards',noticeBoardController::class);
        Route::resource('testimonials',testimonialController::class);

          
        //master
        Route::get('role-master',[masterController::class,'roleMaster']);

        
        // Route::get('log',[HomeController::class,'logIndex']);

        Route::controller(commonController::class)->group(function () {
           Route::get('status-change/{status?}/{id?}/{db?}', 'StatusChange');
        });

   

//     });
// });


Route::controller(homeController::class)->group(function () {
    Route::get('header-menu', 'headerMenu');
    Route::get('org-data', 'orgData');
    Route::get('notice-boardData', 'noticeBoard');
    Route::get('testimonial-Data', 'testimonialData');
});
