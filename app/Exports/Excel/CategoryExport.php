<?php

namespace App\Exports\Excel;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;

class CategoryExport implements FromCollection
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
        $data = Category::where('parent_id', 0)->when($status, function ($centres) use ($status) {
            if ($status != '-1') {
                $status = conditionalStatus($status);
                $centres->where('status', '=', $status);
            }
        })->when($city_id, function ($centres) use ($city_id) {
            $centres->where('city_id', '=', $city_id);
        })
        ->orderBy('id', 'DESC')->get();
        $exportData[] = [
            'category_name' => 'Category Name',
            'slug' => 'Slug',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
        if ($data) {
            foreach ($data as $key => $list) {
                $exportData[] = [
                    'category_name' => $list->category_name,
                    'slug' => $list->category_slug,
                    'status' => ($list->status) ? 'Active' : 'InActive',
                    'created_at' => $list->created_at,
                ];
            }
        }
        return collect($exportData);
    }
}
