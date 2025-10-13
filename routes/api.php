<?php

use Illuminate\Http\Request;
use App\Http\Middleware\clientToken;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FrontendController;
use App\Http\Controllers\PaymentGateway\V1\PaymentGatewayController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::any('/api-get-token', 'API\DashboardController@generateBasicToken');

// ----------frontend---------------------
Route::any('seo/{city}/{locality}', 'API\FrontendController@testListSeo');
Route::any('seo/{city}', 'API\FrontendController@testListSeo');
Route::any('city/{city}', 'API\FrontendController@testList');
Route::any('city/{city}/{locality}', 'API\FrontendController@testList');
Route::any('pathology/{slug}/{city}', 'API\FrontendController@testDetails');
Route::any('pathology-seo/{slug}/{city}', 'API\FrontendController@testDetailsSeo');
Route::any('disease/{disease}/{city}', 'API\FrontendController@diseaseDetails');
// Route::any('disease-seo/{disease}/{city}', 'API\FrontendController@diseaseDetails');
Route::any('global-search', 'API\FrontendController@globalSearch');
Route::any('getTestbyCityId', 'API\FrontendController@getTestbyCityId');
Route::get('getcallback', 'API\FrontendController@getcallBack');
Route::get('callBackTest', 'API\FrontendController@callBackTest');
Route::any('city-list', 'API\FrontendController@cityList');
Route::any('loginAuth', 'API\AuthController@login');
// Route::any('test-details', 'testDetails')->name('testDetails');




Route::group(['prefix' => '', 'namespace' => 'API', 'middleware' => 'basicToken'], function () {
    /**
     * Start Developer Badru Changes
     */


    Route::any('/order/list', 'OrderController@list');


    Route::any('/dashboard', 'DashboardController@dashboard');
    Route::any('/states', 'StateController@stateList');
    Route::get('/getStateList', 'StateController@getStateList');
    Route::any('/cities/{state_id?}', 'CityController@cityList');
    Route::get('/getCityList', 'CityController@getCityList');

    // Route::any('/localities/{state_id?}/{city_id?}', 'LocalityController@localityList');
    // Route::any('/localities/{city?}', 'LocalityController@localityList');
    Route::any('/localities', 'LocalityController@localityList');
    // Route::any('/localilty', 'LocalitiesController@localities');
    Route::any('/categories', 'CategoryController@categoryList');
    Route::any('/category/details/{category_id}', 'CategoryController@categoryDetails');
    Route::any('/category/{slug}', 'CategoryController@categoryDetailsBySlug');

    Route::any('/subcategory/{slug}', 'CategoryController@subCategoryDetailsBySlug');
    Route::any('/subcategory/details/{subcategory_id}', 'CategoryController@subCategoryDetails');
    Route::any('/subcategories/{category_id?}', 'CategoryController@subcategoryList');




    Route::any('/centres', 'CentreController@centreList');

    Route::any('/centre/nearby', 'CentreController@nearByCentre');

    Route::any('/centre/details', 'CentreController@centreDetails');

    Route::any('/centre/{slug}', 'CentreController@centreDetailsBySlug');


    Route::any('/testimonials/list', 'TestimonialController@lists');

    Route::any('/general/details', 'SettingsController@generalDetails');

    Route::any('/available/slots', 'CommonController@availableSlots');
    Route::any('/brochure/list', 'CommonController@brochureList');
    Route::get('/terms-conditions', 'CommonController@termsConditions');
    Route::get('/getMasterArr', 'CommonController@getMasterArr');
});

// ----------Tools---------------------
Route::post('robots-validator', 'API\SEO\RobotsTxtValidatorController@robotsValidator');
Route::post('htaccess-redirect-generator', 'API\SEO\HtaccessRedirectGeneratorController@generateRedirect');
Route::post('ssl-certificate-validator', 'API\SEO\SslCertificateValidatorController@validateCertificate');
Route::post('sitemap-validator', 'API\SEO\SitemapValidatorController@validateSitemap');
Route::post('google-index-checker', 'API\SEO\GoogleIndexCheckerController@checkIndex');
Route::post('domain-age-checker', 'API\SEO\DomainAgeCheckerController@checkDomainAge');
Route::post('schema-validator', 'API\SEO\SchemaValidatorController@schemaValidator');
