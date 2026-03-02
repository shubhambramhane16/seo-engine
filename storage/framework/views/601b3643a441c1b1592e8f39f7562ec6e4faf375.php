

<?php $__env->startSection('userrolemaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">User Role List
                <!-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> -->
            </h3>
        </div>
        <div class="card-toolbar">
            <?php echo $__env->make('admin.layout.partials.filters.common-filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!--begin::Button-->
            <a href="<?php echo e(url('/admin/roles/add')); ?>" class="btn btn-primary font-weight-bolder">
                <i class="la la-plus"></i>Add User Role</a>
            <!--end::Button-->
        </div>
        <!-- <div class="row col-lg-12 pl-0 pr-0"> 

            <div class="col-sm-5">
                <div class="dataTables_length">
                    <label cla>&#160; </label>
                    <button type="submit" class="btn btn-success" data-toggle="tooltip" title="Apply Filter" style="margin-top: 20px;">Filter</button>
                </div>
            </div>
        </div> -->
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <th class="custom_sno">SNo.</th>
                    <th>Role Name</th>
                    <th class="custom_action">Action</th>
                </tr>
            </thead>
            <tbody>


                <?php if(count($roles) > 0): ?>
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($value->role); ?></td>
                    <!-- <td>
                        <a href="javascript:void(0)" data-url="<?php echo e(url('admin/customers/update-status/'.$value->id.'/'.$value->status)); ?>" onclick="changeStatus(this)">
                            <span class="label label-lg font-weight-bold label-light-<?php echo e(($value->status == 1) ? 'success' : 'danger'); ?> label-inline">
                                <?php echo e(($value->status == 1) ? 'Active' : 'InActive'); ?>

                            </span>
                        </a>
                    </td> -->
                    <td>
                        <a href="<?php echo e(url('/admin/roles/edit/'.$value->id)); ?>" class="btn btn-sm btn-clean btn-icon" title="Edit details" data-toggle="tooltip">
                            <i class="la la-edit"></i>
                        </a>
                        <a href="<?php echo e(url('/admin/roles/permissions/'.$value->id)); ?>" class="btn btn-sm btn-clean btn-icon" title="View Permissions" data-toggle="tooltip">
                            <i class="la la-user"></i>
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
<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/adminrole/list.blade.php ENDPATH**/ ?>