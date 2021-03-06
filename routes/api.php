<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\InsuranceCompanyController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\AttachPolicyController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\PaymentsController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'customers'], function () {
    Route::post('/create', [CustomersController::class, 'register']);
    Route::post('/login', [CustomersController::class, 'login']);
    Route::get('/login/google', [CustomersController::class,'redirectToProvider']);
    Route::get('/callback', [CustomersController::class,'handleProviderCallback']);
    Route::post('/logout', [CustomersController::class, 'logout'])->middleware(['auth:sanctum', 'type.customer']);
    Route::get('/getData', [CustomersController::class, 'getData'])->middleware(['auth:sanctum', 'type.customer']);
    Route::post('/uploadImage', [CustomersController::class, 'uploadImage'])->middleware(['auth:sanctum', 'type.customer']);
    Route::post('/password/email',[CustomersController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [CustomersController::class, 'reset']);
    Route::post('/editprofile', [CustomersController::class, 'editprofile'])->middleware(['auth:sanctum', 'type.customer']);
    Route::post('/editpassword', [CustomersController::class, 'editpassword'])->middleware(['auth:sanctum', 'type.customer']);
    
    Route::group(['prefix' => 'policy'], function () {
        Route::get('/list', [PolicyController::class, 'customerPolicylist'])->middleware(['auth:sanctum', 'type.customer']);
    });

    Route::group(['prefix' => 'insurance'], function () {
        Route::post('/list', [InsuranceController::class, 'listInsurance'])->middleware(['auth:sanctum', 'type.customer']);
        Route::get('/listforcustomer', [InsuranceController::class, 'listCustomer'])->middleware(['auth:sanctum', 'type.customer']);
        Route::get('/list/{insurance_id}', [InsuranceController::class, 'oneInsurance'])->middleware(['auth:sanctum', 'type.customer']);
        Route::get('/list/claims/{insurance_id}', [InsuranceController::class, 'oneInsurancewithclaims'])->middleware(['auth:sanctum', 'type.customer']);
        Route::group(['prefix' => 'payment'], function () {
            Route::post('/list', [InsuranceController::class, 'listPayments'])->middleware(['auth:sanctum', 'type.customer']);
        });
    });

    Route::group(['prefix' => 'claim'], function () {
        Route::post('/create', [ClaimController::class, 'createclaim'])->middleware(['auth:sanctum', 'type.customer']);
        Route::post('/list', [ClaimController::class, 'list_claim'])->middleware(['auth:sanctum', 'type.customer']);
        Route::get('/single/{claimid}', [ClaimController::class, 'getclaim'])->middleware(['auth:sanctum', 'type.customer']);
    });

    Route::group(['prefix' => 'insurance/buy'], function () {
        Route::post('/one', [InsuranceController::class, 'createStepone'])->middleware(['auth:sanctum', 'type.customer']);
        Route::post('/two', [InsuranceController::class, 'createSteptwo'])->middleware(['auth:sanctum', 'type.customer']);
        Route::get('/verify/{reference}', [InsuranceController::class, 'verifypayment'])->middleware(['auth:sanctum', 'type.customer']);
        Route::get('/two/{id}', [InsuranceController::class, 'getAttachment'])->middleware(['auth:sanctum', 'type.customer']);
    });
    Route::group(['prefix' => 'payments'], function () {
        Route::post('/list', [PaymentsController::class, 'paymentlistuser'])->middleware(['auth:sanctum', 'type.customer']);
        Route::get('/single/{payment_id}', [PaymentsController::class, 'singlepayment'])->middleware(['auth:sanctum', 'type.customer']);
    });
});

Route::group(['prefix' => 'admins'], function () {
    Route::post('/create', [AdminsController::class, 'createAdmin'])->middleware(['auth:sanctum', 'type.admin']);
    Route::post('/login', [AdminsController::class, 'login']);
    Route::get('/getData', [AdminsController::class, 'getData'])->middleware(['auth:sanctum', 'type.admin']);
    Route::post('/logout', [AdminsController::class, 'logout'])->middleware(['auth:sanctum', 'type.admin']);
    Route::post('/uploadImage', [AdminsController::class, 'uploadImage'])->middleware(['auth:sanctum', 'type.admin']);
    Route::post('/editprofile', [AdminsController::class, 'editprofile'])->middleware(['auth:sanctum', 'type.admin']);
    Route::post('/pin', [AdminsController::class, 'addOremove'])->middleware(['auth:sanctum', 'type.admin']);

    Route::group(['prefix' => 'insurance'], function () {
        Route::post('/create', [InsuranceCompanyController::class, 'createInsurance'])->middleware(['auth:sanctum', 'type.admin']);
        Route::post('/list', [InsuranceCompanyController::class, 'listcompany'])->middleware(['auth:sanctum', 'type.admin']);
        Route::get('/single/{company_id}', [InsuranceCompanyController::class, 'getcompany'])->middleware(['auth:sanctum', 'type.admin']);
        Route::post('/edit', [InsuranceCompanyController::class, 'editcompany'])->middleware(['auth:sanctum', 'type.admin']);
        Route::post('/delete', [InsuranceCompanyController::class, 'deletecompany'])->middleware(['auth:sanctum', 'type.admin']);
    });

    Route::group(['prefix' => 'claims'], function () {
        Route::post('/list', [ClaimController::class, 'adminlist_claim'])->middleware(['auth:sanctum', 'type.admin']);
        Route::get('/single/{claim_id}', [ClaimController::class, 'adminsingle_claim'])->middleware(['auth:sanctum', 'type.admin']);
        Route::post('/change/status', [ClaimController::class, 'claim_changestatus'])->middleware(['auth:sanctum', 'type.admin']);
    });
        // paymentlist
    Route::group(['prefix' => 'payments'], function () {
        Route::post('/list', [PaymentsController::class, 'paymentlist']);
    });    
    Route::group(['prefix' => 'policy'], function () {
        Route::post('/create', [PolicyController::class, 'createpolicy'])->middleware(['auth:sanctum', 'type.admin']);
        Route::post('/edit', [PolicyController::class, 'editpolicy'])->middleware(['auth:sanctum', 'type.admin']);
        Route::post('/list', [PolicyController::class, 'listPolicies'])->middleware(['auth:sanctum', 'type.admin']);
        Route::group(['prefix' => 'attachment'], function () {
            Route::post('/create', [AttachPolicyController::class, 'create_attachemnt'])->middleware(['auth:sanctum', 'type.admin']);
            Route::post('/edit', [AttachPolicyController::class, 'edit_attachemnt'])->middleware(['auth:sanctum', 'type.admin']);
            Route::post('/list', [AttachPolicyController::class, 'list_attachemnt'])->middleware(['auth:sanctum', 'type.admin']);
        });
    });

});

