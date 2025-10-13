

<?php $__env->startSection('pagemaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>


<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">
                <form method="POST" action="" class="w-100">
                    <?php echo e(csrf_field()); ?>

                    <div class="col-lg-9 col-xl-12">
                        <div class="row col-lg-12 pl-0 pr-0">
                            <div class="col-sm-4">
                                <div class="dataTables_length">
                                    <label>Rule Name</label>
                                    <select name="rule_id" class="form-control" id='rule_id' onchange="ruleCombinations(this.value);">
                                        <option value="">Select Rule</option>
                                        <?php if($rules): ?>
                                        <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($rule->id); ?>" <?php if(request('rule_id')==$rule->id ): ?> <?php echo e(runTimeSelection($rule->id, request('rule_id'))); ?> <?php endif; ?>><?php echo e($rule->rule_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-4">
                                <div class="dataTables_length">
                                    <input type="hidden" name="number_of_combination" class='number_of_combination'>
                                    <label>Number of combination on selected rule : </label> <span style="font-size: 18px;" class='number_of_combination'> </span>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center mt-5">
                            <div class="form-group col-md-12">
                                <div class="text-center"><button class="btn btn-success">Generate</button></div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="col-lg-9 col-xl-12">
                    <div class="row col-lg-12 pl-0 pr-0">
                        <div class="col-sm-12">
                            <div class="page-noted">
                                <label><strong>Noted:</strong></label>
                                <p>Different Permutation and combination will be used to target the keywords.</p>
                                <ul>
                                    <li>
                                        Pathology Labs
                                    </li>
                                    <li>
                                        Diagnostic Centres
                                    </li>
                                    <li>
                                        Preventive Health Checkup
                                    </li>
                                    <li>
                                        Full Body Checkup
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row col-lg-12 pl-0 pr-0">
                        <div class="card-body" style="padding: 15px;">
                            <label for=""><strong>History:</strong></label>
                            <!--begin: Datatable-->
                            <table class="table table-bordered table-hover" id="myTable">
                                <thead>
                                    <tr>
                                        <th class="custom_sno">User</th>
                                        <th class="custom_sno">Date & Time</th>
                                        <th>Rule</th>
                                        <th class="custom_status">No. of Pages</th>
                                        <th class="custom_sno">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $pagesHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($value->user?->name); ?></td>
                                        <td><?php echo e($value->created_at); ?></td>
                                        <td><?php echo e($value->rule?->rule_name); ?></td>
                                        <td><?php echo e($value->no_of_pages); ?></td>
                                        <td>
                                           
                                        </td>
                                    </tr>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                                </tbody>
                            </table>
                            <!--end: Datatable-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('styles'); ?>
<!-- <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
<link href="<?php echo e(asset('plugins/custom/datatables/datatables.bundle.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>



<?php $__env->startSection('scripts'); ?>

<script>
    // rule change


    function ruleCombinations(ruleId) {
        $.ajax({
            url: "<?php echo e(url('ajax/ruleCombinations')); ?>",
            type: "GET",
            data: {
                ruleId: ruleId
            },
            success: function(data) {
                console.log(data);
                $('.number_of_combination').val(data);
                $('.number_of_combination').html(data);
            }
        });
    }
</script>



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

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\seo_engine\resources\views/admin/pages/page/add.blade.php ENDPATH**/ ?>