

<?php $__env->startSection('dashboardmaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Dashboard
                <!-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> -->
            </h3>
        </div>
        <!-- <div class="card-toolbar"> 
            <div>
                <a href="<?php echo e(url('/admin/categories/add')); ?>" class="btn btn-primary font-weight-bolder">
                    <i class="la la-plus"></i>Add Category</a>
            </div>
            <div style="margin-left: 7px;">
                <a href="<?php echo e(url('/admin/subcategories/list')); ?>" class="btn btn-primary font-weight-bolder">
                    All Sub Categories</a>
            </div> 

        </div> -->
        <form action="" method="get" class="w-100" style="position: absolute;    right: 0;    top: 10px;">
            <div class="row pl-0 pr-0">


                <div class=" col-lg-12 text-right">
                    <div class="dataTables_length">
                        <input type="text" name="fromtodate" id="fromtodate" class="" placeholder="From Date" autocomplete="off" value="" style="opacity:0; width:0;position:absolute;right:20%">
                        <button type="button" class="btn" onclick="$('#fromtodate').click(),setSubmitAtt()"><i class="icon-2x text-dark-50 ki ki-calendar "></i></button>
                        <button type="submit" class="btn btn-success btn-sm d-none" id="Filter_ME" data-toggle="tooltip" title="Apply Filter">Filter</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h6><?php echo e(request('fromtodate') ? 'Date - '.request('fromtodate') : "Data"); ?> </h6>
            </div>
            <div class="col-md-6">
                <div class="card card-custom wave wave-animate-slow wave-primary mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center p-5">
                            <div class="mr-6">
                                <span class="svg-icon svg-icon-success svg-icon-4x  _count_size">
                                    <?php echo e(getTotalOrders(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'))); ?>

                                </span>
                            </div>
                            <div class="d-flex flex-column w-100">
                                <a href="<?php echo e(url('admin/orders/list')); ?>" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">
                                    Pages
                                </a>
                                <div class="text-dark-75 w-100">
                                    <div class="row">
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Active</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalOrders(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'1')); ?>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Inactive</div>
                                                <div class="_label_count"> <?php echo e(getTotalOrders(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'2')); ?> </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Report Done</div>
                                                <div class="_label_count"> <?php echo e(getTotalOrders(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'6')); ?> </div>
                                            </div>
                                        </div>


                                        <div class="col-md-12 pr-0">
                                            <hr>
                                        </div>
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">During Confirmed</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalOrdersUpdated(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'2')); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">During Report Done</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalOrdersUpdated(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'6')); ?>

                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom wave wave-animate-slow wave-primary mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center p-5">
                            <div class="mr-6">
                                <span class="svg-icon svg-icon-success svg-icon-4x  _count_size">
                                    <?php echo e(getTotalEnquires(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'))); ?>

                                </span>
                            </div>
                            <div class="d-flex flex-column w-100">
                                <a href="<?php echo e(url('admin/enquires/list')); ?>" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">
                                    Pages Indexed
                                </a>
                                <div class="text-dark-75 w-100">
                                    <div class="row">
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Active</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalEnquires(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'new')); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Inactive</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalEnquires(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'pending')); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Converted</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalEnquires(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'converted')); ?>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 pr-0">
                                            <hr>
                                        </div>
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">During Pending</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalEnquiresUpdated(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'pending')); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">During Convert</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalEnquiresUpdated(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'converted')); ?>

                                                </div>
                                            </div>
                                        </div> -->


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom wave wave-animate-slow wave-primary mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center p-5">
                            <div class="mr-6">
                                <span class="svg-icon svg-icon-success svg-icon-4x _count_size">

                                    <?php echo e($getTotalPartnerEnquires = getTotalPartnerEnquires(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'))); ?>



                                </span>
                            </div>
                            <div class="d-flex flex-column w-100">
                                <a href="<?php echo e(url('admin/partnerenquiry/list')); ?>" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">
                                    Number of Rules
                                </a>
                                <div class="text-dark-75 w-100">
                                    <div class="row">
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Active</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalPartnerEnquires(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),1)); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Inactive</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalPartnerEnquires(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),2)); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Convert</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalPartnerEnquires(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),3)); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 pr-0">
                                            <hr>
                                        </div>
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">During Pending</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalPartnerEnquiresUpdated(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),2)); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">During Convert</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalPartnerEnquiresUpdated(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),3)); ?>

                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom wave wave-animate-slow wave-primary mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center p-5">
                            <div class="mr-6">
                                <span class="svg-icon svg-icon-success svg-icon-4x _count_size">
                                    <?php echo e(getTotalQueries(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'))); ?>

                                </span>
                            </div>
                            <div class="d-flex flex-column w-100">
                                <a href="<?php echo e(url('admin/queries/list')); ?>" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">
                                    Layouts
                                </a>
                                <div class="text-dark-75 w-100">
                                    <div class="row">
                                        <div class="col-md-6 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Active</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalQueries(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'inactive')); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Inactive</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalQueries(request('fromtodate') ? request('fromtodate') : date('Y-m-d 00:00:01'),'active')); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <hr><br>
            </div>
            <div class="col-md-4">
                <div class="card card-custom wave wave-animate-slow wave-primary mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center p-5">
                            <div class="mr-6">
                                <span class="svg-icon svg-icon-success svg-icon-4x  _count_size">
                                    <?php echo e(getTotalCentres()); ?>

                                </span>
                            </div>
                            <div class="d-flex flex-column w-100">
                                <a href="<?php echo e(url('admin/centres/list')); ?>" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">
                                    Centres / Franchises
                                </a>
                                <div class="text-dark-75 w-100">
                                    <div class="row">
                                        <div class="col-md-6 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Active</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalCentres('','active')); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">InActive</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalCentres('','inactive')); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom wave wave-animate-slow wave-primary mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center p-5">
                            <div class="mr-6">
                                <span class="svg-icon svg-icon-success svg-icon-4x _count_size">
                                    <?php echo e(getTotalDoctors()); ?>

                                </span>
                            </div>
                            <div class="d-flex flex-column w-100">
                                <a href="<?php echo e(url('admin/doctors/list')); ?>" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">
                                    Items
                                </a>
                                <div class="text-dark-75 w-100">

                                    <div class="row">
                                        <div class="col-md-6 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">Active</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalDoctors('','active')); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pr-0">
                                            <div class="dash_counts text-center">
                                                <div class="_label">InActive</div>
                                                <div class="_label_count">
                                                    <?php echo e(getTotalDoctors('','inactive')); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h5>Pages</h5>
            </div>
            <div class="col-md-12">
                <div id="chart_1"></div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('styles'); ?>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(url('/')); ?>/public/js/apexcharts.js?v=7.2.9"></script>
<script>
    function setSubmitAtt() {
        $('.applyBtn').attr('onclick', "submitDateForm()");
        $('.daterangepicker .ranges ul li').not(':last').attr('onclick', "submitDateForm()");
    }

    function submitDateForm() {
        setTimeout(function() {
            $('#Filter_ME').click();

        }, 200);
    }
    var _demo1 = function(dataArray) {
        dataArray = JSON.parse(dataArray);
        const apexChart = "#chart_1";
        var options = {
            series: [{
                name: "Orders",
                // data: [10, 41, 35, 51, 49, 62, 69, 91, 148, 148, 148, 148]
                data: dataArray
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            colors: [primary]
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }
    _demo1('<?php if (count($ordersMonthWiseData) > 0) {
                echo json_encode($ordersMonthWiseData);
            } ?>');
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\munna sir 01032026\seo-engine-admin\resources\views/admin/pages/dashboard/list.blade.php ENDPATH**/ ?>