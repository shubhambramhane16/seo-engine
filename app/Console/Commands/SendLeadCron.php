<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Query;
use App\Models\PartnerEnquiry;
use Illuminate\Console\Command;

class SendLeadCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lead:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lead Send to CRM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->CRMLeadTracker();
    }


    public function CRMLeadTracker()
    {
        try {
            //order
            echo $this->sendLeadData('Order');
            echo $this->sendLeadData('PartnerEnquiry');
            echo $this->sendLeadData('Query');
        } catch (\Exception $e) {
            Log::info($e);
            $result['Result'] = [];
            $result['status'] = 'false';
            $result['Message'] = $e;
            response()->json($result);
        }
    }

    private function sendLeadData($formType)
    {
        if ($formType == 'Order') {
            $LeadsData = Order::where('is_sync_crm', 0)->limit(30)->get();
        } elseif ($formType == 'PartnerEnquiry') {
            $LeadsData = PartnerEnquiry::where('is_sync_crm', 0)->limit(30)->get();
        } elseif ($formType == 'Query') {
            $LeadsData = Query::where('is_sync_crm', 0)->limit(30)->get();
        }
        $msg = '';
        if (!empty($LeadsData)) {
            foreach ($LeadsData as $key => $lead) {
                $requestData =  $this->createRequest($lead, $formType);
                $response = $this->sendLeadToCRM($requestData);
                if ($response['status'] == 'true') {
                    $this->updateLeadStatus($lead->id, $formType);
                    $msg .=  $formType . ' id: ' . $lead->id . ' send succefully to CRM. <br/> ';
                } else {
                    $msg .= $formType . ' Id : ' . $response['Message'] . '<br/>';
                }
            }
        } else {
            $msg . 'Lead not found.' . '<br/>';
        }
        Log::info($msg);
        return $msg;
    }

    private function updateLeadStatus($leadId, $formType)
    {
        if ($formType == 'Order') {
            $leadObject = Order::where('id', $leadId)->first();
            $leadObject->is_sync_crm = 1;
            $leadObject->save();
        } elseif ($formType == 'PartnerEnquiry') {
            $leadObject = PartnerEnquiry::where('id', $leadId)->first();
            $leadObject->is_sync_crm = 1;
            $leadObject->save();
        } elseif ($formType == 'Query') {
            $leadObject = Query::where('id', $leadId)->first();
            $leadObject->is_sync_crm = 1;
            $leadObject->save();
        }
    }

    private function createRequest($request, $formType)
    {

        if ($formType == 'Order') {
            return $requestData = [
                "lead_type" => "Order Booking",
                "lead_subtype" => "Order Booking",
                "url" => $request->source_url,
                "name" => $request->patient_firstname . ' ' . $request->patient_lastname,
                "age" => $request->patient_age,
                "gender" => $request->gender,
                "email" => $request->patient_email,
                "subject" => "Book a test",
                "mobile" => $request->patient_number,
                "address" => $request->address,
                "city" => "",
                "service_type" => "",
                "service_name" => "",
                "source_id" => "",
                "unq_id" => "",
                "comments" => "",
                "req_date" => $request->schedule_date,
                "req_slot" => $request->schedule_time,
                "others" => [
                    "key" => ""
                ]
            ];
        } elseif ($formType == 'PartnerEnquiry') {
            return $requestData = [
                "lead_type" => "Partner Enquiry",
                "lead_subtype" => "Partner Enquiry",
                "url" => $request->source_url,
                "name" => $request->first_name . ' ' . $request->last_name,
                "age" => '',
                "gender" => '',
                "email" => $request->email_id,
                "subject" => "",
                "mobile" => $request->mobile,
                "address" => $request->address,
                "city" => $request->city_name,
                "service_type" => "",
                "service_name" => "",
                "source_id" => "",
                "unq_id" => "",
                "comments" => "",
                "req_date" => "",
                "req_slot" => "",
                "others" => [
                    "key" => ""
                ]
            ];
        } elseif ($formType == 'Query') {
            return $requestData = [
                "lead_type" => $request->type == 1 ? "Query" : "Booking Lead",
                "lead_subtype" => "",
                "url" => $request->source_url,
                "name" => $request->customer_name,
                "age" => '',
                "gender" => $request->gender,
                "email" => $request->customer_email,
                "subject" => "",
                "mobile" => $request->customer_mobile,
                "address" => $request->address,
                "city" => "",
                "service_type" => "",
                "service_name" => "",
                "source_id" => "",
                "unq_id" => "",
                "comments" => "",
                "req_date" => "",
                "req_slot" => "",
                "others" => [
                    "key" => ""
                ]
            ];
        }
    }



    function sendLeadToCRM($requestData)
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => config('api.add_lead_api_url'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($requestData),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                $result['Result'] = [];
                $result['status'] = 'false';
                $result['Message'] = $error_msg;
                return $result;
            }
            curl_close($curl);
            $result['Result'] = $response;
            $result['status'] = 'true';
            $result['Message'] = 'Success'; 
            return $result;
        } catch (\Exception $e) {
            $result['Result'] = [];
            $result['status'] = 'false';
            $result['Message'] = $e;
            return $result;
        }
    }
}
