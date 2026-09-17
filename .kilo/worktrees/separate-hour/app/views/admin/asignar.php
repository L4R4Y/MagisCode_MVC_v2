<?php
$pageTitle = 'Asignar cursos';
$pageSubtitle = 'Asigna cursos a los aprendices registrados.';
require __DIR__ . '/../partials/header.php';
?>
<?php if (!empty($error)): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if (isset($_GET['ok'])): ?><div class="success">Curso asignado correctamente.</div><?php endif; ?>

<div class="panel">
    <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

    <form method="post" class="form-grid">
        <div class="field">
            <label>Curso <span class="req">*</span></label>
            <select name="curso" required>
                <?php foreach ($c as $x): ?>
                    <option value="<?= $x['id_curso'] ?>"><?= htmlspecialchars($x['titulo_curso']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label>Aprendiz <span class="req">*</span></label>
            <select name="aprendiz" required>
                <?php foreach ($apr as $x): ?>
                    <option value="<?= $x['id_usuario'] ?>"><?= htmlspecialchars($x['nombre'] . ' ' . $x['apellido'] . ' (' . $x['username'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field full">
            <button class="btn btn-primary">Asignar curso</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
