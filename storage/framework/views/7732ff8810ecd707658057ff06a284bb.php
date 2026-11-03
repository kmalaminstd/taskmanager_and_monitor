<?php $__env->startSection('title', 'Sign In | Task Master'); ?>

<?php $__env->startSection('content'); ?>

<div class="auth-wrapper">

    
    <div class="auth-showcase">

        <div class="showcase-content">

            <a href="#" class="brand-logo">
                <span class="brand-icon">
                    <i class="bi bi-check2-square"></i>
                </span>
                <span>TASK<span class="brand-highlight">MASTER</span></span>
            </a>

            <div class="showcase-heading">
                <span class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    WORK SMARTER. ACHIEVE MORE.
                </span>

                <h1>
                    Take control of
                    <span>your team's work.</span>
                </h1>

                <p>
                    Plan projects, manage tasks, monitor progress,
                    and keep your entire team moving forward —
                    all from one powerful workspace.
                </p>
            </div>

            
            <div class="preview-dashboard">

                <div class="preview-topbar">
                    <div>
                        <span class="preview-label">WORKSPACE OVERVIEW</span>
                        <h5>Project Dashboard</h5>
                    </div>

                    <span class="preview-live">
                        <span></span> Live
                    </span>
                </div>

                <div class="preview-stats">

                    <div class="preview-stat">
                        <div class="stat-icon blue">
                            <i class="bi bi-list-task"></i>
                        </div>
                        <div>
                            <span>Total Tasks</span>
                            <h3>128</h3>
                        </div>
                    </div>

                    <div class="preview-stat">
                        <div class="stat-icon green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <span>Completed</span>
                            <h3>86</h3>
                        </div>
                    </div>

                    <div class="preview-stat">
                        <div class="stat-icon orange">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <span>In Progress</span>
                            <h3>32</h3>
                        </div>
                    </div>

                </div>

                <div class="preview-project">

                    <div class="project-heading">
                        <div>
                            <span>PROJECT PROGRESS</span>
                            <h6>Website Development</h6>
                        </div>
                        <strong>78%</strong>
                    </div>

                    <div class="progress preview-progress">
                        <div class="progress-bar" style="width: 78%"></div>
                    </div>

                    <div class="project-footer">
                        <span><i class="bi bi-calendar3"></i> Due Nov 20</span>
                        <span><i class="bi bi-people"></i> 6 Members</span>
                    </div>

                </div>

                <div class="preview-activity">

                    <div class="activity-heading">
                        <h6>Recent Activity</h6>
                        <span>Team Updates</span>
                    </div>

                    <div class="activity-item">
                        <div class="activity-avatar avatar-blue">R</div>
                        <div class="activity-info">
                            <p><strong>Rahim</strong> completed a task</p>
                            <span>UI Design Implementation</span>
                        </div>
                        <span class="activity-time">2m ago</span>
                    </div>

                    <div class="activity-item">
                        <div class="activity-avatar avatar-purple">S</div>
                        <div class="activity-info">
                            <p><strong>Sarah</strong> updated a project</p>
                            <span>Website Development</span>
                        </div>
                        <span class="activity-time">15m ago</span>
                    </div>

                </div>

            </div>

            <div class="showcase-footer">
                <div class="footer-avatars">
                    <span>A</span>
                    <span>R</span>
                    <span>S</span>
                    <span>M</span>
                </div>
                <p>One workspace. One team. Better results.</p>
            </div>

        </div>

        <div class="showcase-decoration decoration-one"></div>
        <div class="showcase-decoration decoration-two"></div>

    </div>


    
    <div class="auth-form-section">

        <div class="auth-form-container">

            <div class="mobile-brand">
                <span class="brand-icon">
                    <i class="bi bi-check2-square"></i>
                </span>
                <span>TASK<span class="brand-highlight">MASTER</span></span>
            </div>

            <div class="login-intro">

                <span class="welcome-badge">
                    <i class="bi bi-shield-lock"></i>
                    SECURE ACCESS
                </span>

                <h2>Welcome back!</h2>

                <p>Sign in to continue managing your workspace.</p>

            </div>


            
            <?php if(session('error')): ?>
                <div class="alert alert-danger auth-alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            
            <?php if($errors->any()): ?>
                <div class="alert alert-danger auth-alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    Please check your login credentials and try again.
                </div>
            <?php endif; ?>


            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                
                <div class="form-group-custom">

                    <label for="email">Email Address</label>

                    <div class="input-wrapper">
                        <i class="bi bi-envelope"></i>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control-custom <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('email')); ?>"
                            placeholder="name@company.com"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>

                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="field-error"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                </div>


                
                <div class="form-group-custom">

                    <div class="password-label">
                        <label for="password">Password</label>
                    </div>

                    <div class="input-wrapper">
                        <i class="bi bi-lock"></i>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control-custom <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                            aria-label="Show password"
                        >
                            <i class="bi bi-eye"></i>
                        </button>

                    </div>

                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="field-error"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                </div>


                
                <div class="login-options">

                    <label class="remember-option">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        <span class="custom-checkbox"></span>
                        <span>Remember me</span>
                    </label>

                </div>


                
                <button type="submit" class="login-button">

                    <span>Sign In to Workspace</span>

                    <i class="bi bi-arrow-right"></i>

                </button>

            </form>


            
            <div class="security-note">
                <i class="bi bi-shield-check"></i>
                <span>Your workspace is protected with secure authentication.</span>
            </div>


            
            <?php if(app()->environment('local')): ?>
                <div class="demo-accounts">

                    <div class="demo-heading">
                        <i class="bi bi-info-circle"></i>
                        Development Demo Accounts
                    </div>

                    <div class="demo-account">
                        <span>Admin</span>
                        <code>admin@taskmaster.test</code>
                    </div>

                    <div class="demo-account">
                        <span>Supervisor</span>
                        <code>supervisor@taskmaster.test</code>
                    </div>

                    <div class="demo-account">
                        <span>Assignee</span>
                        <code>rahim@taskmaster.test</code>
                    </div>

                    <small>Password: password</small>

                </div>
            <?php endif; ?>


            <div class="auth-copyright">
                &copy; <?php echo e(date('Y')); ?> Task Master. All rights reserved.
            </div>

        </div>

    </div>

</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {

        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');

        toggleButton.addEventListener('click', function () {

            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            this.innerHTML = isPassword
                ? '<i class="bi bi-eye-slash"></i>'
                : '<i class="bi bi-eye"></i>';

            this.setAttribute(
                'aria-label',
                isPassword ? 'Hide password' : 'Show password'
            );

        });

    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/auth/login.blade.php ENDPATH**/ ?>