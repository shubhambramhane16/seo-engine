

<?php $__env->startSection('localitymaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>

<?php if(isset($stateId) && isset($cityId)): ?>

<?php else: ?>
<?php
$stateId = '';
$cityId = '';
?>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Locality List
            </h3>
        </div>
        <div class="card-toolbar">
            <?php echo $__env->make('admin.layout.partials.filters.common-filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             <!--begin::Button-->
             <a href="<?php echo e(url('/admin/locality/import')); ?>" class="btn btn-light-primary font-weight-bolder">
                <span class="svg-icon svg-icon-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <path d="M19,11 L18,11 L18,4.00990796 C18,2.89936801 17.1017742,2 16.0099079,2 L3.99009204,2 C2.89102257,2 2,2.88772962 2,4.00990796 L2,19.990092 C2,21.100632 2.89822582,22 3.99009204,22 L16.0099079,22 C17.1089774,22 18,21.1122704 18,19.990092 L18,13 L19,13 L19,19.990092 C19,21.100632 19.8982258,22 20.990092,22 C22.1089364,22 23,21.1122704 23,19.990092 L23,4.00990796 C23,2.89936801 22.1017742,2 21.0099079,2 L21,2 C19.8954284,2 19,2.88772962 19,4.00990796 L19,11 Z M8,14 L8,16 L16,16 L16,14 L8,14 Z M8,9 L8,11 L16,11 L16,9 L8,9 Z" fill="#000000" />
                        </g>
                    </svg>
                </span>Import
            </a>
            &nbsp;
            <a href="<?php echo e(url('/admin/locality/sync')); ?>" class="btn btn-primary font-weight-bolder">
                <i class="la la-sync"></i>Sync</a>
            &nbsp;

            <a href="<?php echo e(url('/admin/locality/add/'.$stateId.'/'.$cityId)); ?>" class="btn btn-primary font-weight-bolder">
                <i class="la la-plus"></i>Add Locality</a>
            <!--end::Button-->
        </div>
        <form action="" method="get" class="w-100">
            <div class="row col-lg-12 pl-0 pr-0">

                <div class="col-sm-3">
                    <div class="dataTables_length">
                        <label>State</label>
                        <?php
                        $states = getAllStates();
                        ?>
                        <select name="state_id" value="" class="form-control">
                            <option value="">Select State</option>
                            <?php if($states): ?>
                            <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($list->id); ?>" <?php echo e(runTimeSelection($list->id, request('state_id'))); ?>><?php echo e($list->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="dataTables_length">
                        <label>Status</label>
                        <select name="status" value="" class="form-control">
                            <option value="-1">All Status</option>
                            <option value="0" <?php if(request('status')=='0' ): ?> <?php echo e(runTimeSelection(0, request('status'))); ?> <?php endif; ?>>InActive</option>
                            <option value="1" <?php if(request('status')=='1' ): ?> <?php echo e(runTimeSelection(1, request('status'))); ?> <?php endif; ?>>Active</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="dataTables_length">
                        <label cla>&#160; </label>
                        <button type="submit" class="btn btn-success" data-toggle="tooltip" title="Apply Filter" style="margin-top: 20px;">Filter</button>
                        <a href="<?php echo e(url('admin/city/list')); ?>" class="btn btn-default" data-toggle="tooltip" title="Reset Filter" style="margin-top: 20px;">Reset</a>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <th class="custom_sno">SNo.</th>
                    <th>Locality Name</th>
                    <th>City Name</th>
                    <th>State Name</th>
                    <th>Slug</th>
                    <th class="custom_status">Status</th>
                    <th class="custom_action">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($localities) > 0): ?>
                <?php $__currentLoopData = $localities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($value->name); ?></td>
                    <td><?php echo e($value->city?->name); ?></td>
                    <td><?php echo e($value->state?->name); ?></td>
                    <td><?php echo e($value->slug); ?></td>
                    <td>
                        <a href="javascript:void(0)" data-url="<?php echo e(url('admin/locality/update-status/'.$value->id.'/'.$value->status)); ?>" onclick="changeStatus(this)"> <span class="label label-lg font-weight-bold label-light-<?php echo e(($value->status == 1) ? 'success' : 'danger'); ?> label-inline"><?php echo e(($value->status == 1) ? 'Active' : 'InActive'); ?></span></a>
                    </td>

                    <td>
                        <a href="<?php echo e(url('/admin/locality/edit/'.$value->state?->id.'/'.$value->city?->id.'/'.$value->id)); ?>" class="btn btn-sm btn-clean btn-icon" title="Edit details" data-toggle="tooltip"> <i class="la la-edit"></i> </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>
        <!--end: Datatable-->
    </div>
</div>


<?php $__env->stopSection(); ?>


<?php $__env->startSection('styles'); ?>
<!-- <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
<link href="<?php echo e(asset('plugins/custom/datatables/datatables.bundle.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>



<?php $__env->startSection('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
        $('.dataTables_filter label input[type=search]').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
    });
</script>

<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
<!-- <script src="<?php echo e(asset('plugins/custom/datatables/datatables.bundle.js')); ?>" type="text/javascript"></script> -->


<!-- <script src="<?php echo e(asset('js/pages/crud/datatables/basic/basic.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script> -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\seo_engine\resources\views/admin/pages/locality/list.blade.php ENDPATH**/ ?>