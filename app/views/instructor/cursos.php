<?php $pageTitle='Mis cursos';$pageSubtitle='Administra y gestiona los cursos que tienes a tu cargo.';require __DIR__.'/../partials/header.php'; ?>
<div class="toolbar">
    <div>
        <h2>Mis cursos</h2>
        <p>Gestiona cursos, contenido y aprendices.</p>
    </div>
    <a class="btn btn-primary" href="index.php?route=crear-curso">+ Nuevo curso</a>
</div>

<div class="grid">
    <?php foreach($c as $x): ?>
    <div class="card course-card">
        <div class="course-image">
            <?php if (!empty($x['ruta_imagen'])): ?>
                <img src="<?=htmlspecialchars($x['ruta_imagen'])?>" alt="<?=htmlspecialchars($x['titulo_curso'])?>">
            <?php else: ?>
                <?=htmlspecialchars($x['titulo_curso'])?>
            <?php endif; ?>
        </div>
        <h3><?=htmlspecialchars($x['titulo_curso'])?></h3>
        <p><?=htmlspecialchars($x['descripcion_curso'])?></p>
        <div class="meta">
            <span><?=$x['aprendices']?> aprendices</span>
            <span><?=$x['modulos']?> módulos</span>
            <span><?=htmlspecialchars($x['estado_curso'])?></span>
        </div>
        <div class="actions">
            <a class="btn btn-primary" href="index.php?route=curso&id=<?=$x['id_curso']?>">Administrar</a>
            <a class="btn btn-secondary" href="index.php?route=instructor-aprendices&id=<?=$x['id_curso']?>">Aprendices</a>
            <?php if ($x['estado_curso'] === 'Activo'): ?>
                <form method="post" action="index.php?route=cambiar-estado-curso">
                    <input type="hidden" name="id" value="<?=$x['id_curso']?>">
                    <input type="hidden" name="estado" value="Inactivo">
                    <button class="btn btn-secondary" type="submit">Inactivar</button>
                </form>
            <?php else: ?>
                <form method="post" action="index.php?route=cambiar-estado-curso">
                    <input type="hidden" name="id" value="<?=$x['id_curso']?>">
                    <input type="hidden" name="estado" value="Activo">
                    <button class="btn btn-secondary" type="submit">Activar</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(!$c): ?>
        <div class="empty">Aún no tienes cursos. Crea el primero.</div>
    <?php endif; ?>
</div>
<?php require __DIR__.'/../partials/footer.php'; ?>
