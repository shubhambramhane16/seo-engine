

<?php $__env->startSection('categorymaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100" enctype="multipart/form-data">
                    <?php echo e(csrf_field()); ?>

                    <div class="col-lg-9 col-xl-12">
                        <div class="row align-items-center">
                            <div class="form-group col-md-12">
                                <label>Category</label>
                                <select name="parent_id" id="" class="form-control" isrequired="required">
                                    <option value="">Select Category</option>
                                    <?php if($categories): ?>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($list->id); ?>" <?php echo e(runTimeSelection($list->id,$parent_id)); ?>><?php echo e($list->category_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sub Category Name</label>
                                <div><input type="text" name="category_name" class="form-control" placeholder="Enter Sub Category Name" isrequired="required"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Short Description</label>
                                <div>
                                    <textarea name="category_short_description" class="form-control" placeholder="Write Short Description"></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sub Category Icon <small>(Width: 96px, Height: 96px)</small></label>
                                <div><input type="file" name="icon" class="form-control"></div>
                            </div>
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
<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\seo_engine\resources\views/admin/pages/subcategory/add.blade.php ENDPATH**/ ?>