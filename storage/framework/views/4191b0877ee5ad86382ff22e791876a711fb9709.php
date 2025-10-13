 

 <?php $__env->startSection('content'); ?>

 <!--begin::Main-->
 <div class="d-flex flex-column flex-root">
     <!--begin::Login-->
     <div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
         <!--begin::Aside-->
         <div class="login-aside d-flex flex-column flex-row-auto cw-40" style="background-color: #F2C98A;  ">
             <!--begin::Aside Top-->
             <div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
                 <!--begin::Aside header-->
                 <!-- <a href="#" class="text-center mb-10">
                     <img src="/metronic/theme/html/demo1/dist/assets/media/logos/logo-letter-1.png" class="max-h-70px" alt="" />
                 </a> -->
                 <!--end::Aside header-->
                 <!--begin::Aside title-->

                 <h3 class="font-weight-bolder text-center font-size-h4 font-size-h1-lg">Welcome to Seo Engine</h3>
                 <h3 class="text-center font-size-h4" style="color: #986923;">
                     Your command center for optimizing and enhancing the digital presence of Dr Lal Pathlabs. Empower your SEO strategies, analyze performance, and elevate online visibility effortlessly. Let Seo Engine be your driving force for unlocking unparalleled success in the digital landscape.
                 </h3>
                 <!--end::Aside title-->
             </div>
             <!--end::Aside Top-->
             <!--begin::Aside Bottom-->
             <div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style="background-image: url(<?php echo e(asset('/media/custom/login-visual-1.svg')); ?>);"></div>
             <!--end::Aside Bottom-->
         </div>
         <!--begin::Aside-->
         <!--begin::Content-->
         <div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
             <!--begin::Content body-->
             <div class="d-flex flex-column-fluid flex-center">
                 <!--begin::Signin-->
                 <div class="login-form login-signin">
                     <!--begin::Form-->
                     <form class="form" novalidate="novalidate" id="kt_login_signin_form" action="<?php echo e(url('admin/auth/login')); ?>" method="post">
                         <?php echo e(csrf_field()); ?>

                         <!--begin::Title-->
                         <div class="pb-13 pt-lg-0 pt-5">
                             <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg"><?php echo e(config('app.name')); ?></h3>
                             <span class="text-muted font-size-h4">Make Dr Lal Pathlabs stand out online with Seo Engine's help.</span>
                         </div>
                         <!--begin::Title-->
                         <!--begin::Form group-->
                         <div class="form-group">
                             <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                             <div class="alert alert-danger" role="alert">
                                 <strong><?php echo e($message); ?></strong>
                             </div>
                             <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                             <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                             <div class="alert alert-danger" role="alert">
                                 <strong><?php echo e($message); ?></strong>
                             </div>
                             <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                             <?php $__errorArgs = ['error'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                             <div class="alert alert-danger" role="alert">
                                 <strong><?php echo e($message); ?></strong>
                             </div>
                             <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                             <?php if(request('error')): ?>
                             <div class="alert alert-danger" role="alert">
                                 <strong><?php echo e(request('error')); ?></strong>
                             </div>
                             <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                         </div>
                         <div class="form-group">
                             <label class="font-size-h6 font-weight-bolder text-dark">Email</label>
                             <input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="text" name="email" autocomplete="off" isrequired="isrequired" />

                         </div>
                         <!--end::Form group-->
                         <!--begin::Form group-->
                         <div class="form-group">
                             <div class="d-flex justify-content-between mt-n5">
                                 <label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>

                             </div>
                             <input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="password" name="password" autocomplete="off" isrequired="isrequired" />

                         </div>
                         <!--end::Form group-->
                         <!--begin::Action-->
                         <div class="pb-lg-0 pb-5">
                             <button type="submit" id="kt_login_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Sign In</button>
                         </div>
                         <!--end::Action-->
                     </form>
                     <!--end::Form-->
                 </div>
                 <!--end::Signin-->
                 <!--begin::Signup-->

                 <!--end::Signup-->
                 <!--begin::Forgot-->

                 <!--end::Forgot-->
             </div>
             <!--end::Content body-->
             <!--begin::Content footer-->

             <!--end::Content footer-->
         </div>
         <!--end::Content-->
     </div>
     <!--end::Login-->
 </div>
 <!--end::Main-->

 <?php $__env->stopSection(); ?>

 
 <?php $__env->startSection('styles'); ?>
 <style>
     .cw-40 {
         width: 40%;
     }

     @media  only screen and (max-width: 900px) {
         .cw-40 {
             width: 100%;
    padding-bottom: 40px;
         }
     }
 </style>
 <?php $__env->stopSection(); ?>


 
 <?php $__env->startSection('scripts'); ?>
 

 <script src="<?php echo e(url('/')); ?>/public/js/custom.js" type="text/javascript"></script>
 
 <?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.pages.auth.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ACER\Downloads\lpl_seoengine_admin\lpl_seoengine_admin\resources\views/admin/pages/auth/login.blade.php ENDPATH**/ ?>