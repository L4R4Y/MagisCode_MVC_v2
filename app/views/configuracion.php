<?php
$pageTitle = 'Configuración';
$pageSubtitle = 'Administra la seguridad y la información de tu cuenta.';
require __DIR__ . '/partials/header.php';

$rolId = (int) ($_SESSION['rol_id'] ?? 0);
$usuario = $usuario ?? [];
?>
<?php if (!empty($error)): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if (!empty($ok)): ?><div class="success"><?= htmlspecialchars($ok) ?></div><?php endif; ?>

<div class="grid">
    <div class="panel">
        <h2>Seguridad</h2>
        <p>Cambia tu contraseña de acceso a MagisCode.</p>
        <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

        <form method="post" action="index.php?route=cambiar-password">
            <div class="field">
                <label>Contraseña actual <span class="req">*</span></label>
                <input type="password" name="actual">
            </div>
            <div class="field">
                <label>Nueva contraseña <span class="req">*</span></label>
                <input type="password" name="nueva">
            </div>
            <div class="field">
                <label>Confirmar contraseña <span class="req">*</span></label>
                <input type="password" name="confirmar">
            </div>
            <br>
            <button class="btn btn-primary">Actualizar contraseña</button>
        </form>
    </div>

    <?php if ($rolId === 3): ?>
        <!-- APRENDIZ: puede corregir sus datos de contacto, pero no todos los campos del perfil. -->
        <div class="panel">
            <h2>Mis datos</h2>
            <p>Actualiza tu nombre, correo o celular si ya no te sirven o si están mal escritos.</p>
            <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

            <form method="post" action="index.php?route=actualizar-perfil" class="form-grid">
                <div class="field">
                    <label>Nombre <span class="req">*</span></label>
                     <input name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Apellido <span class="req">*</span></label>
                     <input name="apellido" value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Correo <span class="req">*</span></label>
                     <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Celular</label>
                    <input name="celular" value="<?= htmlspecialchars($usuario['celular'] ?? '') ?>" placeholder="Ej: 3001234567">
                </div>

                <div class="field">
                    <label>Documento</label>
                    <input value="<?= htmlspecialchars($usuario['id_usuario'] ?? '') ?>" readonly disabled>
                    <span class="field-hint">No se puede modificar. Contacta a un administrador si necesitas cambiarlo.</span>
                </div>
                <div class="field">
                    <label>Usuario</label>
                    <input value="<?= htmlspecialchars($usuario['username'] ?? '') ?>" readonly disabled>
                    <span class="field-hint">No se puede modificar.</span>
                </div>

                <div class="field full">
                    <button class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="panel">
            <h2>Cuenta</h2>
            <div class="list">
                <div class="list-item">
                    <strong>Nombre</strong><br>
                    <?= htmlspecialchars($_SESSION['nombre']) ?>
                </div>
                <div class="list-item">
                    <strong>Usuario</strong><br>
                    <?= htmlspecialchars($_SESSION['username'] ?? '') ?>
                </div>
                <div class="list-item">
                    <strong>Rol</strong><br>
                    <?= htmlspecialchars($_SESSION['rol']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
