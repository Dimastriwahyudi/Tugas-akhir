<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peta Warung</title>

    {{-- Tailwind via CDN (atau gunakan yang sudah ada di project) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }
        #map { width: 100%; height: 100%; }
        .leaflet-popup-content { margin: 0; padding: 0; min-width: 200px; }
        .marker-aktif  { background: #22c55e; width:14px; height:14px; border-radius:50%; border:2px solid white; box-shadow:0 1px 4px rgba(0,0,0,.4); }
        .marker-tutup  { background: #ef4444; width:14px; height:14px; border-radius:50%; border:2px solid white; box-shadow:0 1px 4px rgba(0,0,0,.4); }
        .marker-pindah { background: #eab308; width:14px; height:14px; border-radius:50%; border:2px solid white; box-shadow:0 1px 4px rgba(0,0,0,.4); }
        .marker-temp   { background: #6366f1; width:16px; height:16px; border-radius:50%; border:3px solid white; box-shadow:0 1px 6px rgba(0,0,0,.5); animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.3);opacity:.7} }

        #sidebar {
            position: fixed; top: 0; right: 0; bottom: 0;
            width: 420px; background: white; z-index: 1000;
            box-shadow: -4px 0 20px rgba(0,0,0,.15);
            transform: translateX(100%);
            transition: transform .3s ease;
            display: flex; flex-direction: column;
            overflow: hidden;
        }
        #sidebar.open { transform: translateX(0); }
        #sidebar-body { flex: 1; overflow-y: auto; }

        .dark #sidebar { background: #1f2937; }
        .input-field {
            width: 100%; border: 1px solid #d1d5db; border-radius: .5rem;
            padding: .375rem .75rem; font-size: .875rem; outline: none;
            background: white; color: #111827;
            transition: border-color .15s, box-shadow .15s;
        }
        .input-field:focus { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,.2); }
        .label { display: block; font-size: .75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .25rem; }
        .btn-primary { width: 100%; padding: .625rem; background: #4f46e5; color: white; font-size: .875rem; font-weight: 600; border-radius: .5rem; border: none; cursor: pointer; transition: background .15s; }
        .btn-primary:hover { background: #4338ca; }
        .btn-primary:disabled { background: #a5b4fc; cursor: not-allowed; }
        .btn-secondary { padding: .5rem 1rem; background: #f3f4f6; color: #374151; font-size: .875rem; font-weight: 500; border-radius: .5rem; border: none; cursor: pointer; transition: background .15s; }
        .btn-secondary:hover { background: #e5e7eb; }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

{{-- Navbar --}}
<div style="position:fixed;top:0;left:0;right:0;z-index:999;background:white;border-bottom:1px solid #e5e7eb;padding:.75rem 1.25rem;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 4px rgba(0,0,0,.08)">
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Dashboard</a>
        <span class="text-gray-300">|</span>
        <h1 class="text-base font-semibold text-gray-800">🗺️ Peta Warung</h1>
    </div>
    <div class="flex items-center gap-4 text-xs text-gray-600">
        <span class="flex items-center gap-1.5"><span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block"></span>Aktif</span>
        <span class="flex items-center gap-1.5"><span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block"></span>Tutup</span>
        <span class="flex items-center gap-1.5"><span style="width:10px;height:10px;border-radius:50%;background:#eab308;display:inline-block"></span>Pindah</span>
        <span class="text-gray-400">|</span>
        <span class="font-medium text-indigo-600">{{ $warungs->count() }} Warung</span>
        <button onclick="locateMe()" class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 font-medium">📍 Lokasi Saya</button>
    </div>
</div>

{{-- Map --}}
<div style="position:fixed;top:53px;left:0;right:0;bottom:0;">
    <div id="map" style="width:100%;height:100%;"></div>
</div>

{{-- Sidebar --}}
<div id="sidebar">
    <div style="position:sticky;top:0;background:white;border-bottom:1px solid #e5e7eb;padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;z-index:10;">
        <div>
            <h3 id="sidebar-title" class="font-semibold text-gray-800 text-base"></h3>
            <p id="sidebar-subtitle" class="text-xs text-gray-400 mt-0.5"></p>
        </div>
        <button onclick="tutupSidebar()" style="font-size:1.5rem;color:#9ca3af;background:none;border:none;cursor:pointer;line-height:1;" title="Tutup">×</button>
    </div>
    <div id="sidebar-body" class="p-5"></div>
</div>

{{-- Toast --}}
<div id="toast" style="position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%) translateY(6rem);z-index:9999;transition:transform .3s ease;background:#111827;color:white;padding:.625rem 1.25rem;border-radius:.75rem;font-size:.875rem;white-space:nowrap;box-shadow:0 4px 12px rgba(0,0,0,.2);pointer-events:none;"></div>

<script>

const WARUNG_DATA = @json($warungs);
const PRODUK_DATA = @json($produk);
const SALES_DATA  = @json($sales);

const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const STATUS_COLOR = { aktif: '#22c55e', tutup: '#ef4444', pindah: '#eab308' };

const map = L.map('map', { zoomControl: false }).setView([-7.0051, 110.4203], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
    maxZoom: 19
}).addTo(map);

L.control.zoom({ position: 'bottomright' }).addTo(map);

const markers = {};

WARUNG_DATA.forEach(w => {
    const m = buatMarker(w.latitude, w.longitude, w.status);
    m.addTo(map).on('click', () => bukaDetailWarung(w));
    markers[w.id] = m;
});

let tempMarker = null;

map.on('click', function(e) {
    const { lat, lng } = e.latlng;
    bukaTambahWarung(lat, lng);
});

function bukaTambahWarung(lat, lng) {
    if (tempMarker) map.removeLayer(tempMarker);

    const icon = L.divIcon({ className: '', html: '<div class="marker-temp"></div>', iconSize: [16,16], iconAnchor: [8,8] });
    tempMarker = L.marker([lat, lng], { icon, draggable: true }).addTo(map);

    tempMarker.on('dragend', function() {
        const pos = tempMarker.getLatLng();
        document.getElementById('f-lat').value = pos.lat;
        document.getElementById('f-lng').value = pos.lng;
        reverseGeocode(pos.lat, pos.lng);
    });

    document.getElementById('f-lat').value = lat;
    document.getElementById('f-lng').value = lng;

    // Set waktu sekarang
    setWaktuSekarang('f-tanggal');

    // Auto detect alamat
    reverseGeocode(lat, lng);

    bukaSidebar('tambah-warung', '📍 Tambah Warung Baru', `${lat.toFixed(6)}, ${lng.toFixed(6)}`);
    renderFormTambah();
}

async function reverseGeocode(lat, lng) {
    const el = document.getElementById('f-alamat');
    const lbl = document.getElementById('lbl-detecting');
    if (!el) return;
    if (lbl) lbl.classList.remove('hidden');
    try {
        const res  = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=id`);
        const data = await res.json();
        el.value   = data.display_name || '';
    } catch(e) {}
    if (lbl) lbl.classList.add('hidden');
}

let currentMode = '';

function bukaSidebar(mode, title, subtitle = '') {
    currentMode = mode;
    document.getElementById('sidebar-title').textContent    = title;
    document.getElementById('sidebar-subtitle').textContent = subtitle;
    document.getElementById('sidebar').classList.add('open');
}

function tutupSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-body').innerHTML = '';
    if (tempMarker) { map.removeLayer(tempMarker); tempMarker = null; }
    produkIndex = 0;
}

function buatMarker(lat, lng, status) {
    const color = STATUS_COLOR[status] || '#6366f1';
    const html  = `<div style="width:14px;height:14px;border-radius:50%;background:${color};border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,.4);cursor:pointer;"></div>`;
    const icon  = L.divIcon({ className: '', html, iconSize: [14,14], iconAnchor: [7,7] });
    return L.marker([lat, lng], { icon });
}

function setWaktuSekarang(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    el.value = now.toISOString().slice(0, 16);
}

let produkIndex = 0;

function renderProdukBaris(i) {
    return `
    <div id="pr-${i}" style="border:1px solid #e5e7eb;border-radius:.5rem;padding:.75rem;position:relative;">
        <button type="button" onclick="hapusProduk(${i})"
                style="position:absolute;top:.5rem;right:.5rem;background:none;border:none;cursor:pointer;color:#d1d5db;font-size:1.25rem;line-height:1;"
                title="Hapus">×</button>

        <select name="produk[${i}][id]" onchange="isiHarga(this,${i})" required class="input-field" style="margin-bottom:.5rem;">
            <option value="">-- Pilih Produk --</option>
            ${PRODUK_DATA.map(p => `<option value="${p.id}" data-jual="${p.harga_jual}" data-modal="${p.harga_modal}">${p.nama} (${p.satuan})</option>`).join('')}
        </select>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">
            <div>
                <label class="label">Stok Masuk</label>
                <input type="number" name="produk[${i}][stok_masuk]" min="1" required
                       oninput="hitungProfit()" class="input-field" placeholder="0">
            </div>
            <div>
                <label class="label">Sisa (Keluar)</label>
                <input type="number" name="produk[${i}][stok_keluar]" min="0" value="0" required
                       oninput="hitungProfit()" class="input-field" placeholder="0">
            </div>
            <div>
                <label class="label">Harga Jual (Rp)</label>
                <input type="number" name="produk[${i}][harga_jual]" min="0" required
                       oninput="hitungProfit()" class="input-field" placeholder="0" id="hj-${i}">
            </div>
            <div>
                <label class="label">Harga Modal (Rp)</label>
                <input type="number" name="produk[${i}][harga_modal]" min="0" required
                       oninput="hitungProfit()" class="input-field" placeholder="0" id="hm-${i}">
            </div>
        </div>
        <div id="sub-${i}" style="margin-top:.5rem;font-size:.75rem;color:#6b7280;text-align:right;"></div>
    </div>`;
}

function tambahProduk() {
    document.getElementById('produk-wrap').insertAdjacentHTML('beforeend', renderProdukBaris(produkIndex++));
}

function hapusProduk(i) {
    document.getElementById(`pr-${i}`)?.remove();
    hitungProfit();
}

function isiHarga(sel, i) {
    const opt = sel.options[sel.selectedIndex];
    document.getElementById(`hj-${i}`).value = opt.dataset.jual  || '';
    document.getElementById(`hm-${i}`).value = opt.dataset.modal || '';
    hitungProfit();
}

function hitungProfit() {
    let totalJual = 0, totalModal = 0;
    document.querySelectorAll('[id^="pr-"]').forEach(row => {
        const masuk  = parseFloat(row.querySelector('[name*="stok_masuk"]')?.value)  || 0;
        const keluar = parseFloat(row.querySelector('[name*="stok_keluar"]')?.value) || 0;
        const jual   = parseFloat(row.querySelector('[name*="harga_jual"]')?.value)  || 0;
        const modal  = parseFloat(row.querySelector('[name*="harga_modal"]')?.value) || 0;
        const terjual = Math.max(0, masuk - keluar);
        const subJual = terjual * jual;
        const subModal = terjual * modal;
        totalJual  += subJual;
        totalModal += subModal;

        const id = row.id.replace('pr-', '');
        const sub = document.getElementById(`sub-${id}`);
        if (sub && masuk > 0) sub.textContent = `Terjual: ${terjual} | Sub-profit: Rp ${(subJual - subModal).toLocaleString('id-ID')}`;
    });

    const profit = totalJual - totalModal;
    const fmt    = n => 'Rp ' + n.toLocaleString('id-ID');
    const ring   = document.getElementById('ringkasan');
    if (ring) {
        ring.style.display = 'block';
        document.getElementById('r-jual').textContent  = fmt(totalJual);
        document.getElementById('r-modal').textContent = fmt(totalModal);
        const rp = document.getElementById('r-profit');
        rp.textContent  = fmt(profit);
        rp.style.color  = profit >= 0 ? '#15803d' : '#dc2626';
    }
}

function renderFormTambah(warungId = null, defaultSalesId = null) {
    const isKunjungan = warungId !== null;
    produkIndex = 0;

    document.getElementById('sidebar-body').innerHTML = `
    <form id="form-warung">
        <input type="hidden" id="f-lat" name="latitude">
        <input type="hidden" id="f-lng" name="longitude">
        <input type="hidden" name="warung_id" value="${warungId || ''}">

        <div style="margin-bottom:1rem;">
            <label class="label">Tanggal & Jam Kunjungan *</label>
            <input type="datetime-local" id="f-tanggal" name="tanggal_kunjungan" required class="input-field">
        </div>

        ${!isKunjungan ? `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1rem;">
            <div>
                <label class="label">Nama Pemilik *</label>
                <input type="text" name="nama_pemilik" required class="input-field" placeholder="Pak Budi">
            </div>
            <div>
                <label class="label">Nama Warung</label>
                <input type="text" name="nama_warung" class="input-field" placeholder="Warung Berkah">
            </div>
        </div>

        <div style="margin-bottom:1rem;">
            <label class="label">
                Alamat
                <span id="lbl-detecting" class="hidden" style="color:#6366f1;font-weight:400;text-transform:none;letter-spacing:0;"> ⟳ mendeteksi...</span>
            </label>
            <textarea id="f-alamat" name="alamat" rows="2" class="input-field" style="resize:none;" placeholder="Terdeteksi otomatis dari peta..."></textarea>
        </div>

        <div style="margin-bottom:1rem;">
            <label class="label">Status Warung</label>
            <div style="display:flex;gap:1.25rem;">
                ${['aktif','tutup','pindah'].map(s => `
                <label style="display:flex;align-items:center;gap:.375rem;cursor:pointer;font-size:.875rem;color:#374151;">
                    <input type="radio" name="status" value="${s}" ${s==='aktif'?'checked':''}>
                    <span style="text-transform:capitalize;">${s}</span>
                </label>`).join('')}
            </div>
        </div>
        ` : ''}

        <div style="margin-bottom:1rem;">
            <label class="label">Sales *</label>
            <select name="sales_id" required class="input-field">
                <option value="">-- Pilih Sales --</option>
                ${SALES_DATA.map(s => `<option value="${s.id}" ${s.id == defaultSalesId ? 'selected' : ''}>${s.name}</option>`).join('')}
            </select>
        </div>

        <div style="margin-bottom:1rem;border-top:1px solid #e5e7eb;padding-top:1rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
                <label class="label" style="margin:0;">Produk Titipan *</label>
                <button type="button" onclick="tambahProduk()"
                        style="font-size:.75rem;background:#eef2ff;color:#4f46e5;padding:.25rem .625rem;border-radius:.375rem;border:none;cursor:pointer;font-weight:500;">
                    + Tambah Produk
                </button>
            </div>
            <div id="produk-wrap" style="display:flex;flex-direction:column;gap:.625rem;"></div>

            {{-- Ringkasan Profit --}}
            <div id="ringkasan" style="display:none;margin-top:.75rem;background:#f9fafb;border-radius:.5rem;padding:.75rem;font-size:.8125rem;">
                <div style="display:flex;justify-content:space-between;color:#6b7280;margin-bottom:.25rem;">
                    <span>Total Terjual</span><span id="r-jual" style="font-weight:500;">Rp 0</span>
                </div>
                <div style="display:flex;justify-content:space-between;color:#6b7280;margin-bottom:.25rem;">
                    <span>Total Modal</span><span id="r-modal" style="font-weight:500;">Rp 0</span>
                </div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid #e5e7eb;padding-top:.375rem;margin-top:.375rem;font-weight:600;">
                    <span>Estimasi Profit</span><span id="r-profit">Rp 0</span>
                </div>
            </div>
        </div>

        ${!isKunjungan ? `
        <div style="margin-bottom:1rem;">
            <label class="label">Foto Warung</label>
            <input type="file" name="foto" accept="image/*"
                   style="width:100%;font-size:.8125rem;color:#6b7280;">
        </div>
        ` : ''}

        <div style="margin-bottom:1.25rem;">
            <label class="label">Catatan</label>
            <textarea name="catatan" rows="2" class="input-field" style="resize:none;" placeholder="Catatan tambahan..."></textarea>
        </div>

        <button type="submit" id="btn-simpan" class="btn-primary">
            ${isKunjungan ? 'Simpan Kunjungan' : 'Simpan Warung & Kunjungan'}
        </button>
    </form>`;

    setWaktuSekarang('f-tanggal');
    tambahProduk(); // langsung tambah 1 baris produk

    document.getElementById('form-warung').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-simpan');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        const wId  = this.querySelector('[name=warung_id]').value;
        const url  = wId ? `/maps/warung/${wId}/kunjungan` : '/maps/warung';
        const body = new FormData(this);

        try {
            const res  = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                body
            });
            const data = await res.json();

            if (data.success) {
                if (!wId && data.warung) {
                    const w  = data.warung;
                    const m  = buatMarker(w.latitude, w.longitude, w.status);
                    m.addTo(map).on('click', () => bukaDetailWarung({
                        ...w, sales: w.sales?.name, sales_id: w.sales_id
                    }));
                    markers[w.id] = m;
                }
                tutupSidebar();
                tampilToast('✅ Data berhasil disimpan!');
            } else {
                tampilToast('❌ Gagal menyimpan. Cek data kembali.', true);
            }
        } catch(err) {
            tampilToast('❌ Error: ' + err.message, true);
        }

        btn.disabled = false;
        btn.textContent = isKunjungan ? 'Simpan Kunjungan' : 'Simpan Warung & Kunjungan';
    });
}

function bukaDetailWarung(w) {
    bukaSidebar('detail', w.nama_warung || w.nama_pemilik, w.alamat || 'Tap untuk lihat detail');

    const statusBadge = {
        aktif:  'background:#dcfce7;color:#15803d',
        tutup:  'background:#fee2e2;color:#dc2626',
        pindah: 'background:#fef9c3;color:#a16207',
    };

    document.getElementById('sidebar-body').innerHTML = `
        ${w.foto ? `<img src="${w.foto}" style="width:100%;height:10rem;object-fit:cover;border-radius:.5rem;margin-bottom:1rem;">` : ''}

        <div style="display:flex;flex-direction:column;gap:.625rem;font-size:.875rem;margin-bottom:1.25rem;">
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#6b7280;">Pemilik</span>
                <span style="font-weight:500;color:#111827;">${w.nama_pemilik}</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#6b7280;">Sales</span>
                <span style="font-weight:500;color:#111827;">${w.sales || '-'}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;">
                <span style="color:#6b7280;white-space:nowrap;">Alamat</span>
                <span style="font-weight:500;color:#111827;text-align:right;">${w.alamat || '-'}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#6b7280;">Status</span>
                <span style="padding:.2rem .625rem;border-radius:9999px;font-size:.75rem;font-weight:600;${statusBadge[w.status]}">${w.status}</span>
            </div>
            ${w.catatan ? `
            <div style="border-top:1px solid #f3f4f6;padding-top:.625rem;">
                <p style="font-size:.75rem;color:#9ca3af;margin-bottom:.25rem;">Catatan</p>
                <p style="color:#374151;">${w.catatan}</p>
            </div>` : ''}
        </div>

        <div style="display:flex;flex-direction:column;gap:.5rem;margin-bottom:1.25rem;">
            <button onclick="bukaFormKunjunganBaru(${w.id}, '${w.nama_pemilik}', ${w.sales_id})"
                    class="btn-primary">+ Tambah Kunjungan</button>
            <button onclick="lihatRiwayat(${w.id})"
                    class="btn-secondary" style="width:100%;text-align:center;">📋 Riwayat Kunjungan</button>
        </div>

        <div id="riwayat-wrap"></div>
    `;

    // Zoom ke warung
    map.setView([w.latitude, w.longitude], 16, { animate: true });
}

function bukaFormKunjunganBaru(warungId, namaPemilik, salesId) {
    bukaSidebar('kunjungan', `+ Kunjungan: ${namaPemilik}`, 'Warung existing');
    renderFormTambah(warungId, salesId);
}

async function lihatRiwayat(warungId) {
    const wrap = document.getElementById('riwayat-wrap');
    wrap.innerHTML = '<p style="text-align:center;color:#9ca3af;font-size:.875rem;padding:1rem;">Memuat riwayat...</p>';

    const res  = await fetch(`/maps/warung/${warungId}/riwayat`);
    const data = await res.json();

    if (!data.length) {
        wrap.innerHTML = '<p style="text-align:center;color:#9ca3af;font-size:.875rem;padding:1rem;">Belum ada riwayat kunjungan.</p>';
        return;
    }

    wrap.innerHTML = `
        <h4 style="font-size:.75rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.625rem;">Riwayat Kunjungan (${data.length})</h4>
        ${data.map(k => `
        <div style="border:1px solid #e5e7eb;border-radius:.5rem;padding:.75rem;margin-bottom:.625rem;font-size:.8125rem;">
            <div style="display:flex;justify-content:space-between;color:#6b7280;margin-bottom:.375rem;">
                <span>🕐 ${k.tanggal_kunjungan}</span>
                <span>👤 ${k.sales || '-'}</span>
            </div>
            ${k.produk.map(p => `
            <div style="display:flex;justify-content:space-between;padding:.25rem 0;border-top:1px solid #f3f4f6;color:#374151;">
                <span>${p.nama}</span>
                <span style="color:#6b7280;">${p.terjual}/${p.stok_masuk} terjual</span>
            </div>`).join('')}
            <div style="display:flex;justify-content:space-between;border-top:1px solid #e5e7eb;margin-top:.375rem;padding-top:.375rem;font-weight:600;">
                <span style="color:#6b7280;">Profit</span>
                <span style="color:${k.profit >= 0 ? '#15803d' : '#dc2626'};">Rp ${Number(k.profit).toLocaleString('id-ID')}</span>
            </div>
            ${k.catatan ? `<p style="margin-top:.375rem;font-size:.75rem;color:#9ca3af;">💬 ${k.catatan}</p>` : ''}
        </div>`).join('')}
    `;
}

function locateMe() {
    if (!navigator.geolocation) { tampilToast('Browser tidak support geolokasi', true); return; }
    navigator.geolocation.getCurrentPosition(
        pos => map.setView([pos.coords.latitude, pos.coords.longitude], 16, { animate: true }),
        ()  => tampilToast('Gagal mendapatkan lokasi. Izinkan akses lokasi.', true)
    );
}

function tampilToast(pesan, error = false) {
    const el = document.getElementById('toast');
    el.textContent    = pesan;
    el.style.background = error ? '#dc2626' : '#111827';
    el.style.transform  = 'translateX(-50%) translateY(0)';
    setTimeout(() => { el.style.transform = 'translateX(-50%) translateY(6rem)'; }, 3000);
}
</script>

</body>
</html>