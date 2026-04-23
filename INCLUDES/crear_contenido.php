<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id'])) exit('Acceso denegado');
?>

<div class="card shadow border-0" style="background:rgba(255,255,255,0.9);backdrop-filter:blur(10px);">
<div class="card-body p-4">

    <!-- TABS -->
    <div style="display:flex;gap:1rem;margin-bottom:1.8rem;border-bottom:2px solid #e0eef8;padding-bottom:1rem;">
        <button id="tab-btn-articulo" onclick="switchTabCrear('articulo')"
                style="background:var(--ocean,#0077b6);color:#fff;border:none;border-radius:50px;
                       padding:8px 22px;font-weight:700;cursor:pointer;">
            Articulo
        </button>
        <button id="tab-btn-video" onclick="switchTabCrear('video')"
                style="background:rgba(0,0,0,0.07);color:#333;border:none;border-radius:50px;
                       padding:8px 22px;font-weight:700;cursor:pointer;">
            Video
        </button>
    </div>

    <!-- ══════════════ ARTÍCULO ══════════════ -->
    <div id="tab-articulo">
        <h3 class="mb-4" style="color:var(--navy,#03045e);font-weight:800;">Crear Nuevo Artículo</h3>
        <div id="alerta-articulo" class="alert d-none"></div>

        <form id="form-crear-contenido">
            <div class="mb-3">
                <label class="form-label fw-bold">Título</label>
                <input type="text" name="titulo" class="form-control"
                       placeholder="Ej: El impacto del microplástico" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Categoría</label>
                    <select name="category" class="form-select">
                        <option value="peces">Peces</option>
                        <option value="mamiferos">Mamíferos</option>
                        <option value="moluscos">Moluscos</option>
                        <option value="crustaceos">Crustáceos</option>
                        <option value="noticias">Noticias</option>
                        <option value="conservacion">Conservación</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">URL de Imagen (opcional)</label>
                    <input type="text" name="imagen" class="form-control"
                           placeholder="https://ejemplo.com/imagen.jpg">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Contenido</label>
                <textarea name="contenido" class="form-control" rows="8"
                          placeholder="Escribe aquí toda la información..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary px-4"
                    style="background:var(--ocean,#0077b6);border:none;">
                Publicar Ahora
            </button>
        </form>
    </div>

    <!-- ══════════════ VIDEO ══════════════ -->
    <div id="tab-video" style="display:none;">
        <h3 class="mb-4" style="color:var(--navy,#03045e);font-weight:800;">Publicar Video</h3>
        <div id="alerta-video" class="alert d-none"></div>

        <form id="form-crear-video" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label fw-bold">Título</label>
                <input type="text" name="titulo" class="form-control"
                       placeholder="Ej: Vida en el arrecife de coral" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Categoría</label>
                    <select name="categoria" class="form-select">
                        <option value="peces">Peces</option>
                        <option value="mamiferos">Mamíferos</option>
                        <option value="moluscos">Moluscos</option>
                        <option value="crustaceos">Crustáceos</option>
                        <option value="conservacion">Conservación</option>
                        <option value="general">General</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fuente del Video</label>
                    <select name="fuente" id="select-fuente" class="form-select"
                            onchange="toggleFuente(this.value)">
                        <option value="youtube">URL de YouTube</option>
                        <option value="local">Subir archivo (MP4 / WebM)</option>
                    </select>
                </div>
            </div>

            <!-- URL YouTube -->
            <div id="campo-youtube" class="mb-3">
                <label class="form-label fw-bold">URL de YouTube</label>
                <input type="url" name="video_url" class="form-control"
                       placeholder="https://www.youtube.com/watch?v=...">
                <div class="form-text">Pega el enlace completo de YouTube o youtu.be</div>
            </div>

            <!-- Archivo local -->
            <div id="campo-local" class="mb-3" style="display:none;">
                <label class="form-label fw-bold">Archivo de Video</label>
                <input type="file" name="video_file" class="form-control"
                       accept="video/mp4,video/webm,video/quicktime,video/x-msvideo">
                <div class="form-text">Máximo 200 MB · Formatos: MP4, WebM, MOV, AVI</div>

                <!-- Barra de progreso -->
                <div id="upload-progress" style="display:none;margin-top:.8rem;">
                    <div style="background:#e0eef8;border-radius:8px;height:10px;overflow:hidden;">
                        <div id="upload-bar"
                             style="height:100%;width:0%;background:var(--ocean,#0077b6);transition:width .3s;"></div>
                    </div>
                    <small id="upload-pct" style="color:#555;">0%</small>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Descripción (opcional)</label>
                <textarea name="descripcion" class="form-control" rows="4"
                          placeholder="Breve descripción del video..."></textarea>
            </div>

            <button type="submit" id="btn-publicar-video" class="btn btn-primary px-4"
                    style="background:var(--ocean,#0077b6);border:none;">
                Publicar Video
            </button>
        </form>
    </div>

</div><!-- /card-body -->
</div><!-- /card -->

<script>
/* ── Cambiar tab ─────────────────────────────────────────── */
function switchTabCrear(tab) {
    const isArt = tab === 'articulo';
    document.getElementById('tab-articulo').style.display = isArt ? 'block' : 'none';
    document.getElementById('tab-video').style.display    = isArt ? 'none'  : 'block';

    const bA = document.getElementById('tab-btn-articulo');
    const bV = document.getElementById('tab-btn-video');
    bA.style.background = isArt ? 'var(--ocean,#0077b6)' : 'rgba(0,0,0,0.07)';
    bA.style.color      = isArt ? '#fff' : '#333';
    bV.style.background = isArt ? 'rgba(0,0,0,0.07)' : 'var(--ocean,#0077b6)';
    bV.style.color      = isArt ? '#333' : '#fff';
}

/* ── Mostrar campo según fuente ──────────────────────────── */
function toggleFuente(val) {
    document.getElementById('campo-youtube').style.display = val === 'youtube' ? 'block' : 'none';
    document.getElementById('campo-local').style.display   = val === 'local'   ? 'block' : 'none';
}

/* ── Helper: mostrar alerta ──────────────────────────────── */
function mostrarAlerta(idAlerta, ok, texto) {
    const el = document.getElementById(idAlerta);
    el.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
    el.innerText = texto;
    el.style.display = 'block';
}

/* ── ENVÍO: Artículo ─────────────────────────────────────── */
document.getElementById('form-crear-contenido').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('categoria', fd.get('category')); // el PHP espera 'categoria'

    fetch('../database/procesar_crear_contenido.php', { method:'POST', body:fd })
    .then(r => r.text())
    .then(data => {
        if (data.trim() === 'success') {
            mostrarAlerta('alerta-articulo', true, '¡Artículo publicado con éxito!');
            this.reset();
        } else {
            mostrarAlerta('alerta-articulo', false, 'Error al publicar: ' + data);
        }
    })
    .catch(() => mostrarAlerta('alerta-articulo', false, 'Error de conexión.'));
});

/* ── ENVÍO: Video ────────────────────────────────────────── */
document.getElementById('form-crear-video').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn    = document.getElementById('btn-publicar-video');
    const fd     = new FormData(this);
    fd.append('accion', 'crear_video');
    const fuente = document.getElementById('select-fuente').value;

    btn.disabled    = true;
    btn.innerText   = 'Publicando...';

    // ── Archivo local → XHR con barra de progreso ────────
    if (fuente === 'local') {
        const xhr      = new XMLHttpRequest();
        const bar      = document.getElementById('upload-bar');
        const pct      = document.getElementById('upload-pct');
        const progress = document.getElementById('upload-progress');

        progress.style.display = 'block';

        xhr.upload.addEventListener('progress', ev => {
            if (ev.lengthComputable) {
                const p = Math.round((ev.loaded / ev.total) * 100);
                bar.style.width = p + '%';
                pct.textContent = p + '%';
            }
        });

        xhr.open('POST', '../database/procesar_video.php');
        xhr.onload = function() {
            progress.style.display = 'none';
            procesarRespuestaVideo(xhr.responseText);
            btn.disabled  = false;
            btn.innerText = 'Publicar Video';
        };
        xhr.onerror = function() {
            mostrarAlerta('alerta-video', false, 'Error de red al subir el video.');
            btn.disabled  = false;
            btn.innerText = 'Publicar Video';
        };
        xhr.send(fd);

    // ── YouTube → fetch normal ────────────────────────────
    } else {
        fetch('../database/procesar_video.php', { method:'POST', body:fd })
        .then(r => r.text())
        .then(data => procesarRespuestaVideo(data))
        .catch(() => mostrarAlerta('alerta-video', false, 'Error de conexión.'))
        .finally(() => {
            btn.disabled  = false;
            btn.innerText = 'Publicar Video';
        });
    }
});

/* ── Procesar respuesta JSON de procesar_video.php ───────── */
function procesarRespuestaVideo(raw) {
    try {
        const res = JSON.parse(raw);
        if (res.ok) {
            mostrarAlerta('alerta-video', true, res.mensaje);
            document.getElementById('form-crear-video').reset();
            toggleFuente('youtube');
        } else {
            mostrarAlerta('alerta-video', false, res.mensaje);
        }
    } catch(e) {
        // Si no es JSON válido, mostrar el texto crudo (útil para depuración)
        mostrarAlerta('alerta-video', false, 'Respuesta inesperada del servidor: ' + raw.substring(0, 200));
    }
}
</script>