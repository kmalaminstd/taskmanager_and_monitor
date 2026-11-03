<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Login'); ?> - TASK MASTER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;
            --sidebar: #0F172A;
        }
        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            width: 100%;
            max-width: 420px;
        }
        .login-header {
            background: var(--primary);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background-color: #1D4ED8;
            border-color: #1D4ED8;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <?php echo $__env->yieldContent('content'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/layouts/auth.blade.php ENDPATH**/ ?>