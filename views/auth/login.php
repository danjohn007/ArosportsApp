<div class="container-fluid vh-100">
    <div class="row justify-content-center align-items-center h-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-trophy text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mt-2"><?= APP_NAME ?></h2>
                        <p class="text-muted">Iniciar Sesión</p>
                    </div>

                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/login">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       placeholder="correo@ejemplo.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       placeholder="Contraseña">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <small class="text-muted">
                            <strong>Credenciales de prueba:</strong><br>
                            Admin: admin@arosports.com / password<br>
                            Cliente: cliente@demo.com / password
                        </small>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="<?= BASE_URL ?>/test-db" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-database-check"></i> Probar Conexión DB
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>