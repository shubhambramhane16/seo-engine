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
use App\Models\City;

class FrontendController extends Controller
{
    public function __construct()
    {
        $this->cities = [
            ['name' => 'agra', 'id' => '23'],
            ['name' => 'bengaluru', 'id' => '3'],
            ['name' => 'chennai', 'id' => '6'],
            ['name' => 'patna', 'id' => '19'],
            ['name' => 'lucknow', 'id' => '14'],
            ['name' => 'kolkata', 'id' => '7'],
            ['name' => 'gurugram', 'id' => '55'],
            ['name' => 'pune', 'id' => '9'],
            ['name' => 'jaipur', 'id' => '10'],
            ['name' => 'hyderabad', 'id' => '4'],
            ['name' => 'indore', 'id' => '17'],
            ['name' => 'ludhiana', 'id' => '22'],
            ['name' => 'meerut', 'id' => '26'],
            ['name' => 'ghaziabad', 'id' => '21'],
            ['name' => 'noida', 'id' => '69'],
            ['name' => 'bhubaneswar', 'id' => '58'],
            ['name' => 'muzaffarpur', 'id' => '126'],
            ['name' => 'ranchi', 'id' => '38'],
            ['name' => 'begusarai', 'id' => '182'],
            ['name' => 'darbhanga', 'id' => '154'],
            ['name' => 'dhanbad', 'id' => '33'],
            ['name' => 'jamshedpur', 'id' => '70'],
            ['name' => 'purnia', 'id' => '165'],
            ['name' => 'siwan', 'id' => '278'],
            ['name' => 'kochi', 'id' => '74'],
            ['name' => 'mumbai', 'id' => '1'],
            ['name' => 'bhopal', 'id' => '16'],
            ['name' => 'gwalior', 'id' => '39'],
            ['name' => 'raipur', 'id' => '45'],
            ['name' => 'bhilai', 'id' => '71'],
            ['name' => 'bilaspur', 'id' => '134'],
            ['name' => 'jabalpur', 'id' => '40'],
            ['name' => 'agartala', 'id' => '114'],
            ['name' => 'guwahati', 'id' => '48'],
            ['name' => 'shillong', 'id' => '316'],
            ['name' => 'silchar', 'id' => '314'],
            ['name' => 'kota', 'id' => '63'],
            ['name' => 'udaipur', 'id' => '102'],
            ['name' => 'jodhpur', 'id' => '43'],
            ['name' => 'bahadurgarh', 'id' => '2378'],
            ['name' => 'hisar', 'id' => '307'],
            ['name' => 'karnal', 'id' => '159'],
            ['name' => 'palwal', 'id' => '2422'],
            ['name' => 'rohtak', 'id' => '122'],
            ['name' => 'sirsa', 'id' => '251'],
            ['name' => 'sonipat', 'id' => '168'],
            ['name' => 'yamuna-nagar', 'id' => '211'],
            ['name' => 'amritsar', 'id' => '34'],
            ['name' => 'chandigarh', 'id' => '46'],
            ['name' => 'jalandhar', 'id' => '57'],
            ['name' => 'panipat', 'id' => '153'],
            ['name' => 'ambala', 'id' => '239'],
            ['name' => 'bathinda', 'id' => '160'],
            ['name' => 'hamirpur', 'id' => '2500'],
            ['name' => 'hoshiarpur', 'id' => '308'],
            ['name' => 'pathankot', 'id' => '312'],
            ['name' => 'patiala', 'id' => '112'],
            ['name' => 'mohali', 'id' => '310'],
            ['name' => 'jammu', 'id' => '92'],
            ['name' => 'srinagar', 'id' => '31'],
            ['name' => 'haldwani', 'id' => '306'],
            ['name' => 'haridwar', 'id' => '195'],
            ['name' => 'dehradun', 'id' => '77'],
            ['name' => 'aligarh', 'id' => '56'],
            ['name' => 'bareilly', 'id' => '52'],
            ['name' => 'bijnor', 'id' => '303'],
            ['name' => 'deoria', 'id' => '304'],
            ['name' => 'faizabad', 'id' => '305'],
            ['name' => 'firozabad', 'id' => '73'],
            ['name' => 'gorakhpur', 'id' => '66'],
            ['name' => 'moradabad', 'id' => '311'],
            ['name' => 'saharanpur', 'id' => '65'],
            ['name' => 'sultanpur', 'id' => '8589'],
            ['name' => 'prayagraj', 'id' => '36'],
            ['name' => 'varanasi', 'id' => '30'],
            ['name' => 'cuttack', 'id' => '72'],
            ['name' => 'dibrugarh', 'id' => '984'],
            ['name' => 'durgapur', 'id' => '78'],
            ['name' => 'howrah', 'id' => '37'],
            ['name' => 'siliguri', 'id' => '89'],
            ['name' => 'delhi', 'id' => '2'],
            ['name' => 'faridabad', 'id' => '25'],
            ['name' => 'greater-noida', 'id' => '9724'],
            ['name' => 'gaya', 'id' => '100'],
            ['name' => 'panchkula', 'id' => '216'],
            ['name' => 'ahmedabad', 'id' => '5'],
        ];

        $this->diseases = [
            ['name' => 'allergy'],
            ['name' => 'pregnancy'],
            ['name' => 'liver'],
            ['name' => 'thyroid-function'],
            ['name' => 'heart-diseases'],
            ['name' => 'thyroid-gland-function'],
        ];
    }

private function safeApiCall($callback, $cacheKey = null, $cacheMinutes = 60)
{
    $attempt = 0;
    $maxAttempts = 5;

    while ($attempt < $maxAttempts) {
        $attempt++;
        $response = $callback();

        if (!$this->isInvalidResponse($response)) {
            // Success → Cache kar do
            if ($cacheKey) {
                Cache::put($cacheKey, $response, now()->addMinutes($cacheMinutes));
                Log::info("API Success → Cached", ['key' => $cacheKey]);
            }
            return $response;
        }

        Log::warning("API Attempt #$attempt failed", [
            'preview' => substr($response, 0, 300),
            'key' => $cacheKey
        ]);

        if ($attempt < $maxAttempts) {
            usleep(1200000); // 1.2 sec wait
        }
    }

    // Sab attempts fail → Cache se dikhao
    if ($cacheKey && Cache::has($cacheKey)) {
        $cached = Cache::get($cacheKey);
        Log::info("All attempts failed → Serving from CACHE", ['key' => $cacheKey]);
        return $cached;
    }

    Log::error("API & Cache both failed!", ['key' => $cacheKey]);
    return json_encode([
        'status' => false,
        'mesage' => 'Service temporarily unavailable. Please try again later.',
        'data' => null
    ]);
}

private function isInvalidResponse($response)
{
    if (empty($response) || trim($response) === '' || trim($response) === 'null' || $response === null) {
        return true;
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return true;
    }

    if (isset($data['status']) && $data['status'] === false) {
        return true;
    }

    if (
        !isset($data['data']['result']) ||
        $data['data']['result'] === null ||
        $data['data']['result'] === [] ||
        (is_array($data['data']['result']) && count($data['data']['result']) === 0)
    ) {
        return true;
    }

    return false;
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
                    $enquiryResponse = $this->sendEnquiry($request, $city);
                    Log::info(['equiry lpl' => $enquiryResponse]);
                    if ($enquiryResponse['id'] != 0) {
                        $data = $request->except(['_token', 'otp']);
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
                return response()->json([
                    'html' => view('frontend.page.data-load.locality-package', compact('localityData'))->render(),
                    'nextPage' => $localityData->hasMorePages() ? $localityData->currentPage() + 1 : null,
                    'hasMorePages' => $localityData->hasMorePages(),
                ]);
            }

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
                ],
                'schema_markup' => $page->schema_markup ?? $this->getLocalSchemaMarkup($city),
                'page_script' => $page->page_script ?? '',
            ];

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
                    'tests' => $tests ?? [],
                    'packagelist' => $packagelist ?? [],
                    'pagination' => $pagination ?? [],
                    'seo' => $seo_tags,
                ],
            ], 200);
        } catch (\Exception $e) {
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
}
    }

    public function testList(Request $request, $city = null, $locality = null)
    {
        $currentUrl = 'https://www.lalpathlabs.com/test/city/' . $city . ($locality ? '/' . $locality : '');

        try {
            $page_title = ' Diagnostic Centre and Pathology Lab for Blood Test | Dr Lal PathLabs';
            $page_description = '';
            $breadcrumbs = [
                ['title' => 'Home Page', 'url' => url($city)],
            ];

            $getcity = DB::table('cities')->whereRaw('LOWER(name) = LOWER(?)', [$city])->first();
            $localityDataNew = [];
            $cities = DB::table('cities')->where('status', 1)->get();

            if ($getcity) {
                $localities = DB::table('locality')->where('city_id', $getcity->id)->take(40)->get();
                $localityData = DB::table('centres')->where('city_name', $city)->paginate(6);
                $page = DB::table('pages')->select('*')->where('page_url', $currentUrl)->first();
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
                return response()->json([
                    'html' => view('frontend.page.data-load.locality-package', compact('localityData'))->render(),
                    'nextPage' => $localityData->hasMorePages() ? $localityData->currentPage() + 1 : null,
                    'hasMorePages' => $localityData->hasMorePages(),
                ]);
            }

            $city_id = null;
            foreach ($this->cities as $cityArray) {
                if ($cityArray['name'] == $city) {
                    $city_id = $cityArray['id'];
                    break;
                }
            }
            if ($city_id == null) {
                return redirect()->back()->with('error', 'Invalid Request');
            }

            $testcontroller = new TestController();
            $pageNo = $request->input('page') ?? 1;

            $tests = $this->safeApiCall(function () use ($testcontroller, $request, $city_id, $pageNo) {
                return $testcontroller->getTestbyCityId($request, $city_id, $pageNo);
            });
            $result = json_decode($tests, true);

            $packagelist = $this->safeApiCall(function () use ($testcontroller, $request, $city_id) {
                return $testcontroller->packageList($request, $city_id, 1);
            });
            $packagelist = json_decode($packagelist, true);


            $pagination = $testcontroller->getTestbyCityId($request, $city_id, 1);
            $pagination = json_decode($pagination, true);


            return response()->json([
                'status' => true,
                'mesage' => 'Data successfully retrieved.',
                'data' => [
                    'result' => $result,
                    'packagelist' => $packagelist,
                    'pagination' => $pagination,
                ],
            ], 200);
        } catch (\Exception $e) {
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
}
    }

    public function testListSeo(Request $request, $city = null, $locality = null)
    {
        $currentUrl = 'https://www.lalpathlabs.com/test/' . 'city/' . $city;
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

            //  $currentUrl = 'https://www.lalpathlabs.com/test/city/'.$city;

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

            $footer_content = '<div class="lab-test"><h2>Lab Test & Blood Test in ' . $master_keyword . '</h2><p>Experience the advantage of convenient and affordable lab tests in ' . $master_keyword . ' through Dr Lal PathLabs. As a renowned diagnostic center, we offer an extensive range of lab test services, ensuring accessibility to accurate and dependable health information.</p> <h2 class="n-me">Blood Test Near Me and Significance of Lab Tests</h2> <h3>Diagnosis and Disease Detection</h3>

                        <p>Instrumental in diagnosing various medical conditions, lab tests analyze blood samples,
                            urine, or
                            other bodily substances. Healthcare professionals in
                            ' . $master_keyword . '
                            can pinpoint the presence of diseases or disorders, emphasizing the importance of early
                            detection for timely and effective treatment.</p>

                        <h3>Monitoring and Treatment</h3>

                        <p>For individuals already diagnosed with a health condition, lab tests play a crucial role in
                            monitoring disease progression. Regular testing allows healthcare providers in
                            ' . $master_keyword . '
                            to assess treatment effectiveness and make necessary adjustments for improved patient
                            outcomes.
                        </p>

                        <h3>Preventive Care</h3>

                        <p>Lab tests are not just reactive but also proactive in
                            ' . $master_keyword . '.
                            They are integral in preventive care, identifying potential health risks before they
                            manifest
                            into full-fledged diseases. This proactive approach empowers individuals to make lifestyle
                            changes or seek early interventions, preventing the onset of serious health issues.</p>

                        <h2>Common Blood Tests with Price in
                            ' . $master_keyword . '
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
                            ' . $master_keyword . '
                            : Unparalleled Commitment</h2>

                        <p>Dr Lal PathLabs in
                            ' . $master_keyword . '
                            provides a comprehensive and advanced approach to pathology tests and health diagnostics.
                            From
                            convenient scheduling to cutting-edge facilities and a commitment to excellence, our
                            services
                            significantly contribute to the well-being of individuals and the community. Choose Dr Lal
                            PathLabs in
                            ' . $master_keyword . '
                            for a holistic and reliable healthcare experience.</p>

                        <h2>Seamless Convenience at Your Doorstep</h2>

                        <p>In addition to in-lab testing, Dr Lal PathLabs in
                            ' . $master_keyword . '
                            offers the added convenience of home collection for blood tests. This service ensures
                            individuals can undergo necessary tests without leaving their homes, promoting accessibility
                            and
                            adherence to healthcare routines.</p>

                        <h2>Choosing Dr Lal PathLabs for Diagnostic Excellence in
                            ' . $master_keyword . '
                        </h2>

                        <h3>Key Factors for Consideration</h3>

                        <p>When seeking diagnostic excellence in
                            ' . $master_keyword . ',
                            factors such as accuracy, reliability, affordability, and convenience take center stage. Dr
                            Lal
                            PathLabs excels in meeting these criteria, establishing itself as the preferred choice among
                            residents.</p>

                        <h3>A Benchmark in Pathology Services</h3>

                        <p>Dr Lal PathLabs in
                            ' . $master_keyword . '
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
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
}
    }

    public function diseaseDetails(Request $request, $disease = null, $city = null, $locality = null)
    {
        try {
            $currentUrl = 'https://www.lalpathlabs.com/test/disease/' . $disease . ($city ? '/' . $city : '');

            $city_id = null;
            foreach ($this->cities as $cityArray) {
                if ($cityArray['name'] == $city) {
                    $city_id = $cityArray['id'];
                    break;
                }
            }
            if ($city_id == null) {
                return redirect()->back()->with('error', 'Invalid Request');
            }

            $testcontroller = new TestController();

            $tests = $this->safeApiCall(function () use ($testcontroller, $request, $city_id, $disease) {
                return $testcontroller->getTestbyCategory($request, $city_id, $disease);
            });
            $result = json_decode($tests, true);

            if (empty($result['data']['result'])) {
                return redirect()->back()->with('error', 'Invalid Request');
            }

            $packagelist = $this->safeApiCall(function () use ($testcontroller, $request, $city_id) {
                return $testcontroller->packageList($request, $city_id, 1);
            });
            $packagelist = json_decode($packagelist, true);



            $pagination = $testcontroller->getTestbyCategory($request, $city_id, $disease);
            $pagination = json_decode($pagination, true);
            // dd($pagination);


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
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
}
    }

    public function testDetails(Request $request, $slug = null, $city = null)
    {
        try {
            $currentUrl = 'https://www.lalpathlabs.com/test/pathology/' . $slug . ($city ? '/' . $city : '');

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

            $testController = new TestController();
            $page = DB::table('pages')->where('page_url', $currentUrl)->first();

            $tests = $this->safeApiCall(function () use ($testController, $request, $city_id, $slug) {
                return $testController->getTestbyItemId($request, $city_id, $slug);
            });
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

            $relatedTestsPackages = $this->safeApiCall(function () use ($testController, $request, $city_id) {
                return $testController->getRelatedTestPackage($request, $city_id);
            });
            $relatedTestsPackages = json_decode($relatedTestsPackages, true);

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
                ],
                'schema_markup' => $page->schema_markup ?? $this->getLocalSchemaMarkup($city),
                'page_script' => $page->page_script ?? '',
            ];

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
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
}
    }

    public function testDetailsSeo(Request $request, $slug = null, $city = null)
    {
        try {
            $currentUrl = 'https://www.lalpathlabs.com/test/' . 'pathology/' . $slug;
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

            $footer_content = '<div class="lab-test"><h2>Lab Test & Blood Test in ' . $master_keyword . '</h2><p>Experience the advantage of convenient and affordable lab tests in ' . $master_keyword . ' through Dr Lal PathLabs. As a renowned diagnostic center, we offer an extensive range of lab test services, ensuring accessibility to accurate and dependable health information.</p> <h2 class="n-me">Blood Test Near Me and Significance of Lab Tests</h2> <h3>Diagnosis and Disease Detection</h3>

                        <p>Instrumental in diagnosing various medical conditions, lab tests analyze blood samples,
                            urine, or
                            other bodily substances. Healthcare professionals in
                            ' . $master_keyword . '
                            can pinpoint the presence of diseases or disorders, emphasizing the importance of early
                            detection for timely and effective treatment.</p>

                        <h3>Monitoring and Treatment</h3>

                        <p>For individuals already diagnosed with a health condition, lab tests play a crucial role in
                            monitoring disease progression. Regular testing allows healthcare providers in
                            ' . $master_keyword . '
                            to assess treatment effectiveness and make necessary adjustments for improved patient
                            outcomes.
                        </p>

                        <h3>Preventive Care</h3>

                        <p>Lab tests are not just reactive but also proactive in
                            ' . $master_keyword . '.
                            They are integral in preventive care, identifying potential health risks before they
                            manifest
                            into full-fledged diseases. This proactive approach empowers individuals to make lifestyle
                            changes or seek early interventions, preventing the onset of serious health issues.</p>

                        <h2>Common Blood Tests with Price in
                            ' . $master_keyword . '
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
                            ' . $master_keyword . '
                            : Unparalleled Commitment</h2>

                        <p>Dr Lal PathLabs in
                            ' . $master_keyword . '
                            provides a comprehensive and advanced approach to pathology tests and health diagnostics.
                            From
                            convenient scheduling to cutting-edge facilities and a commitment to excellence, our
                            services
                            significantly contribute to the well-being of individuals and the community. Choose Dr Lal
                            PathLabs in
                            ' . $master_keyword . '
                            for a holistic and reliable healthcare experience.</p>

                        <h2>Seamless Convenience at Your Doorstep</h2>

                        <p>In addition to in-lab testing, Dr Lal PathLabs in
                            ' . $master_keyword . '
                            offers the added convenience of home collection for blood tests. This service ensures
                            individuals can undergo necessary tests without leaving their homes, promoting accessibility
                            and
                            adherence to healthcare routines.</p>

                        <h2>Choosing Dr Lal PathLabs for Diagnostic Excellence in
                            ' . $master_keyword . '
                        </h2>

                        <h3>Key Factors for Consideration</h3>

                        <p>When seeking diagnostic excellence in
                            ' . $master_keyword . ',
                            factors such as accuracy, reliability, affordability, and convenience take center stage. Dr
                            Lal
                            PathLabs excels in meeting these criteria, establishing itself as the preferred choice among
                            residents.</p>

                        <h3>A Benchmark in Pathology Services</h3>

                        <p>Dr Lal PathLabs in
                            ' . $master_keyword . '
                            stands out as a benchmark in pathology services, offering unparalleled expertise in sample
                            analysis and delivering accurate results. Our unwavering commitment to quality ensures that
                            individuals receive reliable information, empowering them to make informed healthcare
                            decisions.
                        </p>';


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
                'footer_content' => $page->footer_content ?? $footer_content ?? '',

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
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
}
    }

    function sendEnquiry($request, $city)
    {
        try {
            $otp = implode('', $request->otp);
            $name = $request->name;
            $number = $request->number;
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://liveapi.lalpathlabs.com/api/Common/homecollectionchemistlead',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'PatientName' => $name,
                    'PhoneNumber' => $number,
                    'Otp' => $otp,
                    'City' => $city,
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
            curl_close($curl);
            return json_decode($response, true);
        } catch (\Exception $e) {
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
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
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
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
                CURLOPT_POSTFIELDS => '{"CategoryId":"1","ReferenceId": "' . $item_id . '"}',
                CURLOPT_HTTPHEADER => array(
                    'x-access-token: 60f291aa46ea447060f291aa46ea447019d83ba30be508e419d83ba30be508e4',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } catch (\Exception $e) {
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
}
    }

    public function globalSearch(Request $request)
    {
        try {
            $search_string = $request->input('search_string');
            $city = $request->input('city');
            $city_id = null;

            foreach ($this->cities as $cityArray) {
                if ($cityArray['name'] == $city) {
                    $city_id = $cityArray['id'];
                    break;
                }
            }

            if (!$city_id || !$search_string) {
                return response()->json(['status' => false, 'message' => 'Invalid Request'], 400);
            }

            $testcontroller = new TestController();

            $response = $this->safeApiCall(function () use ($testcontroller, $request, $city_id, $search_string) {
                return $testcontroller->globalSearch($request, $city_id, $search_string);
            });

            return json_decode($response, true);
        } catch (\Exception $e) {
    Log::error("FrontendController Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'city' => $city ?? null,
        'url' => request()->fullUrl()
    ]);

    return response()->json([
        'status' => false,
        'mesage' => 'Temporary service issue. Please try again in a few seconds.',
        'data' => null,
        'debug' => app()->environment('local') ? $e->getMessage() : null
    ], 503); // 503 = Service Unavailable (better than 402)
}
    }

    function thankYou()
    {
        return view('frontend.page.thank-you');
    }

    public function getTestbyCityId(Request $request)
    {
        $testcontroller = new TestController();
        $city = $request->input('city');
        $city_id = null;
        foreach ($this->cities as $cityArray) {
            if ($cityArray['name'] == $city) {
                $city_id = $cityArray['id'];
                break;
            }
        }
        $page = 1;
        $tests = $this->safeApiCall(function () use ($testcontroller, $request, $city_id, $page) {
            return $testcontroller->getTestbyCityId($request, $city_id, $page);
        });
        return json_decode($tests, true);
    }

    public function getpackagelist(Request $request)
    {
        $testcontroller = new TestController();
        $city = $request->input('city');
        $city_id = null;
        foreach ($this->cities as $cityArray) {
            if ($cityArray['name'] == $city) {
                $city_id = $cityArray['id'];
                break;
            }
        }
        $page = 1;
        $tests = $this->safeApiCall(function () use ($testcontroller, $request, $city_id, $page) {
            return $testcontroller->packageList($request, $city_id, $page);
        });
        return json_decode($tests, true);
    }

    public function getcallBack(Request $request)
    {
        $phone = $request->input('phone');
        $city = $request->input('city');
        $this->validate($request, ['phone' => 'digits:10']);
        if (!$phone || !$city) {
            return redirect()->back()->with('error', 'Invalid Request');
        }

        $data = [
            'name' => 'Guest',
            'number' => $phone,
            'city' => $city,
            'page' => $request->page ?? '',
            'form' => 'Request call back form',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('enquiries')->insert($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://liveapi.lalpathlabs.com/api/Common/homecollectionchemistlead',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'PatientName' => 'Guest',
                'PhoneNumber' => $phone,
                'Otp' => '',
                'City' => $city,
                'MarketingLead' => 'true',
                'Vendor' => 'SEO_Page',
                'opt' => 'true',
                'tc' => 'true'
            ),
        ));
        curl_exec($curl);
        curl_close($curl);

        return response()->json(['status' => true, 'message' => 'Callback requested']);
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

    private function getLocalSchemaMarkup($city)
    {
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

    public function cityList()
    {
        $cities = City::where('status', 1)->orderBy('name', 'asc')->get();
        return response()->json([
            'status' => true,
            'mesage' => 'Data successfully retrieved.',
            'data' => ['result' => $cities],
        ], 200);
    }
}
