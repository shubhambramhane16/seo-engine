

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
                <?php
                $modules = modulesList();
                ?>
                <form method="POST" action="" class="w-100">
                    <?php echo e(csrf_field()); ?>


                    <div class="w-100 col-lg-9 col-xl-12">
                        <h2> ROLE : <?php echo e($details->role); ?> </h2>
                        <hr>
                    </div>
                    <div class="col-lg-9 col-xl-12">

                        <div class="row align-items-center col-lg-12 pr-0 mr-0">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Module Name</th>
                                        <th>
                                            <label class="checkbox checkbox-lg checkbox-light-success checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" name="select" value="1" id="ckbCheckView" className="Checkbox1" class="Checkbox1">
                                                <span></span>
                                                &nbsp; View
                                            </label>

                                        </th>
                                        <th>
                                            <label class="checkbox checkbox-lg checkbox-light-primary checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" name="select" value="1" id="ckbCheckAddEdit" className="Checkbox1" class="Checkbox1">
                                                <span></span>
                                                &nbsp; View | Edit
                                            </label>
                                        </th>
                                        <th>
                                            <label class="checkbox checkbox-lg checkbox-light-warning checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" name="select" value="1" id="ckbCheckAdd" className="Checkbox1" class="Checkbox1">
                                                <span></span>
                                                &nbsp; View | Add
                                            </label>
                                        </th>
                                        <th>
                                            <label class="checkbox checkbox-lg checkbox-light-info checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" name="select" value="1" id="ckbCheckEdit" className="Checkbox1" class="Checkbox1">
                                                <span></span>
                                                &nbsp; View | Add | Edit
                                            </label>
                                        </th>
                                        <th>
                                            <label class="checkbox checkbox-lg checkbox-light-danger checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" name="select" value="1" id="ckbCheckDelete" className="Checkbox1" class="Checkbox1">
                                                <span></span>
                                                &nbsp; View | Add | Edit | Delete
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($details->permission) {
                                    $existingPermissions = json_decode($details->permission, true);
                                    }else{
                                    $existingPermissions = [];
                                    }
                                    ?>
                                    <?php if($modules): ?>
                                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>$module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $permission = 0;
                                    if(isset($existingPermissions[$module['slug']])){
                                    $permission =$existingPermissions[$module['slug']];
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo e($module['slug']); ?>

                                            <input type="hidden" value="<?php echo e($module['slug']); ?>" name="module_slug[<?php echo e($module['id']); ?>]">
                                        </td>
                                        <td>
                                            <label class="checkbox checkbox-lg checkbox-light-success checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" value="1" <?php echo e(runTimeChecked(1, $permission)); ?> id="Checkbox<?php echo e($module['id']); ?>" name="permissions[<?php echo e($module['id']); ?>]" class="checkBoxView individual_checkbox">
                                                <span></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="checkbox checkbox-lg checkbox-light-primary checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" value="2" <?php echo e(runTimeChecked(2, $permission)); ?> id="Checkbox<?php echo e($module['id']); ?>" name="permissions[<?php echo e($module['id']); ?>]" class="checkBoxViewEdit individual_checkbox" />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="checkbox checkbox-lg checkbox-light-warning checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" value="3" <?php echo e(runTimeChecked(3, $permission)); ?> id="Checkbox<?php echo e($module['id']); ?>" name="permissions[<?php echo e($module['id']); ?>]" class="checkBoxViewAdd individual_checkbox" />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="checkbox checkbox-lg checkbox-light-info checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" value="4" <?php echo e(runTimeChecked(4, $permission)); ?> id="Checkbox<?php echo e($module['id']); ?>" name="permissions[<?php echo e($module['id']); ?>]" class="checkBoxViewAddEdit individual_checkbox" />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="checkbox checkbox-lg checkbox-light-danger checkbox-inline flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" value="5" <?php echo e(runTimeChecked(5, $permission)); ?> id="Checkbox<?php echo e($module['id']); ?>" name="permissions[<?php echo e($module['id']); ?>]" class="checkBoxViewAddEditDelete individual_checkbox" />
                                                <span></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>

                                </tbody>
                            </table>

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
<script>
    $(document).ready(function() {
        $("#ckbCheckView").click(function() {
            $(".Checkbox1").not(this).prop("checked", false);

            $(".checkBoxViewEdit").removeAttr("checked");
            $(".checkBoxViewAdd").removeAttr("checked");
            $(".checkBoxViewAddEdit").removeAttr("checked");
            $(".checkBoxViewAddEditDelete").removeAttr("checked");

            $(".checkBoxView").attr("checked", this.checked);
        });
        $("#ckbCheckAddEdit").click(function() {
            $(".Checkbox1").not(this).prop("checked", false);

            $(".checkBoxView").removeAttr("checked");
            $(".checkBoxViewAdd").removeAttr("checked");
            $(".checkBoxViewAddEdit").removeAttr("checked");
            $(".checkBoxViewAddEditDelete").removeAttr("checked");

            $(".checkBoxViewEdit").attr("checked", this.checked);
        });
        $("#ckbCheckAdd").click(function() {
            $(".Checkbox1").not(this).prop("checked", false);

            $(".checkBoxView").removeAttr("checked");
            $(".checkBoxViewEdit").removeAttr("checked");
            $(".checkBoxViewAddEdit").removeAttr("checked");
            $(".checkBoxViewAddEditDelete").removeAttr("checked");

            $(".checkBoxViewAdd").attr("checked", this.checked);
        });
        $("#ckbCheckEdit").click(function() {
            $(".Checkbox1").not(this).prop("checked", false);

            $(".checkBoxView").removeAttr("checked");
            $(".checkBoxViewEdit").removeAttr("checked");
            $(".checkBoxViewAdd").removeAttr("checked");
            $(".checkBoxViewAddEditDelete").removeAttr("checked");

            $(".checkBoxViewAddEdit").attr("checked", this.checked);
        });
        $("#ckbCheckDelete").click(function() {

            $(".Checkbox1").not(this).prop("checked", false);

            $(".checkBoxView").removeAttr("checked");
            $(".checkBoxViewEdit").removeAttr("checked");
            $(".checkBoxViewAdd").removeAttr("checked");
            $(".checkBoxViewAddEdit").removeAttr("checked");

            $(".checkBoxViewAddEditDelete").attr("checked", this.checked);
        });
        $(".individual_checkbox").click(function() {
            $(this).parents('tr').find("input[type=checkbox]").not(this).removeAttr("checked").prop("checked", false);
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/adminrole/permission.blade.php ENDPATH**/ ?>