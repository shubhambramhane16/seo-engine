<?php if(config('layout.self.layout') == 'blank'): ?>
<div class="d-flex flex-column flex-root">
    <?php echo $__env->yieldContent('content'); ?>
</div>
<?php else: ?>

<?php echo $__env->make('admin.layout.base._header-mobile', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="d-flex flex-column flex-root">
    <div class="d-flex flex-row flex-column-fluid page">

        <?php if(config('layout.aside.self.display')): ?>
        <?php echo $__env->make('admin.layout.base._aside', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">

            <?php echo $__env->make('admin.layout.base._header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <div class="content <?php echo e(Metronic::printClasses('content', false)); ?> d-flex flex-column flex-column-fluid" id="kt_content">
 

                <?php echo $__env->make('admin.layout.base._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <?php echo $__env->make('admin.layout.base._footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>

<?php endif; ?>
 <?php /**PATH D:\munna sir 01032026\seo-engine-admin\resources\views/admin/layout/base/_layout.blade.php ENDPATH**/ ?>