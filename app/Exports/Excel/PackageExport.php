<?php

namespace App\Exports\Excel;

use App\Models\PathologyPackages;
use Maatwebsite\Excel\Concerns\FromCollection;

class PackageExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $status = request('status');
        if ($status == '0') {
            $status = '2';
        }
        $data = PathologyPackages::when($status, function ($data) use ($status) {
            if ($status != '-1') {
                $status = conditionalStatus($status);
                $data->where('status', '=', $status);
            }
        })->orderBy('id', 'DESC')->get();
        $exportData[] = [
            'package_code' => 'Package Code',
            'slug' => 'Slug',
            'package_name ' => 'Package Name   ',
            'lab_name' => 'Lab Name',
            'component_count' => 'Component Count',
            'recommendation' => 'Recommendation',
            'sample_type' => 'Sample Type',
            'age_group' => 'Age Group',
            'mrp' => 'MRP',
            'selling_price' => 'Selling Price',
            'report_tat' => 'Report Tat',
            'state_name' => 'State',
            'city_name' => 'City',

            'gender' => 'Gender',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
        if ($data) {
            foreach ($data as $key => $list) {
                $exportData[] = [
                    'package_code' => $list->package_code,
                    'slug' => $list->slug,
                    'package_name ' => $list->package_name,
                    'lab_name' => $list->lab_name,
                    'component_count' => $list->component_count,
                    'recommendation' => $list->recommendation,
                    'sample_type' => $list->sample_type,
                    'age_group' => $list->age_group,
                    'mrp' => $list->mrp,
                    'selling_price' => $list->selling_price,
                    'report_tat' => $list->report_tat,
                    'state_name' => $list->state_name,
                    'city_name' => $list->city_name,

                    'gender' => $list->gender,
                    'description' => $list->description,
                    'status' => ($list->status) ? 'Active' : 'InActive',
                    'created_at' => $list->created_at,
                ];
            }
        }
        return collect($exportData);
    }
}
