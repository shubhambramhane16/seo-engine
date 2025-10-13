<?php

namespace App\Exports\Excel;

use App\Models\Enquiry;
use App\Models\Testimonial;
use Maatwebsite\Excel\Concerns\FromCollection;

class TestimonialExport implements FromCollection
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
        $data = Testimonial::when($status, function ($data) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $data->where('status', '=', $status);
                }
            })->orderBy('id', 'DESC')->get();

        $exportData[] = [
            'title' => 'Title',
            'slug' => 'Slug',
            'content' => 'Content',
            'city_id' => 'City',
            'locality_id' => 'Locality',
            'centre_id' => 'Centre',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
        if ($data) {
            foreach ($data as $key => $list) {
                $exportData[] = [
                    'title' => $list->title,
                    'slug' => $list->slug,
                    'content' => $list->content,
                    'city_id' => $list->city->name,
                    'locality_id' => $list->locality->name,
                    'centre_id' => $list->centre->centre_name,
                    'status' => ($list->status) ? 'Active' : 'InActive',
                    'created_at' => $list->created_at,
                    'updated_at' => $list->updated_at,
                ];
            }
        }
        return collect($exportData);
    }
}
