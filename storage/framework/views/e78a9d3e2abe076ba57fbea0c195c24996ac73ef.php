<?php $__env->startSection('userlist','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">User List
                <!-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> -->
            </h3>
        </div>
        <div class="card-toolbar">
            <!--begin::Button-->
            <a href="<?php echo e(url('/admin/users/add-user')); ?>" class="btn btn-primary font-weight-bolder">
                <i class="la la-plus"></i>Add User</a>
            <!--end::Button-->
        </div>
        <form action="" method="get" class="w-100">
            <div class="row col-lg-12 pl-0 pr-0">
                <div class="col-sm-3">
                    <div class="dataTables_length">
                        <label>Status</label>
                        <select name="status" value="" class="form-control">
                            <option value="">All Status</option>
                            <option value="0" <?php if(request('status')=='0' ): ?> <?php echo e(runTimeSelection(0, request('status'))); ?> <?php endif; ?>>InActive</option>
                            <option value="1" <?php if(request('status')=='1' ): ?> <?php echo e(runTimeSelection(1, request('status'))); ?> <?php endif; ?>>Active</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="dataTables_length">
                        <label cla>&#160; </label>
                        <button type="submit" class="btn btn-success mt-7" data-toggle="tooltip" title="Apply Filter">Filter</button>
                        <a href="<?php echo e(url('/admin/users/list')); ?>" class="btn btn-default mt-7" data-toggle="tooltip" title="Reset Filter">Reset</a>

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
                    <th>Name</th>
                    <th>Email/User Name</th>
                    <th>Mobile</th>
                    <th>User Role</th>
                    <?php if(!empty($isSuperAdmin) && $isSuperAdmin): ?>
                    <th>Reporting Manager</th>
                    <th>Final Approver</th>
                    <?php endif; ?>
                    <th class="custom_status">Status</th>
                    <th class="custom_action">Action</th>
                </tr>
            </thead>
            <tbody>

                <?php if(count($users) > 0): ?>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($value->name); ?></td>
                    <td><?php echo e($value->email); ?></td>
                    <td><?php echo e($value->mobile); ?></td>
                    <td><?php echo e($value->role->role); ?></td>
                    <?php if(!empty($isSuperAdmin) && $isSuperAdmin): ?>
                    <td><?php echo e(optional(optional($value->approvalHierarchy)->manager)->name ?? '-'); ?></td>
                    <td><?php echo e(optional(optional($value->approvalHierarchy)->admin)->name ?? '-'); ?></td>
                    <?php endif; ?>
                    <td>
                        <a href="javascript:void(0)" data-url="<?php echo e(url('admin/users/update-status/'.$value->id.'/'.$value->status)); ?>" onclick="changeStatus(this)"> <span class="label label-lg font-weight-bold label-light-<?php echo e(($value->status == 1) ? 'success' : 'danger'); ?> label-inline"><?php echo e(($value->status == 1) ? 'Active' : 'InActive'); ?></span></a>
                    </td>
                    <td>
                        <a href="<?php echo e(url('/admin/users/edit/'.$value->id)); ?>" class="btn btn-sm btn-clean btn-icon" title="Edit details" data-toggle="tooltip">
                            <i class="la la-edit"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            </tbody>
        </table>
        <!--end: Datatable-->
    </div>
</div>


<script>
    function changeStatus() {
        confirm("Do you want to change status?");
    }
</script>
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

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/users/list.blade.php ENDPATH**/ ?>