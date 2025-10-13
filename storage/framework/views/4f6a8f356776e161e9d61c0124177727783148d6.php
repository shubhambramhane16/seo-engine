<div class="">
    <?php if($errors->any()): ?>
    <?php echo implode('', $errors->all('<div class="alert alert-danger"><i class="fas fa-exclamation-circle error-icons"></i> <strong>:message</strong> </div>')); ?>

    <?php endif; ?>


    <?php $__errorArgs = ['error'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle error-icons"></i><strong><?php echo e($message); ?></strong> </div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <?php $__errorArgs = ['success'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <div class="alert alert-success"><i class="fad fa-check-double error-icons"></i><strong><?php echo e($message); ?></strong> </div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <?php if(session()->has('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-double error-icons"></i><strong><?php echo e(session()->get('success')); ?></strong>
    </div>
    <?php endif; ?>

    <?php if(session()->has('message')): ?>
    <div class="alert alert-primary">
        <strong><?php echo e(session()->get('message')); ?></strong>
    </div>
    <?php endif; ?>
</div><?php /**PATH C:\xampp\htdocs\seo_engine\resources\views/admin/layout/partials/errors/error_messages.blade.php ENDPATH**/ ?>