<?php
$pageTitle = $curso['titulo_curso'];
$pageSubtitle = $curso['descripcion_curso'];
require __DIR__ . '/partials/header.php';
?>
<div class="toolbar">
    <div>
        <h2>Contenido del curso</h2>
        <p>Instructor: <?= htmlspecialchars($curso['instructor']) ?></p>
        <?php if (!empty($curso['duracion_total']) && (int) $curso['duracion_total'] > 0): ?>
            <span class="duracion-curso-titulo">
                ⏱ <?= VideoInfo::formatearDuracion((int) $curso['duracion_total']) ?>
            </span>
        <?php endif; ?>
        <?php if ($miAvance !== null): ?>
            <div class="progreso-cursos">
                <div class="progress-bar">
                    <div class="progress-fill" style="width:<?= $miAvance ?>%"></div>
                    <div class="progress-fill-videos" style="width:<?= $progresoVideos * 0.9 ?>%"></div>
                </div>
                <span class="progreso-text">
                    <?= $miAvance ?>% completado · <?= $progresoVideos ?>% videos / 10% evaluaciones
                </span>
            </div>
        <?php endif; ?>
    </div>
    <div class="toolbar-duracion">
        <?php if ((int) $_SESSION['rol_id'] === 2): ?>
            <a class="btn btn-primary" href="index.php?route=crear-evaluacion&curso=<?= $curso['id_curso'] ?>">+ Evaluación</a>
        <?php endif; ?>
    </div>
</div>

<div class="grid">
    <div class="panel" style="grid-column:1/-1">
        <h2>Módulos</h2>
        <?php foreach ($modulos as $m): ?>
            <div class="card module">
                <h3><?= htmlspecialchars($m['nombre_modulo']) ?></h3>
                <?php foreach ($m['lecciones'] as $l): ?>
                    <div class="lesson">
                        <strong><?= htmlspecialchars($l['titulo_leccion']) ?></strong>

                        <?php if (!empty($l['recursos'])): ?>
                            <div class="lesson-recursos">
                                <?php foreach ($l['recursos'] as $r): ?>
    <?php $visto = in_array((int) $r['id_recurso'], $recursosVistos, true); ?>
    <?php if ($r['tipo_recurso'] === 'MP4'): ?>
        <div class="video-recurso">
            <div class="video-header">
                <span class="recurso-icon">▶</span>
                <span class="recurso-nombre"><?= htmlspecialchars($r['nombre_recurso']) ?></span>
                <?php if (!empty($r['duracion']) && (int) $r['duracion'] > 0): ?>
                    <span class="recurso-duracion">
                        <?= VideoInfo::formatearDuracion((int) $r['duracion']) ?>
                    </span>
                <?php endif; ?>
                <?php if ($visto): ?>
                    <span class="recurso-visto" title="Completado">✓</span>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary btn-sm video-open"
                        data-ruta="<?= htmlspecialchars($r['ruta_archivo']) ?>"
                        data-nombre="<?= htmlspecialchars($r['nombre_recurso']) ?>"
                        data-duracion="<?= (int) ($r['duracion'] ?? 0) ?>"
                        data-recurso="<?= (int) $r['id_recurso'] ?>">
                    Ver video
                </button>
            </div>
        </div>
    <?php else: ?>
        <a class="recurso-link <?php if ($visto): ?>recurso-visto-link<?php endif; ?>" target="_blank" href="<?= htmlspecialchars($r['ruta_archivo']) ?>" data-recurso="<?= (int) $r['id_recurso'] ?>">
            <span class="recurso-icon">📄</span>
            <span class="recurso-nombre"><?= htmlspecialchars($r['nombre_recurso']) ?></span>
            <span class="recurso-tipo"><?= htmlspecialchars($r['tipo_recurso']) ?></span>
            <?php if ($visto): ?>
                <span class="recurso-visto" title="Visto">✓</span>
            <?php endif; ?>
        </a>
    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="sin-recursos">Sin recursos</span>
                        <?php endif; ?>

                        <?php if ((int) $_SESSION['rol_id'] === 2): ?>
                            <form class="recurso-form" method="post" action="index.php?route=crear-recurso" enctype="multipart/form-data">
                                <input type="hidden" name="curso" value="<?= $curso['id_curso'] ?>">
                                <input type="hidden" name="leccion" value="<?= (int) $l['id_leccion'] ?>">
                                <div class="recurso-form-row">
                                    <input type="text" name="nombre" placeholder="Nombre del recurso" required class="recurso-nombre-input">
                                    <input type="file" name="archivo" accept=".pdf,.mp4" required class="recurso-archivo-input">
                                    <button type="submit" class="btn btn-secondary btn-sm">Subir</button>
                                </div>
                                <span class="field-hint">Formatos: PDF, MP4. Máx. 50 MB.</span>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if ((int) $_SESSION['rol_id'] === 2): ?>
                    <div class="actions">
                        <form method="post" action="index.php?route=crear-leccion">
                            <input type="hidden" name="curso" value="<?= $curso['id_curso'] ?>">
                            <input type="hidden" name="modulo" value="<?= $m['id_modulo'] ?>">
                            <input type="hidden" name="orden" value="<?= count($m['lecciones']) + 1 ?>">
                            <input class="search" name="titulo" placeholder="Nueva lección" required>
                            <button class="btn btn-secondary">Agregar lección</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if ((int) $_SESSION['rol_id'] === 2): ?>
            <form method="post" action="index.php?route=crear-modulo" class="actions">
                <input type="hidden" name="curso" value="<?= $curso['id_curso'] ?>">
                <input type="hidden" name="orden" value="<?= count($modulos) + 1 ?>">
                <input class="search" name="nombre" placeholder="Nuevo módulo" required>
                <button class="btn btn-primary">Agregar módulo</button>
            </form>
        <?php endif; ?>

        <?php if (!$modulos): ?>
            <div class="empty">Este curso todavía no tiene módulos.</div>
        <?php endif; ?>
    </div>

    <div class="panel" style="grid-column:1/-1">
        <h2>Evaluaciones</h2>
        <?php foreach ($evaluaciones as $e): ?>
            <?php $abierta = (int) $e['id_estado_e'] === 1; ?>
            <div class="list-item">
                <strong><?= htmlspecialchars($e['titulo_evaluacion']) ?></strong> · <?=$e['preguntas']?> preguntas · aprobación <?=$e['puntaje_aprobacion']?>
                <?php if ((int) $e['id_estado_e'] === 3): ?>
                    <span class="badge cerrada" title="Evaluación cerrada">Cerrada</span>
                <?php endif; ?>
                <?php if ((int) $_SESSION['rol_id'] === 2): ?>
                    <?php if ($abierta): ?>
                        <a class="btn btn-secondary" href="index.php?route=crear-pregunta&evaluacion=<?=$e['id_evaluacion']?>">Agregar pregunta</a>
                        <form method="post" action="index.php?route=cerrar-evaluacion" style="display:inline">
                            <input type="hidden" name="evaluacion" value="<?=$e['id_evaluacion']?>">
                            <input type="hidden" name="curso" value="<?= $curso['id_curso'] ?>">
                            <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('¿Cerrar esta evaluación? No se podrán agregar más preguntas.')">Cerrar</button>
                        </form>
                    <?php else: ?>
                        <span class="badge cerrada">Cerrada</span>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($progresoVideos >= 90): ?>
                        <a class="btn btn-primary" href="index.php?route=evaluacion&id=<?=$e['id_evaluacion']?>">Presentar</a>
                    <?php else: ?>
                        <span class="badge" style="background:#f1c40f;color:#10213b">Requiere 90% de videos</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if ((int) $_SESSION['rol_id'] === 3 && empty($evaluaciones)): ?>
            <div class="empty">No hay una evaluación creada para ser evaluado y certificado.</div>
        <?php elseif ((int) $_SESSION['rol_id'] !== 3 && !$evaluaciones): ?>
            <div class="empty">No hay evaluaciones disponibles.</div>
        <?php elseif ((int) $_SESSION['rol_id'] === 3): ?>
            <?php if ($progresoVideos < 90): ?>
                <div class="empty">Debes ver el 90% de los videos antes de poder presentar las evaluaciones.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div id="video-modal" class="vm-modal" style="display:none;">
    <div class="vm-modal-content">
        <div class="vm-modal-header">
            <h3 id="vm-titulo"></h3>
            <span id="vm-duracion" class="duracion-total"></span>
            <button type="button" class="vm-close" id="vm-close" aria-label="Cerrar">&times;</button>
        </div>
        <div class="vm-modal-body">
            <video id="vm-player" class="vm-video" controls preload="metadata" controlsList="nodownload">
                <source id="vm-source" src="" type="video/mp4">
                Tu navegador no soporta la etiqueta de video.
            </video>
        </div>
    </div>
    <div class="vm-overlay"></div>
</div>

<script>
(function() {
    var modal = document.getElementById('video-modal');
    var player = document.getElementById('vm-player');
    var source = document.getElementById('vm-source');
    var titulo = document.getElementById('vm-titulo');
    var duracion = document.getElementById('vm-duracion');
    var cursoId = <?= (int) $curso['id_curso'] ?>;
    var vistoIds = <?= json_encode(array_values($recursosVistos ?? [])) ?>;
    var totalRecursos = <?= $totalRecursos ?? 0 ?>;
    var totalVideos = <?= $totalVideos ?? 0 ?>;
    var markadoRecurso = null;

    function abrirModal(ruta, nombre, seg, recursoId) {
        source.src = ruta;
        player.load();
        titulo.textContent = nombre;
        if (seg && parseInt(seg) > 0) {
            var h = Math.floor(seg / 3600);
            var m = Math.floor((seg % 3600) / 60);
            var s = seg % 60;
            var txt = h > 0 ? h + 'h ' + m + 'm ' + s + 's' : (m > 0 ? m + 'm ' + s + 's' : s + 's');
            duracion.textContent = '⏱ ' + txt;
            duracion.style.display = 'inline-block';
        } else {
            duracion.style.display = 'none';
        }
        markadoRecurso = recursoId ? parseInt(recursoId) : null;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        player.focus();
        var playPromise = player.play();
        if (playPromise !== undefined && playPromise.catch) {
            playPromise.catch(function() {});
        }
    }

    function cerrarModal() {
        player.pause();
        source.src = '';
        player.load();
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function marcarComoVisto() {
        if (!markadoRecurso || vistoIds.indexOf(markadoRecurso) !== -1) {
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'index.php?route=marcar-video-visto&recurso=' + markadoRecurso + '&curso=' + cursoId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                vistoIds.push(markadoRecurso);
                var btn = document.querySelector('.video-open[data-recurso="' + markadoRecurso + '"]');
                if (btn) {
                    var header = btn.closest('.video-header');
                    if (header && !header.querySelector('.recurso-visto')) {
                        var span = document.createElement('span');
                        span.className = 'recurso-visto';
                        span.title = 'Completado';
                        span.textContent = '✓';
                        header.insertBefore(span, btn);
                    }
                }
                var link = document.querySelector('.recurso-link[data-recurso="' + markadoRecurso + '"]');
                if (link && !link.querySelector('.recurso-visto')) {
                    var span = document.createElement('span');
                    span.className = 'recurso-visto';
                    span.title = 'Visto';
                    span.textContent = '✓';
                    link.appendChild(span);
                }
                var progressBar = document.querySelector('.progress-fill');
                var progresoText = document.querySelector('.progreso-text');
                if (progressBar && totalRecursos > 0) {
                    var nuevo = Math.round((vistoIds.length / totalRecursos) * 100);
                    progressBar.style.width = nuevo + '%';
                    if (progresoText) {
                        progresoText.textContent = nuevo + '% completado';
                    }
                }
            }
        };
        xhr.send();
    }

    function marcarLinkVisto(link) {
        var recursoId = parseInt(link.getAttribute('data-recurso'));
        if (!recursoId || vistoIds.indexOf(recursoId) !== -1) {
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'index.php?route=marcar-video-visto&recurso=' + recursoId + '&curso=' + cursoId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                vistoIds.push(recursoId);
                if (!link.querySelector('.recurso-visto')) {
                    var span = document.createElement('span');
                    span.className = 'recurso-visto';
                    span.title = 'Visto';
                    span.textContent = '✓';
                    link.appendChild(span);
                }
                var progressBar = document.querySelector('.progress-fill');
                var progresoText = document.querySelector('.progreso-text');
                if (progressBar && totalRecursos > 0) {
                    var nuevo = Math.round((vistoIds.length / totalRecursos) * 100);
                    progressBar.style.width = nuevo + '%';
                    if (progresoText) {
                        progresoText.textContent = nuevo + '% completado';
                    }
                }
            }
        };
        xhr.send();
    }

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.video-open');
        if (btn) {
            e.preventDefault();
            abrirModal(
                btn.getAttribute('data-ruta'),
                btn.getAttribute('data-nombre'),
                btn.getAttribute('data-duracion'),
                btn.getAttribute('data-recurso')
            );
        }

        var link = e.target.closest('.recurso-link');
        if (link) {
            setTimeout(function() {
                marcarLinkVisto(link);
            }, 100);
        }
    });

    player.addEventListener('ended', marcarComoVisto);

    document.getElementById('vm-close').addEventListener('click', cerrarModal);
    document.querySelector('.vm-overlay').addEventListener('click', cerrarModal);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModal();
        }
    });
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
