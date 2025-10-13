<?php

namespace App\Exports\Excel;

use App\Models\State;
use Maatwebsite\Excel\Concerns\FromCollection;

class StateExport implements FromCollection
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
        $data = State::when($status, function ($data) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $data->where('status', '=', $status);
                }
            })->orderBy('id', 'DESC')->get();

        $exportData[] = [
            'name' => 'State Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
        if ($data) {
            foreach ($data as $key => $list) {
                $exportData[] = [
                    'name' => $list->name,
                    'status' => ($list->status) ? 'Active' : 'InActive',
                    'created_at' => $list->created_at,
                    'updated_at' => $list->updated_at,
                ];
            }
        }
        return collect($exportData);
    }
}
