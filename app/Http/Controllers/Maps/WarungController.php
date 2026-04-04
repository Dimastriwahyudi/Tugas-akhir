<?php

namespace App\Http\Controllers\Maps;

use App\Http\Controllers\Controller;
use App\Models\Warung;
use App\Models\Kunjungan;
use App\Models\KunjunganProduk;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WarungController extends Controller
{

    public function index()
    {
        $warungs = Warung::with(['sales:id,name'])
                    ->select('id','nama_pemilik','nama_warung','latitude',
                            'longitude','status','alamat','foto','catatan','sales_id')
                    ->get()
                    ->map(fn($w) => [
                        'id'           => $w->id,
                        'nama_pemilik' => $w->nama_pemilik,
                        'nama_warung'  => $w->nama_warung,
                        'latitude'     => (float) $w->latitude,
                        'longitude'    => (float) $w->longitude,
                        'status'       => $w->status,
                        'alamat'       => $w->alamat,
                        'foto'         => $w->foto ? asset('storage/' . $w->foto) : null,
                        'catatan'      => $w->catatan,
                        'sales'        => $w->sales?->name,
                        'sales_id'     => $w->sales_id,
                    ]);

        $produk = Produk::where('is_active', true)
                    ->select('id','nama','harga_jual','harga_modal','satuan')
                    ->orderBy('nama')
                    ->get();

        $sales = User::role('sales')
                    ->select('id','name')
                    ->orderBy('name')
                    ->get();

        return view('maps.index', compact('warungs', 'produk', 'sales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pemilik'         => 'required|string|max:255',
            'nama_warung'          => 'nullable|string|max:255',
            'latitude'             => 'required|numeric',
            'longitude'            => 'required|numeric',
            'alamat'               => 'nullable|string',
            'status'               => 'required|in:aktif,tutup,pindah',
            'catatan'              => 'nullable|string',
            'sales_id'             => 'required|exists:users,id',
            'foto'                 => 'nullable|image|max:2048',
            'tanggal_kunjungan'    => 'required|date',
            'produk'               => 'required|array|min:1',
            'produk.*.id'          => 'required|exists:produk,id',
            'produk.*.stok_masuk'  => 'required|integer|min:1',
            'produk.*.stok_keluar' => 'required|integer|min:0',
            'produk.*.harga_jual'  => 'required|numeric|min:0',
            'produk.*.harga_modal' => 'required|numeric|min:0',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('warung', 'public');
        }

        $warung = Warung::create([
            'nama_pemilik' => $request->nama_pemilik,
            'nama_warung'  => $request->nama_warung,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'alamat'       => $request->alamat,
            'status'       => $request->status,
            'catatan'      => $request->catatan,
            'sales_id'     => $request->sales_id,
            'foto'         => $fotoPath,
        ]);

        $kunjungan = $this->simpanKunjungan($warung, $request);

        return response()->json([
            'success'   => true,
            'warung'    => $warung->load('sales'),
            'kunjungan' => $kunjungan,
        ]);
    }

    public function tambahKunjungan(Request $request, Warung $warung)
    {
        $request->validate([
            'tanggal_kunjungan'    => 'required|date',
            'sales_id'             => 'nullable|exists:users,id',
            'catatan'              => 'nullable|string',
            'produk'               => 'required|array|min:1',
            'produk.*.id'          => 'required|exists:produk,id',
            'produk.*.stok_masuk'  => 'required|integer|min:1',
            'produk.*.stok_keluar' => 'required|integer|min:0',
            'produk.*.harga_jual'  => 'required|numeric|min:0',
            'produk.*.harga_modal' => 'required|numeric|min:0',
        ]);

        $kunjungan = $this->simpanKunjungan($warung, $request);

        return response()->json(['success' => true, 'kunjungan' => $kunjungan]);
    }

    public function riwayat(Warung $warung)
    {
        $kunjungan = $warung->kunjungan()
            ->with(['produk.produk', 'sales'])
            ->latest('tanggal_kunjungan')
            ->get()
            ->map(fn($k) => [
                'id'                => $k->id,
                'tanggal_kunjungan' => $k->tanggal_kunjungan->format('d/m/Y H:i'),
                'sales'             => $k->sales?->name,
                'total_harga_jual'  => $k->total_harga_jual,
                'total_modal'       => $k->total_modal,
                'profit'            => $k->profit,
                'catatan'           => $k->catatan,
                'produk'            => $k->produk->map(fn($p) => [
                    'nama'         => $p->produk?->nama,
                    'stok_masuk'   => $p->stok_masuk,
                    'stok_keluar'  => $p->stok_keluar,
                    'terjual'      => $p->stok_masuk - $p->stok_keluar,
                    'harga_jual'   => $p->harga_jual,
                    'harga_modal'  => $p->harga_modal,
                    'profit'       => ($p->stok_masuk - $p->stok_keluar) * ($p->harga_jual - $p->harga_modal),
                ]),
            ]);

        return response()->json($kunjungan);
    }

    public function update(Request $request, Warung $warung)
    {
        $request->validate([
            'nama_pemilik' => 'required|string|max:255',
            'nama_warung'  => 'nullable|string|max:255',
            'status'       => 'required|in:aktif,tutup,pindah',
            'catatan'      => 'nullable|string',
            'foto'         => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($warung->foto) Storage::disk('public')->delete($warung->foto);
            $warung->foto = $request->file('foto')->store('warung', 'public');
        }

        $warung->update($request->only(['nama_pemilik', 'nama_warung', 'status', 'catatan']));

        return response()->json(['success' => true]);
    }

    private function simpanKunjungan(Warung $warung, Request $request): Kunjungan
    {
        $totalJual  = 0;
        $totalModal = 0;

        foreach ($request->produk as $p) {
            $terjual     = $p['stok_masuk'] - $p['stok_keluar'];
            $totalJual  += $terjual * $p['harga_jual'];
            $totalModal += $terjual * $p['harga_modal'];
        }

        $kunjungan = Kunjungan::create([
            'warung_id'         => $warung->id,
            'sales_id'          => $request->sales_id ?? $warung->sales_id,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'total_harga_jual'  => $totalJual,
            'total_modal'       => $totalModal,
            'profit'            => $totalJual - $totalModal,
            'catatan'           => $request->catatan,
        ]);

        foreach ($request->produk as $p) {
            KunjunganProduk::create([
                'kunjungan_id' => $kunjungan->id,
                'produk_id'    => $p['id'],
                'stok_masuk'   => $p['stok_masuk'],
                'stok_keluar'  => $p['stok_keluar'],
                'harga_jual'   => $p['harga_jual'],
                'harga_modal'  => $p['harga_modal'],
            ]);
        }

        return $kunjungan;
    }
}
