<?php
$pageTitle = 'Editar usuario';
$pageSubtitle = 'Actualiza el correo y el rol del usuario.';
require __DIR__ . '/../partials/header.php';
?>
<?php if (!empty($error)): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="panel">
        <p class="form-note">
        Documento: <strong><?= htmlspecialchars($u['id_usuario']) ?></strong> &middot;
        Usuario: <strong><?= htmlspecialchars($u['username']) ?></strong>
    </p>

    <form method="post" class="form-grid">
        <div class="field full">
            <label>Documento</label>
            <input value="<?= htmlspecialchars($u['id_usuario']) ?>" readonly disabled>
        </div>

        <div class="field">
            <label>Nombre <span class="req">*</span></label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']) ?>">
        </div>

        <div class="field">
            <label>Apellido <span class="req">*</span></label>
            <input type="text" name="apellido" value="<?= htmlspecialchars($u['apellido']) ?>">
        </div>

        <div class="field full">
            <label>Usuario</label>
            <input value="<?= htmlspecialchars($u['username']) ?>" readonly disabled>
            <span class="field-hint">No se puede modificar.</span>
        </div>

        <div class="field">
            <label>Correo <span class="req">*</span></label>
            <input type="email" name="correo" value="<?= htmlspecialchars($u['correo']) ?>">
        </div>

        <div class="field">
            <label>Rol <span class="req">*</span></label>
            <select name="rol">
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id_rol'] ?>" <?= (int) $u['id_rol_u'] === (int) $r['id_rol'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre_rol']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field full">
            <button class="btn btn-primary">Guardar cambios</button>
            <a class="btn btn-secondary" href="index.php?route=usuarios">Cancelar</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
