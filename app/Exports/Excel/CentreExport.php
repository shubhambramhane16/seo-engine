<?php

namespace App\Exports\Excel;

use App\Models\Centre;
use Maatwebsite\Excel\Concerns\FromCollection;

class CentreExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $status = request('status');
        $city_id = request('city_id');
        if ($status == '0') {
            $status = '2';
        }
        $data = Centre::when($status, function ($centres) use ($status) {
            if ($status != '-1') {
                $status = conditionalStatus($status);
                $centres->where('status', '=', $status);
            }
        })->when($city_id, function ($centres) use ($city_id) {
            $centres->where('city_id', '=', $city_id);
        })->get();
        $exportData[] = [
            'centre_name ' => 'Centre Name',
            'slug' => 'Slug ',
            'phone' => 'Phone',
            // 'landline' => 'Landline',
            'email' => 'Email ID',
            'address_line1' => 'Address line1 ',
            'address_line2' => 'Address line2 ',
            'locality' => 'Locality',
            'landmark' => 'Landmark',
            'state_name' => 'State',
            'city_name' => 'City',
            'pincode' => 'Pincode',
            'centre_lat' => 'Centre Lat',
            'centre_lng' => 'Centre Lng',
            'head_name' => 'Head Name',
            'head_mobile' => 'Head Mobile',
            'head_email' => 'Head Email',
            'created_at' => 'Created At',
        ];
        if ($data) {
            foreach ($data as $key => $list) {
                $exportData[] = [
                    'centre_name' => $list->centre_name,
                    'slug' => $list->slug,
                    'phone' => $list->phone,
                    // 'landline' => $list->landline,
                    'email' => $list->email,
                    'address_line1' => $list->address_line1,
                    'address_line2' => $list->address_line2,
                    'locality' => $list->locality,
                    'landmark' => $list->landmark,
                    'state_name' => $list->state_name,
                    'city' =>   $list->city_name,
                    'pincode' => $list->pincode,
                    'centre_lat' => $list->centre_lat,
                    'centre_lng' => $list->centre_lng,
                    'head_name' => $list->head_name,
                    'head_mobile' => $list->head_mobile,
                    'head_email' => $list->head_email,
                    'created_at' => $list->created_at,
                ];
            }
        }
        return collect($exportData);
    }
}
