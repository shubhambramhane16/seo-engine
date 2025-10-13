

<?php $__env->startSection('categorymaster','active menu-item-open'); ?>
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
                                    <option value="<?php echo e($list->id); ?>" <?php echo e(runTimeSelection($list->id,$details->parent_id)); ?>><?php echo e($list->category_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sub Category Name</label>
                                <div><input type="text" name="category_name" value="<?php echo e($details->category_name); ?>" class="form-control" placeholder="Enter Sub Category Name" isrequired="required"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Short Description</label>
                                <div>
                                    <textarea name="category_short_description" class="form-control" placeholder="Write Short Description"><?php echo e($details->category_short_description); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sub Category Icon <small>(Width: 96px, Height: 96px)</small></label>
                                <?php if($details->category_icon): ?>
                                <div class="_update_img_action">
                                    <?php if(str_contains($details->category_icon,'AWS')): ?>
                                    <a target="_black" href="<?php echo e(Storage::disk('s3')->url($details->category_icon)); ?>" class="btn btn-success btn-sm">View</a> &nbsp;
                                    <?php else: ?>
                                    <a target="_black" href="<?php echo e(asset('uploads/category/icons/'.$details->category_icon)); ?>" class="btn btn-success btn-sm">View</a> &nbsp;
                                    <?php endif; ?>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="updateImage()">Update</a>
                                    <br>
                                    <div class="_icon_display">
                                        <?php if(str_contains($details->category_icon,'AWS')): ?>
                                        <img src="<?php echo e(Storage::disk('s3')->url($details->category_icon)); ?>" />
                                        <?php else: ?> <img src="<?php echo e(asset('uploads/category/icons/'.$details->category_icon)); ?>" />
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="image_file _update_img_file" style="<?php echo e($details->category_icon ? 'display:none' : ''); ?>">
                                    <input type="file" name="icon" class="form-control">
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
<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\seo_engine\resources\views/admin/pages/subcategory/edit.blade.php ENDPATH**/ ?>