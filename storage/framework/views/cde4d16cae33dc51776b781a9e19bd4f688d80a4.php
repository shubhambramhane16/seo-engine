
<?php if(config('layout.content.extended')): ?>
<?php echo $__env->yieldContent('content'); ?>
<?php else: ?>
<div class="breadcrumbs-div ">
    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">

        <li class="breadcrumb-item text-muted">
            <a href="<?php echo e(url('admin')); ?>" class="text-muted">Dashboard</a>
        </li>
        <?php if(isset($breadcrumbs) && count($breadcrumbs) > 0): ?>
        <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="breadcrumb-item text-muted">
            <a <?php if($list['url']): ?> href="<?php echo e($list['url']); ?>" <?php else: ?> onclick="javascript:void(0)" <?php endif; ?> class="text-muted"><?php echo e($list['title']); ?></a>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

    </ul>
</div>
<div class="d-flex flex-column-fluid">
    <div class="<?php echo e(Metronic::printClasses('content-container', false)); ?>">

        <?php echo $__env->make('admin.layout.partials.errors.error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</div>
<?php endif; ?><?php /**PATH D:\sumeshwar sir plans\seo-engine\resources\views/admin/layout/base/_content.blade.php ENDPATH**/ ?>