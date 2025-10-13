<?php

namespace App\Exports\Excel;

use App\Models\Locality;
use Maatwebsite\Excel\Concerns\FromCollection;

class LocalityExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $stateId = request('state_id');
        $cityId = request('city_id');
        $status = request('status');
        if ($status == '0') {
            $status = '2';
        }
        $data = Locality::with(['state', 'city'])
            ->when($stateId, function ($data) use ($stateId) {
                if (!empty($stateId)) {
                    $data->where('state_id', '=', $stateId);
                }
            })
            ->when($cityId, function ($data) use ($cityId) {
                if (!empty($cityId)) {
                    $data->where('city_id', '=', $cityId);
                }
            })
            ->when($status, function ($data) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $data->where('status', '=', $status);
                }
            })->orderBy('id', 'DESC')->get();
        $exportData[] = [
            'name' => 'Locality Name',
            'city_id' => 'City',
            'state_id' => 'State',
            'description' => 'Description ',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
        if ($data) {
            foreach ($data as $key => $list) {
                $exportData[] = [
                    'name' => $list->name,
                    'city_id' => $list->city ?  $list->city->name : '',
                    'state_id' => $list->state ?  $list->state->name : '',
                    'description' => $list->description,
                    'status' => ($list->status) ? 'Active' : 'InActive',
                    'created_at' => $list->created_at,
                ];
            }
        }
        return collect($exportData);
    }
}
