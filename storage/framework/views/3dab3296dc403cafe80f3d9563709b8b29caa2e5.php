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
                <i class="la la-plus"></i> Generator </a>
            <a href="<?php echo e(url('/admin/page/approval-requests')); ?>" class="btn btn-warning font-weight-bolder ml-2">
                <i class="la la-check"></i> Approval Requests </a>
            <!--end::Button-->
        </div>
        <div class="w-100">
            <div class="row col-lg-12 pl-0 pr-0">
                <div class="col-sm-3">
                    <div class="dataTables_length">
                        <label>Status</label>
                        <select id="statusFilter" class="form-control">
                            <option value="-1">All Status</option>
                            <option value="0">InActive</option>
                            <option value="1">Active</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="dataTables_length">
                        <label cla>&#160; </label>
                        <button type="button" id="applyFilter" class="btn btn-success" data-toggle="tooltip" title="Apply Filter" style="margin-top: 20px;">Filter</button>
                        <button type="button" id="resetFilter" class="btn btn-default" data-toggle="tooltip" title="Reset Filter" style="margin-top: 20px;">Reset</button>
                    </div>
                </div>

            </div>
        </div>
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
                    <th>Approval Status</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
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
<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
<!-- <script src="<?php echo e(asset('plugins/custom/datatables/datatables.bundle.js')); ?>" type="text/javascript"></script> -->

<script>
    var table;
    $(document).ready(function() {
        // Initialize DataTable with AJAX
        table = $('#myTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(url('admin/page/list')); ?>",
                type: 'GET',
                data: function(d) {
                    d.status = $('#statusFilter').val();
                }
            },
            columns: [
                { data: 'counter', name: 'id', orderable: true, searchable: false },
                { data: 'page_name', name: 'page_name', orderable: true },
                { data: 'slug', name: 'slug', orderable: true },
                { data: 'meta_title', name: 'seo_title', orderable: true },
                { data: 'approval_status', name: 'approval_status', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: true, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            drawCallback: function() {
                // Reinitialize tooltips after each draw
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        // Apply custom styles to DataTables elements
        $('.dataTables_filter label input[type=search]').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');

        // Apply Filter Button
        $('#applyFilter').on('click', function() {
            table.ajax.reload();
        });

        // Reset Filter Button
        $('#resetFilter').on('click', function() {
            $('#statusFilter').val('-1');
            table.ajax.reload();
        });
    });

    // Change Status Function
    function changeStatus(element) {
        var url = $(element).data('url');
        if (confirm('Are you sure you want to change the status?')) {
            window.location.href = url;
        }
    }
</script>


<!-- <script src="<?php echo e(asset('js/pages/crud/datatables/basic/basic.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script> -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/page/list.blade.php ENDPATH**/ ?>