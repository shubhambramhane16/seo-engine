

<?php $__env->startSection('userrolemaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">
    <!-- <div class="card-header flex-wrap border-0 pt-6 pb-0">
        <div class="card-title">
            <h3 class="card-label">Edit Category
            </h3>
        </div>
    </div> -->
    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100">
                    <?php echo e(csrf_field()); ?>

                    <div class="col-lg-9 col-xl-12">
                        <div class="row align-items-center">
                            <div class="form-group col-md-12">
                                <label>Role Name</label>
                                <div>
                                    <input type="text" name="role" value="<?php echo e(old('role')); ?>" class="form-control" placeholder="Enter User Role Name" isrequired="required">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <center><button class="btn btn-success">Update</button></center>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('styles'); ?>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/adminrole/add.blade.php ENDPATH**/ ?>