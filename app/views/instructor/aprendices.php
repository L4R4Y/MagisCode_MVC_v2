<?php
$pageTitle = 'Aprendices';
$pageSubtitle = 'Consulta y realiza seguimiento al progreso de tus aprendices.';
require __DIR__ . '/../partials/header.php';
?>

<?php if (!$curso): ?>
    <div class="empty">Aún no tienes cursos creados. Crea un curso para poder gestionar sus aprendices.</div>
<?php else: ?>
    <div class="toolbar">
        <div>
            <h2>Aprendices</h2>
            <p>Realiza seguimiento al progreso de los aprendices de tus cursos.</p>
        </div>
        <form method="get">
            <input type="hidden" name="route" value="instructor-aprendices">
            <select name="id" class="field-select" onchange="this.form.submit()" aria-label="Seleccionar curso">
                <?php foreach ($cursos as $c): ?>
                    <option value="<?= (int)$c['id_curso'] ?>" <?= (int)$c['id_curso'] === (int)$curso['id_curso'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['titulo_curso']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="toolbar" style="margin-bottom:0;">
            <div>
                <h2><?= htmlspecialchars($curso['titulo_curso']) ?></h2>
                <p><?= htmlspecialchars($curso['descripcion_curso'] ?? '') ?></p>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <span class="badge"><?= count($apr) ?> aprendiz<?= count($apr) === 1 ? '' : 'es' ?></span>
                <button type="button" class="btn btn-primary" onclick="abrirModalAgregar()">+ Agregar aprendices</button>
            </div>
        </div>
    </div>

    <!-- Modal para agregar aprendices -->
    <div id="modal-agregar" class="modal" style="display:none;" role="dialog" aria-labelledby="modal-titulo" aria-modal="true">
        <div class="modal-content" style="max-width:600px;">
            <div class="modal-header">
                <h3 id="modal-titulo">Agregar aprendices al curso</h3>
                <button type="button" class="modal-close" onclick="cerrarModalAgregar()" aria-label="Cerrar">&times;</button>
            </div>
            <form method="post" action="index.php?route=agregar-aprendices">
                <input type="hidden" name="curso_id" value="<?= (int)$curso['id_curso'] ?>">
                <div class="modal-body" style="max-height:300px; overflow-y:auto;">
                    <?php if (!$disponibles): ?>
                        <p class="empty">No hay aprendices disponibles para agregar.</p>
                    <?php else: ?>
                        <div class="checkbox-list">
                            <?php foreach ($disponibles as $a): ?>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="aprendiz_ids[]" value="<?= (int)$a['id_usuario'] ?>">
                                    <span>
                                        <strong><?= htmlspecialchars(($a['nombre'] ?? '') . ' ' . ($a['apellido'] ?? '')) ?></strong><br>
                                        <small><?= htmlspecialchars($a['correo'] ?? '') ?> | @<?= htmlspecialchars($a['username'] ?? '') ?></small>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:10px; margin-top:15px; padding-top:15px; border-top:1px solid #e1e9f1;">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalAgregar()">Cancelar</button>
                    <button type="submit" class="btn btn-primary" <?= !$disponibles ? 'disabled' : '' ?>>Agregar seleccionados</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-overlay" class="modal-overlay" style="display:none;" onclick="cerrarModalAgregar()"></div>

    <?php if (!$apr): ?>
        <div class="empty">Este curso todavía no tiene aprendices asignados.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Aprendiz</th>
                        <th>Correo</th>
                        <th>Progreso</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($apr as $x): ?>
                    <?php $avance = max(0, min(100, (float)($x['avance'] ?? 0))); ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars(($x['nombre'] ?? '') . ' ' . ($x['apellido'] ?? '')) ?></strong><br>
                            <small><?= htmlspecialchars($x['username'] ?? '') ?></small>
                        </td>
                        <td><?= htmlspecialchars($x['correo'] ?? '') ?></td>
                        <td style="min-width:220px;">
                            <div class="progress"><span style="width:<?= $avance ?>%"></span></div>
                            <small><?= rtrim(rtrim(number_format($avance, 1, '.', ''), '0'), '.') ?>%</small>
                        </td>
                        <td>
                            <span class="badge <?= (($x['estado_c_a'] ?? '') === 'Activo' ? '' : 'gray') ?>">
                                <?= htmlspecialchars($x['estado_c_a'] ?? '') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
function abrirModalAgregar() {
    document.getElementById('modal-agregar').style.display = 'block';
    document.getElementById('modal-overlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function cerrarModalAgregar() {
    document.getElementById('modal-agregar').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalAgregar();
    }
});
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
