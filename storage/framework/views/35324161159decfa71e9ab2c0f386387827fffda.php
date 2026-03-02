<?php $__env->startSection('pagemaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <?php if($details->latestApprovalRequest): ?>
                <?php
                    $approvalStatus = $details->latestApprovalRequest->status;
                    $approvalClass = 'secondary';
                    $approvalText = 'Draft';
                    if($approvalStatus == 'pending_manager' || $approvalStatus == 'pending_admin') {
                        $approvalClass = 'warning';
                        $approvalText = 'Pending Approval';
                    } elseif($approvalStatus == 'approved') {
                        $approvalClass = 'success';
                        $approvalText = 'Approved';
                    } elseif($approvalStatus == 'rejected') {
                        $approvalClass = 'danger';
                        $approvalText = 'Rejected';
                    }
                ?>
                <div class="col-md-12 mb-5">
                    <div class="alert alert-light-<?php echo e($approvalClass); ?>">
                        Latest Request Status: <strong><?php echo e($approvalText); ?></strong>
                        <?php if($details->latestApprovalRequest->approver_comments): ?>
                        <br><strong>Reviewer Comments:</strong> <?php echo e($details->latestApprovalRequest->approver_comments); ?>

                        <?php endif; ?>
                        <br><a href="<?php echo e(url('/admin/page/approval-requests/'.$details->latestApprovalRequest->id)); ?>">View request details</a>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="" class="w-100">
                    <?php echo e(csrf_field()); ?>

                    <div class="col-lg-9 col-xl-12">
                        <div class="row align-items-center">

                            <div class="form-group col-md-12">
                                <label>Page Url</label>
                                <div><input type="text" name="page_url" placeholder="Enter Page Url" class="form-control" value="<?php echo e($details->page_url); ?>" ></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Reference Name</label>
                                <div><input type="text" name="page_name" placeholder="Enter Reference Name" class="form-control" value="<?php echo e($details->page_name); ?>" ></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Slug</label>
                                <div><input type="text" name="slug" placeholder="Enter Slug" class="form-control" value="<?php echo e($details->slug); ?>"  readonly></div>
                            </div>


                            <div class="form-group col-md-6">
                                <label>Title</label>
                                <div><input type="text" name="seo_title" placeholder="Enter Title" class="form-control" value="<?php echo e($details->seo_title); ?>" ></div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Meta Description</label>

                                <textarea class="form-control" name="seo_description"><?php echo e($details->seo_description); ?></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Meta Keywords</label>

                                <textarea class="form-control" name="seo_keywords"><?php echo e($details->seo_keywords); ?></textarea>
                            </div>

                            <div class="form-group col-md-6">
                                <label>OG Meta Title</label>
                                <div><input type="text" name="og_meta_title" placeholder="Enter OG Meta Title" class="form-control" value="<?php echo e($details->og_meta_title); ?>" ></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>OG Meta Description</label>

                                <textarea class="form-control" name="og_meta_description"><?php echo e($details->og_meta_description); ?></textarea>
                            </div>

                            <div class="form-group col-md-6">
                                <label>OG Meta Image Url</label>
                                <div><input type="text" name="og_meta_image_url" placeholder="Enter OG Meta Image Url" class="form-control" value="<?php echo e($details->og_meta_image_url); ?>" ></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Twitter card Title </label>
                                <div><input type="text" name="twitter_card_title" placeholder="Enter Twitter card Title" class="form-control" value="<?php echo e($details->twitter_card_title); ?>" ></div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Twitter card Description</label>
                                <textarea class="form-control" name="twitter_card_description"><?php echo e($details->twitter_card_description); ?></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Schema Markup</label>

                                <textarea class="form-control" name="schema_markup"><?php echo e($details->schema_markup); ?></textarea>

                            </div>

                            <div class="form-group col-md-12">
                                <label>Header Content</label>

                                <textarea id="textEditor" class="form-control textEditor" name="header_content"><?php echo e($details->header_content); ?></textarea>

                            </div>

                            <div class="form-group col-md-12">
                                <label>Center Content</label>

                                <textarea id="textEditor2" class="form-control textEditor" name="center_content"><?php echo e($details->center_content); ?></textarea>


                            </div>

                            <div class="form-group col-md-12">
                                <label>Footer Content</label>
                                <textarea id="textEditor3" class="form-control textEditor" name="footer_content"><?php echo e($details->footer_content); ?></textarea>


                            </div>

                            <div class="form-group col-md-12">
                                <label>Page Script</label>
                                <textarea class="form-control" name="page_script"><?php echo e($details->page_script); ?></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <div class="text-center"><button class="btn btn-success">Update</button></div>
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
<script src="https://ckeditor.com/docs/vendors/4.11.3/ckeditor/ckeditor.js" type="text/javascript"></script>
<script>
 $(function() {
        CKEDITOR.replace('textEditor');
        CKEDITOR.replace('textEditor2');
        CKEDITOR.replace('textEditor3');

        //
 });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/page/edit.blade.php ENDPATH**/ ?>