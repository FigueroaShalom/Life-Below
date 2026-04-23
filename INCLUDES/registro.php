<!-- includes/registro.php -->
<style>
/* override global */
/* features registro */
.os-brand-features {
    display:flex; flex-direction:column; gap:.7rem;
    margin-top:.8rem; width:100%;
}
.os-feat {
    display:flex; align-items:center; gap:.8rem;
    background:rgba(255,255,255,0.1); border-radius:10px;
    padding:.6rem 1rem; text-align:left;
}
.os-feat span { font-size:1.2rem; flex-shrink:0; }
.os-feat p {
    font-family:'Nunito',sans-serif; font-size:.82rem;
    color:rgba(255,255,255,0.9); font-weight:600; margin:0;
}
/* centrar textos panel brand registro */
.os-brand .os-brand-title,
.os-brand .os-brand-desc { text-align:center; }

.os-form-title, .os-brand-title { text-decoration:none !important; border-bottom:none !important; }
.os-card * { box-sizing:border-box; }
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900;1000&display=swap');

.os-bg {
    position: fixed; inset: 0; z-index: 0;
    background:
        radial-gradient(ellipse at 100% 0%,   #003d66 0%, transparent 55%),
        radial-gradient(ellipse at 0%   0%,   #006b7a 0%, transparent 50%),
        radial-gradient(ellipse at 0%   100%, #004455 0%, transparent 55%),
        radial-gradient(ellipse at 100% 100%, #002244 0%, transparent 50%),
        #001e33;
}
.os-wrapper {
    position: relative; z-index: 10;
    min-height: calc(100vh - 90px);
    display: flex; align-items: center; justify-content: center;
    padding: 2rem;
    animation: pageFadeIn .5s ease both;
}
@keyframes pageFadeIn {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
.__os-wrapper-hidden {
    position: relative; z-index: 10;
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 2rem; margin: -2rem -2rem 0;
}
.os-card {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    width: min(900px, 96vw);
    min-height: 560px;
    border-radius: 24px; overflow: hidden;
    box-shadow: 0 50px 120px rgba(0,0,0,0.6), 0 0 0 1px rgba(0,200,220,0.12);
    animation: fadeUp .7s cubic-bezier(.22,1,.36,1) both;
}
@keyframes fadeUp {
    from { opacity:0; transform:translateY(32px) scale(.97); }
    to   { opacity:1; transform:translateY(0)    scale(1);   }
}

/* ── FORM PANEL (izquierdo en registro) ── */
.os-form-panel {
    background: #0a1628;
    display: flex; flex-direction: column; justify-content: center;
    padding: 3rem 3rem;
    border-right: 1px solid rgba(0,180,200,0.12);
}
.os-form-title {
    font-family: 'Nunito', sans-serif;
    font-weight: 900; font-size: 2.2rem;
    color: #fff; letter-spacing: -.5px; margin-bottom: .3rem;
}
.os-form-sub {
    font-family: 'Nunito', sans-serif; font-size: .75rem; font-weight: 600;
    letter-spacing: 2px; text-transform: uppercase;
    color: rgba(100,180,210,0.55); margin-bottom: 1.8rem;
}
#registroMsg {
    font-family: 'Nunito', sans-serif; font-size: .88rem;
    min-height: 22px; margin-bottom: 12px;
    border-radius: 10px; padding: 6px 14px; transition: all .3s;
}
.os-group { margin-bottom: 1rem; }
.os-group small {
    display: block; font-family: 'Nunito', sans-serif;
    font-size: .74rem; margin-top: 5px; min-height: 16px; font-weight: 600;
}
.os-input-wrap { position: relative; }
.os-input-wrap svg {
    position: absolute; left: 14px; top: 50%;
    transform: translateY(-50%);
    color: rgba(80,150,190,0.5); pointer-events: none; transition: color .2s;
}
.os-input {
    width: 100%;
    background: rgba(255,255,255,0.04);
    border: 1.5px solid rgba(0,160,200,0.2);
    border-radius: 12px;
    padding: 12px 14px 12px 42px;
    color: #e4f4ff;
    font-family: 'Nunito', sans-serif; font-size: .95rem; font-weight: 500;
    outline: none;
    transition: border-color .25s, box-shadow .25s, background .25s;
}
.os-input::placeholder { color: rgba(100,160,200,0.32); font-weight: 400; }
.os-input:focus {
    border-color: rgba(0,200,220,0.7);
    background: rgba(0,40,80,0.45);
    box-shadow: 0 0 0 3px rgba(0,180,220,0.12);
}
.os-input-wrap:focus-within svg { color: #00d4e8; }
.os-btn {
    width: 100%; padding: 14px; margin-top: .5rem;
    background: linear-gradient(135deg, #0088c8, #00b8cc);
    border: none; border-radius: 12px; color: #fff;
    font-family: 'Nunito', sans-serif;
    font-weight: 900; font-size: 1rem;
    letter-spacing: 2.5px; text-transform: uppercase; cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 4px 20px rgba(0,160,200,0.3);
}
.os-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,170,210,0.5); }
.os-btn:active { transform: translateY(0); }
.os-form-foot {
    text-align: center; margin-top: 1.4rem;
    font-family: 'Nunito', sans-serif;
    font-size: .84rem; color: rgba(130,185,210,0.55);
}
.os-form-foot a { color: #00d4e8; text-decoration: none; font-weight: 800; }
.os-form-foot a:hover { color: #fff; }

/* ── BRAND (derecho en registro) ── */
.os-brand {
    background: linear-gradient(150deg, #009fb5 0%, #00b8a0 50%, #00c48a 100%);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 3rem 2.5rem; gap: 1rem;
    position: relative; overflow: hidden;
    text-align: center;
}
.os-brand::after  { content:''; position:absolute; bottom:-60px; left:-60px;  width:260px; height:260px; border-radius:50%; background:rgba(255,255,255,0.08); }
.os-brand::before { content:''; position:absolute; top:-40px;    right:-40px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,0.06); }

.os-brand .os-turtle {
    width: 80px; height: 80px;
    animation: tFloat 3.5s ease-in-out infinite;
    filter: drop-shadow(0 4px 14px rgba(0,0,0,0.2));
    position: relative; z-index: 1;
    display: block; margin: 0 auto 0.5rem auto;
}
@keyframes tFloat {
    0%,100% { transform:translateY(0) rotate(-3deg); }
    50%      { transform:translateY(-9px) rotate(3deg); }
}
.t-eye { animation: blink 4.5s ease-in-out infinite; transform-box:fill-box; transform-origin:center; }
.t-eye-r { animation-delay:.1s; }
@keyframes blink {
    0%,44%,48%,100% { transform:scaleY(1);   }
    46%             { transform:scaleY(0.06); }
}
.os-brand-body { position: relative; z-index: 1; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; width:100%; }
.os-brand-tag  { display:inline-block; background:rgba(255,255,255,0.2); color:#fff; font-family:'Nunito',sans-serif; font-weight:700; font-size:.72rem; letter-spacing:2.5px; text-transform:uppercase; padding:5px 14px; border-radius:50px; margin-bottom:1rem; }
.os-brand-title { font-family:'Nunito',sans-serif; font-weight:900; font-size:2.4rem; color:#fff; line-height:1.1; margin-bottom:.8rem; text-shadow:0 2px 16px rgba(0,0,0,0.15); }
.os-brand-desc  { font-family:'Nunito',sans-serif; font-weight:400; font-size:.92rem; color:rgba(255,255,255,0.88); line-height:1.65; margin-bottom:2rem; }
.os-brand-btn   { display:inline-block; padding:12px 30px; border:2.5px solid rgba(255,255,255,0.85); border-radius:50px; color:#fff; font-family:'Nunito',sans-serif; font-weight:800; font-size:.9rem; text-decoration:none; transition:background .25s, box-shadow .25s; }
.os-brand-btn:hover { background:rgba(255,255,255,0.18); box-shadow:0 0 24px rgba(255,255,255,0.15); }
.os-brand-foot { font-family:'Nunito',sans-serif; font-size:.75rem; color:rgba(255,255,255,0.5); position:relative; z-index:1; }

@media (max-width: 640px) {
    .os-card { grid-template-columns: 1fr; }
    .os-brand { padding: 2rem 1.8rem; min-height: 200px; }
    .os-brand-title { font-size: 1.7rem; }
    .os-form-panel { padding: 2.2rem 1.8rem; }
    .os-form-title { font-size: 1.7rem; }
}
</style>

<div class="os-bg"></div>

<div class="os-wrapper">
<div class="os-card">
    <!-- ── FORM IZQ ── -->
    <div class="os-form-panel">
        <div class="os-form-title">Crear cuenta</div>
        <div class="os-form-sub">Únete a la exploración</div>

        <div id="registroMsg"></div>

        <form id="registroForm">
            <div class="os-group">
                <div class="os-input-wrap">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                    <input class="os-input" type="text" id="user" placeholder="Usuario" required>
                </div>
                <small id="userMsg"></small>
            </div>
            <div class="os-group">
                <div class="os-input-wrap">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/>
                    </svg>
                    <input class="os-input" type="email" id="email" placeholder="Email" required>
                </div>
                <small id="emailMsg"></small>
            </div>
            <div class="os-group">
                <div class="os-input-wrap">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input class="os-input" type="password" id="password" placeholder="Contraseña" required>
                </div>
                <small id="passwordMsg"></small>
            </div>
            <button type="submit" class="os-btn">Registrarse</button>
        </form>

        <div class="os-form-foot">
            ¿Ya tienes cuenta? <a href="?section=login" class="os-nav-link">Inicia sesión</a>
        </div>
    </div>
    <!-- ── BRAND DER ── -->
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
                <ellipse cx="24" cy="42" rx="7"  ry="4"   fill="#0d6e40" transform="rotate(-30,24,42)"/>
                <ellipse cx="76" cy="42" rx="7"  ry="4"   fill="#0d6e40" transform="rotate(30,76,42)"/>
                <ellipse cx="27" cy="63" rx="6"  ry="3.5" fill="#0d6e40" transform="rotate(25,27,63)"/>
                <ellipse cx="73" cy="63" rx="6"  ry="3.5" fill="#0d6e40" transform="rotate(-25,73,63)"/>
                <ellipse cx="50" cy="24" rx="11" ry="9"   fill="#1a9a58"/>
                <ellipse cx="47" cy="20" rx="4"  ry="3"   fill="rgba(255,255,255,0.14)" transform="rotate(-10,47,20)"/>
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
            <div class="os-brand-tag">🌊 HYDRON · Vida Marina</div>
            <div class="os-brand-title">¡Hola,<br>explorador!</div>
            <div class="os-brand-desc">
                Regístrate con tus datos<br>
                para usar todas las<br>
                funciones de la plataforma.
            </div>
            <a href="?section=login" class="os-brand-btn os-nav-link">Iniciar Sesión</a>
        </div>

</div>

</div>
</div>

<script>
function validarPassword(pass) {
    let errores = [];
    if (pass.length < 8) errores.push("al menos 8 caracteres");
    if (!/[A-Z]/.test(pass)) errores.push("una mayúscula");
    if (!/[0-9]/.test(pass) && !/[^a-zA-Z0-9]/.test(pass)) errores.push("un número o símbolo especial");
    return errores.length === 0 ? true : "Falta: " + errores.join(", ");
}

document.getElementById("user").addEventListener("keyup", function() {
    const msg = document.getElementById("userMsg");
    if (!this.value.trim()) { msg.textContent=''; return; }
    fetch("database/validar_registro.php", {
        method:"POST", headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"action=validar_user&user="+encodeURIComponent(this.value)
    }).then(r=>r.text()).then(d=>{
        d=d.trim();
        msg.style.color = d==="existe" ? "#ff6b6b" : "#00e676";
        msg.textContent  = d==="existe" ? "❌ Usuario ya existe" : "✅ Disponible";
    });
});
document.getElementById("email").addEventListener("keyup", function() {
    const msg = document.getElementById("emailMsg");
    if (!this.value.trim()) { msg.textContent=''; return; }
    fetch("database/validar_registro.php", {
        method:"POST", headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"action=validar_email&email="+encodeURIComponent(this.value)
    }).then(r=>r.text()).then(d=>{
        d=d.trim();
        msg.style.color = d==="existe" ? "#ff6b6b" : "#00e676";
        msg.textContent  = d==="existe" ? "❌ Email ya registrado" : "✅ Disponible";
    });
});
document.getElementById("password").addEventListener("keyup", function() {
    const msg = document.getElementById("passwordMsg");
    const pass = this.value.trim();
    if (!pass) { msg.textContent=''; return; }
    const check = validarPassword(pass);
    if (check === true) {
        msg.style.color = "#00e676";
        msg.textContent = "✅ Contraseña segura";
    } else {
        msg.style.color = "#ffaa00";
        msg.textContent = "⚠️ " + check;
    }
});
document.getElementById("registroForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const user  = document.getElementById("user").value.trim();
    const email = document.getElementById("email").value.trim();
    const pass  = document.getElementById("password").value.trim();
    const msg   = document.getElementById("registroMsg");
    if (!user||!email||!pass) {
        msg.style.cssText="background:rgba(255,150,0,.15);color:#ffaa00;border:1px solid rgba(255,150,0,.3);";
        msg.textContent="⚠️ Completa todos los campos"; return;
    }
    const passCheck = validarPassword(pass);
    if (passCheck !== true) {
        msg.style.cssText="background:rgba(255,150,0,.15);color:#ffaa00;border:1px solid rgba(255,150,0,.3);";
        msg.textContent="⚠️ " + passCheck; return;
    }
    msg.style.cssText="color:rgba(0,210,230,.7);"; msg.textContent="Registrando explorador...";
    fetch("database/validar_registro.php", {
        method:"POST", headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"action=registro&user="+encodeURIComponent(user)+"&email="+encodeURIComponent(email)+"&password="+encodeURIComponent(pass)
    }).then(r=>r.text()).then(data=>{
        data=data.trim();
        if (data==="ok") {
            msg.style.cssText="background:rgba(0,200,100,.15);color:#00e676;border:1px solid rgba(0,200,100,.3);";
            msg.textContent="✅ ¡Cuenta creada!";
            setTimeout(()=>window.location.href="index.php?section=login",1100);
        } else if (data==="user") {
            msg.style.cssText="background:rgba(255,60,60,.15);color:#ff6b6b;border:1px solid rgba(255,60,60,.3);";
            msg.textContent="❌ Usuario ya existe";
        } else if (data==="email") {
            msg.style.cssText="background:rgba(255,60,60,.15);color:#ff6b6b;border:1px solid rgba(255,60,60,.3);";
            msg.textContent="❌ Email ya registrado";
        } else if (data.startsWith("password_weak:")) {
            msg.style.cssText="background:rgba(255,150,0,.15);color:#ffaa00;border:1px solid rgba(255,150,0,.3);";
            msg.textContent="⚠️ Contraseña débil: " + data.substring(14);
        } else {
            msg.style.cssText="background:rgba(255,150,0,.15);color:#ffaa00;border:1px solid rgba(255,150,0,.3);";
            msg.textContent="⚠️ "+data;
        }
    }).catch(()=>{
        msg.style.cssText="background:rgba(255,60,60,.15);color:#ff6b6b;border:1px solid rgba(255,60,60,.3);";
        msg.textContent="⚠️ Error de conexión";
    });
});

document.querySelectorAll('.os-nav-link, .fp-footer a').forEach(function(a){
    a.addEventListener('click', function(e){
        e.preventDefault();
        var href = this.getAttribute('href');
        var wrapper = document.querySelector('.os-wrapper');
        if(wrapper){
            wrapper.style.transition = 'opacity .4s ease, transform .4s ease';
            wrapper.style.opacity = '0';
            wrapper.style.transform = 'translateY(-16px)';
            setTimeout(function(){ window.location.href = href; }, 380);
        } else { window.location.href = href; }
    });
});
</script>

<script>
// Transición de salida suave al navegar entre login y registro
document.querySelectorAll('a[href*="section=registro"], a[href*="section=login"]').forEach(function(link){
    link.addEventListener('click', function(e){
        e.preventDefault();
        var href = this.href;
        var overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;z-index:9999;background:linear-gradient(135deg,#005f9e,#009aaa);opacity:0;transition:opacity 0.4s ease,transform 0.4s ease;transform:translateY(100%);pointer-events:none;';
        document.body.appendChild(overlay);
        requestAnimationFrame(function(){
            overlay.style.opacity = '1';
            overlay.style.transform = 'translateY(0)';
        });
        setTimeout(function(){ window.location.href = href; }, 420);
    });
});
</script>