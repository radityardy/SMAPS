<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SMAPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#0f766e',secondary:'#0284c7'}}}}</script>
</head>
<body class="bg-slate-50 min-h-screen font-sans text-slate-800">
    <nav class="bg-teal-700 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="/" class="text-2xl font-bold tracking-wider">🏥 SMAPS</a>
            <div class="flex items-center space-x-3" id="nav-right">
                <span id="nav-user" class="text-sm font-medium bg-teal-800 px-3 py-1 rounded-full"></span>
                <a href="/" class="bg-teal-600 border border-teal-500 hover:bg-teal-500 text-white px-3 py-1.5 rounded-md text-sm font-medium transition">🏠 Beranda</a>
                <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm font-medium transition">Logout</button>
            </div>
        </div>
    </nav>

    <!-- Not authenticated -->
    <div id="not-auth" class="hidden max-w-md mx-auto mt-20 text-center">
        <div class="bg-white p-8 rounded-xl shadow border">
            <h2 class="text-2xl font-bold mb-4">⚠️ Belum Login</h2>
            <p class="text-slate-500 mb-6">Silakan login terlebih dahulu untuk mengakses dashboard.</p>
            <a href="/" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-lg font-bold">← Ke Halaman Login</a>
        </div>
    </div>

    <!-- Loading -->
    <div id="loading" class="max-w-7xl mx-auto px-4 py-20 text-center text-slate-400">
        <div class="animate-pulse text-lg">Memuat dashboard...</div>
    </div>

    <div id="toast" class="hidden fixed bottom-5 right-5 z-50 px-6 py-4 rounded-lg shadow-xl text-white font-medium"></div>

    <!-- ======= ADMIN DASHBOARD ======= -->
    <div id="admin-dashboard" class="hidden max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-extrabold mb-6">🛡️ Admin Dashboard</h1>

        <!-- Take Queue for Patient -->
        <div class="bg-white rounded-xl shadow border p-6 mb-6">
            <h2 class="text-lg font-bold mb-4 flex items-center"><span class="bg-amber-100 text-amber-700 p-2 rounded-lg mr-3">🎫</span> Ambil Antrian untuk Pasien</h2>
            <form onsubmit="adminTakeQueue(event)" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dokter Tujuan</label>
                    <select id="admin-q-doctor" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none"></select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Pasien</label>
                    <input type="text" id="admin-q-name" required placeholder="Nama lengkap pasien" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">No. Telepon</label>
                    <input type="text" id="admin-q-phone" placeholder="Opsional" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Keluhan</label>
                    <input type="text" id="admin-q-complaint" placeholder="Opsional" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-lg font-bold shadow transition">🎫 Ambil Nomor Antrian</button>
                </div>
            </form>
            <div id="admin-q-result" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg"></div>
        </div>

        <!-- Manage Doctors -->
        <div class="bg-white rounded-xl shadow border p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold flex items-center"><span class="bg-teal-100 text-teal-700 p-2 rounded-lg mr-3">👨‍⚕️</span> Kelola Dokter</h2>
                <button onclick="showEl('admin-add-doctor-form')" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Tambah Dokter</button>
            </div>
            <!-- Add Doctor Form -->
            <div id="admin-add-doctor-form" class="hidden bg-slate-50 border rounded-lg p-4 mb-4">
                <h3 class="font-bold text-sm mb-3">Tambah Dokter Baru</h3>
                <form onsubmit="adminAddDoctor(event)" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" id="add-doc-name" required placeholder="Nama Lengkap" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <input type="email" id="add-doc-email" required placeholder="Email" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <input type="text" id="add-doc-specialization" required placeholder="Spesialisasi" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <input type="text" id="add-doc-prefix" required placeholder="Prefix Antrian (A/B/C)" maxlength="3" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none uppercase">
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                        <button type="button" onclick="hideEl('admin-add-doctor-form')" class="bg-slate-300 hover:bg-slate-400 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium">Batal</button>
                    </div>
                </form>
            </div>
            <div id="admin-doctors-list" class="space-y-3"></div>
        </div>

        <!-- All Queues Today -->
        <div class="bg-white rounded-xl shadow border p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold flex items-center"><span class="bg-blue-100 text-blue-700 p-2 rounded-lg mr-3">📋</span> Semua Antrian Hari Ini</h2>
                <button onclick="loadAdminQueues()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">🔄 Refresh</button>
            </div>
            <div id="admin-queues-list" class="overflow-x-auto"></div>
        </div>
    </div>

    <!-- ======= DOCTOR DASHBOARD ======= -->
    <div id="doctor-dashboard" class="hidden max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-extrabold mb-6">👨‍⚕️ Doctor Dashboard</h1>
        <div id="doctor-no-profile" class="hidden bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-lg mb-6">Anda belum memiliki profil dokter. Hubungi admin.</div>

        <div id="doctor-panel" class="hidden space-y-6">
            <!-- Doctor Info & Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow border p-4 text-center">
                    <div class="text-xs text-slate-400 font-semibold uppercase">Total Antrian</div>
                    <div id="doc-stat-total" class="text-3xl font-black text-slate-800 mt-1">0</div>
                </div>
                <div class="bg-white rounded-xl shadow border p-4 text-center">
                    <div class="text-xs text-slate-400 font-semibold uppercase">Menunggu</div>
                    <div id="doc-stat-waiting" class="text-3xl font-black text-blue-600 mt-1">0</div>
                </div>
                <div class="bg-white rounded-xl shadow border p-4 text-center">
                    <div class="text-xs text-slate-400 font-semibold uppercase">Sedang Dilayani</div>
                    <div id="doc-stat-serving" class="text-3xl font-black text-green-600 mt-1">0</div>
                </div>
                <div class="bg-white rounded-xl shadow border p-4 text-center">
                    <div class="text-xs text-slate-400 font-semibold uppercase">Selesai</div>
                    <div id="doc-stat-done" class="text-3xl font-black text-slate-500 mt-1">0</div>
                </div>
            </div>

            <!-- Current Patient -->
            <div class="bg-white rounded-xl shadow border p-6">
                <h2 class="text-lg font-bold mb-4">Pasien Saat Ini</h2>
                <div id="doc-current-patient" class="text-center py-6 text-slate-400">Tidak ada pasien yang sedang dilayani.</div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
                <button onclick="doctorCallNext()" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg text-lg transition">📢 Panggil Berikutnya</button>
            </div>

            <!-- Queue List -->
            <div class="bg-white rounded-xl shadow border p-6">
                <h2 class="text-lg font-bold mb-4">Daftar Antrian</h2>
                <div id="doc-queue-list" class="space-y-2"></div>
            </div>
        </div>
    </div>

    <!-- ======= PATIENT DASHBOARD ======= -->
    <div id="patient-dashboard" class="hidden max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-extrabold mb-6">🧑‍🤝‍🧑 Dashboard Pasien</h1>

        <!-- Take Queue -->
        <div class="bg-white rounded-xl shadow border p-6 mb-6">
            <h2 class="text-lg font-bold mb-4 flex items-center"><span class="bg-teal-100 text-teal-700 p-2 rounded-lg mr-3">🎫</span> Ambil Nomor Antrian</h2>
            <form onsubmit="patientTakeQueue(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Dokter</label>
                    <select id="patient-q-doctor" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none"></select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Keluhan</label>
                    <textarea id="patient-q-complaint" rows="2" placeholder="Deskripsikan keluhan Anda (opsional)" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:outline-none"></textarea>
                </div>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg font-bold shadow transition">🎫 Ambil Nomor Antrian</button>
            </form>
            <div id="patient-q-result" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg"></div>
        </div>

        <!-- My Queues -->
        <div class="bg-white rounded-xl shadow border p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold flex items-center"><span class="bg-blue-100 text-blue-700 p-2 rounded-lg mr-3">📋</span> Antrian Saya Hari Ini</h2>
                <button onclick="loadPatientQueues()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium">🔄 Refresh</button>
            </div>
            <div id="patient-queue-list" class="space-y-3"></div>
        </div>
    </div>

    <script>
        const API = '/api';
        let currentUser = null;
        let doctorProfile = null;

        function getToken() { return localStorage.getItem('smaps_token'); }
        function authHeaders() { return { 'Authorization': `Bearer ${getToken()}`, 'Accept': 'application/json', 'Content-Type': 'application/json' }; }

        function showToast(msg, type='success') {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = `fixed bottom-5 right-5 z-50 px-6 py-4 rounded-lg shadow-xl text-white font-medium ${type==='error'?'bg-red-600':'bg-teal-600'}`;
            setTimeout(() => t.classList.add('hidden'), 4000);
        }
        function showEl(id) { document.getElementById(id).classList.remove('hidden'); }
        function hideEl(id) { document.getElementById(id).classList.add('hidden'); }

        document.addEventListener('DOMContentLoaded', init);

        async function init() {
            const token = getToken();
            if (!token) { showNotAuth(); return; }
            try {
                const res = await fetch(`${API}/me`, { headers: authHeaders() });
                if (!res.ok) { showNotAuth(); return; }
                const d = await res.json();
                currentUser = d.user;
                document.getElementById('nav-user').textContent = `${currentUser.name} (${currentUser.role.toUpperCase()})`;
                hideEl('loading');

                if (currentUser.role === 'admin') { showEl('admin-dashboard'); initAdmin(); }
                else if (currentUser.role === 'doctor') { showEl('doctor-dashboard'); initDoctor(); }
                else { showEl('patient-dashboard'); initPatient(); }
            } catch (e) { console.error(e); showNotAuth(); }
        }

        function showNotAuth() { hideEl('loading'); showEl('not-auth'); }

        async function logout() {
            await fetch(`${API}/logout`, { method: 'POST', headers: authHeaders() });
            localStorage.removeItem('smaps_token');
            window.location.href = '/';
        }

        // ========== ADMIN ==========
        async function initAdmin() {
            await loadDoctorOptions('admin-q-doctor');
            await loadAdminDoctors();
            await loadAdminQueues();
        }

        async function loadDoctorOptions(selectId) {
            const res = await fetch(`${API}/doctors`);
            const data = await res.json();
            const sel = document.getElementById(selectId);
            sel.innerHTML = '<option value="">-- Pilih Dokter --</option>';
            (data.data || data.doctors || []).forEach(d => {
                sel.innerHTML += `<option value="${d.id}">${d.user?.name || d.name} - ${d.specialization}</option>`;
            });
        }

        async function adminTakeQueue(e) {
            e.preventDefault();
            try {
                const res = await fetch(`${API}/queues`, {
                    method: 'POST', headers: authHeaders(),
                    body: JSON.stringify({
                        doctor_id: document.getElementById('admin-q-doctor').value,
                        patient_name: document.getElementById('admin-q-name').value,
                        patient_phone: document.getElementById('admin-q-phone').value || null,
                        complaint: document.getElementById('admin-q-complaint').value || null,
                    })
                });
                const data = await res.json();
                const rd = document.getElementById('admin-q-result');
                if (res.ok) {
                    const q = data.data || data.queue;
                    rd.className = 'mt-4 p-4 bg-green-50 border border-green-200 rounded-lg';
                    rd.innerHTML = `<div class="text-center"><div class="text-xs text-green-600 font-bold uppercase">Nomor Antrian</div><div class="text-4xl font-black text-green-800 my-2">${q.queue_number}</div><div class="text-sm text-green-700">Pasien: ${q.patient_name} | Dokter: ${q.doctor_name||'-'}</div></div>`;
                    showEl('admin-q-result');
                    showToast('Antrian berhasil dibuat!');
                    loadAdminQueues();
                } else { showToast(data.message || 'Gagal membuat antrian.', 'error'); }
            } catch (err) { showToast('Kesalahan koneksi.', 'error'); }
        }

        async function loadAdminDoctors() {
            const res = await fetch(`${API}/doctors`);
            const data = await res.json();
            const list = document.getElementById('admin-doctors-list');
            const docs = data.data || data.doctors || [];
            if (!docs.length) { list.innerHTML = '<p class="text-slate-400 text-sm">Belum ada dokter.</p>'; return; }
            list.innerHTML = docs.map(d => `<div class="flex items-center justify-between bg-slate-50 border rounded-lg p-3"><div><span class="font-bold text-slate-800">${d.user?.name || d.name}</span><span class="text-sm text-slate-500 ml-2">${d.specialization}</span><span class="ml-2 text-xs font-mono bg-slate-200 px-1.5 py-0.5 rounded">${d.queue_prefix}</span></div><div class="flex items-center gap-2"><span class="text-xs px-2 py-1 rounded-full ${d.is_active?'bg-green-100 text-green-700':'bg-red-100 text-red-700'}">${d.is_active?'Aktif':'Nonaktif'}</span><button onclick="deleteDoctor(${d.id})" class="text-red-500 hover:text-red-700 text-sm font-medium">🗑️</button></div></div>`).join('');
        }

        async function adminAddDoctor(e) {
            e.preventDefault();
            try {
                const res = await fetch(`${API}/doctors`, {
                    method: 'POST', headers: authHeaders(),
                    body: JSON.stringify({
                        name: document.getElementById('add-doc-name').value,
                        email: document.getElementById('add-doc-email').value,
                        password: 'password',
                        specialization: document.getElementById('add-doc-specialization').value,
                        queue_prefix: document.getElementById('add-doc-prefix').value.toUpperCase(),
                    })
                });
                const data = await res.json();
                if (res.ok) {
                    showToast('Dokter berhasil ditambahkan!');
                    hideEl('admin-add-doctor-form');
                    loadAdminDoctors();
                    loadDoctorOptions('admin-q-doctor');
                } else { showToast(data.message || JSON.stringify(data.errors || 'Gagal'), 'error'); }
            } catch (err) { showToast('Kesalahan koneksi.', 'error'); }
        }

        async function deleteDoctor(id) {
            if (!confirm('Yakin hapus dokter ini?')) return;
            const res = await fetch(`${API}/doctors/${id}`, { method: 'DELETE', headers: authHeaders() });
            if (res.ok) { showToast('Dokter dihapus.'); loadAdminDoctors(); }
            else { showToast('Gagal menghapus.', 'error'); }
        }

        async function loadAdminQueues() {
            const res = await fetch(`${API}/queues`, { headers: authHeaders() });
            const data = await res.json();
            const queues = data.data || data.queues || [];
            const container = document.getElementById('admin-queues-list');
            if (!queues.length) { container.innerHTML = '<p class="text-slate-400 text-sm py-4">Belum ada antrian hari ini.</p>'; return; }
            const sc = { waiting:'bg-blue-100 text-blue-800', called:'bg-amber-100 text-amber-800', serving:'bg-green-100 text-green-800', done:'bg-slate-200 text-slate-600', skipped:'bg-red-100 text-red-800' };
            container.innerHTML = `<table class="w-full text-sm"><thead><tr class="text-left text-xs text-slate-400 uppercase border-b"><th class="pb-2 pr-3">No</th><th class="pb-2 pr-3">Pasien</th><th class="pb-2 pr-3">Dokter</th><th class="pb-2 pr-3">Status</th><th class="pb-2 pr-3">Aksi</th></tr></thead><tbody>${queues.map(q => {
                let actions = '';
                if (q.status === 'waiting') actions = `<button onclick="adminCallQueue(${q.id})" class="text-amber-600 hover:text-amber-800 font-medium text-xs mr-1">📢 Panggil</button>`;
                if (q.status === 'called') actions = `<button onclick="adminServeQueue(${q.id})" class="text-green-600 hover:text-green-800 font-medium text-xs mr-1">✅ Layani</button><button onclick="adminSkipQueue(${q.id})" class="text-red-600 hover:text-red-800 font-medium text-xs">⏭️ Skip</button>`;
                if (q.status === 'serving') actions = `<button onclick="adminCompleteQueue(${q.id})" class="text-slate-600 hover:text-slate-800 font-medium text-xs">✔️ Selesai</button>`;
                return `<tr class="border-b border-slate-100"><td class="py-2 pr-3 font-bold">${q.queue_number}</td><td class="py-2 pr-3">${q.patient_name}</td><td class="py-2 pr-3">${q.doctor_name||'-'}</td><td class="py-2 pr-3"><span class="px-2 py-0.5 rounded-full text-xs font-bold ${sc[q.status]||''}">${q.status.toUpperCase()}</span></td><td class="py-2 pr-3">${actions}</td></tr>`;
            }).join('')}</tbody></table>`;
        }

        async function adminCallQueue(id) { await fetch(`${API}/queues/${id}/call`, { method:'POST', headers:authHeaders() }); loadAdminQueues(); showToast('Pasien dipanggil!'); }
        async function adminServeQueue(id) { await fetch(`${API}/queues/${id}/serve`, { method:'POST', headers:authHeaders() }); loadAdminQueues(); showToast('Mulai melayani.'); }
        async function adminCompleteQueue(id) { await fetch(`${API}/queues/${id}/complete`, { method:'POST', headers:authHeaders() }); loadAdminQueues(); showToast('Selesai.'); }
        async function adminSkipQueue(id) { await fetch(`${API}/queues/${id}/skip`, { method:'POST', headers:authHeaders() }); loadAdminQueues(); showToast('Dilewati.'); }

        // ========== DOCTOR ==========
        async function initDoctor() {
            // Find doctor profile linked to this user
            const res = await fetch(`${API}/doctors`);
            const data = await res.json();
            const docs = data.data || data.doctors || [];
            doctorProfile = docs.find(d => (d.user?.id || d.user_id) === currentUser.id);
            if (!doctorProfile) { showEl('doctor-no-profile'); return; }
            showEl('doctor-panel');
            loadDoctorDashboard();
        }

        async function loadDoctorDashboard() {
            if (!doctorProfile) return;
            // Load summary
            try {
                const sRes = await fetch(`${API}/doctors/${doctorProfile.id}/summary`, { headers: authHeaders() });
                if (sRes.ok) {
                    const sd = await sRes.json();
                    document.getElementById('doc-stat-total').textContent = sd.total ?? 0;
                    document.getElementById('doc-stat-waiting').textContent = sd.waiting ?? 0;
                    document.getElementById('doc-stat-serving').textContent = sd.serving ?? 0;
                    document.getElementById('doc-stat-done').textContent = sd.done ?? 0;
                }
            } catch(e){}

            // Load queues for this doctor
            const qRes = await fetch(`${API}/queues?doctor_id=${doctorProfile.id}`, { headers: authHeaders() });
            const qData = await qRes.json();
            const queues = qData.data || qData.queues || [];

            // Current patient
            const cp = document.getElementById('doc-current-patient');
            const current = queues.find(q => q.status === 'called' || q.status === 'serving');
            if (current) {
                const isCalled = current.status === 'called';
                cp.innerHTML = `<div class="text-center"><div class="text-5xl font-black ${isCalled?'text-amber-600 animate-bounce':'text-green-600'} mb-2">${current.queue_number}</div><div class="text-lg font-bold">${current.patient_name}</div><div class="text-sm text-slate-500">${current.complaint||'Tidak ada keluhan'}</div><span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold ${isCalled?'bg-amber-100 text-amber-800':'bg-green-100 text-green-800'}">${current.status.toUpperCase()}</span><div class="mt-4 flex justify-center gap-3">${isCalled?`<button onclick="docServe(${current.id})" class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-lg font-bold">✅ Mulai Layani</button><button onclick="docSkip(${current.id})" class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg font-bold">⏭️ Skip</button>`:`<button onclick="docComplete(${current.id})" class="bg-slate-600 hover:bg-slate-700 text-white px-5 py-2 rounded-lg font-bold">✔️ Selesai</button>`}</div></div>`;
            } else { cp.innerHTML = '<p class="text-slate-400 text-center py-4">Tidak ada pasien saat ini. Tekan "Panggil Berikutnya".</p>'; }

            // Queue list
            const ql = document.getElementById('doc-queue-list');
            const sc = { waiting:'bg-blue-100 text-blue-800', called:'bg-amber-100 text-amber-800', serving:'bg-green-100 text-green-800', done:'bg-slate-200 text-slate-600', skipped:'bg-red-100 text-red-800' };
            if (!queues.length) { ql.innerHTML = '<p class="text-slate-400 text-sm">Belum ada antrian.</p>'; return; }
            ql.innerHTML = queues.map(q => `<div class="flex items-center justify-between bg-slate-50 border rounded-lg p-3"><div><span class="font-bold text-lg mr-2">${q.queue_number}</span><span class="text-sm text-slate-600">${q.patient_name}</span></div><span class="px-2 py-0.5 rounded-full text-xs font-bold ${sc[q.status]||''}">${q.status.toUpperCase()}</span></div>`).join('');
        }

        async function doctorCallNext() {
            if (!doctorProfile) return;
            const res = await fetch(`${API}/doctors/${doctorProfile.id}/call-next`, { method:'POST', headers:authHeaders() });
            const data = await res.json();
            if (res.ok) { showToast(`Memanggil ${data.data?.queue_number || 'pasien'}!`); }
            else { showToast(data.message || 'Tidak ada antrian menunggu.', 'error'); }
            loadDoctorDashboard();
        }

        async function docServe(id) { await fetch(`${API}/queues/${id}/serve`, { method:'POST', headers:authHeaders() }); loadDoctorDashboard(); showToast('Mulai melayani.'); }
        async function docComplete(id) { await fetch(`${API}/queues/${id}/complete`, { method:'POST', headers:authHeaders() }); loadDoctorDashboard(); showToast('Selesai.'); }
        async function docSkip(id) { await fetch(`${API}/queues/${id}/skip`, { method:'POST', headers:authHeaders() }); loadDoctorDashboard(); showToast('Dilewati.'); }

        // ========== PATIENT ==========
        async function initPatient() {
            await loadDoctorOptions('patient-q-doctor');
            await loadPatientQueues();
        }

        async function patientTakeQueue(e) {
            e.preventDefault();
            try {
                const res = await fetch(`${API}/queues`, {
                    method: 'POST', headers: authHeaders(),
                    body: JSON.stringify({
                        doctor_id: document.getElementById('patient-q-doctor').value,
                        patient_name: currentUser.name,
                        complaint: document.getElementById('patient-q-complaint').value || null,
                    })
                });
                const data = await res.json();
                const rd = document.getElementById('patient-q-result');
                if (res.ok) {
                    const q = data.data || data.queue;
                    rd.className = 'mt-4 p-4 bg-green-50 border border-green-200 rounded-lg';
                    rd.innerHTML = `<div class="text-center"><div class="text-xs text-green-600 font-bold uppercase">Nomor Antrian Anda</div><div class="text-5xl font-black text-green-800 my-3">${q.queue_number}</div><div class="text-sm text-green-700">Dokter: ${q.doctor_name||'-'} | ${q.doctor_specialization||''}</div><p class="text-xs text-green-600 mt-2">Silakan pantau status di halaman Beranda atau refresh halaman ini.</p></div>`;
                    showEl('patient-q-result');
                    showToast('Antrian berhasil diambil!');
                    loadPatientQueues();
                } else { showToast(data.message || 'Gagal mengambil antrian.', 'error'); }
            } catch (err) { showToast('Kesalahan koneksi.', 'error'); }
        }

        async function loadPatientQueues() {
            const res = await fetch(`${API}/queues`, { headers: authHeaders() });
            const data = await res.json();
            const queues = (data.data || data.queues || []).filter(q => q.patient_name === currentUser.name);
            const container = document.getElementById('patient-queue-list');
            if (!queues.length) { container.innerHTML = '<p class="text-slate-400 text-sm py-4">Anda belum punya antrian hari ini.</p>'; return; }
            const sc = { waiting:'bg-blue-100 text-blue-800 border-blue-300', called:'bg-amber-100 text-amber-800 border-amber-300', serving:'bg-green-100 text-green-800 border-green-300', done:'bg-slate-200 text-slate-600 border-slate-300', skipped:'bg-red-100 text-red-800 border-red-300' };
            container.innerHTML = queues.map(q => `<div class="bg-white border rounded-xl p-4 flex items-center justify-between shadow-sm"><div><div class="text-2xl font-black text-slate-800">${q.queue_number}</div><div class="text-sm text-slate-500">Dokter: ${q.doctor_name||'-'}</div><div class="text-xs text-slate-400">${q.complaint||'Tidak ada keluhan'}</div></div><div class="text-right"><span class="px-3 py-1 rounded-full text-xs font-bold border ${sc[q.status]||''}">${q.status.toUpperCase()}</span><div class="text-xs text-slate-400 mt-2">${new Date(q.created_at).toLocaleTimeString('id-ID')}</div></div></div>`).join('');
        }
    </script>
</body>
</html>