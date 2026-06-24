<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SahabatCuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }

        .login-card {
            margin-top: 100px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card login-card p-4">
                    <div class="card-body">
                        <h3 class="text-center mb-4 fw-bold text-primary">SahabatCuan</h3>
                        <p class="text-center text-muted mb-4">Silakan masuk ke akun Anda</p>

                        <!-- Pesan Error jika login gagal -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger p-2 small">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="/auth/login" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="admin@gmail.com" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="******" required>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary py-2">Masuk</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <small class="text-muted small">Admin: <b>admin@gmail.com</b> | pw: <b>admin123</b></small>
                </div>
            </div>
        </div>
    </div>

</body>

</html>