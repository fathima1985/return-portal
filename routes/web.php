<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ShipmentLabelsController;
use App\Http\Controllers\DashboardController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['scheme' => 'https'], function () {
	Route::get('/', [HomeController::class, 'home']);
	Route::post('confirm-shipping', [HomeController::class, 'ConfirmShipping']);
	Route::get('confirm-shipping', [HomeController::class, 'ConfirmShipping']);
	
});

Route::get('/', [HomeController::class, 'home']);
Route::post('confirm-shipping', [HomeController::class, 'ConfirmShipping']);
Route::get('confirm-shipping', [HomeController::class, 'ConfirmShippingMethod']);
Route::post('post-summary', [HomeController::class, 'ConfirmSummary']);

Route::post('do-payment', [HomeController::class, 'DoPayment']);
//Route::get('do-payment', [HomeController::class, 'ConfirmPayment']);
Route::get('return-summary', [HomeController::class, 'ReturnSummary']);
Route::get('get-order-details', [HomeController::class, 'PullOrdersdetails']);
Route::post('complete-payment', [PaymentController::class, 'index']);
Route::post('proccess-payment', [PaymentController::class, 'ProcessPayment']);


Route::post('client/notification', [ClientController::class, 'index']);
Route::get('client/notification', [ClientController::class, 'index']);

//Route::get('confirm/create-label/{shipment_id}', [ShipmentLabelsController::class, 'index']);
Route::get('confirm/create-label/', [ShipmentLabelsController::class, 'CreateLabel']);
Route::get('return-complete/{shipment_id}/{order_id}', [HomeController::class, 'thanks']); 
Route::get('return-request/{shipment_id}/{order_id}', [HomeController::class, 'returnView'])->name('return-view'); 
Route::get('webhook', [PaymentController::class, 'WebHookIndex'])->name('WebHookIndex'); 
Route::post('webhook', [PaymentController::class, 'WebHookIndex'])->name('WebHookIndex-post'); 



Route::group(['middleware' => 'auth'], function () {


	Route::get('dashboard', [DashboardController::class, 'DashboardHome'])->name('dashboard'); 
	Route::get('return-requests', [DashboardController::class, 'ShipmentLists'])->name('shipment-lists'); 
	Route::get('/return-item/{shipmentId}', [DashboardController::class, 'ViewShipment'])->name('return-item'); 
	Route::post('update-shipment', [DashboardController::class, 'UpdateShipment']); 
	Route::post('/update-comments', [DashboardController::class, 'UpdateComments']); 
	Route::get('/users', [InfoUserController::class, 'Users'])->name('users');
	Route::get('/create-user', [InfoUserController::class, 'createUser'])->name('create-user'); 
	Route::post('/create-new-user', [InfoUserController::class, 'createNewUser'])->name('new-user'); 

    Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/edit-profile', [InfoUserController::class, 'EditUser']);
	
	Route::post('/user-profile', [InfoUserController::class, 'store']);
	Route::get('/user-delete', [InfoUserController::class, 'DeleteUser'])->name('delete');
	Route::get('/delete-request/{shipmentId}', [DashboardController::class, 'DeleteRequest'])->name('delete-request');

	Route::post('/assign-users', [DashboardController::class, 'assingTasks'])->name('assign-users'); 
	Route::get('/assign-users', [DashboardController::class, 'assingTasks'])->name('assign-user'); 

	
    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');

	
	
   
	/*Route::get('dashboard', function () {
		return view('dashboard');
	})->name('dashboard');

	Route::get('billing', function () {
		return view('billing');
	})->name('billing');

	Route::get('profile', function () {
		return view('profile');
	})->name('profile');

	Route::get('rtl', function () {
		return view('rtl');
	})->name('rtl');

	Route::get('user-management', function () {
		return view('laravel-examples/user-management');
	})->name('user-management');

	Route::get('tables', function () {
		return view('tables');
	})->name('tables');

    Route::get('virtual-reality', function () {
		return view('virtual-reality');
	})->name('virtual-reality');

    Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

    Route::get('static-sign-up', function () {
		return view('static-sign-up');
	})->name('sign-up'); */
});



Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');