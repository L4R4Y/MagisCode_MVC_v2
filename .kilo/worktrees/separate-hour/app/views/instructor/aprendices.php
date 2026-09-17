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
            <span class="badge"><?= count($apr) ?> aprendiz<?= count($apr) === 1 ? '' : 'es' ?></span>
        </div>
    </div>

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

<?php require __DIR__ . '/../partials/footer.php'; ?>
