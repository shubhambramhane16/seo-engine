<?php $__env->startSection('pagemaster','active menu-item-open'); ?>
<?php $__env->startSection('content'); ?>
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Review Approval Request</h3>
        </div>
        <div class="card-toolbar">
            <a href="<?php echo e(url('/admin/page/approval-requests')); ?>" class="btn btn-light-primary font-weight-bolder">
                <i class="la la-arrow-left"></i> Back to Queue
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="mb-6">
            <div><strong>Page:</strong> <?php echo e(optional($requestDetails->page)->page_name); ?> (<?php echo e(optional($requestDetails->page)->slug); ?>)</div>
            <div><strong>Requested By:</strong> <?php echo e(optional($requestDetails->requester)->name); ?></div>
            <div><strong>Current Approver:</strong> <?php echo e(optional($requestDetails->currentApprover)->name ?? '-'); ?></div>
            <div><strong>Status:</strong> <?php echo e(ucfirst(str_replace('_', ' ', $requestDetails->status))); ?></div>
            <?php if($requestDetails->approver_comments): ?>
            <div><strong>Latest Comments:</strong> <?php echo e($requestDetails->approver_comments); ?></div>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 20%">Field</th>
                        <th style="width: 40%">Old (Live)</th>
                        <th style="width: 40%">New (Proposed)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $diffRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($row['field']); ?></td>
                        <td class="<?php echo e($row['changed'] ? 'bg-light-danger' : ''); ?>"><pre class="mb-0" style="white-space: pre-wrap"><?php echo e($row['old']); ?></pre></td>
                        <td class="<?php echo e($row['changed'] ? 'bg-light-success' : ''); ?>"><pre class="mb-0" style="white-space: pre-wrap"><?php echo e($row['new']); ?></pre></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if(in_array($requestDetails->status, ['pending_manager', 'pending_admin']) && ($requestDetails->current_approver_id == auth()->user()->id || $isSuperAdmin)): ?>
        <div class="row mt-8">
            <div class="col-md-6">
                <form method="POST" action="<?php echo e(url('/admin/page/approval-requests/'.$requestDetails->id.'/approve')); ?>">
                    <?php echo e(csrf_field()); ?>

                    <div class="form-group">
                        <label>Approval Comments</label>
                        <textarea class="form-control" name="comments" rows="3" placeholder="Optional comments"></textarea>
                    </div>
                    <button class="btn btn-success" type="submit">Approve & Publish</button>
                </form>
            </div>
            <div class="col-md-6">
                <form method="POST" action="<?php echo e(url('/admin/page/approval-requests/'.$requestDetails->id.'/reject')); ?>">
                    <?php echo e(csrf_field()); ?>

                    <div class="form-group">
                        <label>Rejection Comments</label>
                        <textarea class="form-control" name="comments" rows="3" placeholder="Reason for rejection" required></textarea>
                    </div>
                    <button class="btn btn-danger" type="submit">Reject</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-10">
            <h5>Audit Trail</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Action</th>
                        <th>By</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $requestDetails->logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e(date('d M Y H:i', strtotime($log->created_at))); ?></td>
                        <td><?php echo e(ucfirst(str_replace('_', ' ', $log->action))); ?></td>
                        <td><?php echo e(optional($log->actionBy)->name); ?></td>
                        <td><?php echo e($log->from_status); ?></td>
                        <td><?php echo e($log->to_status); ?></td>
                        <td><?php echo e($log->comments); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/pages/page_approval/review.blade.php ENDPATH**/ ?>