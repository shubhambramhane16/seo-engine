<?php $__env->startSection('userlist','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100">
                    <?php echo e(csrf_field()); ?>

                    <div class="col-lg-9 col-xl-12">
                        <div class="row align-items-center">
                            <div class="form-group col-md-6">
                                <label>Name</label>
                                <div><input type="text" name="name" value="<?php echo e(old('name')); ?>" isrequired="required" class="form-control" placeholder="Enter User Name"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Email/User Name </label>
                                <div><input type="email" name="email" value="<?php echo e(old('email')); ?>" isrequired="required" class="form-control" placeholder="Enter Email/User Name"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Mobile No.</label>
                                <div><input type="text" name="mobile" value="<?php echo e(old('mobile')); ?>" isrequired="required" class="form-control" placeholder="Enter Mobile Number"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Password </label>
                                <div><input type="password" name="password" class="form-control" placeholder="Enter Password"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>User Role</label>
                                <select class="form-control" name="role_id" id="role_id" isrequired="required">
                                    <option value="">Select Role</option>
                                    <?php if($roles = getSystemRoles()): ?>
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php echo e(runTimeSelection(old('role_id'),$role->id)); ?>><?php echo e($role->role); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <?php if(!empty($isSuperAdmin) && $isSuperAdmin): ?>
                            <div class="form-group col-md-6">
                                <label>Reporting Manager</label>
                                <select class="form-control" name="reporting_manager_id">
                                    <option value="">Select Reporting Manager</option>
                                    <?php $__currentLoopData = $approvers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($approver->id); ?>" <?php echo e(runTimeSelection(old('reporting_manager_id'),$approver->id)); ?>><?php echo e($approver->name); ?> (<?php echo e($approver->email); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Final Approver (Admin)</label>
                                <select class="form-control" name="admin_approver_id">
                                    <option value="">Select Final Approver</option>
                                    <?php $__currentLoopData = $approvers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($approver->id); ?>" <?php echo e(runTimeSelection(old('admin_approver_id'),$approver->id)); ?>><?php echo e($approver->name); ?> (<?php echo e($approver->email); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="form-group col-md-12">
                                <center><button class="btn btn-success">Submit</button></center>
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

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/users/add.blade.php ENDPATH**/ ?>