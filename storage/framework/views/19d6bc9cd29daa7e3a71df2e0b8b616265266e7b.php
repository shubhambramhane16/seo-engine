<?php $__env->startSection('pagemaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Approval Requests</h3>
        </div>
        <div class="card-toolbar">
            <a href="<?php echo e(url('/admin/page/list')); ?>" class="btn btn-light-primary font-weight-bolder">
                <i class="la la-arrow-left"></i> Back to Pages
            </a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-hover" id="approvalTable">
            <thead>
                <tr>
                    <th>SNo.</th>
                    <th>Page</th>
                    <th>Requested By</th>
                    <th>Current Approver</th>
                    <th>Status</th>
                    <th>Requested On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $statusClass = 'secondary';
                    $statusLabel = ucfirst(str_replace('_', ' ', $item->status));
                    if($item->status == 'pending_manager' || $item->status == 'pending_admin') {
                        $statusClass = 'warning';
                        $statusLabel = 'Pending Approval';
                    } elseif($item->status == 'approved') {
                        $statusClass = 'success';
                    } elseif($item->status == 'rejected') {
                        $statusClass = 'danger';
                    }
                ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e(optional($item->page)->page_name); ?><br><small><?php echo e(optional($item->page)->slug); ?></small></td>
                    <td><?php echo e(optional($item->requester)->name); ?></td>
                    <td><?php echo e(optional($item->currentApprover)->name ?? '-'); ?></td>
                    <td><span class="label label-lg font-weight-bold label-light-<?php echo e($statusClass); ?> label-inline"><?php echo e($statusLabel); ?></span></td>
                    <td><?php echo e(date('d M Y H:i', strtotime($item->created_at))); ?></td>
                    <td>
                        <a href="<?php echo e(url('/admin/page/approval-requests/'.$item->id)); ?>" class="btn btn-sm btn-clean btn-icon" title="Review Request" data-toggle="tooltip">
                            <i class="la la-search"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<link href="<?php echo e(asset('plugins/custom/datatables/datatables.bundle.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#approvalTable').DataTable();
        $('.dataTables_filter label input[type=search]').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/page_approval/list.blade.php ENDPATH**/ ?>