<?php

namespace App\Exports\Excel;

use App\Models\Enquiry;
use Maatwebsite\Excel\Concerns\FromCollection;

class EnquiryExport implements FromCollection
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
        $data = Enquiry::when($status, function ($data) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $data->where('status', '=', $status);
                }
            })->orderBy('id', 'DESC')->get();

        $exportData[] = [
            'name' => 'Enquiry Name',
            'number' => 'Enquiry number',
            'slot_date' => 'Enquiry Slot Date',
            'slot_time' => 'Enquiry Slot Time',
            'city' => 'Enquiry city',
            'locality' => 'Enquiry locality',
            'page' => 'Enquiry page',
            'item_id' => 'Enquiry item id',
            'item_reference' => 'Enquiry item_reference',
            'form' => 'Enquiry Form',
            'query' => 'Enquiry Query',
            'is_sync' => 'Enquiry  Sync',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
        if ($data) {
            foreach ($data as $key => $list) {
                $exportData[] = [
                    'name' => $list->name,
                    'number' => $list->number,
                    'slot_date' => $list->slot_date,
                    'slot_time' => $list->slot_time,
                    'city' => $list->city,
                    'locality' => $list->locality,
                    'page' => $list->page,
                    'item_id' => $list->item_id,
                    'item_reference' => $list->item_reference,
                    'form' => $list->form,
                    'query' => $list->query,
                    'is_sync' => $list->is_sync,
                    'status' => ($list->status) ? 'Active' : 'InActive',
                    'created_at' => $list->created_at,
                    'updated_at' => $list->updated_at,
                ];
            }
        }
        return collect($exportData);
    }
}
