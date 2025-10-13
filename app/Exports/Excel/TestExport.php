<?php

namespace App\Exports\Excel;

use App\Models\PathologyTest;
use Maatwebsite\Excel\Concerns\FromCollection;

class TestExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $categoryId = request('category_id');
        $subCategoryId = request('sub_category_id');
        $departmentId = request('department_id');
        $status = request('status');
        if ($status == '0') {
            $status = '2';
        }

        $searchTerm = request('test_name');
        $data = PathologyTest::orderBy('id', 'DESC')->when($departmentId, function ($data) use ($departmentId) {
            $data->where('department_id', '=', $departmentId);
        })->when($status, function ($data) use ($status) {
            if ($status != '-1') {
                $status = conditionalStatus($status);
                $data->where('status', '=', $status);
            }
        })->when($categoryId, function ($data) use ($categoryId) {
            if ($categoryId) {
                $data->whereRaw(DB::raw('(categories REGEXP "' . $categoryId . '")'));
            }
        })->when($subCategoryId, function ($data) use ($subCategoryId) {
            if ($subCategoryId) {
                $data->whereRaw(DB::raw('(sub_categories REGEXP "' . $subCategoryId . '")'));
            }
        })->when($searchTerm, function ($data) use ($searchTerm) {
            $data->whereRaw("(test_name like '%" . $searchTerm . "%' OR test_code like '%" . $searchTerm . "%' )");
        })->get();
        $exportData[] = [
            'test_code' => 'Test Code',
            'test_name' => 'Test Name ',
            'slug' => 'Slug  ',
            'lab_name' => 'Lab Name',
            'component_count' => 'Component Count',
            'recommendation' => 'Recommendation',
            'age_group' => 'Age Group',
            'mrp' => 'MRP',
            'selling_price' => 'Selling Price',
            'technique' => 'Technique',
            'specimen' => 'Specimen',
            'temperature' => 'Temperature',
            'instructions' => 'Instructions',
            'container' => 'Container',
            'volume' => 'Volume',
            'method' => 'Method',
            'schedule' => 'Schedule',
            'test_type' => 'Test Type',
            'profile' => 'Profile',
            'cut_off' => 'Cut off',
            'gender' => 'Gender',
            'description' => 'Description',
            'Category' => 'Category',
            'Sub Category' => 'Sub Category',
            'Department' => 'Department',
            'Report TAT' => 'Report TAT',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
        if ($data) {
            foreach ($data as $key => $list) {
                $catArr = $list->categories ? json_decode($list->categories, 1) : null;
                $subcatArr = $list->sub_categories ? json_decode($list->sub_categories, 1) : null;
                if ($list->other_departments) {
                    $Department =  explode(',', $list->other_departments);
                }elseif($list->department_id){
                    $Department =  [$list->department_id];
                }else{
                    $Department = null;
                }
                // dd(  $Department);
                $exportData[] = [
                    'test_code' => $list->test_code,
                    'test_name' => $list->test_name,
                    'slug' => $list->slug,
                    'lab_name' => $list->lab_name,
                    'component_count' => $list->component_count,
                    'recommendation' => $list->recommendation,
                    'age_group' => $list->age_group,
                    'mrp' => $list->mrp,
                    'selling_price' => $list->selling_price,
                    'technique' => $list->technique,
                    'specimen' => $list->specimen,
                    'temperature' => $list->temperature,
                    'instructions' => $list->instructions,
                    'container' => $list->container,
                    'volume' => $list->volume,
                    'method' => $list->method,
                    'schedule' => $list->schedule,
                    'test_type' => $list->test_type,
                    'profile' => $list->profile,
                    'cut_off' => $list->cut_off,
                    'gender' => $list->gender,
                    'description' => $list->description,
                    'categories' => $catArr  ?  getCategoriesName($catArr) : "",
                    'subcategories' => $subcatArr  ?  getCategoriesName($subcatArr) : "",
                    'Department' => $Department ? getDepartments($Department) : "",
                    'report_tat' => $list->report_tat,
                    'status' => ($list->status) ? 'Active' : 'InActive',
                    'created_at' => $list->created_at,
                ];
            }
        } 
        return collect($exportData);
    }
}
