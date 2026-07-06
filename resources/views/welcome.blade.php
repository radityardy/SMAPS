<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMAPS - Sistem Manajemen Antrian & Pelayanan Kesehatan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#0f766e', secondary: '#0284c7' } } }
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen font-sans text-slate-800">
    <nav class="bg-teal-700 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl font-bold tracking-wider">🏥 SMAPS</span>
                </div>
                <div class="flex items-center space-x-3" id="nav-actions">
                    <a href="/dashboard" class="bg-teal-600 border border-teal-500 hover:bg-teal-500 text-white px-4 py-2 rounded-md text-sm font-semibold transition hidden" id="nav-dashboard-link">📊 Dashboard</a>
                    <button onclick="showModal('login-modal')" id="btn-login" class="bg-white text-teal-700 hover:bg-teal-50 px-4 py-2 rounded-md text-sm font-semibold shadow transition">Login</button>
                    <button onclick="showModal('register-modal')" id="btn-register" class="bg-teal-600 border border-teal-500 hover:bg-teal-500 text-white px-4 py-2 rounded-md text-sm font-semibold transition">Register</button>
                    <div id="user-info" class="hidden items-center space-x-3">
                        <span id="user-name-display" class="text-sm font-medium bg-teal-800 px-3 py-1 rounded-full"></span>
                        <a href="/dashboard" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md text-sm font-semibold transition">📊 Dashboard</a>
                        <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm font-medium transition">Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div id="toast" class="hidden fixed bottom-5 right-5 z-50 px-6 py-4 rounded-lg shadow-xl text-white font-medium transition-all duration-300"></div>

        <!-- Hero Section -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-slate-800 mb-3">Sistem Manajemen Antrian & Pelayanan</h1>
            <p class="text-slate-500 max-w-2xl mx-auto">Pantau antrian secara real-time, ambil nomor antrian online, dan kelola pelayanan kesehatan dengan mudah.</p>
            <div class="mt-6 flex justify-center gap-4">
                <button onclick="showModal('login-modal')" id="hero-login" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition">Login / Masuk</button>
                <button onclick="showModal('register-modal')" id="hero-register" class="bg-white border-2 border-teal-600 text-teal-700 hover:bg-teal-50 px-6 py-3 rounded-xl font-bold shadow transition">Daftar Akun Baru</button>
                <a href="/dashboard" id="hero-dashboard" class="hidden bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition">Buka Dashboard →</a>
            </div>
        </div>

        <!-- Queue Check Section -->
        <div class="max-w-xl mx-auto mb-10">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-700 p-2 rounded-lg mr-3">🔍</span> Cek Status Antrian
                </h2>
                <p class="text-sm text-slate-500 mb-4">Masukkan kode antrian (contoh: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-red-600 font-mono">A-001</code>)</p>
                <form onsubmit="checkQueueStatus(event)" class="flex space-x-2">
                    <input type="text" id="check-code-input" placeholder="Kode Antrian" required
                        class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none uppercase">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">Cek</button>
                </form>
                <div id="check-result" class="mt-6 hidden p-4 rounded-lg border"></div>
            </div>
        </div>

        <!-- Display Board -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800">📺 Live Queue Display Board</h2>
                <p class="text-slate-500 text-sm mt-1">Pantau status antrian poliklinik & dokter secara real-time</p>
            </div>
            <div class="flex items-center space-x-3">
                <label class="flex items-center text-sm font-medium text-slate-600 cursor-pointer">
                    <input type="checkbox" id="auto-refresh-toggle" onchange="toggleAutoRefresh()" class="mr-2 h-4 w-4 text-teal-600 rounded">
                    Auto-refresh (5s)
                </label>
                <button onclick="fetchDisplayBoard()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow">🔄 Refresh</button>
            </div>
        </div>
        <div id="display-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="col-span-full text-center py-12 text-slate-400">Loading display board...</div>
        </div>
    </main>

    <!-- Login Modal -->
    <div id="login-modal" class="hidden fixed inset-0 bg-slate-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-lg font-bold text-slate-800">Login</h3>
                <button onclick="hideModal('login-modal')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>
            <form onsubmit="handleLogin(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" id="login-email" required placeholder="email@example.com"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" id="login-password" required value="password"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <div class="text-xs text-slate-500 bg-slate-50 p-3 rounded border">
                    <strong>Demo (Password: password):</strong><br>
                    • Admin: <code class="text-teal-700">admin@smaps.test</code><br>
                    • Dokter: <code class="text-teal-700">dr.andi@smaps.test</code><br>
                    • Pasien: <code class="text-teal-700">budi@example.com</code>
                </div>
                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-lg font-medium shadow transition">Login</button>
            </form>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="register-modal" class="hidden fixed inset-0 bg-slate-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-lg font-bold text-slate-800">Register Akun Baru</h3>
                <button onclick="hideModal('register-modal')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>
            <form onsubmit="handleRegister(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="reg-name" required placeholder="Budi Santoso" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" id="reg-email" required placeholder="budi@example.com" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" id="reg-password" required minlength="8" placeholder="Minimal 8 karakter" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" id="reg-password-confirm" required minlength="8" placeholder="Ulangi password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <p class="text-xs text-slate-400">Akun baru otomatis terdaftar sebagai <strong>Pasien</strong>.</p>
                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-lg font-medium shadow transition">Daftar</button>
            </form>
        </div>
    </div>

    <script>
        const API_BASE = '/api';
        let currentUser = null;
        let autoRefreshInterval = null;
        let lastCalledQueues = {};

        document.addEventListener('DOMContentLoaded', () => {
            checkAuth();
            fetchDisplayBoard();
        });

        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = `fixed bottom-5 right-5 z-50 px-6 py-4 rounded-lg shadow-xl text-white font-medium transition-all duration-300 ${type === 'error' ? 'bg-red-600' : 'bg-teal-600'}`;
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 4000);
        }

        function showModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function hideModal(id) { document.getElementById(id).classList.add('hidden'); }

        async function checkAuth() {
            const token = localStorage.getItem('smaps_token');
            if (!token) { updateUI(null); return; }
            try {
                const res = await fetch(`${API_BASE}/me`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
                if (res.ok) { const d = await res.json(); currentUser = d.user; updateUI(currentUser); }
                else { localStorage.removeItem('smaps_token'); updateUI(null); }
            } catch (e) { console.error(e); }
        }

        function updateUI(user) {
            const btnL = document.getElementById('btn-login');
            const btnR = document.getElementById('btn-register');
            const uInfo = document.getElementById('user-info');
            const heroL = document.getElementById('hero-login');
            const heroR = document.getElementById('hero-register');
            const heroD = document.getElementById('hero-dashboard');

            if (user) {
                btnL.classList.add('hidden'); btnR.classList.add('hidden');
                uInfo.classList.remove('hidden'); uInfo.classList.add('flex');
                document.getElementById('user-name-display').textContent = `${user.name} (${user.role.toUpperCase()})`;
                heroL.classList.add('hidden'); heroR.classList.add('hidden');
                heroD.classList.remove('hidden');
            } else {
                currentUser = null;
                btnL.classList.remove('hidden'); btnR.classList.remove('hidden');
                uInfo.classList.add('hidden');
                heroL.classList.remove('hidden'); heroR.classList.remove('hidden');
                heroD.classList.add('hidden');
            }
        }

        async function handleLogin(e) {
            e.preventDefault();
            try {
                const res = await fetch(`${API_BASE}/login`, {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: document.getElementById('login-email').value, password: document.getElementById('login-password').value })
                });
                const data = await res.json();
                if (res.ok) {
                    localStorage.setItem('smaps_token', data.token || data.access_token);
                    currentUser = data.user;
                    updateUI(currentUser);
                    hideModal('login-modal');
                    showToast(`Selamat datang, ${currentUser.name}!`);
                    window.location.href = '/dashboard';
                } else { showToast(data.message || 'Login gagal.', 'error'); }
            } catch (err) { showToast('Kesalahan koneksi.', 'error'); }
        }

        async function handleRegister(e) {
            e.preventDefault();
            try {
                const res = await fetch(`${API_BASE}/register`, {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        name: document.getElementById('reg-name').value,
                        email: document.getElementById('reg-email').value,
                        password: document.getElementById('reg-password').value,
                        password_confirmation: document.getElementById('reg-password-confirm').value,
                        role: 'patient'
                    })
                });
                const data = await res.json();
                if (res.ok) {
                    localStorage.setItem('smaps_token', data.token || data.access_token);
                    currentUser = data.user;
                    updateUI(currentUser);
                    hideModal('register-modal');
                    showToast('Registrasi berhasil!');
                    window.location.href = '/dashboard';
                } else { showToast(data.message || 'Registrasi gagal.', 'error'); }
            } catch (err) { showToast('Kesalahan koneksi.', 'error'); }
        }

        async function logout() {
            const token = localStorage.getItem('smaps_token');
            if (token) await fetch(`${API_BASE}/logout`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
            localStorage.removeItem('smaps_token');
            updateUI(null);
            showToast('Logout berhasil.');
        }

        async function checkQueueStatus(e) {
            e.preventDefault();
            const code = document.getElementById('check-code-input').value.trim().toUpperCase();
            const rd = document.getElementById('check-result');
            try {
                const res = await fetch(`${API_BASE}/check-queue?queue_code=${encodeURIComponent(code)}`);
                const data = await res.json();
                rd.classList.remove('hidden');
                if (res.ok) {
                    const q = data.data;
                    const sc = { 'waiting':'bg-blue-100 text-blue-800 border-blue-300', 'called':'bg-amber-100 text-amber-800 border-amber-300', 'serving':'bg-green-100 text-green-800 border-green-300', 'done':'bg-slate-100 text-slate-800 border-slate-300', 'skipped':'bg-red-100 text-red-800 border-red-300' };
                    rd.className = 'mt-6 p-4 rounded-xl border bg-white shadow-sm border-slate-200';
                    rd.innerHTML = `<div class="flex justify-between items-center border-b pb-3 mb-3"><div><span class="text-xs text-slate-400 font-semibold">KODE TIKET</span><h4 class="text-2xl font-black text-slate-800">${q.queue_number}</h4></div><span class="px-3 py-1 rounded-full text-xs font-bold border ${sc[q.status]||''}">${q.status.toUpperCase()}</span></div><div class="text-sm space-y-1 text-slate-600"><p><strong>Dokter:</strong> ${q.doctor_name||'-'}</p><p><strong>Spesialisasi:</strong> ${q.doctor_specialization||'-'}</p><p><strong>Keluhan:</strong> ${q.complaint||'-'}</p><p class="text-xs text-slate-400 mt-2">Daftar: ${new Date(q.created_at).toLocaleString('id-ID')}</p></div>`;
                } else {
                    rd.className = 'mt-6 p-4 rounded-xl border bg-red-50 border-red-200 text-red-700 text-sm font-medium';
                    rd.textContent = data.message || 'Antrian tidak ditemukan.';
                }
            } catch (err) { showToast('Kesalahan saat memeriksa antrian.', 'error'); }
        }

        async function fetchDisplayBoard() {
            try {
                const res = await fetch(`${API_BASE}/display`);
                const data = await res.json();
                const grid = document.getElementById('display-grid');
                grid.innerHTML = '';
                if (!data.display || data.display.length === 0) { grid.innerHTML = '<div class="col-span-full text-center py-12 text-slate-400">Belum ada data dokter / antrian.</div>'; return; }
                let playSound = null;
                data.display.forEach(doc => {
                    const sc = { 'serving':'bg-green-100 text-green-800 border-green-300 animate-pulse', 'called':'bg-amber-100 text-amber-800 border-amber-300 animate-bounce', 'waiting':'bg-blue-100 text-blue-800 border-blue-300', 'done':'bg-slate-100 text-slate-600 border-slate-300' };
                    const bc = doc.current_status ? (sc[doc.current_status]||'bg-slate-100 text-slate-800') : 'bg-slate-100 text-slate-500';
                    const st = doc.current_status ? doc.current_status.toUpperCase() : 'STANDBY';
                    grid.innerHTML += `<div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden flex flex-col justify-between transition hover:shadow-lg"><div class="bg-teal-700 text-white p-4"><h3 class="font-bold text-lg truncate">${doc.doctor_name}</h3><p class="text-teal-200 text-xs mt-0.5">${doc.specialization}</p></div><div class="p-6 text-center"><div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Sedang Dipanggil / Dilayani</div><div class="text-5xl font-black text-slate-800 my-3 tracking-tight">${doc.current_queue||'<span class="text-3xl text-slate-300">-</span>'}</div><span class="inline-block px-3 py-1 rounded-full text-xs font-bold border ${bc}">${st}</span></div><div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-between text-sm text-slate-600"><span>Menunggu: <strong class="text-teal-700">${doc.waiting}</strong></span><span>Total: <strong class="text-slate-800">${doc.total}</strong></span></div></div>`;
                    if (doc.current_status === 'called' && doc.current_queue) {
                        const key = `${doc.doctor_id}-${doc.current_queue}-${doc.current_updated_at||''}`;
                        if (!lastCalledQueues[doc.doctor_id] || lastCalledQueues[doc.doctor_id] !== key) { playSound = { number: doc.current_queue, doctor: doc.doctor_name }; lastCalledQueues[doc.doctor_id] = key; }
                    } else if (doc.current_status !== 'called') { delete lastCalledQueues[doc.doctor_id]; }
                });
                if (playSound) playCallingSound(playSound.number, playSound.doctor);
            } catch (err) { console.error(err); }
        }

        function playCallingSound(num, doc) {
            playChime(() => {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                    const u = new SpeechSynthesisUtterance(`Nomor antrian, ${num.split('').join(' ')}, silakan menuju ke, ${doc}`);
                    u.lang = 'id-ID'; u.rate = 0.95;
                    const v = window.speechSynthesis.getVoices().find(v => v.lang.includes('id'));
                    if (v) u.voice = v;
                    window.speechSynthesis.speak(u);
                }
            });
        }

        function playChime(cb) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const tone = (f, t, d, next) => { const o = ctx.createOscillator(), g = ctx.createGain(); o.type='sine'; o.frequency.setValueAtTime(f,t); g.gain.setValueAtTime(0.1,t); g.gain.exponentialRampToValueAtTime(0.001,t+d); o.connect(g); g.connect(ctx.destination); o.start(t); o.stop(t+d); if(next) setTimeout(next, d*1000-100); };
                tone(392, ctx.currentTime, 0.4, () => tone(523, ctx.currentTime, 0.6, cb));
            } catch(e) { if(cb) cb(); }
        }

        function toggleAutoRefresh() {
            const t = document.getElementById('auto-refresh-toggle');
            if (t.checked) {
                if (autoRefreshInterval) clearInterval(autoRefreshInterval);
                autoRefreshInterval = setInterval(() => fetchDisplayBoard(), 5000);
                showToast('Auto-refresh ON (5s)');
            } else { clearInterval(autoRefreshInterval); autoRefreshInterval = null; showToast('Auto-refresh OFF'); }
        }
    </script>
</body>
</html>