<?php

use Illuminate\Support\Facades\Route;

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
// Route::get('phpinfo', function(){
//     phpinfo();
// });
Route::get('/', 'Auth\LoginController@index');
Route::get('/admin', 'Auth\LoginController@index');
Route::get('/admin/login', 'Auth\LoginController@index');
Route::post('/admin/auth/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout');



Route::get('/ajax/cities/{state_id}', 'CommonController@getAjaxCities');
Route::get('/ajax/subcategories', 'CommonController@getAjaxSubCategories');
Route::get('/ajax/ruleCombinations', 'CommonController@ruleCombinations');
Route::get('/ajax/specilities', 'CommonController@getAjaxSpecilities');
Route::get('/ajax/checkSeoSlug', 'CommonController@checkSeoSlug');



Route::group(['prefix' => 'admin', 'namespace' => 'admin', 'middleware' => 'Checksession'], function () {


    // Tested URL's


    /**
     *  admin/dashboard
     */
    Route::get('dashboard', 'DashboardController@dashboard');



    /**
     * Page Master
     */

    Route::get('/page/list', 'PageController@index');
    Route::any('/page/add', 'PageController@add');
    Route::any('/page/edit/{id}', 'PageController@edit');
    Route::any('/page/delete/{id}', 'PageController@delete');
    Route::any('/page/update-status/{id}/{status}', 'PageController@updateStatus');


    /**
     *  Category Master
     */

    Route::get('/categories/list', 'CategoryController@index');
    Route::any('/categories/add', 'CategoryController@add');
    Route::get('/categories/export', 'CategoryController@exportExcel');
    Route::any('/categories/import', 'CategoryController@import');
    Route::any('/categories/edit/{id}', 'CategoryController@edit');
    Route::any('/categories/delete/{id}', 'CategoryController@delete');
    Route::any('/categories/update-status/{id}/{status}', 'CategoryController@updateStatus');
    Route::any('/categories/sync', 'CategoryController@sync');


    /**
     *  Sub Category Master
     */
    Route::any('/subcategories/list/', 'SubCategoryController@index');
    Route::any('/subcategories/list/{category_id}', 'SubCategoryController@index');
    Route::any('/subcategories/add/{category_id}', 'SubCategoryController@add');
    Route::any('/subcategories/edit/{category_id}/{id}', 'SubCategoryController@edit');
    Route::any('/subcategories/delete/{id}', 'SubCategoryController@delete');
    Route::any('/subcategories/update-status/{id}/{status}', 'SubCategoryController@updateStatus');
    Route::any('/subcategories/sync', 'SubCategoryController@sync');



    /**
     * Items Master
     */
    Route::get('/items/list', 'ItemController@index');
    Route::any('/items/add', 'ItemController@add');
    Route::any('/items/import', 'ItemController@import');
    Route::get('/items/export', 'ItemController@exportExcel');
    Route::any('/items/edit/{id}', 'ItemController@edit');
    Route::any('/items/update-status/{id}/{status}', 'ItemController@updateStatus');
    Route::any('/items/delete/{id}', 'ItemController@delete');
    Route::any('/items/sync', 'ItemController@sync');


    /**
     * City Master
     */
    Route::get('/city/list', 'CityController@index');
    Route::any('/city/add', 'CityController@add');
    Route::get('/city/export', 'CityController@exportExcel');
    Route::any('/city/import', 'CityController@import');
    Route::any('/city/edit/{city_id}', 'CityController@edit');
    Route::any('/city/update-status/{city_id}/{status}', 'CityController@updateStatus');
    Route::any('/city/{city_id}/locality/{state_id?}', 'CityController@localityList');
    Route::any('/city/sync', 'CityController@sync');

    /**
     *  State Master
     */
    Route::get('/state/list', 'StateController@index');
    Route::any('/state/add', 'StateController@add');
    Route::get('/state/export', 'StateController@exportExcel');
    Route::any('/state/import', 'StateController@import');
    Route::any('/state/edit/{state_id}', 'StateController@edit');
    Route::any('/state/update-status/{state_id}/{status}', 'StateController@updateStatus');
    Route::any('/state/sync', 'StateController@sync');
    Route::any('/state/StateAPi', 'StateController@StateAPi');



    /**
     * City Master
     */
    Route::get('/locality/list', 'LocalityController@index');
    Route::any('/locality/add/{state_id?}/{city_id?}', 'LocalityController@add');
    Route::get('/locality/export', 'LocalityController@exportExcel');
    Route::any('/locality/import', 'LocalityController@import');
    Route::any('/locality/edit/{state_id?}/{city_id?}/{locality_id?}', 'LocalityController@edit');
    Route::any('/locality/update-status/{locality_id}/{status}', 'LocalityController@updateStatus');
    Route::any('/locality/sync', 'LocalityController@sync');


    /**
     * Center Master
     */
    Route::get('/centres/list', 'CentreController@index');
    Route::any('/centres/add', 'CentreController@add');
    Route::get('/centres/export', 'CentreController@exportExcel');
    Route::any('/centres/import', 'CentreController@import');
    Route::any('/centres/update-status/{id}/{status}', 'CentreController@updateStatus');
    Route::any('/centres/delete/{id}', 'CentreController@delete');
    Route::any('/centres/edit/{id}', 'CentreController@edit');
    Route::any('/centres/sync', 'CentreController@sync');



    /**
     *  Enquiry Master
     */


    Route::get('/enquiry/list', 'EnquiryController@index');
    Route::any('/enquiry/add', 'EnquiryController@add');
    Route::any('/enquiry/edit/{id}', 'EnquiryController@edit');
    Route::any('/enquiry/delete/{id}', 'EnquiryController@delete');
    Route::any('/enquiry/export', 'EnquiryController@exportExcel');
    Route::any('/enquiry/update-status/{id}/{status}', 'EnquiryController@updateStatus');



    /**
     *  testimonials Master
     */

    Route::get('/testimonials/list', 'TestimonialController@index');
    Route::any('/testimonials/add', 'TestimonialController@add');
    Route::any('/testimonials/edit/{id}', 'TestimonialController@edit');
    Route::any('/testimonials/delete/{id}', 'TestimonialController@delete');
    Route::any('/testimonials/export', 'TestimonialController@exportExcel');
    Route::any('/testimonials/update-status/{id}/{status}', 'TestimonialController@updateStatus');



    /**
     *  Templates Master
     */
    Route::get('/templates/list', 'TemplateController@index');
    Route::any('/templates/add', 'TemplateController@add');
    Route::any('/templates/edit/{id}', 'TemplateController@edit');
    Route::any('/templates/delete/{id}', 'TemplateController@delete');
    Route::any('/templates/update-status/{id}/{status}', 'TemplateController@updateStatus');

    /**
     *  Rules Master
     */
    Route::get('/rules/list', 'RuleController@index');
    Route::any('/rules/add', 'RuleController@add');
    Route::any('/rules/edit/{id}', 'RuleController@edit');
    Route::any('/rules/delete/{id}', 'RuleController@delete');
    Route::any('/rules/update-status/{id}/{status}', 'RuleController@updateStatus');




    /* * User Management
     */
    // Route::get('/', 'UserController@index');
    Route::get('/users/list', 'UserController@index');
    Route::any('/users/add-user', 'UserController@addUser');
    Route::any('/users/edit/{id}', 'UserController@editUser');
    Route::any('/users/delete/{id}', 'UserController@delete');
    Route::any('/users/update-status/{id}/{status}', 'UserController@updateStatus');

    /**
     * Roles
     */
    Route::get('/roles/list', 'RoleController@index');
    Route::any('/roles/permissions/{role_id}', 'RoleController@permissions');
    Route::any('/roles/edit/{role_id}', 'RoleController@edit');
    Route::any('/roles/add', 'RoleController@add');

    /**
     * Settings
     */
    Route::any('/settings', 'SettingsController@index');
    Route::post('/admin/settings/api', 'SettingsController@ApiDetails');


});
// });




Route::get('storage-link', function () {
    \Artisan::call('storage:link');
    echo 'Storage link created successfully.';
});


Route::get('storage-delink', function () {
    \Artisan::call('storage:link');
    echo 'Storage link deleted successfully.';
});


Route::get('clear-cache', function () {
    \Artisan::call('optimize:clear');
    echo 'Cache cleared successfully.';
});

Route::get('/check-env', function () {
    return [
        'XCUBE_BASE_URL' => config('api.XCUBE_BASE_URL'),
        'XCUBE_LOGIN_BASE_URL' => config('api.XCUBE_LOGIN_BASE_URL'),
        'secretKey' => config('api.secretKey'),
        'X_APP_ID' => config('api.X_APP_ID'),
        'X_SOURCE' => config('api.X_SOURCE'),
        'X_APP_VERSION' => config('api.X_APP_VERSION'),
    ];
});

Route::get('/refresh-guest-token', function () {
    try {
        $auth = new App\Http\Controllers\API\AuthController();
        $request = request();
        $response = $auth->loginbyGuest($request);
        $data = json_decode($response, true);
        
        if (!empty($data['data']['token'])) {
            $newToken = $data['data']['token'];
            Cache::put('guest_token6', $newToken, now()->addMinutes(50));
            Log::info('Background Guest Token Refreshed Successfully');
        }
    } catch (\Exception $e) {
        Log::warning('Background token refresh failed', ['error' => $e->getMessage()]);
    }
    return response()->json(['status' => 'ok']);
})->name('refresh-guest-token');
// Route::get('remove', function () {
// //    How To Restore Files.txt
//     $file = 'HOW TO RESTORE FILES.txt';

//     // SEARCH FILE NAME IN WHOLE PROJECT DIRECTORY AND DELETE IT

//     $path = base_path();
//     $directory = new RecursiveDirectoryIterator($path);
//     $iterator = new RecursiveIteratorIterator($directory);
//     $files = new RegexIterator($iterator, '/^.+\.txt$/i', RecursiveRegexIterator::GET_MATCH);

//     foreach ($files as $file) {
//         if (file_exists($file[0])) {
//             unlink($file[0]);
//         }
//     }

//     echo 'File removed successfully.';

// });

// generate sitemap
// Route::get('/sitemap', function () {
//     sitemapCity();
//     sitemapTest();
//     echo "Sitemap generated successfully.";
// });
// generate sitemap and download as zip

Route::get('/sitemap', function () {
    sitemapCity();
    sitemapTest();

    $zipFileName = 'sitemaps.zip';
    $sitemapDir = base_path('test'); // Directory where all sitemaps are generated

    // Create a temporary zip file
    $zipPath = storage_path('app/' . $zipFileName); // Or public_path() if you prefer

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        // Add all files from the 'test' directory recursively
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sitemapDir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sitemapDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    } else {
        return response()->json(['error' => 'Could not create zip file'], 500);
    }

    // Trigger download and optionally delete the zip afterwards
    return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

    // If you want to just generate without download, comment the above and echo message
    // echo "Sitemap generated and zipped successfully.";
});


function sitemapCity()
{
     $cities = App\Models\City::where('status', 1)->get();

    // $sitemapPath = public_path('test/sitemap-se.xml');
    $sitemapPath = base_path('test/sitemap-se.xml');

    // Ensure the directory exists
    $sitemapDir = dirname($sitemapPath);
    if (!file_exists($sitemapDir)) {
        mkdir($sitemapDir, 0777, true);
    }

    // If file exists, remove it before creating new
    if (file_exists($sitemapPath)) {
        unlink($sitemapPath);
    }

    $sitemap = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>');
    foreach ($cities as $city) {
        $sitemapItem = $sitemap->addChild('sitemap');
        $sitemapItem->addChild('loc', "https://www.lalpathlabs.com/test/sitemap-test-" . strtolower($city->slug) . ".xml");
        $sitemapItem->addChild('lastmod', date('Y-m-d'));
    }
    $sitemap->asXML($sitemapPath);
}

function sitemapTest(){
    $cities = App\Models\City::where('status', 1)->pluck('slug');

    // dd( $cities );

    foreach ($cities as $city) {

        // $sitemapPath = public_path("test/sitemap-test-{$city}.xml");
        $sitemapPath = base_path("test/sitemap-test-{$city}.xml");

        // disease/allergy/agra where agra is the city is found at end of the url
        $pages = App\Models\Pages::where('status', 1)->where('slug', 'LIKE', "%/{$city}")->get();

        // dd($sitemapPath);

        // Ensure the directory exists
        $sitemapDir = dirname($sitemapPath);
        if (!file_exists($sitemapDir)) {
            mkdir($sitemapDir, 0777, true);
        }

        // If file exists, remove it before creating new
        if (file_exists($sitemapPath)) {
            unlink($sitemapPath);
        }

        $sitemap = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"></urlset>');
        foreach ($pages as $page) {
            $url = $sitemap->addChild('url');
            $url->addChild('loc', "https://www.lalpathlabs.com/test/{$page->slug}");
            $url->addChild('lastmod', date('Y-m-d'));
            $url->addChild('changefreq', 'daily');
            $url->addChild('priority', '0.9');
        }
        $sitemap->asXML($sitemapPath);
    }
}
