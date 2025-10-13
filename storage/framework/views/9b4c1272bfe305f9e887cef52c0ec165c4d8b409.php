<?php $__env->startSection('pagemaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Page List
            </h3>
        </div>
        <div class="card-toolbar">
            
            <!--begin::Button-->
            <a href="<?php echo e(url('/admin/page/add')); ?>" class="btn btn-primary font-weight-bolder">
                <i class="la la-plus"></i>Generator</a>
            <!--end::Button-->
        </div>
        <form action="" method="get" class="w-100">
            <div class="row col-lg-12 pl-0 pr-0">
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
                        <a href="<?php echo e(url('admin/page/list')); ?>" class="btn btn-default" data-toggle="tooltip" title="Reset Filter" style="margin-top: 20px;">Reset</a>
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
                    <th>Sr. No.</th>
                    <th>Page Reference</th>
                    <th>Slug Name</th>
                    <th>Meta Title</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($page->page_name); ?></td>
                    <td><?php echo e($page->slug); ?></td>
                    <td><?php echo e($page->meta_title); ?></td>
                    <td>
                        <a href="javascript:void(0)" data-url="<?php echo e(url('admin/page/update-status/'.$page->id.'/'.$page->status)); ?>" onclick="changeStatus(this)"> <span class="label label-lg font-weight-bold label-light-<?php echo e(($page->status == 1) ? 'success' : 'danger'); ?> label-inline"><?php echo e(($page->status == 1) ? 'Active' : 'InActive'); ?></span></a>
                    </td>
                    <td>
                        <a href="<?php echo e(url('/admin/page/edit/'.$page->id)); ?>" class="btn btn-sm btn-clean btn-icon" title="Edit details" data-toggle="tooltip">
                            <i class="la la-edit"></i>
                        </a>

                        <a href="<?php echo e(url('/admin/page/delete/'.$page->id)); ?>" class="btn btn-sm btn-clean btn-icon" title="Edit details" data-toggle="tooltip">
                            <i class="la la-trash"></i>
                        </a>
                    </td>



                </tr>


                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\seo_engine\resources\views/admin/pages/page/list.blade.php ENDPATH**/ ?>