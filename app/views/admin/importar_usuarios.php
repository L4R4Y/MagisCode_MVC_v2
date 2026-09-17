<?php
$pageTitle = 'Crear usuarios masivamente';
$pageSubtitle = 'Importa usuarios desde un archivo CSV y crea cuentas en segundos.';
require __DIR__ . '/../partials/header.php';
?>
<?php if (!empty($error)): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="panel">
    <h2>Cargar archivo CSV</h2>
    <p class="form-note">
        <strong>Columnas admitidas:</strong> nombre, apellido, correo, username (o usuario),
        rol, tipo_documento (opcional), documento (opcional), estado (opcional).
        <br>
        <strong>Formato:</strong> archivo CSV con encabezado, separador <code>;</code> o <code>,</code>.
        Tamaño máximo: 2 MB.
    </p>

    <form method="post" enctype="multipart/form-data" class="form-grid">
        <div class="field full">
            <label>Archivo CSV <span class="req">*</span></label>
            <input type="file" name="archivo" accept=".csv,text/csv" required>
        </div>

        <div class="field full">
            <button class="btn btn-primary">Importar usuarios</button>
            <a class="btn btn-secondary" href="index.php?route=usuarios">Volver a usuarios</a>
        </div>
    </form>
</div>

<div class="panel">
    <h2>Ejemplo de archivo CSV</h2>
    <p class="form-note">Puedes descargar este ejemplo, rellenarlo y subirlo.</p>

    <pre style="background:#f8f9fa;border:1px solid #d4dee9;border-radius:9px;padding:16px;overflow-x:auto;font-size:13px;line-height:1.6"><?php echo htmlspecialchars("nombre,apellido,correo,username,rol,tipo_documento,documento,estado
Carlos,Ramirez,carlos.ramirez@correo.com,cramirez,Aprendiz,CC,1000000021,Activo
Ana, Lopez,ana.lopez@correo.com,alopez,Instructor,PASAPORTE,1000000022,Activo
Luis,Martinez,luis.mtz@gmail.com,lmtz,Administrador,CC,1000000023,Activo"); ?></pre>

    <p class="form-note">
        <strong>Rol:</strong> Administrador, Instructor o Aprendiz (o su ID: 1, 2, 3).
        <br>
        <strong>tipo_documento:</strong> CC, CE, TI o PASAPORTE (o su ID: 1, 2, 3, 4).
        <br>
        <strong>estado:</strong> Activo, Inactivo o Bloqueado (o su ID: 1, 2, 3).
        <br>
        <strong>documento:</strong> Opcional. Si se omite, se asigna un ID secuencial automáticamente.
    </p>
</div>

<?php if ($resultado !== null): ?>
    <div class="panel">
        <h2>Resultado de la importación</h2>

        <div class="form-note">
            Total de filas procesadas: <strong><?= count($resultado['errores']) + $resultado['creados'] ?></strong>
            &middot; Creados: <strong><?= $resultado['creados'] ?></strong>
            &middot; Errores: <strong><?= count($resultado['errores']) ?></strong>
        </div>

        <?php if (!empty($resultado['errores'])): ?>
            <ul style="color:#c0392b;padding-left:20px">
                <?php foreach ($resultado['errores'] as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <p class="form-note">
            Las credenciales de todos los usuarios creados se registraron en
            <code>correo_debug.log</code>.
        </p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
