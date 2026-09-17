<?php
$pageTitle = 'Nuevo usuario';
$pageSubtitle = 'Crea una nueva cuenta para la plataforma.';
require __DIR__ . '/../partials/header.php';
?>
        <?php if (!empty($error)): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="panel">
            <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

            <form method="post" class="form-grid">
                <div class="field">
                    <label>Documento <span class="req">*</span></label>
                    <input name="id_usuario" value="<?= htmlspecialchars($d['id_usuario'] ?? '') ?>">
                </div>

                <div class="field">
                    <label>Tipo de documento <span class="req">*</span></label>
                    <select name="tipo_documento">
                        <?php foreach ($tiposDocumento as $t): ?>
                            <option value="<?= $t['id_tipo_documento'] ?>" <?= ($d['tipo_documento'] ?? 1) == $t['id_tipo_documento'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nombre_tipo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Nombre <span class="req">*</span></label>
                    <input name="nombre" value="<?= htmlspecialchars($d['nombre'] ?? '') ?>">
                </div>

                <div class="field">
                    <label>Apellido <span class="req">*</span></label>
                    <input name="apellido" value="<?= htmlspecialchars($d['apellido'] ?? '') ?>">
                </div>

                <div class="field">
                    <label>Correo <span class="req">*</span></label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($d['correo'] ?? '') ?>">
                </div>

                <div class="field">
                    <label>Rol <span class="req">*</span></label>
                    <select name="rol">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id_rol'] ?>" <?= ($d['rol'] ?? 3) == $r['id_rol'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre_rol']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field full">
                    <button class="btn btn-primary">Crear usuario</button>
                </div>
            </form>
        </div>
        <?php require __DIR__ . '/../partials/footer.php'; ?>
