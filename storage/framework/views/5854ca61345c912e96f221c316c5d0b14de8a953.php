
<div id="kt_header" class="header <?php echo e(Metronic::printClasses('header', false)); ?>" <?php echo e(Metronic::printAttrs('header')); ?>>

    
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <?php if(config('layout.header.self.display')): ?>

        <?php
        $kt_logo_image = 'logo-light.png';
        ?>

        <?php if(config('layout.header.self.theme') === 'light'): ?>
        <?php $kt_logo_image = 'logo-dark.png' ?>
        <?php elseif(config('layout.header.self.theme') === 'dark'): ?>
        <?php $kt_logo_image = 'logo-light.png' ?>
        <?php endif; ?>

        
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <?php if(config('layout.aside.self.display') == false): ?>
            <div class="header-logo">
                <a href="<?php echo e(url('/')); ?>">
                    <img alt="Logo" src="<?php echo e(asset('media/logos/'.$kt_logo_image)); ?>" />
                </a>
            </div>
            <?php endif; ?>
 
    </div>

    <?php else: ?>
    <div></div>
    <?php endif; ?>

    <?php echo $__env->make('admin.layout.partials.extras._topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
</div><?php /**PATH C:\xampp\htdocs\seo_engine\resources\views/admin/layout/base/_header.blade.php ENDPATH**/ ?>