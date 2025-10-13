<?php

namespace App\Http\Controllers\API;

use App\Models\Module;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Http\Controllers\API\TestController;

class FrontendController extends Controller
{

    // create a cons
    public function __construct()
    {
        $this->cities = [
             [
                'name' => 'agra',
                'id' => '23',

            ],
            [
                'name' => 'bengaluru',
                'id' => '3',

            ],
            [
                'name' => 'chennai',
                'id' => '6'
            ],
            [
                'name' => 'patna',
                'id' => '19'
            ],
            [
                'name' => 'lucknow',
                'id' => '14'
            ],
            [
                'name' => 'kolkata',
                'id' => '7'
            ],
            [
                'name' => 'gurugram',
                'id' => '55'
            ],
            [
                'name' => 'pune',
                'id' => '9'
            ],
            [
                'name' => 'jaipur',
                'id' => '10'
            ],
            [
                'name' => 'hyderabad',
                'id' => '4'
            ],
            [
                'name' => 'indore',
                'id' => '17'
            ],
            [
                'name' => 'ludhiana',
                'id' => '22'
            ],
            [
                'name' => 'meerut',
                'id' => '26'
            ],
            [
                'name' => 'ghaziabad',
                'id' => '21'
            ],
            [
                'name' => 'noida',
                'id' => '69'
            ]
        ];

        // 5 diseases – Allergy, pregnancy, liver, thyroid, heart-diseases

        $this->diseases = [
            [
                'name' => 'allergy',
            ],
            [
                'name' => 'pregnancy',

            ],
            [
                'name' => 'liver',

            ],
            [
                'name' => 'thyroid-function',

            ],
            [
                'name' => 'heart-diseases',

            ],
            [
                'name' => 'thyroid-gland-function',
            ]
        ];
    }




    public function page(Request $request, $city = null, $locality = null)
    {
        $client = new Client();
        $url = 'https://admin-api.lalpathlabs.com/api/test/GetAllTestByCategoryName?x-api-version=1&Page=1&Size=1&CityName=delhi&Itemid=WDM46,A001,B001,B080,WDM47,WDM78,WM64';

        $headers = [
            'headers' => [
                'x-access-token' => '60f291aa46ea447060f291aa46ea447019d83ba30be508e419d83ba30be508e4',
            ],
        ];

        try {

            $response = $client->get($url, $headers);
            $jsonData = $response->getBody()->getContents();

            $result = json_decode($jsonData, true);
            $page_title = ' Diagnostic Centre and Pathology Lab for Blood Test | Dr Lal PathLabs';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Home Page',
                    'url' => url($city),
                ],
            ];

            if ($request->isMethod('post')) {

                // return $request->all();
                // return implode('', $request->otp);
                $validator = Validator::make($request->all(), [
                    'name' => 'required|regex:/^[a-zA-Z]+(?:\s+[a-zA-Z]+)*$/|max:30',
                    'number' => ['required', 'regex:/^[6789]\d{9}$/'],
                    'otp' => 'required|max:4',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => Response::HTTP_BAD_REQUEST,
                        'errors' => $validator->errors()->toArray(),
                    ]);
                } else {
                    DB::beginTransaction();
                    //send enguiry in lpl
                    $enquiryResponse = $this->sendEnquiry($request, $city);
                    Log::info(['equiry lpl' => $enquiryResponse]);
                    if ($enquiryResponse['id'] !=  0) {
                        // Your successful response handling here
                        $data = $request->except(['_token', 'otp']);
                        // Insert data into the specified table with timestamps
                        $dataWithTimestamps = array_merge($data, [
                            'city' => $city,
                            'locality' => request('locality'),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        DB::table('enquiries')->insert($dataWithTimestamps);
                        DB::commit();
                        return response()->json([
                            'status' => true,
                            'message' => 'Enquiry sent successfully.',
                            'data' => $dataWithTimestamps,
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid otp.',
                            'data' => 'OTP not verified.',
                        ]);
                    }
                }
            }
            $getcity = DB::table('cities')->whereRaw('LOWER(name) = LOWER(?)', [$city])->first();

            $localityDataNew = [];
            $cities = DB::table('cities')->get();
            if ($getcity) {

                $localities = DB::table('locality')->where('city_id', $getcity->id)->take(40)->get();

                $localityData = DB::table('centres')->where('city_name', $city)->paginate(6);

                $page = DB::table('pages')->select('*')->where('page_url', url()->full())->first();

                $localityOne = DB::table('locality')->select('*')->where('slug', $locality)->first();
                if ($localityOne) {
                    if ($cities != '' and $locality != '') {
                        $localityDataNew = DB::table('centres')->where('city_name', $city)
                            ->whereRaw('LOWER(locality) LIKE ?', ['%' . strtolower($localityOne->name) . '%'])
                            ->paginate(6);
                    }
                }
            } else {
                $localities = [];
            }

            $localityData = count($localityDataNew) != 0 ? $localityDataNew ?? [] : $localityData ?? [];
            $page = $page ?? [];


            $localityOne = $localityOne ?? [];

            if ($request->ajax()) {
                // Check if it's an AJAX request and return a JSON response
                return response()->json([
                    'html' => view('frontend.page.data-load.locality-package', compact('localityData'))->render(),
                    'nextPage' => $localityData->hasMorePages() ? $localityData->currentPage() + 1 : null,
                    'hasMorePages' => $localityData->hasMorePages(),
                ]);
            }

            // Prepare SEO tags
            $master_keyword = $locality
                ? ucfirst(str_replace('-', ' ', $locality)) . ', ' . ucfirst($city)
                : ucfirst($city);

            $seo_tags = [
                'title' => $page->seo_title ?? "Book Blood Test in $master_keyword Path Labs Near Me - Dr Lal PathLabs",
                'meta' => [
                    'title' => $page->seo_title ?? "Book Blood Test in $master_keyword, Path Labs Near Me - Dr Lal PathLabs",
                    'description' => $page->seo_description ?? "Book Blood Test in $master_keyword, Path Labs Near Me - Dr Lal PathLabs",
                    'keywords' => $page->seo_keywords ?? '',
                    'robots' => 'index, follow',
                    'og' => [
                        'title' => $page->og_meta_title ?? "Book Blood Test in $master_keyword, Path Labs Near Me - Dr Lal PathLabs",
                        'site_name' => 'Dr Lal PathLabs',
                        'url' => $page->og_meta_image_url ?? $currentUrl,
                        'description' => $page->og_meta_description ?? 'Dr Lal Pathlabs has top-rated blood test labs & diagnostic centers in Surat. Blood sample home collection available. Book Lab test for accurate results.',
                        'type' => 'article',
                    ],
                    'twitter' => [
                        'card' => 'summary_large_image',
                        'description' => $page->twitter_card_description ?? '',
                        'title' => $page->twitter_card_title ?? '',
                        'site' => '@DrLalPathLabs',
                    ],
                    // 'canonical' => url()->current(),
                ],
                'schema_markup' => $page->schema_markup ?? $this->getLocalSchemaMarkup($city),
                'page_script' => $page->page_script ?? '',
            ];

            // Prepare JSON response
            return response()->json([
                'status' => true,
                'mesage' => 'Data successfully retrieved.',
                'data' => [
                    'page_title' => $page_title,
                    'breadcrumbs' => $breadcrumbs,
                    'cities' => $cities,
                    'getcity' => $getcity,
                    'localities' => $localities,
                    'localityData' => $localityData,
                    'localityOne' => $localityOne,
                    'page' => $page,
                    'tests' => $tests,
                    'packagelist' => $packagelist,
                    'pagination' => $pagination,
                    'seo' => $seo_tags,
                ],
            ], 200);
        } catch (\Exception $e) {
            // Prepare JSON response
            return response()->json([
                'status' => false,
                'mesage' => 'Data not found.',
                'data' =>  null,
            ], 402);
        }
    }


    public function testList(Request $request, $city = null, $locality = null)
    {


        $currentUrl = env('APP_URL') . 'city/' . $city;
        if ($locality) {
            $currentUrl = $currentUrl . '/' . $locality;
        }

        try {

            $page_title = ' Diagnostic Centre and Pathology Lab for Blood Test | Dr Lal PathLabs';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Home Page',
                    'url' => url($city),
                ],
            ];


            $getcity = DB::table('cities')->whereRaw('LOWER(name) = LOWER(?)', [$city])->first();
            //echo "pre>"; print_r($getcity); dd();
            $localityDataNew = [];
            $cities = DB::table('cities')->where('status', 1)->get();

            if ($getcity) {

                $localities = DB::table('locality')->where('city_id', $getcity->id)->take(40)->get();

                $localityData = DB::table('centres')->where('city_name', $city)->paginate(6);

                $page = DB::table('pages')->select('*')->where('page_url', $currentUrl)->first();
                // dd($page);
                $localityOne = DB::table('locality')->select('*')->where('slug', $locality)->first();
                if ($localityOne) {
                    if ($cities != '' and $locality != '') {
                        $localityDataNew = DB::table('centres')->where('city_name', $city)
                            ->whereRaw('LOWER(locality) LIKE ?', ['%' . strtolower($localityOne->name) . '%'])
                            ->paginate(6);
                    }
                }
            } else {
                $localities = [];
            }

            $localityData = count($localityDataNew) != 0 ? $localityDataNew ?? [] : $localityData ?? [];
            $page = $page ?? [];
            // dd($page);

            $localityOne = $localityOne ?? [];

            if ($request->ajax()) {

                // Check if it's an AJAX request and return a JSON response
                return response()->json([
                    'html' => view('frontend.page.data-load.locality-package', compact('localityData'))->render(),
                    'nextPage' => $localityData->hasMorePages() ? $localityData->currentPage() + 1 : null,
                    'hasMorePages' => $localityData->hasMorePages(),
                ]);
            }
            // get city id from the consructor
            //
            $citiesArray = $this->cities;

            $city_id = null;

            foreach ($citiesArray as $cityArray) {
                if ($cityArray['name'] == $city) {
                    $city_id = $cityArray['id'];
                }
            }
            if ($city_id == null) {
                return redirect()->back()->with('error', 'Invalid Request');
            }


            $testcontroller = new TestController();
            $pageNo = request('page') ?? 1;
            $tests = $testcontroller->getTestbyCityId($request, $city_id, $pageNo);
            $result = json_decode($tests, true);


            $packagelist = $testcontroller->packageList($request, $city_id, 1);
            // dd(json_decode($packagelist));

            $packagelist = json_decode($packagelist, true);
            // die();

            $pagination = $testcontroller->getTestbyCityId($request, $city_id, 1);
            $pagination = json_decode($pagination, true);

            // Prepare JSON response
            return response()->json([
                'status' => true,
                'mesage' => 'Data successfully retrieved.',
                'data' => [
                    // 'page_title' => $page_title,
                    // 'breadcrumbs' => $breadcrumbs,
                    'result' => $result,
                    // 'cities' => $cities,
                    // 'getcity' => $getcity,
                    // 'localities' => $localities,
                    // 'localityData' => $localityData,
                    // 'localityOne' => $localityOne,
                    // 'page' => $page,
                    // 'tests' => $tests,
                    'packagelist' => $packagelist,
                    'pagination' => $pagination,
                ],
            ], 200);
        } catch (\Exception $e) {
            // Prepare JSON response
            return response()->json([
                'status' => false,
                'mesage' => 'Data not found.',
                'data' =>  null,
            ], 402);
        }
    }


    public function testListSeo(Request $request, $city = null, $locality = null)
    {


        $currentUrl = env('APP_URL') . 'city/' . $city;
        if ($locality) {
            $currentUrl = $currentUrl . '/' . $locality;
        }

        try {

            $page_title = ' Diagnostic Centre and Pathology Lab for Blood Test | Dr Lal PathLabs';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Home Page',
                    'url' => url($city),
                ],
            ];

            // $currentUrl = 'https://www.lalpathlabs.com/test/city/agra';



            $getcity = DB::table('cities')->whereRaw('LOWER(name) = LOWER(?)', [$city])->first();
            //echo "pre>"; print_r($getcity); dd();
            $localityDataNew = [];
            $cities = DB::table('cities')->where('status', 1)->get();

            if ($getcity) {

                $localities = DB::table('locality')->where('city_id', $getcity->id)->take(40)->get();

                $localityData = DB::table('centres')->where('city_name', $city)->paginate(6);

                $page = DB::table('pages')->select('*')->where('page_url', $currentUrl)->first();


                // dd($page);
                $localityOne = DB::table('locality')->select('*')->where('slug', $locality)->first();
                if ($localityOne) {
                    if ($cities != '' and $locality != '') {
                        $localityDataNew = DB::table('centres')->where('city_name', $city)
                            ->whereRaw('LOWER(locality) LIKE ?', ['%' . strtolower($localityOne->name) . '%'])
                            ->paginate(6);
                    }
                }
            } else {
                $localities = [];
            }

            $localityData = count($localityDataNew) != 0 ? $localityDataNew ?? [] : $localityData ?? [];
            $page = $page ?? [];
            // dd($page);






            // Prepare SEO tags
            $master_keyword = $locality
                ? ucfirst(str_replace('-', ' ', $locality)) . ', ' . ucfirst($city)
                : ucfirst($city);

            $footer_content = '<div class="lab-test"><h2>Lab Test & Blood Test in {{ $master_keyword }}</h2><p>Experience the advantage of convenient and affordable lab tests in {{ $master_keyword }} through Dr Lal PathLabs. As a renowned diagnostic center, we offer an extensive range of lab test services, ensuring accessibility to accurate and dependable health information.</p> <h2 class="n-me">Blood Test Near Me and Significance of Lab Tests</h2> <h3>Diagnosis and Disease Detection</h3>

                        <p>Instrumental in diagnosing various medical conditions, lab tests analyze blood samples,
                            urine, or
                            other bodily substances. Healthcare professionals in
                            {{ $master_keyword }}
                            can pinpoint the presence of diseases or disorders, emphasizing the importance of early
                            detection for timely and effective treatment.</p>

                        <h3>Monitoring and Treatment</h3>

                        <p>For individuals already diagnosed with a health condition, lab tests play a crucial role in
                            monitoring disease progression. Regular testing allows healthcare providers in
                            {{ $master_keyword }}
                            to assess treatment effectiveness and make necessary adjustments for improved patient
                            outcomes.
                        </p>

                        <h3>Preventive Care</h3>

                        <p>Lab tests are not just reactive but also proactive in
                            {{ $master_keyword }}.
                            They are integral in preventive care, identifying potential health risks before they
                            manifest
                            into full-fledged diseases. This proactive approach empowers individuals to make lifestyle
                            changes or seek early interventions, preventing the onset of serious health issues.</p>

                        <h2>Common Blood Tests with Price in
                            {{ $master_keyword }}
                            and Nearby</h2>

                        <ul>
                            <li>HbA1c Test</li>
                            <li>Liver Function Test(Lft)</li>
                            <li>Kidney Function Test (Kft)</li>
                            <li>Lipid Profile Test</li>
                            <li>CBC Test</li>
                            <li>Cholesterol Test</li>
                            <li>Vitamin D Test</li>
                            <li>Vitamin B12 Test</li>
                            <li>CA125 Test</li>
                        </ul>

                        <h2>Dr Lal PathLabs in
                            {{ $master_keyword }}
                            : Unparalleled Commitment</h2>

                        <p>Dr Lal PathLabs in
                            {{ $master_keyword }}
                            provides a comprehensive and advanced approach to pathology tests and health diagnostics.
                            From
                            convenient scheduling to cutting-edge facilities and a commitment to excellence, our
                            services
                            significantly contribute to the well-being of individuals and the community. Choose Dr Lal
                            PathLabs in
                            {{ $master_keyword }}
                            for a holistic and reliable healthcare experience.</p>

                        <h2>Seamless Convenience at Your Doorstep</h2>

                        <p>In addition to in-lab testing, Dr Lal PathLabs in
                            {{ $master_keyword }}
                            offers the added convenience of home collection for blood tests. This service ensures
                            individuals can undergo necessary tests without leaving their homes, promoting accessibility
                            and
                            adherence to healthcare routines.</p>

                        <h2>Choosing Dr Lal PathLabs for Diagnostic Excellence in
                            {{ $master_keyword }}
                        </h2>

                        <h3>Key Factors for Consideration</h3>

                        <p>When seeking diagnostic excellence in
                            {{ $master_keyword }},
                            factors such as accuracy, reliability, affordability, and convenience take center stage. Dr
                            Lal
                            PathLabs excels in meeting these criteria, establishing itself as the preferred choice among
                            residents.</p>

                        <h3>A Benchmark in Pathology Services</h3>

                        <p>Dr Lal PathLabs in
                            {{ $master_keyword }}
                            stands out as a benchmark in pathology services, offering unparalleled expertise in sample
                            analysis and delivering accurate results. Our unwavering commitment to quality ensures that
                            individuals receive reliable information, empowering them to make informed healthcare
                            decisions.
                        </p>';


            $seo_tags = [
                'title' => $page->seo_title ?? "Book Blood Test in $master_keyword Path Labs Near Me - Dr Lal PathLabs",
                'meta' => [
                    'title' => $page->seo_title ?? "Book Blood Test in $master_keyword, Path Labs Near Me - Dr Lal PathLabs",
                    'description' => $page->seo_description ?? "Book Blood Test in $master_keyword, Path Labs Near Me - Dr Lal PathLabs",
                    'keywords' => $page->seo_keywords ?? '',
                    'robots' => 'index, follow',
                    'og' => [
                        'title' => $page->og_meta_title ?? "Book Blood Test in $master_keyword, Path Labs Near Me - Dr Lal PathLabs",
                        'site_name' => 'Dr Lal PathLabs',
                        'url' => $page->og_meta_image_url ?? $currentUrl,
                        'description' => $page->og_meta_description ?? 'Dr Lal Pathlabs has top-rated blood test labs & diagnostic centers in Surat. Blood sample home collection available. Book Lab test for accurate results.',
                        'type' => 'article',
                    ],
                    'twitter' => [
                        'card' => 'summary_large_image',
                        'description' => $page->twitter_card_description ?? '',
                        'title' => $page->twitter_card_title ?? '',
                        'site' => '@DrLalPathLabs',
                    ],
                    // 'canonical' => url()->current(),
                ],
                'schema_markup' => $page->schema_markup ?? $this->getLocalSchemaMarkup($city),
                'page_script' => $page->page_script ?? '',
                'footer_content' => $page->footer_content ?? $footer_content ?? '',
            ];




            // Prepare JSON response
            return response()->json([
                'status' => true,
                'mesage' => 'Data successfully retrieved.',
                'data' => [
                    // 'page_title' => $page_title,
                    'breadcrumbs' => $breadcrumbs,
                    'seo' => $seo_tags,
                ],
            ], 200);
        } catch (\Exception $e) {
            // Prepare JSON response
            return response()->json([
                'status' => false,
                'mesage' => 'Data not found.',
                'data' =>  null,
            ], 402);
        }
    }


    public function diseaseDetails(Request $request, $disease = null, $city = null, $locality = null)
    {

        try {
            $currentUrl = env('APP_URL') . 'disease/' . $disease;
            if ($city) {
                $currentUrl = $currentUrl . '/' . $city;
            }

            $page_title = ' Diagnostic Centre and Pathology Lab for Blood Test | Dr Lal PathLabs';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Home Page',
                    'url' => url($city),
                ],
            ];


            $getcity = DB::table('cities')->whereRaw('LOWER(name) = LOWER(?)', [$city])->first();

            $localityDataNew = [];
            $cities =  DB::table('cities')->where('status', 1)->get();
            if ($getcity) {

                $localities = DB::table('locality')->where('city_id', $getcity->id)->take(40)->get();

                $localityData = DB::table('centres')->where('city_name', $city)->paginate(6);

                $page = DB::table('pages')->select('*')->where('page_url', $currentUrl)->first();
                // dd($page);

                $localityOne = DB::table('locality')->select('*')->where('slug', $locality)->first();
                if ($localityOne) {
                    if ($cities != '' and $locality != '') {
                        $localityDataNew = DB::table('centres')->where('city_name', $city)
                            ->whereRaw('LOWER(locality) LIKE ?', ['%' . strtolower($localityOne->name) . '%'])
                            ->paginate(6);
                    }
                }
            } else {
                $localities = [];
            }

            $localityData = count($localityDataNew) != 0 ? $localityDataNew ?? [] : $localityData ?? [];
            $page = $page ?? [];
            // dd($page);

            $localityOne = $localityOne ?? [];

            if ($request->ajax()) {
                // Check if it's an AJAX request and return a JSON response
                return response()->json([
                    'html' => view('frontend.page.data-load.locality-package', compact('localityData'))->render(),
                    'nextPage' => $localityData->hasMorePages() ? $localityData->currentPage() + 1 : null,
                    'hasMorePages' => $localityData->hasMorePages(),
                ]);
            }
            // get city id from the consructor
            //
            $citiesArray = $this->cities;

            $city_id = null;

            foreach ($citiesArray as $cityArray) {
                if ($cityArray['name'] == $city) {
                    $city_id = $cityArray['id'];
                }
            }
            if ($city_id == null) {
                return redirect()->back()->with('error', 'Invalid Request');
            }



            $testcontroller = new TestController();
            $tests = $testcontroller->getTestbyCategory($request, $city_id, $disease);
            $result = json_decode($tests, true);
            // dd($result);
            if ($result['data']['result'] == null) {
                return redirect()->back()->with('error', 'Invalid Request');
            }

            $packagelist = $testcontroller->packageList($request, $city_id, 1);
            $packagelist = json_decode($packagelist, true);
            // dd($packagelist);

            $pagination = $testcontroller->getTestbyCategory($request, $city_id, $disease);
            $pagination = json_decode($pagination, true);
            // dd($pagination);


            return view('frontend.page.index2', compact('page_title', 'page_description', 'breadcrumbs', 'result', 'cities', 'getcity', 'localities', 'localityData', 'localityOne', 'page', 'packagelist', 'pagination'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }





    public function testDetails(Request $request, $slug = null, $city = null)
    {
        try {
            $currentUrl = env('APP_URL') . 'pathology/' . $slug;
            if ($city) {
                $currentUrl .= '/' . $city;
            }

            // Validate city ID
            $city_id = null;
            foreach ($this->cities as $cityArray) {
                if ($cityArray['name'] == $city) {
                    $city_id = $cityArray['id'];
                    break;
                }
            }

            if ($city_id === null || $slug === null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid city or slug.',
                ], 400);
            }

            // Fetch test and page data
            $testController = new TestController();
            $slug_name = $slug;
            $page = DB::table('pages')->where('page_url', $currentUrl)->first();
            $tests = $testController->getTestbyItemId($request, $city_id, $slug_name);
            $result = json_decode($tests, true);

            $page_title = 'Diagnostic Centre and Pathology Lab for Blood Test | Dr Lal PathLabs';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Home Page',
                    'url' => '',
                ],
            ];

            $cities = DB::table('cities')->where('status', 1)->get();
            $faqlist = "";
            $title = '';

            // Fetch FAQs if test data is available
            if ($result && isset($result['data']['result'][0]) && $result['data']['result'][0]['item_id']) {
                $item_id = $result['data']['result'][0]['item_id'];
                $title = $result['data']['result'][0]['item_name'];
                $faqlist = $this->faqList($item_id);
                $faqlist = json_decode($faqlist, true);
            }

            // Fetch related tests and packages
            $relatedTestsPackages = $testController->getRelatedTestPackage($request, $city_id);
            $relatedTestsPackages = json_decode($relatedTestsPackages, true);

            // Prepare SEO tags
            $master_keyword = $request->input('locality')
                ? ucfirst(str_replace('-', ' ', $request->input('locality'))) . ', ' . ucfirst($city)
                : ucfirst($city);

            $seo_tags = [
                'title' => $page->seo_title ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " | Lal PathLabs",
                'meta' => [
                    'title' => $page->seo_title ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " | Lal PathLabs",
                    'description' => $page->seo_description ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " from Dr. Lal Pathlabs for secure home sample collection and precise results. Book test for early diagnosis & treatment",
                    'keywords' => $page->seo_keywords ?? ucfirst($title) . " in " . ucfirst($city),
                    'robots' => 'index, follow',
                    'og' => [
                        'title' => $page->og_meta_title ?? "Book Blood " . ucfirst($title) . " Test in " . ucfirst(str_replace('-', ' ', $city)) . " | Lal PathLabs",
                        'site_name' => 'Dr Lal PathLabs',
                        'url' => $page->og_meta_image_url ?? url()->current(),
                        'description' => $page->og_meta_description ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " from Dr. Lal Pathlabs for secure home sample collection and precise results. Book test for early diagnosis & treatment",
                        'type' => 'article',
                    ],
                    'twitter' => [
                        'card' => 'summary_large_image',
                        'description' => $page->twitter_card_description ?? ucfirst($title) . " Test in " . ucfirst($city) . " from Dr. Lal Pathlabs for secure home sample collection and precise results. Book test for early diagnosis & treatment",
                        'title' => $page->twitter_card_title ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " | Lal PathLabs",
                        'site' => '@DrLalPathLabs',
                    ],
                    // 'canonical' => url()->current(),
                ],
                'schema_markup' => $page->schema_markup ?? $this->getLocalSchemaMarkup($city),
                'page_script' => $page->page_script ?? '',
            ];

            // Prepare JSON response
            return response()->json([
                'status' => true,
                'data' => [
                    'result' => $result,
                    'page_title' => $page_title,
                    'breadcrumbs' => $breadcrumbs,
                    'cities' => $cities,
                    'faqlist' => $faqlist,
                    'relatedTestsPackages' => $relatedTestsPackages,
                    'page' => $page ?? (object)[],
                    'title' => $title,
                    'seo' => $seo_tags,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function testDetailsSeo(Request $request, $slug = null, $city = null)
    {
        try {
            $currentUrl = env('APP_URL') . 'pathology/' . $slug;
            if ($city) {
                $currentUrl .= '/' . $city;
            }

            // Validate city ID
            $city_id = null;
            foreach ($this->cities as $cityArray) {
                if ($cityArray['name'] == $city) {
                    $city_id = $cityArray['id'];
                    break;
                }
            }

            if ($city_id === null || $slug === null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid city or slug.',
                ], 400);
            }

            // Fetch test and page data
            $testController = new TestController();
            $slug_name = $slug;
            $page = DB::table('pages')->where('page_url', $currentUrl)->first();
            $tests = $testController->getTestbyItemId($request, $city_id, $slug_name);
            $result = json_decode($tests, true);

            $page_title = 'Diagnostic Centre and Pathology Lab for Blood Test | Dr Lal PathLabs';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Home Page',
                    'url' => '',
                ],
            ];

            $cities = DB::table('cities')->where('status', 1)->get();
            $faqlist = "";
            $title = '';

            // Fetch FAQs if test data is available
            if ($result && isset($result['data']['result'][0]) && $result['data']['result'][0]['item_id']) {
                $item_id = $result['data']['result'][0]['item_id'];
                $title = $result['data']['result'][0]['item_name'];
                $faqlist = $this->faqList($item_id);
                $faqlist = json_decode($faqlist, true);
            }

            // Fetch related tests and packages
            $relatedTestsPackages = $testController->getRelatedTestPackage($request, $city_id);
            $relatedTestsPackages = json_decode($relatedTestsPackages, true);

            // Prepare SEO tags
            $master_keyword = $request->input('locality')
                ? ucfirst(str_replace('-', ' ', $request->input('locality'))) . ', ' . ucfirst($city)
                : ucfirst($city);

            $seo_tags = [
                'title' => $page->seo_title ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " | Lal PathLabs",
                'meta' => [
                    'title' => $page->seo_title ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " | Lal PathLabs",
                    'description' => $page->seo_description ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " from Dr. Lal Pathlabs for secure home sample collection and precise results. Book test for early diagnosis & treatment",
                    'keywords' => $page->seo_keywords ?? ucfirst($title) . " in " . ucfirst($city),
                    'robots' => 'index, follow',
                    'og' => [
                        'title' => $page->og_meta_title ?? "Book Blood " . ucfirst($title) . " Test in " . ucfirst(str_replace('-', ' ', $city)) . " | Lal PathLabs",
                        'site_name' => 'Dr Lal PathLabs',
                        'url' => $page->og_meta_image_url ?? url()->current(),
                        'description' => $page->og_meta_description ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " from Dr. Lal Pathlabs for secure home sample collection and precise results. Book test for early diagnosis & treatment",
                        'type' => 'article',
                    ],
                    'twitter' => [
                        'card' => 'summary_large_image',
                        'description' => $page->twitter_card_description ?? ucfirst($title) . " Test in " . ucfirst($city) . " from Dr. Lal Pathlabs for secure home sample collection and precise results. Book test for early diagnosis & treatment",
                        'title' => $page->twitter_card_title ?? "Book " . ucfirst($title) . " Test in " . ucfirst($city) . " | Lal PathLabs",
                        'site' => '@DrLalPathLabs',
                    ],
                    // 'canonical' => url()->current(),
                ],
                'schema_markup' => $page->schema_markup ?? $this->getLocalSchemaMarkup($city),
                'page_script' => $page->page_script ?? '',
            ];

            // Prepare JSON response
            return response()->json([
                'status' => true,
                'data' => [
                    // 'page_title' => $page_title,
                    'breadcrumbs' => $breadcrumbs,
                    'seo' => $seo_tags,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }




    function sendEnquiry($request, $city)
    {

        try {
            $otp = implode('', $request->otp);
            $name =  $request->name;
            $number =   $request->number;
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://liveapi.lalpathlabs.com/api/Common/homecollectionchemistlead',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                //   CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'PatientName' => $name,
                    'PhoneNumber' => $number,
                    'Otp' => $otp,
                    'City' => $city,
                    // 'City' => 'Hyderabad',
                    'MarketingLead' => 'true',
                    'token' => 'null',
                    'UtmCampaign' => 'null',
                    'UtmMedium' => 'Lalpath seo engine',
                    'UtmSource' => $request->page,
                    'Fbclid' => 'null',
                    'Vendor' => 'seo-initiative',
                    'opt' => 'true',
                    'tc' => 'true'
                ),
            ));

            $response = curl_exec($curl);
            // $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // echo 'HTTP Code: ' . $httpCode . PHP_EOL;

            curl_close($curl);
            // echo $response;
            // Decode the cURL response and return it as an array
            return json_decode($response, true);
        } catch (\Exception $e) {
            // Log the exception and handle it appropriately
            echo json_encode(['error' => 'Enquiry Failed.', 'message' => $e->getMessage()]);
        }
    }



    public function getModule(Request $request)
    {
        try {
            $module = Module::where('slug', $request->slug)->first();
            $page_title = $module->name;
            $page_description = 'enquiry';
            $breadcrumbs = [
                [
                    'title' => $module,
                    'url' => '',
                ],
            ];
            if ($request->isMethod('post')) {


                // Initialize an empty array for validation rules
                $rules = [];
                // Decode the JSON string into an array
                $moduleCodeArray = json_decode($module->module_code, true);
                // Loop through each field in the dynamic form data and generate validation rules
                foreach ($moduleCodeArray as $field) {
                    $fieldName = $field['name']; // Assuming each field has a 'name' attribute

                    // Example rule: If 'required' is true, make the field required
                    if ($field['required']) {
                        $rules[$fieldName] = 'required';
                    }

                    // You can add more rules based on other attributes of your form fields
                    // Example: $rules[$fieldName] = 'numeric|min:5|max:10';
                }

                $validator = Validator::make([], $rules);

                if ($validator->fails()) {
                    // Validation failed
                    return redirect()->back()->withErrors($validator)->withInput($request->input());
                }

                DB::beginTransaction();

                $data = $request->except('_token');

                foreach ($data as $key => $value) {
                    // Check if the value is an array
                    if (is_array($value)) {
                        // Convert the array to a string or handle it appropriately
                        $data[$key] = implode(', ', $value); // This example assumes you want to concatenate array values into a comma-separated string
                    }
                }

                // Specify the table name
                $tableName = $module->table_name;
                //insert time
                // Insert data into the specified table with timestamps
                $dataWithTimestamps = array_merge($data, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                // Insert data into the specified table
                DB::table($tableName)->insert($dataWithTimestamps);
                DB::commit();
                return back()->with('success', 'Enquiry sent successfully.');
            }
            return view('frontend.page.enquiry', compact('page_title', 'page_description', 'breadcrumbs', 'module'));
        } catch (\Exception $e) {
            //dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }



    public function faqList($item_id)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://admin-api.lalpathlabs.com/api/faqs/by-category',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => '{
                "CategoryId":"1",
                "ReferenceId": "' . $item_id . '"
            }',
                CURLOPT_HTTPHEADER => array(
                    'x-access-token: 60f291aa46ea447060f291aa46ea447019d83ba30be508e419d83ba30be508e4',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        } catch (\Exception $e) {
            //dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function globalSearch(Request $request)
    {
        try {
            //dd($request->all());
            $search_string = request('search_string');
            // dd($search_string);
            $citiesArray = $this->cities;

            $city = request('city');
            $city_id = null;
            foreach ($citiesArray as $cityArray) {
                if ($cityArray['name'] == $city) {
                    $city_id = $cityArray['id'];
                }
            }
            if ($city_id == null && $search_string == null) {
                return redirect()->back()->with('error', 'Invalid Request');
            }
            $testcontroller = new TestController();
            $tests = $testcontroller->globalSearch($request, $city_id, $search_string);
            //dd($tests);
            $result = json_decode($tests, true);
            return $result;
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }



    function thankYou()
    {
        return view('frontend.page.thank-you');
    }


    public function getTestbyCityId(Request $request)
    {
        $testcontroller = new TestController();
        $city = request('city');
        $city_id = null;
        foreach ($this->cities as $cityArray) {
            if ($cityArray['name'] == $city) {
                $city_id = $cityArray['id'];
            }
        }
        $page = 1;
        $tests = $testcontroller->getTestbyCityId($request, $city_id, $page);
        $result = json_decode($tests, true);
        return $result;
    }

    public function getpackagelist(Request $request)
    {
        $testcontroller = new TestController();
        $city = request('city');
        $city_id = null;
        foreach ($this->cities as $cityArray) {
            if ($cityArray['name'] == $city) {
                $city_id = $cityArray['id'];
            }
        }
        $page = 1;
        $tests = $testcontroller->packageList($request, $city_id, $page);
        $result = json_decode($tests, true);
        return $result;
    }



    public function getcallBack(Request $request)
    {

        $phone = request('phone');
        $city = request('city');
        $this->validate($request, [
            'phone' => 'digits:10',
        ]);
        if ($phone == null || $city == null) {
            return redirect()->back()->with('error', 'Invalid Request');
        }

        $page = $request->page ? $request->page : '';
        if ($phone == null || $city == null) {
            return redirect()->back()->with('error', 'Invalid Request');
        }
        $data = [
            'name' => 'Guest',
            'number' => $phone,
            'city' => $city,
            'page' => $page,
            'form' => 'Request call back form',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        // Insert data into the specified table
        $data = DB::table('enquiries')->insert($data);
        //  DB::getPdo()->lastInsertId();
        DB::commit();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://liveapi.lalpathlabs.com/api/Common/homecollectionchemistlead',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('PatientName' => 'Guest', 'PhoneNumber' => $phone, 'Otp' => '', 'City' => $city, 'MarketingLead' => 'true', 'token' => 'null', 'UtmCampaign' => '', 'UtmMedium' => '', 'UtmSource' => '', 'Fbclid' => '', 'Vendor' => 'SEO_Page', 'opt' => 'true', 'tc' => 'true'),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }


    public function  callBackTest()
    {

        $curl = curl_init();
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://liveapi.lalpathlabs.com/api/Common/homecollectionchemistlead',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('PatientName' => 'suraj', 'PhoneNumber' => '9625168500', 'Otp' => '', 'City' => 'agra', 'MarketingLead' => 'true', 'token' => 'null', 'UtmCampaign' => 'null', 'UtmMedium' => 'null', 'UtmSource' => 'null', 'Fbclid' => 'null', 'Vendor' => 'null', 'opt' => 'true', 'tc' => 'true'),
        ));
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            print_r($error);
            curl_close($curl);
        } else {
            return $response;
            curl_close($curl);
        }
    }


    // Placeholder for getLocalSchemaMarkup function
    private function getLocalSchemaMarkup($city)
    {
        // Implement schema markup generation logic here
        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => ucfirst($city),
            ],
            'name' => 'Dr Lal PathLabs',
        ]);
    }



    function cityList()
    {

        try {
            $cities =  $this->cities;

            // Prepare JSON response
            return response()->json([
                'status' => true,
                'mesage' => 'Data successfully retrieved.',
                'data' => [
                    'result' => $cities,
                ],
            ], 200);
        } catch (\Exception $e) {
            // Prepare JSON response
            return response()->json([
                'status' => false,
                'mesage' => 'Data not found.',
                'data' =>  null,
            ], 402);
        }
    }
}
