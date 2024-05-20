<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify', [AuthController::class, 'verify']);
    Route::get('/check_token', [AuthController::class, 'check_token']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/upload', [AuthController::class, 'upload_iamge']);
});

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('/user', [ContactController::class, 'check_contacts']);
    Route::get('/contact/{id}', [ContactController::class, 'create_contac']);
    Route::get('/contact_show', [ContactController::class, 'show_contact']);
    Route::delete('/delete_conatct/{id}', [ContactController::class, 'delete']);
    Route::post('/send_notify_addcontact/{id}', [ContactController::class, 'send_notify_addcontact']);
    Route::get('/get_add_notification', [ContactController::class, 'get_add_notification']);
    Route::post('/send_request_amount/{id}', [RequestController::class, 'send_request_amount']);
    Route::post('/send_refused_addcontact/{id}', [ContactController::class, 'refused_add_contact_notify']);
    Route::post('/send_refused_amountrequest/{id}/{req_id}', [RequestController::class, 'send_refused_request_amount']);
    Route::post('/send_confirm_requestamount/{id}/{req_id}', [RequestController::class, 'send_confirm_request_amount']);
    Route::get('/get_amount_request', [RequestController::class, 'get_amount_request']);
    Route::get('/get_add_request', [RequestController::class, 'get_add_request']);
    Route::delete('/delete_current_acount', [ContactController::class, 'delete_current_user']);
    Route::get('/tarnsaction', [TransactionController::class, 'get_all_transactoin']);
    Route::get('/tarnsaction_last_month', [TransactionController::class, 'get_transactoin_last_month']);
    Route::delete('/notification/delete/{id}', [ContactController::class, 'delete_notification']);
    Route::delete('/notification/delete_all', [ContactController::class, 'delete_all_notifications']);
    Route::delete('/tarnsaction/delete/{id}', [TransactionController::class, 'delete_transactoin']);
    Route::delete('/tarnsaction/delete_all', [TransactionController::class, 'delete_all_transactoin']);
    Route::post('/send_message/{id}', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{id}/{count}', [ChatController::class, 'getMessages']);
    Route::get('/get_currencies', [RequestController::class, 'currencies']);
    Route::get('/invitation', [ContactController::class, 'invitation']);



});

