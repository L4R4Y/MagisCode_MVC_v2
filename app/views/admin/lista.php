<?php
$pageTitle = $titulo;
$pageSubtitle = $subtitulo;
require __DIR__ . '/../partials/header.php';
?>

<div class="grid">
    <?php foreach ($items as $x): ?>
        <div class="card">
            <h3><?= htmlspecialchars($x['nombre'] . ' ' . $x['apellido']) ?></h3>
            <p><?= htmlspecialchars($x['correo'] ?? '') ?></p>
            <span class="badge">Activo</span>
        </div>
    <?php endforeach; ?>

    <?php if (!$items): ?>
        <div class="empty">No hay registros.</div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
