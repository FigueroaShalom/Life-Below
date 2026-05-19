
<!-- includes/login.php — HYDRON Auth v2 -->
<style>
/* ── RESET / IMPORTS ── */
.os-form-title, .os-brand-title { text-decoration:none !important; border-bottom:none !important; }
.os-card * { box-sizing:border-box; }
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900;1000&display=swap');

/* ── TOAST SYSTEM ── */
#os-toast-container {
    position: fixed; top: 1.2rem; right: 1.2rem;
    z-index: 99999; display: flex; flex-direction: column; gap: .6rem;
    pointer-events: none;
}
.os-toast {
    font-family: 'Nunito', sans-serif; font-size: .88rem; font-weight: 700;
    padding: .75rem 1.2rem; border-radius: 14px; max-width: 340px;
    display: flex; align-items: center; gap: .65rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.45);
    pointer-events: auto;
    animation: toastIn .35s cubic-bezier(.22,1,.36,1) both;
}
.os-toast.hide { animation: toastOut .3s ease forwards; }
@keyframes toastIn  { from { opacity:0; transform:translateX(60px) scale(.93); } to { opacity:1; transform:translateX(0) scale(1); } }
@keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(60px); } }
.os-toast.success { background:rgba(0,30,50,0.97); border:1.5px solid rgba(0,200,120,.5); color:#00e676; }
.os-toast.error   { background:rgba(0,30,50,0.97); border:1.5px solid rgba(255,80,80,.4);  color:#ff6b6b; }
.os-toast.warning { background:rgba(0,30,50,0.97); border:1.5px solid rgba(255,170,0,.4);  color:#ffaa00; }
.os-toast.info    { background:rgba(0,30,50,0.97); border:1.5px solid rgba(0,200,230,.3);  color:#00d4e8; }

/* ── FONDO ── */
.os-bg {
    position: fixed; inset: 0; z-index: 0;
    background:
        radial-gradient(ellipse at 0%   0%,   #003d66 0%, transparent 55%),
        radial-gradient(ellipse at 100% 0%,   #006b7a 0%, transparent 50%),
        radial-gradient(ellipse at 100% 100%, #004455 0%, transparent 55%),
        radial-gradient(ellipse at 0%   100%, #002244 0%, transparent 50%),
        #001e33;
}

/* ── WRAPPER ── */
.os-wrapper {
    position: relative; z-index: 10;
    min-height: calc(100vh - 90px);
    display: flex; align-items: center; justify-content: center;
    padding: 2rem;
    animation: pageFadeIn .5s ease both;
}
@keyframes pageFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }

/* ── CARD ── */
.os-card {
    display: grid; grid-template-columns: 1fr 1.15fr;
    width: min(900px, 96vw); min-height: 520px;
    border-radius: 24px; overflow: hidden;
    box-shadow: 0 50px 120px rgba(0,0,0,0.6), 0 0 0 1px rgba(0,200,220,0.12);
    animation: fadeUp .7s cubic-bezier(.22,1,.36,1) both;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(32px) scale(.97); } to { opacity:1; transform:translateY(0) scale(1); } }

/* ── BRAND ── */
.os-brand {
    background: linear-gradient(150deg, #0077a8 0%, #009fb5 45%, #00c4a0 100%);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; padding: 3rem 2.5rem; gap: 1.1rem;
    position: relative; overflow: hidden;
}
.os-brand::after  { content:''; position:absolute; bottom:-60px; right:-60px; width:260px; height:260px; border-radius:50%; background:rgba(255,255,255,0.08); }
.os-brand::before { content:''; position:absolute; top:-40px; left:-40px;   width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,0.06); }
.os-turtle { width:80px; height:80px; animation:tFloat 3.5s ease-in-out infinite; filter:drop-shadow(0 4px 14px rgba(0,0,0,0.25)); position:relative; z-index:1; display:block; margin:0 auto; }
@keyframes tFloat { 0%,100% { transform:translateY(0) rotate(-3deg); } 50% { transform:translateY(-9px) rotate(3deg); } }
.t-eye   { animation:blink 4.5s ease-in-out infinite; transform-box:fill-box; transform-origin:center; }
.t-eye-r { animation-delay:.1s; }
@keyframes blink { 0%,44%,48%,100% { transform:scaleY(1); } 46% { transform:scaleY(0.06); } }
.os-brand-body  { position:relative; z-index:1; display:flex; flex-direction:column; align-items:center; text-align:center; width:100%; }
.os-brand-tag   { display:inline-block; background:rgba(255,255,255,0.2); color:#fff; font-family:'Nunito',sans-serif; font-weight:700; font-size:.72rem; letter-spacing:2.5px; text-transform:uppercase; padding:5px 14px; border-radius:50px; margin-bottom:1rem; }
.os-brand-title { font-family:'Nunito',sans-serif; font-weight:900; font-size:2.4rem; color:#fff; line-height:1.1; margin-bottom:.8rem; text-shadow:0 2px 16px rgba(0,0,0,0.15); }
.os-brand-desc  { font-family:'Nunito',sans-serif; font-weight:400; font-size:.92rem; color:rgba(255,255,255,0.88); line-height:1.65; margin-bottom:2rem; }
.os-brand-btn   { display:inline-block; padding:12px 30px; border:2.5px solid rgba(255,255,255,0.85); border-radius:50px; color:#fff; font-family:'Nunito',sans-serif; font-weight:800; font-size:.9rem; text-decoration:none; transition:background .25s,box-shadow .25s; }
.os-brand-btn:hover { background:rgba(255,255,255,0.18); box-shadow:0 0 24px rgba(255,255,255,0.15); }
.os-brand-foot  { font-family:'Nunito',sans-serif; font-size:.75rem; color:rgba(255,255,255,0.55); position:relative; z-index:1; }

/* ── FORM PANEL ── */
.os-form-panel {
    background: #0a1628;
    display: flex; flex-direction: column; justify-content: center;
    padding: 3.2rem 3rem;
    border-left: 1px solid rgba(0,180,200,0.12);
}
.os-form-title { font-family:'Nunito',sans-serif; font-weight:900; font-size:2.2rem; color:#fff; letter-spacing:-.5px; margin-bottom:.3rem; }
.os-form-sub   { font-family:'Nunito',sans-serif; font-size:.75rem; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:rgba(100,180,210,0.55); margin-bottom:2rem; }

/* grupos + inputs */
.os-group { margin-bottom: 1.1rem; }
.os-input-wrap { position:relative; }
.os-input-wrap .os-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:rgba(80,150,190,0.5); pointer-events:none; transition:color .2s; }
.os-input-wrap:focus-within .os-icon { color:#00d4e8; }
.os-input {
    width:100%; background:rgba(255,255,255,0.04);
    border:1.5px solid rgba(0,160,200,0.2); border-radius:12px;
    padding:13px 44px 13px 42px;
    color:#e4f4ff; font-family:'Nunito',sans-serif; font-size:.96rem; font-weight:500;
    outline:none; transition:border-color .25s,box-shadow .25s,background .25s;
}
.os-input::placeholder { color:rgba(100,160,200,0.32); font-weight:400; }
.os-input:focus { border-color:rgba(0,200,220,0.7); background:rgba(0,40,80,0.45); box-shadow:0 0 0 3px rgba(0,180,220,0.12); }
.os-input.input-error { border-color:rgba(255,80,80,0.6); box-shadow:0 0 0 3px rgba(255,80,80,0.1); }
.os-input.input-ok    { border-color:rgba(0,200,120,0.5); }

/* toggle contraseña */
.os-eye-btn {
    position:absolute; right:13px; top:50%; transform:translateY(-50%);
    background:none; border:none; cursor:pointer; padding:4px;
    color:rgba(80,150,190,0.5); transition:color .2s;
    display:flex; align-items:center;
}
.os-eye-btn:hover { color:#00d4e8; }

/* checkbox recordarme + enlace olvidaste */
.os-row-meta {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom: 1rem;
}
.os-check-label {
    display:flex; align-items:center; gap:.55rem;
    font-family:'Nunito',sans-serif; font-size:.84rem;
    color:rgba(140,190,215,0.75); cursor:pointer; user-select:none;
}
.os-check-label input[type=checkbox] { display:none; }
.os-checkmark {
    width:18px; height:18px; border-radius:6px;
    border:1.5px solid rgba(0,160,200,0.35);
    background:rgba(255,255,255,0.04);
    display:flex; align-items:center; justify-content:center;
    transition:background .2s, border-color .2s;
    flex-shrink:0;
}
.os-check-label input:checked + .os-checkmark {
    background:linear-gradient(135deg,#0088c8,#00b8cc);
    border-color:transparent;
}
.os-check-label input:checked + .os-checkmark::after {
    content:''; width:5px; height:9px;
    border:2px solid #fff; border-top:none; border-left:none;
    transform:rotate(45deg) translate(-1px,-1px);
    display:block;
}
.os-forgot {
    font-family:'Nunito',sans-serif; font-size:.83rem; font-weight:700;
    color:rgba(0,212,232,0.7); text-decoration:none;
    transition:color .2s; cursor:pointer; background:none; border:none;
}
.os-forgot:hover { color:#fff; }

/* botón principal */
.os-btn {
    width:100%; padding:14px; margin-top:.4rem;
    background:linear-gradient(135deg,#0088c8,#00b8cc);
    border:none; outline:none; border-radius:12px; color:#fff;
    font-family:'Nunito',sans-serif; font-weight:900; font-size:1rem;
    letter-spacing:2.5px; text-transform:uppercase;
    cursor:pointer; -webkit-appearance:none; appearance:none;
    transition:transform .2s,box-shadow .2s,opacity .2s;
    box-shadow:0 4px 20px rgba(0,160,200,0.3);
    display:flex; align-items:center; justify-content:center; gap:.6rem;
    position:relative; overflow:hidden;
}
.os-btn:hover  { transform:translateY(-2px); box-shadow:0 8px 32px rgba(0,170,210,0.5); }
.os-btn:active { transform:translateY(0); }
.os-btn:disabled { opacity:.6; cursor:not-allowed; transform:none; }

/* spinner */
.os-spinner {
    width:18px; height:18px;
    border:2.5px solid rgba(255,255,255,0.3);
    border-top-color:#fff; border-radius:50%;
    animation:spin .7s linear infinite; flex-shrink:0;
}
@keyframes spin { to { transform:rotate(360deg); } }

/* separador */
.os-divider {
    display:flex; align-items:center; gap:1rem;
    margin: 1.2rem 0;
}
.os-divider::before, .os-divider::after { content:''; flex:1; height:1px; background:rgba(0,160,200,0.15); }
.os-divider span { font-family:'Nunito',sans-serif; font-size:.75rem; font-weight:600; color:rgba(100,160,200,0.4); letter-spacing:1px; text-transform:uppercase; white-space:nowrap; }

/* botón Google */
.os-btn-google {
    width:100%; padding:12px 14px;
    background:rgba(255,255,255,0.05);
    border:1.5px solid rgba(0,160,200,0.2);
    border-radius:12px; color:#e4f4ff;
    font-family:'Nunito',sans-serif; font-weight:800; font-size:.9rem;
    cursor:pointer; transition:background .2s,border-color .2s,transform .2s;
    display:flex; align-items:center; justify-content:center; gap:.7rem;
}
.os-btn-google:hover { background:rgba(255,255,255,0.1); border-color:rgba(0,200,220,0.4); transform:translateY(-1px); }
.os-btn-google svg { flex-shrink:0; }

/* pie del form */
.os-form-foot { text-align:center; margin-top:1.4rem; font-family:'Nunito',sans-serif; font-size:.84rem; color:rgba(130,185,210,0.55); }
.os-form-foot a { color:#00d4e8; text-decoration:none; font-weight:800; transition:color .2s; }
.os-form-foot a:hover { color:#fff; }

/* bloqueo de intentos */
.os-lockout-msg { font-family:'Nunito',sans-serif; font-size:.84rem; text-align:center; padding:.6rem 1rem; border-radius:10px; margin-bottom:1rem; }

/* ── MODAL RECUPERACIÓN ── */
.os-modal-overlay {
    position:fixed; inset:0; z-index:5000;
    background:rgba(0,10,25,0.82);
    display:flex; align-items:center; justify-content:center;
    padding:1.5rem;
    opacity:0; pointer-events:none;
    transition:opacity .3s;
}
.os-modal-overlay.open { opacity:1; pointer-events:auto; }
.os-modal {
    background:#0a1628; border:1px solid rgba(0,180,200,0.18);
    border-radius:20px; padding:2.5rem 2.2rem; width:min(440px,100%);
    box-shadow:0 40px 100px rgba(0,0,0,0.6);
    transform:translateY(24px) scale(.96);
    transition:transform .35s cubic-bezier(.22,1,.36,1);
}
.os-modal-overlay.open .os-modal { transform:translateY(0) scale(1); }
.os-modal h3 { font-family:'Nunito',sans-serif; font-weight:900; font-size:1.55rem; color:#fff; margin-bottom:.3rem; }
.os-modal p  { font-family:'Nunito',sans-serif; font-size:.88rem; color:rgba(130,185,210,0.7); margin-bottom:1.4rem; line-height:1.55; }
.os-modal .os-group { margin-bottom:1rem; }
#fpMsg { font-family:'Nunito',sans-serif; font-size:.86rem; min-height:20px; border-radius:10px; padding:5px 12px; transition:all .3s; margin-bottom:8px; }
.os-modal-btns { display:flex; gap:.8rem; margin-top:.3rem; }
.os-modal-btns .os-btn { margin:0; flex:1; letter-spacing:1.5px; }
.os-btn-outline {
    flex:1; padding:13px; background:transparent;
    border:1.5px solid rgba(0,160,200,0.3); border-radius:12px;
    color:rgba(100,180,210,0.7); font-family:'Nunito',sans-serif;
    font-weight:800; font-size:.9rem; cursor:pointer;
    transition:border-color .2s,color .2s;
}
.os-btn-outline:hover { border-color:rgba(0,200,220,0.5); color:#fff; }

/* ── RESPONSIVE ── */
@media (max-width:640px) {
    .os-card { grid-template-columns:1fr; }
    .os-brand { padding:2rem 1.8rem; min-height:200px; }
    .os-brand-title { font-size:1.7rem; }
    .os-form-panel { padding:2.2rem 1.8rem; }
    .os-form-title { font-size:1.7rem; }
}
</style>

<!-- TOAST CONTAINER -->
<div id="os-toast-container"></div>

<div class="os-bg"></div>

<div class="os-wrapper">
<div class="os-card">

    <!-- ── BRAND ── -->
    <div class="os-brand">
        <div class="os-turtle">
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="50" cy="53" rx="28" ry="22" fill="rgba(0,0,0,0.15)"/>
                <ellipse cx="50" cy="50" rx="28" ry="22" fill="#0d6e40"/>
                <circle  cx="50" cy="50" r="15" fill="#1a9a58" opacity="0.45"/>
                <circle  cx="50" cy="50" r="8"  fill="#22c46e" opacity="0.3"/>
                <line x1="50" y1="28" x2="50" y2="72" stroke="#0a5230" stroke-width="1.2" opacity="0.55"/>
                <line x1="22" y1="50" x2="78" y2="50" stroke="#0a5230" stroke-width="1.2" opacity="0.55"/>
                <line x1="30" y1="32" x2="70" y2="68" stroke="#0a5230" stroke-width=".8" opacity="0.35"/>
                <line x1="70" y1="32" x2="30" y2="68" stroke="#0a5230" stroke-width=".8" opacity="0.35"/>
                <ellipse cx="42" cy="40" rx="10" ry="6" fill="rgba(255,255,255,0.1)" transform="rotate(-20,42,40)"/>
                <ellipse cx="24" cy="42" rx="7" ry="4" fill="#0d6e40" transform="rotate(-30,24,42)"/>
                <ellipse cx="76" cy="42" rx="7" ry="4" fill="#0d6e40" transform="rotate(30,76,42)"/>
                <ellipse cx="27" cy="63" rx="6" ry="3.5" fill="#0d6e40" transform="rotate(25,27,63)"/>
                <ellipse cx="73" cy="63" rx="6" ry="3.5" fill="#0d6e40" transform="rotate(-25,73,63)"/>
                <ellipse cx="50" cy="24" rx="11" ry="9" fill="#1a9a58"/>
                <ellipse cx="47" cy="20" rx="4"  ry="3" fill="rgba(255,255,255,0.14)" transform="rotate(-10,47,20)"/>
                <g class="t-eye">
                    <circle cx="44" cy="22" r="4"   fill="#001508"/>
                    <circle cx="44" cy="22" r="2.5" fill="#00ff88" opacity=".9"/>
                    <circle cx="44" cy="22" r="1.2" fill="#001508"/>
                    <circle cx="43" cy="21" r=".6"  fill="white"/>
                </g>
                <g class="t-eye t-eye-r">
                    <circle cx="56" cy="22" r="4"   fill="#001508"/>
                    <circle cx="56" cy="22" r="2.5" fill="#00ff88" opacity=".9"/>
                    <circle cx="56" cy="22" r="1.2" fill="#001508"/>
                    <circle cx="55" cy="21" r=".6"  fill="white"/>
                </g>
                <path d="M45 29 Q50 33 55 29" stroke="#094d2a" stroke-width="1.3" fill="none" stroke-linecap="round"/>
                <ellipse cx="50" cy="72" rx="4" ry="3" fill="#0d6e40"/>
            </svg>
        </div>
        <div class="os-brand-body">
            <div class="os-brand-tag">Life Below     · Comunidad Marina</div>
            <div class="os-brand-title">¡Bienvenido<br>de vuelta!!</div>
            <div class="os-brand-desc">
               Nos alegra verte otra vez<br>
                Sigue explorando las maravillas del mar
            </div>
            <a href="?section=registro" class="os-brand-btn">Registrarse →</a>
        </div>
        <div class="os-brand-foot">© <?php echo date('Y'); ?> Life Below</div>
    </div>

    <!-- ── FORM ── -->
    <div class="os-form-panel">
        <div class="os-form-title">Iniciar Sesión</div>
        <div class="os-form-sub">Usuario o correo electrónico</div>

        <?php
        /* ── Bloqueo de intentos ── */
        $bloqueado = false;
        $segundosRestantes = 0;
        if (!empty($_SESSION['login_bloqueado_hasta'])) {
            $diff = $_SESSION['login_bloqueado_hasta'] - time();
            if ($diff > 0) {
                $bloqueado = true;
                $segundosRestantes = $diff;
            } else {
                unset($_SESSION['login_intentos'], $_SESSION['login_bloqueado_hasta']);
            }
        }
        ?>

        <?php if ($bloqueado): ?>
        <div class="os-lockout-msg" style="background:rgba(255,60,60,.12);color:#ff6b6b;border:1.5px solid rgba(255,60,60,.3);">
            🔒 Demasiados intentos fallidos. Espera <span id="lockCountdown"><?php echo $segundosRestantes; ?></span>s para intentar de nuevo.
        </div>
        <?php endif; ?>

        <form id="loginForm" autocomplete="on">
            <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <!-- Identifier -->
            <div class="os-group">
                <div class="os-input-wrap">
                    <svg class="os-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                    <input class="os-input" type="text" id="identifier" name="identifier"
                           placeholder="Usuario o correo electrónico"
                           autocomplete="username" required
                           <?php if ($bloqueado) echo 'disabled'; ?>>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="os-group">
                <div class="os-input-wrap">
                    <svg class="os-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input class="os-input" type="password" id="password" name="password"
                           placeholder="Contraseña"
                           autocomplete="current-password" required
                           <?php if ($bloqueado) echo 'disabled'; ?>>
                    <button type="button" class="os-eye-btn" id="eyeBtn" aria-label="Mostrar contraseña">
                        <svg id="eyeIcon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Recordarme + Olvidé contraseña -->
            <div class="os-row-meta">
                <label class="os-check-label">
                    <input type="checkbox" id="rememberMe">
                    <span class="os-checkmark"></span>
                    Recordarme
                </label>
                <button type="button" class="os-forgot" id="openForgot">¿Olvidaste tu contraseña?</button>
            </div>

            <button type="submit" class="os-btn" id="loginBtn"
                    <?php if ($bloqueado) echo 'disabled'; ?>>
                <span id="loginBtnText">Ingresar</span>
            </button>
        </form>

        <!-- Separador -->
        <div class="os-divider"><span>o continúa con</span></div>

        <!-- Google -->
        <button class="os-btn-google" id="googleLoginBtn" type="button">
            <svg width="20" height="20" viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.2l6.7-6.7C35.7 2.4 30.2 0 24 0 14.8 0 6.9 5.4 3 13.3l7.8 6C12.7 13 17.9 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.2-.4-4.7H24v9h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8C43.5 37.4 46.5 31.4 46.5 24.5z"/>
                <path fill="#FBBC05" d="M10.8 28.7A14.7 14.7 0 0 1 9.5 24c0-1.6.3-3.2.8-4.7l-7.8-6A23.9 23.9 0 0 0 0 24c0 3.8.9 7.4 2.5 10.6l8.3-5.9z"/>
                <path fill="#34A853" d="M24 48c6.2 0 11.4-2 15.2-5.5l-7.5-5.8c-2 1.4-4.6 2.2-7.7 2.2-6.1 0-11.3-3.5-13.2-8.8l-8.3 5.9C6.9 42.6 14.8 48 24 48z"/>
                <path fill="none" d="M0 0h48v48H0z"/>
            </svg>
            Iniciar sesión con Google
        </button>

        <div class="os-form-foot">
            ¿No tienes cuenta? <a href="?section=registro">Regístrate aquí</a>
        </div>
    </div>

</div>
</div>

<!-- ── MODAL RECUPERAR CONTRASEÑA ── -->
<div class="os-modal-overlay" id="fpModal" role="dialog" aria-modal="true" aria-label="Recuperar contraseña">
    <div class="os-modal" id="fpModalBox">

        <!-- Paso 1: ingresar correo -->
        <div id="fpStep1">
            <h3>🔑 Recuperar contraseña</h3>
            <p>Ingresa tu correo y te enviaremos un código de verificación.</p>
            <div id="fpMsg"></div>
            <div class="os-group">
                <div class="os-input-wrap">
                    <svg class="os-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/>
                    </svg>
                    <input class="os-input" type="email" id="fpEmail" placeholder="tu@correo.com" autocomplete="email">
                </div>
            </div>
            <div class="os-modal-btns">
                <button type="button" class="os-btn-outline" id="closeFpModal">Cancelar</button>
                <button type="button" class="os-btn" id="fpSendBtn">
                    <span id="fpSendText">Enviar código</span>
                </button>
            </div>
        </div>

        <!-- Paso 2: ingresar código -->
        <div id="fpStep2" style="display:none">
            <h3>📩 Revisa tu correo</h3>
            <p>Ingresa el código de 6 dígitos que enviamos a <strong id="fpEmailDisplay" style="color:#00d4e8;"></strong></p>
            <div id="fpCodeMsg"></div>
            <div class="os-group">
                <div class="os-input-wrap">
                    <svg class="os-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="9" y="2" width="6" height="10" rx="1"/><path d="M5 10h14v10a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"/>
                    </svg>
                    <input class="os-input" type="text" id="fpCode" placeholder="123456"
                           maxlength="6" pattern="\d{6}" inputmode="numeric"
                           style="letter-spacing:6px;font-size:1.2rem;text-align:center;padding-left:14px;">
                </div>
            </div>
            <div class="os-modal-btns">
                <button type="button" class="os-btn-outline" id="fpBackToStep1">← Volver</button>
                <button type="button" class="os-btn" id="fpVerifyBtn">
                    <span id="fpVerifyText">Verificar</span>
                </button>
            </div>
            <p style="margin-top:1rem;font-size:.78rem;color:rgba(130,185,210,0.5);text-align:center;">
                ¿No llegó? <button type="button" class="os-forgot" id="fpResendBtn">Reenviar código</button>
            </p>
        </div>

        <!-- Paso 3: nueva contraseña -->
        <div id="fpStep3" style="display:none">
            <h3>🔒 Nueva contraseña</h3>
            <p>Crea una contraseña segura para tu cuenta.</p>
            <div id="fpNewMsg"></div>
            <div class="os-group">
                <div class="os-input-wrap">
                    <svg class="os-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input class="os-input" type="password" id="fpNewPass" placeholder="Nueva contraseña">
                    <button type="button" class="os-eye-btn" id="fpEye1" aria-label="Mostrar">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <!-- Barra de fuerza -->
                <div style="margin-top:6px;">
                    <div style="height:4px;border-radius:4px;background:rgba(255,255,255,0.07);overflow:hidden;">
                        <div id="fpStrengthBar" style="height:100%;width:0%;border-radius:4px;transition:width .3s,background .3s;"></div>
                    </div>
                    <small id="fpStrengthLabel" style="font-family:'Nunito',sans-serif;font-size:.74rem;margin-top:4px;display:block;min-height:16px;font-weight:600;"></small>
                </div>
            </div>
            <div class="os-group">
                <div class="os-input-wrap">
                    <svg class="os-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input class="os-input" type="password" id="fpConfirmPass" placeholder="Confirmar contraseña">
                    <button type="button" class="os-eye-btn" id="fpEye2" aria-label="Mostrar">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <small id="fpConfirmMsg" style="font-family:'Nunito',sans-serif;font-size:.74rem;margin-top:4px;font-weight:600;display:block;min-height:16px;"></small>
            </div>
            <div class="os-modal-btns">
                <button type="button" class="os-btn" id="fpSaveBtn" style="letter-spacing:1.5px;">
                    <span id="fpSaveText">Guardar contraseña</span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
/* ══════════════════════════════════════
   TOAST HELPER
══════════════════════════════════════ */
function osToast(msg, type = 'info', duration = 3500) {
    const icons = { success:'✅', error:'❌', warning:'⚠️', info:'🌊' };
    const c = document.getElementById('os-toast-container');
    const t = document.createElement('div');
    t.className = 'os-toast ' + type;
    t.innerHTML = `<span>${icons[type]||'ℹ️'}</span><span>${msg}</span>`;
    c.appendChild(t);
    setTimeout(() => {
        t.classList.add('hide');
        t.addEventListener('animationend', () => t.remove());
    }, duration);
}

/* ══════════════════════════════════════
   SPINNER HELPER
══════════════════════════════════════ */
function setLoading(btn, textEl, loading, label = '') {
    if (loading) {
        btn.disabled = true;
        textEl.innerHTML = '<span class="os-spinner"></span>' + (label ? `<span>${label}</span>` : '');
    } else {
        btn.disabled = false;
        textEl.textContent = label;
    }
}

/* ══════════════════════════════════════
   TOGGLE CONTRASEÑA
══════════════════════════════════════ */
function makeEyeToggle(btnId, inputId) {
    const btn = document.getElementById(btnId);
    const inp = document.getElementById(inputId);
    if (!btn || !inp) return;
    btn.addEventListener('click', () => {
        const isPass = inp.type === 'password';
        inp.type = isPass ? 'text' : 'password';
        btn.querySelector('svg').innerHTML = isPass
            ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
               <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
               <line x1="1" y1="1" x2="23" y2="23"/>`
            : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
    });
}
makeEyeToggle('eyeBtn', 'password');

/* ══════════════════════════════════════
   COUNTDOWN BLOQUEO
══════════════════════════════════════ */
(function() {
    const el = document.getElementById('lockCountdown');
    if (!el) return;
    let s = parseInt(el.textContent);
    const iv = setInterval(() => {
        s--;
        if (s <= 0) { clearInterval(iv); location.reload(); return; }
        el.textContent = s;
    }, 1000);
})();

/* ══════════════════════════════════════
   FORM LOGIN
══════════════════════════════════════ */
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const identifier = document.getElementById('identifier').value.trim();
    const password   = document.getElementById('password').value;
    const csrf       = document.getElementById('csrf_token').value;
    const remember   = document.getElementById('rememberMe').checked;
    const btn        = document.getElementById('loginBtn');
    const btnTxt     = document.getElementById('loginBtnText');

    if (!identifier || !password) { osToast('Completa todos los campos', 'warning'); return; }

    setLoading(btn, btnTxt, true, 'Conectando...');

    fetch('database/validar_login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'identifier=' + encodeURIComponent(identifier)
            + '&password='   + encodeURIComponent(password)
            + '&csrf_token=' + encodeURIComponent(csrf)
            + '&remember='   + (remember ? '1' : '0')
    })
    .then(r => r.text())
    .then(data => {
        data = data.trim();
        setLoading(btn, btnTxt, false, 'Ingresar');

        if (data === 'ok') {
            osToast('¡Bienvenido de vuelta!', 'success');
            btn.disabled = true;
            setTimeout(() => window.location.href = 'index.php?section=inicio', 1200);

        } else if (data === 'not_verified') {
            osToast('Verifica tu correo antes de iniciar sesión', 'warning', 5000);

        } else if (data === 'error_user') {
            document.getElementById('identifier').classList.add('input-error');
            osToast('Usuario o correo no encontrado', 'error');

        } else if (data === 'error_password') {
            document.getElementById('password').classList.add('input-error');
            osToast('Contraseña incorrecta', 'error');

        } else if (data.startsWith('locked:')) {
            const secs = data.split(':')[1];
            osToast('Demasiados intentos. Espera ' + secs + 's', 'error', 6000);
            setTimeout(() => location.reload(), 500);

        } else {
            osToast(data || 'Error inesperado', 'warning');
        }
    })
    .catch(() => { setLoading(btn, btnTxt, false, 'Ingresar'); osToast('Error de conexión', 'error'); });
});

// Limpiar error al retipear
['identifier','password'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', () => el.classList.remove('input-error'));
});

/* ══════════════════════════════════════
   GOOGLE LOGIN
══════════════════════════════════════ */
document.getElementById('googleLoginBtn').addEventListener('click', () => {
    window.location.href = 'auth/google_auth.php';
});

/* ══════════════════════════════════════
   MODAL RECUPERAR CONTRASEÑA
══════════════════════════════════════ */
const fpModal   = document.getElementById('fpModal');
const fpEmail   = document.getElementById('fpEmail');
let   fpToken   = '';

function openFpModal() {
    fpModal.classList.add('open');
    showFpStep(1);
    fpEmail.value = '';
    setTimeout(() => fpEmail.focus(), 350);
}
function closeFpModal() { fpModal.classList.remove('open'); }
function showFpStep(n) {
    [1,2,3].forEach(i => document.getElementById('fpStep'+i).style.display = i===n ? 'block' : 'none');
}
function fpSetMsg(elId, msg, type) {
    const el = document.getElementById(elId);
    const colors = { error:'rgba(255,60,60,.15);color:#ff6b6b;border:1px solid rgba(255,60,60,.3)', success:'rgba(0,200,100,.15);color:#00e676;border:1px solid rgba(0,200,100,.3)', warning:'rgba(255,150,0,.15);color:#ffaa00;border:1px solid rgba(255,150,0,.3)', info:'color:rgba(0,210,230,.7)' };
    el.style.cssText = 'background:' + (colors[type]||colors.info) + ';border-radius:10px;padding:6px 12px;margin-bottom:8px;font-size:.86rem;';
    el.textContent = msg;
}

document.getElementById('openForgot').addEventListener('click', openFpModal);
document.getElementById('closeFpModal').addEventListener('click', closeFpModal);
fpModal.addEventListener('click', e => { if (e.target === fpModal) closeFpModal(); });

// Paso 1: enviar código
document.getElementById('fpSendBtn').addEventListener('click', () => {
    const email = fpEmail.value.trim();
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        fpSetMsg('fpMsg', '⚠️ Ingresa un correo válido', 'warning'); return;
    }
    const btn = document.getElementById('fpSendBtn');
    const txt = document.getElementById('fpSendText');
    setLoading(btn, txt, true, 'Enviando...');

    fetch('database/recuperar_password.php', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'action=enviar_codigo&email='+encodeURIComponent(email)
    }).then(r=>r.text()).then(d=>{
        d=d.trim(); setLoading(btn, txt, false, 'Enviar código');
        if (d==='ok') {
            document.getElementById('fpEmailDisplay').textContent = email;
            showFpStep(2);
            setTimeout(()=>document.getElementById('fpCode').focus(),200);
        } else if (d==='not_found') {
            fpSetMsg('fpMsg','❌ Correo no registrado','error');
        } else { fpSetMsg('fpMsg','⚠️ '+d,'warning'); }
    }).catch(()=>{ setLoading(btn, txt, false, 'Enviar código'); fpSetMsg('fpMsg','⚠️ Error de conexión','warning'); });
});

// Volver al paso 1
document.getElementById('fpBackToStep1').addEventListener('click',()=>showFpStep(1));

// Reenviar
document.getElementById('fpResendBtn').addEventListener('click',()=>{
    document.getElementById('fpBackToStep1').click();
    document.getElementById('fpSendBtn').click();
});

// Paso 2: verificar código
document.getElementById('fpVerifyBtn').addEventListener('click',()=>{
    const email = fpEmail.value.trim();
    const code  = document.getElementById('fpCode').value.trim();
    if (code.length !== 6 || !/^\d+$/.test(code)) { fpSetMsg('fpCodeMsg','⚠️ El código son 6 dígitos','warning'); return; }
    const btn = document.getElementById('fpVerifyBtn');
    const txt = document.getElementById('fpVerifyText');
    setLoading(btn, txt, true, 'Verificando...');
    fetch('database/recuperar_password.php',{
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'action=verificar_codigo&email='+encodeURIComponent(email)+'&code='+encodeURIComponent(code)
    }).then(r=>r.text()).then(d=>{
        d=d.trim(); setLoading(btn, txt, false, 'Verificar');
        if (d.startsWith('ok:')) {
            fpToken = d.split(':')[1];
            showFpStep(3);
        } else if (d==='expired')   { fpSetMsg('fpCodeMsg','❌ Código expirado. Solicita uno nuevo','error'); }
          else if (d==='invalid')   { fpSetMsg('fpCodeMsg','❌ Código incorrecto','error'); }
          else                       { fpSetMsg('fpCodeMsg','⚠️ '+d,'warning'); }
    }).catch(()=>{ setLoading(btn, txt, false, 'Verificar'); fpSetMsg('fpCodeMsg','⚠️ Error de conexión','warning'); });
});

// Solo números en código
document.getElementById('fpCode').addEventListener('input',function(){ this.value=this.value.replace(/\D/g,''); });

/* ── Fuerza contraseña (modal) ── */
function calcStrength(p){
    let s=0;
    if(p.length>=8) s++;
    if(/[A-Z]/.test(p)) s++;
    if(/[a-z]/.test(p)) s++;
    if(/[0-9]/.test(p)) s++;
    if(/[^a-zA-Z0-9]/.test(p)) s++;
    return s;
}
document.getElementById('fpNewPass').addEventListener('input',function(){
    const s=calcStrength(this.value);
    const bar=document.getElementById('fpStrengthBar');
    const lbl=document.getElementById('fpStrengthLabel');
    const w=[0,20,40,65,85,100];
    const bc=['#ff4444','#ff6b00','#ffaa00','#7ecf00','#00e676'];
    const lb=['','Muy débil','Débil','Regular','Fuerte','Muy fuerte'];
    bar.style.width=w[s]+'%'; bar.style.background=bc[s-1]||'transparent';
    lbl.textContent=lb[s]||'';
    lbl.style.color=bc[s-1]||'transparent';
    checkConfirm();
});
document.getElementById('fpConfirmPass').addEventListener('input', checkConfirm);
makeEyeToggle('fpEye1','fpNewPass');
makeEyeToggle('fpEye2','fpConfirmPass');
function checkConfirm(){
    const p1=document.getElementById('fpNewPass').value;
    const p2=document.getElementById('fpConfirmPass').value;
    const m=document.getElementById('fpConfirmMsg');
    if(!p2){m.textContent='';return;}
    if(p1===p2){ m.style.color='#00e676'; m.textContent='✅ Contraseñas coinciden'; }
    else       { m.style.color='#ff6b6b'; m.textContent='❌ Las contraseñas no coinciden'; }
}

// Paso 3: guardar nueva contraseña
document.getElementById('fpSaveBtn').addEventListener('click',()=>{
    const p1=document.getElementById('fpNewPass').value;
    const p2=document.getElementById('fpConfirmPass').value;
    if (!p1||!p2) { fpSetMsg('fpNewMsg','⚠️ Completa ambos campos','warning'); return; }
    if (p1!==p2)  { fpSetMsg('fpNewMsg','❌ Las contraseñas no coinciden','error'); return; }
    if (calcStrength(p1)<3) { fpSetMsg('fpNewMsg','⚠️ Contraseña demasiado débil','warning'); return; }
    const btn=document.getElementById('fpSaveBtn');
    const txt=document.getElementById('fpSaveText');
    setLoading(btn,txt,true,'Guardando...');
    fetch('database/recuperar_password.php',{
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'action=nueva_password&token='+encodeURIComponent(fpToken)+'&password='+encodeURIComponent(p1)
    }).then(r=>r.text()).then(d=>{
        d=d.trim(); setLoading(btn,txt,false,'Guardar contraseña');
        if(d==='ok'){
            osToast('✅ ¡Contraseña actualizada! Inicia sesión','success',4000);
            closeFpModal();
        } else if(d==='expired'){ fpSetMsg('fpNewMsg','❌ Token expirado. Inicia el proceso de nuevo','error'); }
          else { fpSetMsg('fpNewMsg','⚠️ '+d,'warning'); }
    }).catch(()=>{ setLoading(btn,txt,false,'Guardar contraseña'); fpSetMsg('fpNewMsg','⚠️ Error de conexión','warning'); });
});

/* ══════════════════════════════════════
   TRANSICIÓN DE SALIDA SUAVE
══════════════════════════════════════ */
document.querySelectorAll('a[href*="section=registro"], a[href*="section=login"]').forEach(function(link){
    link.addEventListener('click', function(e){
        if (fpModal.classList.contains('open')) return;
        e.preventDefault();
        var href = this.href;
        var overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;z-index:9999;background:linear-gradient(135deg,#005f9e,#009aaa);opacity:0;transition:opacity 0.4s ease,transform 0.4s ease;transform:translateY(100%);pointer-events:none;';
        document.body.appendChild(overlay);
        requestAnimationFrame(function(){
            overlay.style.opacity='1'; overlay.style.transform='translateY(0)';
        });
        setTimeout(function(){ window.location.href=href; }, 420);
    });
});
</script>