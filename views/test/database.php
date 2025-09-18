<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-database-check"></i> Test de Conexión a la Base de Datos
                    </h4>
                </div>
                <div class="card-body">
                    <!-- URL Base -->
                    <div class="mb-4">
                        <h5><i class="bi bi-globe"></i> Configuración del Sistema</h5>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>URL Base:</strong></td>
                                    <td><code><?= htmlspecialchars($results['base_url']) ?></code></td>
                                    <td><span class="badge bg-success">OK</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Host DB:</strong></td>
                                    <td><code><?= htmlspecialchars($results['db_config']['host']) ?></code></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><strong>Base de Datos:</strong></td>
                                    <td><code><?= htmlspecialchars($results['db_config']['database']) ?></code></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><strong>Usuario DB:</strong></td>
                                    <td><code><?= htmlspecialchars($results['db_config']['user']) ?></code></td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Conexión a la Base de Datos -->
                    <div class="mb-4">
                        <h5><i class="bi bi-database"></i> Conexión a la Base de Datos</h5>
                        <div class="alert alert-<?= $results['connection'] ? 'success' : 'danger' ?>">
                            <i class="bi bi-<?= $results['connection'] ? 'check-circle' : 'x-circle' ?>"></i>
                            <?php if ($results['connection']): ?>
                                <strong>¡Éxito!</strong> Conexión a la base de datos establecida correctamente.
                            <?php else: ?>
                                <strong>Error:</strong> No se pudo conectar a la base de datos. Verifique la configuración.
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Estado de las Tablas -->
                    <?php if ($results['connection'] && isset($results['tables'])): ?>
                    <div class="mb-4">
                        <h5><i class="bi bi-table"></i> Estado de las Tablas</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tabla</th>
                                        <th>Estado</th>
                                        <th>Registros</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $tables = ['usuarios', 'clubes', 'fraccionamientos', 'empresas', 'reservas'];
                                    foreach ($tables as $table): 
                                        $exists = $results['tables'][$table] ?? false;
                                        $count = $results['tables'][$table . '_count'] ?? 0;
                                    ?>
                                    <tr>
                                        <td><code><?= $table ?></code></td>
                                        <td>
                                            <?php if ($exists): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check"></i> Existe
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x"></i> No existe
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($exists): ?>
                                                <span class="badge bg-info"><?= $count ?> registros</span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Error en tablas -->
                    <?php if (isset($results['tables_error'])): ?>
                    <div class="mb-4">
                        <div class="alert alert-danger">
                            <strong>Error al verificar tablas:</strong>
                            <code><?= htmlspecialchars($results['tables_error']) ?></code>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Instrucciones -->
                    <div class="mb-4">
                        <h5><i class="bi bi-info-circle"></i> Instrucciones</h5>
                        <?php if (!$results['connection']): ?>
                        <div class="alert alert-warning">
                            <h6>Para configurar la base de datos:</h6>
                            <ol>
                                <li>Asegúrese de que MySQL esté ejecutándose</li>
                                <li>Verifique las credenciales en <code>config/config.php</code></li>
                                <li>Ejecute el script SQL: <code>sql/arosports_structure.sql</code></li>
                                <li>Recargue esta página para verificar la conexión</li>
                            </ol>
                        </div>
                        <?php elseif (isset($results['tables']) && !$results['tables']['usuarios']): ?>
                        <div class="alert alert-warning">
                            <h6>Para crear las tablas:</h6>
                            <ol>
                                <li>Importe el archivo <code>sql/arosports_structure.sql</code> en su base de datos</li>
                                <li>El archivo contiene la estructura completa y datos de ejemplo</li>
                                <li>Recargue esta página para verificar las tablas</li>
                            </ol>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-success">
                            <h6>¡Sistema listo para usar!</h6>
                            <p>La base de datos está configurada correctamente. Puede acceder al sistema con las siguientes credenciales:</p>
                            <ul>
                                <li><strong>SuperAdmin:</strong> admin@arosports.com / password</li>
                                <li><strong>Cliente:</strong> cliente@demo.com / password</li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botones de navegación -->
                    <div class="text-center">
                        <?php if ($results['connection'] && isset($results['tables']) && $results['tables']['usuarios']): ?>
                        <a href="<?= BASE_URL ?>/login" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Ir al Login
                        </a>
                        <?php endif; ?>
                        <button onclick="location.reload()" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Verificar Nuevamente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>