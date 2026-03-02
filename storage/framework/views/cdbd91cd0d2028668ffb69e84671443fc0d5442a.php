

<?php $__env->startSection('rulemaster', 'active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>


    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="" class="w-100">
                        <?php echo e(csrf_field()); ?>

                        <div class="col-lg-9 col-xl-12">
                            <div class="row">
                            <div class="form-group col-md-8">
                                    <label>Rule Name</label>
                                    <div>
                                        <input type="text" id="rule_name" name="rule_name" placeholder="Enter Rule Name"
                                            class="form-control" isrequired="isrequired" value="<?php echo e(old('rule_name')); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="form-group col-md-4">
                                    <label>Prefix</label>
                                    <div>
                                        <input type="text" id="prefix" name="prefix" placeholder="Enter Prefix Name"
                                            class="form-control" value="<?php echo e(old('prefix')); ?>">
                                    </div>
                                </div>
                                <div class="form-group col-md-8">
                                    <ul id="sortable" class="row">
                                        <li class="col-3"><input type="checkbox" id="item-category" name="items[]"
                                                value="category-name">Item Category</li>
                                        <li class="col-3"><input type="checkbox" id="item-name" name="items[]"
                                                value="item-name">Item Name</li>
                                        <li class="col-3"><input type="checkbox" id="city" name="items[]"
                                                value="city-name">City</li>
                                        <li class="col-3"><input type="checkbox" id="locality" name="items[]"
                                                value="locality-name">Locality</li>
                                    </ul>
                                </div>

                            </div>
                            <div class="row align-items-center">
                                <div class="form-group col-md-12">
                                    <label>URL Structure</label>
                                    <div><input type="text" id="url" name="url_structure" value="" placeholder=""
                                            class="form-control" isrequired="isrequired"></div>
                                </div>

                            </div>
                            <hr>



                            <div class="row align-items-center">
                                <input type="hidden" name="template_id" value="" id="template_id">
                                <div class="form-group col-md-6">
                                    <div class="dataTables_length">
                                        <label>Template</label>
                                        <div class="temp-act-temp">
                                            <div class="row mt-2">

                                                <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <div class="col-md-3">
                                                        <div class="activated-temp" data-id="<?php echo e($template->id); ?>">
                                                            <img src="<?php echo e(Storage::disk('s3')->url($template->template_image)); ?>"
                                                                style="filter: grayscale(1);" alt="" />

                                                        </div>
                                                        <div class="temp-name">
                                                            <h4><?php echo e($template->template_name); ?></h4>
                                                        </div>
                                                    </div>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <?php endif; ?>


                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row align-items-center">
                                <div class="form-group col-md-12">
                                    <label>Description</label>
                                    <textarea name="description" id="" placeholder="Enter Description" class="form-control" cols="30"
                                        rows="5"><?php echo e(old('description')); ?></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="text-center"><button class="btn btn-success">Submit</button></div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $(function() {
            $("#sortable").sortable();
        });

        $(document).ready(function() {
            $("#prefix,#item-category,#item-name,#city,#locality").change(function() {
                var url = ($('#prefix').val());
                $('input[type=checkbox]').each(function(e) {
                    if ($(this).prop('checked')) {
                        url = url + '/' + $(this).val();
                    }
                });

                $('#url').val(url);
            });
        });
    </script>
    <script>
        // select template and set template id and add style="filter: grayscale(0); border: 1px solid #a6a4a4;" to selected template
        $('.activated-temp').click(function() {
            $('.activated-temp').each(function() {
                $(this).find('img').css('filter', 'grayscale(1)');
                $(this).find('img').css('border', 'none');
            });
            $(this).find('img').css('filter', 'grayscale(0)');
            $(this).find('img').css('border', '1px solid #a6a4a4');
            $('#template_id').val($(this).attr('data-id'));
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('styles'); ?>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/rules/add.blade.php ENDPATH**/ ?>