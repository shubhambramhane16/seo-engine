

<?php
    $kt_logo_image = 'logo-light.png';
?>

<?php if(config('layout.brand.self.theme') === 'light'): ?>
    <?php $kt_logo_image = 'logo-dark.png' ?>
<?php elseif(config('layout.brand.self.theme') === 'dark'): ?>
    <?php $kt_logo_image = 'seo-engine.png' ?>
<?php endif; ?>

<div class="aside aside-left <?php echo e(Metronic::printClasses('aside', false)); ?> d-flex flex-column flex-row-auto"
    id="kt_aside">

    
    <div class="brand flex-column-auto <?php echo e(Metronic::printClasses('brand', false)); ?>" id="kt_brand">
        <div class="brand-logo text-center">
            <a href="<?php echo e(url('/')); ?>">

                <img class="pt-10  w-70" alt="<?php echo e(config('app.name')); ?>"
                    src="<?php echo e(asset('media/logos/' . $kt_logo_image)); ?>" />
            </a>
        </div>

        <?php if(config('layout.aside.self.minimize.toggle')): ?>
            <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
                <?php echo e(Metronic::getSVG('media/svg/icons/Navigation/Angle-double-left.svg', 'svg-icon-xl')); ?>

            </button>
        <?php endif; ?>

    </div>

    
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

        <?php if(config('layout.aside.self.display') === false): ?>
            <div class="header-logo">
                <a href="<?php echo e(url('/')); ?>">
                   

                    <img alt="<?php echo e(config('app.name')); ?>" src="<?php echo e(asset('media/logos/' . $kt_logo_image)); ?>" />
                </a>
            </div>
        <?php endif; ?>
<?php  $systemRolesArray = request()->session()->get('system_roles');
// dd($systemRolesArray);
?>

        <div id="kt_aside_menu" class="aside-menu <?php echo e(Metronic::printClasses('aside_menu', false)); ?>"
            data-menu-vertical="1" <?php echo e(Metronic::printAttrs('aside_menu')); ?>>
            <ul class="menu-nav <?php echo e(Metronic::printClasses('aside_menu_nav', false)); ?>">
                <li class="menu-item menu-item-submenu <?php echo $__env->yieldContent('dashboardmaster'); ?>" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="<?php echo e(url('/')); ?>/admin/dashboard" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <span class="flaticon-dashboard"></span>
                        </span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>

     <?php if(isset($systemRolesArray['page'])): ?> <?php if($systemRolesArray['page']!=0): ?>
                <li class="menu-item menu-item-submenu   <?php echo $__env->yieldContent('pagemaster'); ?>" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="<?php echo e(url('/admin/page/list')); ?>" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <!-- Generator: Sketch 50.2 (55047) - http://www.bohemiancoding.com/sketch -->
                                <title>Stockholm-icons / General / Clipboard</title>
                                <desc>Created with Sketch.</desc>
                                <defs></defs>
                                <g id="Stockholm-icons-/-General-/-Clipboard" stroke="none" stroke-width="1"
                                    fill="none" fill-rule="evenodd">
                                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                    <path
                                        d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                        id="Combined-Shape" fill="#000000" opacity="0.3"></path>
                                    <path
                                        d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                        id="Combined-Shape" fill="#000000"></path>
                                    <rect id="Rectangle-152" fill="#000000" opacity="0.3" x="7" y="10" width="5"
                                        height="2" rx="1"></rect>
                                    <rect id="Rectangle-152-Copy" fill="#000000" opacity="0.3" x="7" y="14"
                                        width="9" height="2" rx="1"></rect>
                                </g>
                            </svg>
                        </span>
                        <span class="menu-text">Pages</span>
                    </a>
                </li>
                <?php endif; ?>    <?php endif; ?>
                <?php if(isset($systemRolesArray['categories']) || isset($systemRolesArray['items'])): ?>  <?php if(($systemRolesArray['categories'])!=0 || ($systemRolesArray['items'])!=0): ?>
                <li class="menu-item menu-item-submenu <?php echo $__env->yieldContent('itemsmaster'); ?> <?php echo $__env->yieldContent('categorymaster'); ?>  <?php echo $__env->yieldContent('packagemaster'); ?>"
                    aria-haspopup="true" data-menu-toggle="hover">
                    <a href="#" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <!-- Generator: Sketch 50.2 (55047) - http://www.bohemiancoding.com/sketch -->
                                <title>Stockholm-icons / Shopping / Box2</title>
                                <desc>Created with Sketch.</desc>
                                <defs></defs>
                                <g id="Stockholm-icons-/-Shopping-/-Box2" stroke="none" stroke-width="1" fill="none"
                                    fill-rule="evenodd">
                                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                    <path
                                        d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                        id="Combined-Shape" fill="#000000"></path>
                                    <path
                                        d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                        id="Path" fill="#000000" opacity="0.3"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="menu-text">Products</span><i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu " kt-hidden-height="320" style=""><span class="menu-arrow"></span>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                                        class="menu-text">Admin</span></span></li>
                <?php if($systemRolesArray['categories']!=0): ?>
                            <li class="menu-item <?php echo $__env->yieldContent('itemsmaster'); ?>" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="<?php echo e(url('/admin/categories/list')); ?>" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Categories</span>
                                </a>
                            </li>
                <?php endif; ?>
                <?php if($systemRolesArray['items']!=0): ?>
                            <li class="menu-item menu-item-submenu <?php echo $__env->yieldContent('packagemaster'); ?>" aria-haspopup="true"
                                data-menu-toggle="hover">
                                <a href="<?php echo e(url('/admin/items/list')); ?>" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Items</span>
                                </a>
                            </li>
                <?php endif; ?>
                        </ul>
                    </div>
                </li>

                      <?php endif; ?>
                <?php endif; ?>

                <?php if(isset($systemRolesArray['city']) || isset($systemRolesArray['state']) || isset($systemRolesArray['locality'])): ?>  
                  <?php if(($systemRolesArray['city'])!=0 || ($systemRolesArray['state'])!=0 || ($systemRolesArray['locality'])!=0): ?>

                <li class="menu-item menu-item-submenu  <?php echo $__env->yieldContent('statemaster'); ?>  <?php echo $__env->yieldContent('citymaster'); ?> <?php echo $__env->yieldContent('localitymaster'); ?> "
                    aria-haspopup="true" data-menu-toggle="hover">
                    <a href="#" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path
                                        d="M9.82829464,16.6565893 C7.02541569,15.7427556 5,13.1079084 5,10 C5,6.13400675 8.13400675,3 12,3 C15.8659932,3 19,6.13400675 19,10 C19,13.1079084 16.9745843,15.7427556 14.1717054,16.6565893 L12,21 L9.82829464,16.6565893 Z M12,12 C13.1045695,12 14,11.1045695 14,10 C14,8.8954305 13.1045695,8 12,8 C10.8954305,8 10,8.8954305 10,10 C10,11.1045695 10.8954305,12 12,12 Z"
                                        fill="#000000"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="menu-text">Locations</span><i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu" kt-hidden-height="320" style=""><span class="menu-arrow"></span>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true"><span
                                    class="menu-link"><span class="menu-text">Admin</span></span></li>
                <?php if($systemRolesArray['state']!=0): ?>

                            <li class="menu-item <?php echo $__env->yieldContent('statemaster'); ?>" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="<?php echo e(url('/admin/state/list')); ?>" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">State</span>
                                </a>
                            </li>
                            <?php endif; ?>
                <?php if($systemRolesArray['city']!=0): ?> 
                            <li class="menu-item <?php echo $__env->yieldContent('citymaster'); ?>" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="<?php echo e(url('/admin/city/list')); ?>" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">City</span>
                                </a>
                            </li>
                <?php endif; ?>
                <?php if($systemRolesArray['locality']!=0): ?> 

                            <li class="menu-item <?php echo $__env->yieldContent('localitymaster'); ?>" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="<?php echo e(url('/admin/locality/list')); ?>" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Locality</span>
                                </a>
                            </li>
                <?php endif; ?>
                        </ul>
                    </div>
                </li>
                      <?php endif; ?>
                <?php endif; ?>


                <?php if(isset($systemRolesArray['centres'])): ?>  <?php if($systemRolesArray['centres']!=0): ?>
                <li class="menu-item menu-item-submenu   <?php echo $__env->yieldContent('centermaster'); ?>" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="<?php echo e(url('/admin/centres/list')); ?>" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <!-- Generator: Sketch 50.2 (55047) - http://www.bohemiancoding.com/sketch -->
                                <title>Stockholm-icons / Home / Building</title>
                                <desc>Created with Sketch.</desc>
                                <defs></defs>
                                <g id="Stockholm-icons-/-Home-/-Building" stroke="none" stroke-width="1"
                                    fill="none" fill-rule="evenodd">
                                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                    <path
                                        d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z"
                                        id="Combined-Shape" fill="#000000"></path>
                                    <rect id="Rectangle-Copy-2" fill="#FFFFFF" x="13" y="8" width="3"
                                        height="3" rx="1"></rect>
                                    <path
                                        d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z"
                                        id="Rectangle-2" fill="#000000" opacity="0.3"></path>
                                </g>
                            </svg>
                        </span>

                        <span class="menu-text">Centres / Franchises</span>

                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if(isset($systemRolesArray['enquires'])): ?>  <?php if($systemRolesArray['enquires']!=0): ?>
                <li class="menu-item menu-item-submenu   <?php echo $__env->yieldContent('enquiry-master'); ?>" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="<?php echo e(url('/admin/enquiry/list')); ?>" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg width="800px" height="800px" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M37 16C34.2386 16 32 13.7614 32 11C32 8.23858 34.2386 6 37 6C39.7614 6 42 8.23858 42 11C42 13.7614 39.7614 16 37 16Z" fill="#2F88FF" stroke="#000000" stroke-width="4" stroke-miterlimit="2"/>
                                <path d="M12 12C9.79086 12 8 10.2091 8 8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8C16 10.2091 14.2091 12 12 12Z" fill="#2F88FF" stroke="#000000" stroke-width="4" stroke-miterlimit="2"/>
                                <path d="M26 39L32 34V28C32 24.5339 34 22 37 22C40 22 42 24.5339 42 28V32.8372V42" stroke="#000000" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M24 33L18 28V24C18 20.5339 16 18 13 18C10 18 8 20.5339 8 24V26.8372V42" stroke="#000000" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                        </span>
                        <span class="menu-text">Enquiries</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>


                <?php if(isset($systemRolesArray['testimonials'])): ?>
                <?php if($systemRolesArray['testimonials']!=0): ?>
                <li class="menu-item menu-item-submenu   <?php echo $__env->yieldContent('testimonial_master'); ?>" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="<?php echo e(url('/admin/testimonials/list')); ?>" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg fill="#000000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="800px" height="800px"
                                viewBox="0 0 478.248 478.248" xml:space="preserve">
                                <g>
                                    <g>
                                        <g>
                                            <path d="M456.02,44.821H264.83c-12.26,0-22.232,9.972-22.232,22.229v98.652c0,12.258,9.974,22.23,22.232,22.23h16.787v39.161
    c0,2.707,1.58,5.165,4.043,6.292c0.92,0.42,1.901,0.627,2.875,0.627c1.631,0,3.244-0.576,4.523-1.685l51.383-44.396h111.576
    c12.26,0,22.23-9.973,22.23-22.23V67.05C478.25,54.792,468.277,44.821,456.02,44.821z M319.922,112.252l-10.209,9.953
    l2.41,14.054c0.174,1.015-0.242,2.038-1.076,2.643c-0.469,0.342-1.027,0.516-1.588,0.516c-0.428,0-0.861-0.103-1.256-0.31
    l-12.621-6.635l-12.619,6.635c-0.912,0.478-2.016,0.398-2.848-0.206s-1.248-1.628-1.074-2.643l2.41-14.054l-10.211-9.953
    c-0.734-0.718-1.002-1.792-0.685-2.769c0.317-0.978,1.164-1.691,2.183-1.839l14.11-2.05l6.31-12.786
    c0.457-0.923,1.396-1.507,2.424-1.507s1.969,0.584,2.422,1.507l6.312,12.786l14.107,2.05c1.02,0.148,1.863,0.861,2.184,1.839
    C320.924,110.46,320.658,111.535,319.922,112.252z M384.766,112.252l-10.211,9.953l2.412,14.054
    c0.172,1.015-0.244,2.038-1.076,2.643c-0.469,0.342-1.025,0.516-1.588,0.516c-0.43,0-0.859-0.103-1.26-0.31l-12.619-6.635
    l-12.619,6.635c-0.912,0.478-2.014,0.398-2.846-0.206c-0.834-0.604-1.25-1.628-1.076-2.643l2.41-14.054l-10.209-9.953
    c-0.734-0.718-1.002-1.792-0.684-2.769c0.316-0.978,1.16-1.691,2.182-1.839l14.109-2.05l6.311-12.786
    c0.455-0.923,1.396-1.507,2.422-1.507c1.029,0,1.967,0.584,2.422,1.507l6.312,12.786l14.109,2.05
    c1.021,0.148,1.863,0.861,2.182,1.839C385.768,110.46,385.5,111.535,384.766,112.252z M449.607,112.252l-10.211,9.953
    l2.408,14.054c0.176,1.015-0.238,2.038-1.072,2.643c-0.471,0.342-1.027,0.516-1.59,0.516c-0.43,0-0.859-0.103-1.258-0.31
    l-12.621-6.635l-12.621,6.635c-0.908,0.478-2.012,0.398-2.844-0.206c-0.834-0.604-1.248-1.628-1.076-2.643l2.412-14.054
    l-10.211-9.953c-0.734-0.718-1-1.792-0.684-2.769c0.316-0.978,1.164-1.691,2.182-1.839l14.111-2.05l6.311-12.786
    c0.453-0.923,1.395-1.507,2.42-1.507c1.027,0,1.971,0.584,2.426,1.507L434,105.594l14.109,2.05
    c1.018,0.148,1.861,0.861,2.182,1.839C450.609,110.46,450.344,111.535,449.607,112.252z" />
                                            <path d="M152.844,112.924c-46.76,0-72.639,24.231-72.166,70.921c0.686,63.947,27.859,102.74,72.166,102.063
    c0,0,72.131,2.924,72.131-102.063C224.975,137.155,200.605,112.924,152.844,112.924z" />
                                            <path d="M280.428,334.444l-72.074-28.736l-16.877-14.223c-4.457-3.766-11.041-3.488-15.178,0.621l-23.463,23.336l-23.533-23.342
    c-4.137-4.104-10.713-4.369-15.164-0.615l-16.881,14.223l-72.074,28.739C1.975,343.69,1.995,425.884,0,433.427h305.646
    C303.656,425.9,303.646,343.679,280.428,334.444z" />
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </span>

                        <span class="menu-text">Testimonials</span>

                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if(isset($systemRolesArray['templates'])): ?>
                <?php if($systemRolesArray['templates']!=0): ?>
                <li class="menu-item menu-item-submenu   <?php echo $__env->yieldContent('templatesmaster'); ?>" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="<?php echo e(url('/admin/templates/list')); ?>" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <defs></defs>
                                <g id="Stockholm-icons-/-Layout-/-Layout-horizontal" stroke="none" stroke-width="1"
                                    fill="none" fill-rule="evenodd">
                                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                    <rect id="Rectangle-7" fill="#000000" opacity="0.3" x="4" y="5" width="16"
                                        height="6" rx="1.5"></rect>
                                    <rect id="Rectangle-7-Copy" fill="#000000" x="4" y="13" width="16"
                                        height="6" rx="1.5"></rect>
                                </g>
                            </svg>
                        </span>
                        <span class="menu-text">Templates</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
                <?php if(isset($systemRolesArray['rules'])): ?>
                     <?php if($systemRolesArray['rules']!=0): ?>
                    
                <li class="menu-item menu-item-submenu   <?php echo $__env->yieldContent('rulemaster'); ?>" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="<?php echo e(url('/admin/rules/list')); ?>" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <defs></defs>
                                <g id="Stockholm-icons-/-Communication-/-Clipboard-check" stroke="none"
                                    stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                    <path
                                        d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                        id="Combined-Shape" fill="#000000" opacity="0.3"></path>
                                    <path
                                        d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z"
                                        id="check-path" fill="#000000"></path>
                                    <path
                                        d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                        id="Combined-Shape" fill="#000000"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="menu-text">Rules</span>
                    </a>
                </li>

                    <?php endif; ?>
                <?php endif; ?>
                <?php if(isset($systemRolesArray['users']) || isset($systemRolesArray['roles'])): ?>
                      <?php if(($systemRolesArray['users'])!=0 || ($systemRolesArray['roles'])!=0): ?>
                <li class="menu-item menu-item-submenu <?php echo $__env->yieldContent('userlist'); ?>" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="#" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" height="16" width="20"
                                viewBox="0 0 640 512">
                                <path
                                    d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z" />
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-text">Admin Users</span><i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu " kt-hidden-height="320" style=""><span
                            class="menu-arrow"></span>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true"><span
                                    class="menu-link"><span class="menu-text">Admin</span></span></li>
                     <?php if($systemRolesArray['roles']!=0): ?>
                            <li class="menu-item  <?php echo $__env->yieldContent('userrole'); ?>" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="<?php echo e(url('/admin/roles/list')); ?>" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Roles</span>
                                </a>
                            </li>
                            <?php endif; ?>
                     <?php if($systemRolesArray['users']!=0): ?>

                            <li class="menu-item  <?php echo $__env->yieldContent('userlist'); ?>" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="<?php echo e(url('/admin/users/list')); ?>" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Users</span>
                                </a>
                            </li>
                            <?php endif; ?>

                        </ul>
                    </div>
                </li>
                     <?php endif; ?>
                <?php endif; ?>
                <!-- <li class="menu-section">
                    <h4 class="menu-text">Settings</h4>
                    <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                </li> -->
                
                <?php if(isset($systemRolesArray['settings'])): ?>
                     <?php if(($systemRolesArray['settings'])!=0): ?>
                <li class="menu-item menu-item-submenu  <?php echo $__env->yieldContent('settings'); ?>" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="<?php echo e(url('/admin/settings')); ?>" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M5,8.6862915 L5,5 L8.6862915,5 L11.5857864,2.10050506 L14.4852814,5 L19,5 L19,9.51471863 L21.4852814,12 L19,14.4852814 L19,19 L14.4852814,19 L11.5857864,21.8994949 L8.6862915,19 L5,19 L5,15.3137085 L1.6862915,12 L5,8.6862915 Z M12,15 C13.6568542,15 15,13.6568542 15,12 C15,10.3431458 13.6568542,9 12,9 C10.3431458,9 9,10.3431458 9,12 C9,13.6568542 10.3431458,15 12,15 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <span class="menu-text">Settings</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
                
            </ul>
        </div>
    </div>

</div>

<?php /**PATH C:\xampp\htdocs\seo_engine\resources\views/admin/layout/base/_aside.blade.php ENDPATH**/ ?>