<?php

namespace App\Http\Controllers;

use App\Models\Skp;
use App\Models\Offer;
use Illuminate\Http\Request;

class SkpController extends Controller
{
    // === 1. FORMULIR PEMBUATAN SKP ===
    public function create(Offer $offer)
    {
        // Cek jika SKP sudah pernah dibuat
        if (Skp::where('offer_id', $offer->id)->exists()) {
            return redirect()->back()->with('error', 'SKP untuk penawaran ini sudah dibuat.');
        }

        $bulanRomawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");

        // 1. Generate Nomor Surat SKP (Baru)
        $nextId = Skp::max('id') + 1;
        $noSurat = sprintf('00%d/SPK/TGI-1/%s/%s', $nextId, $bulanRomawi[date('n')], date('Y'));

        // 2. Generate Nomor Surat Penawaran (Ref ID) Lengkap
        // Asumsi format penawaran sama: 00{ID}/SP/TGI-1/{BULAN}/{TAHUN}
        // Kita ambil bulan/tahun dari created_at penawaran agar akurat
        $bulanOffer = $bulanRomawi[$offer->created_at->format('n')];
        $tahunOffer = $offer->created_at->format('Y');
        $refNoPenawaran = sprintf('00%d/SP/TGI-1/%s/%s', $offer->id, $bulanOffer, $tahunOffer);

        $pihakDua = [
            'nama' => 'Samsu Rizal',
            'jabatan' => 'General Manager',
            'perusahaan' => 'PT. Tasniem Gerai Inspirasi',
            'alamat' => 'Ruko KDA Junction Blok C No 8-9 Batam Center - Batam'
        ];

        return view('skp.create', [
            'offer' => $offer,
            'noSurat' => $noSurat,
            'refNoPenawaran' => $refNoPenawaran, // Kirim variabel baru ini
            'pihakDua' => $pihakDua
        ]);
    }

    // === 2. SIMPAN DATA SKP ===
    public function store(Request $request, Offer $offer)
    {
        // 1. Validasi
        $request->validate([
            'no_surat' => 'required',
            // 'tanggal_surat' dihapus dari required jika tidak ada di form,
            // atau kita set default di bawah.
            'pihak_satu_nama' => 'required',
            'pihak_satu_alamat' => 'required',
            'judul_pekerjaan' => 'required',
            'lokasi_pekerjaan' => 'required',
            'durasi_hari' => 'required', // Pastikan di blade ada name="durasi_hari"
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'nilai_pekerjaan' => 'required|numeric',
            'diskon' => 'nullable|numeric',
            'termin_keterangan' => 'nullable|array',
            'termin_tanggal' => 'nullable|array',
            'termin_jumlah' => 'nullable|array',
        ]);

        // 2. Proses Termin Pembayaran
        $terminPembayaran = [];
        if ($request->has('termin_keterangan')) {
            foreach ($request->termin_keterangan as $index => $ket) {
                if (!empty($ket)) {
                    $terminPembayaran[] = [
                        'keterangan' => $ket,
                        'tanggal' => $request->termin_tanggal[$index] ?? null,
                        'jumlah' => $request->termin_jumlah[$index] ?? 0
                    ];
                }
            }
        }

        // 3. Simpan Data
        $skp = Skp::create([
            'offer_id' => $offer->id,
            'no_surat' => $request->no_surat,
            'tanggal_surat' => $request->tanggal_surat ?? now(), // Gunakan tanggal sekarang jika tidak ada di form
            'pihak_satu_nama' => $request->pihak_satu_nama,
            'pihak_satu_jabatan' => $request->pihak_satu_jabatan ?? '-',
            'pihak_satu_perusahaan' => $request->pihak_satu_perusahaan ?? '-',
            'pihak_satu_alamat' => $request->pihak_satu_alamat,
            'pihak_dua_nama' => $request->pihak_dua_nama,
            'pihak_dua_jabatan' => $request->pihak_dua_jabatan ?? 'General Manager', // Default dari create method
            'pihak_dua_perusahaan' => $request->pihak_dua_perusahaan ?? 'PT. Tasniem Gerai Inspirasi',
            'pihak_dua_alamat' => $request->pihak_dua_alamat,
            'judul_pekerjaan' => $request->judul_pekerjaan,
            'lokasi_pekerjaan' => $request->lokasi_pekerjaan,
            'durasi_hari' => $request->durasi_hari,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'nilai_pekerjaan' => $request->nilai_pekerjaan,
            'diskon' => $request->diskon ?? 0,
            'termin_pembayaran' => $terminPembayaran, // Pastikan Model Skp sudah cast ini ke 'array' atau 'json'
        ]);

        return redirect()->route('skp.index')->with('success', 'SKP berhasil dibuat!');
    }

    public function update(Request $request, Skp $skp)
    {
        $request->validate([
            'no_surat' => 'required',
            'tanggal_surat' => 'required|date',
            'pihak_satu_nama' => 'required',
            'pihak_satu_alamat' => 'required',
            'judul_pekerjaan' => 'required',
            'lokasi_pekerjaan' => 'required',
            'durasi_hari' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            // Tambahkan validasi nominal di sini agar aman
            'nilai_pekerjaan' => 'required|numeric',
            'diskon' => 'nullable|numeric',
            'termin_keterangan' => 'nullable|array',
            'termin_tanggal' => 'nullable|array',
            'termin_jumlah' => 'nullable|array',
        ]);

        $terminPembayaran = [];
        if ($request->has('termin_keterangan')) {
            foreach ($request->termin_keterangan as $index => $ket) {
                if (!empty($ket)) {
                    $terminPembayaran[] = [
                        'keterangan' => $ket,
                        'tanggal' => $request->termin_tanggal[$index] ?? null,
                        'jumlah' => $request->termin_jumlah[$index] ?? 0
                    ];
                }
            }
        }

        $skp->update([
            'no_surat' => $request->no_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'pihak_satu_nama' => $request->pihak_satu_nama,
            'pihak_satu_jabatan' => $request->pihak_satu_jabatan ?? '-',
            'pihak_satu_perusahaan' => $request->pihak_satu_perusahaan ?? '-',
            'pihak_satu_alamat' => $request->pihak_satu_alamat,
            'pihak_dua_nama' => $request->pihak_dua_nama,
            'pihak_dua_jabatan' => $request->pihak_dua_jabatan ?? '-',
            'pihak_dua_perusahaan' => $request->pihak_dua_perusahaan ?? '-',
            'pihak_dua_alamat' => $request->pihak_dua_alamat,
            'judul_pekerjaan' => $request->judul_pekerjaan,
            'lokasi_pekerjaan' => $request->lokasi_pekerjaan,
            'durasi_hari' => $request->durasi_hari,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            // PERBAIKAN: Tambahkan field ini agar nilai pekerjaan & diskon terupdate
            'nilai_pekerjaan' => $request->nilai_pekerjaan,
            'diskon' => $request->diskon ?? 0,
            'termin_pembayaran' => $terminPembayaran,
        ]);

        return redirect()->route('skp.index')->with('success', 'SKP berhasil diperbarui!');
    }
    // ... Method index, show, destroy, edit, update lainnya TETAP SAMA ...

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Skp::query()->with('offer');

        if ($search) {
            $query->where('no_surat', 'like', '%' . $search . '%')
                ->orWhere('pihak_satu_nama', 'like', '%' . $search . '%')
                ->orWhere('judul_pekerjaan', 'like', '%' . $search . '%');
        }

        $skps = $query->latest()->paginate(15);
        return view('skp.index', ['skps' => $skps, 'search' => $search]);
    }

    public function print($id)
    {
        $skp = \App\Models\Skp::with('offer')->findOrFail($id);

        // Decode JSON termin pembayaran jika perlu (tergantung cara simpan)
        if (is_string($skp->termin_pembayaran)) {
            $skp->termin_pembayaran = json_decode($skp->termin_pembayaran, true);
        }

        return view('skp.print', compact('skp'));
    }


    public function show(Skp $skp)
    {
        $skp->load('offer');
        return view('skp.show', ['skp' => $skp]);
    }

    public function destroy(Skp $skp)
    {
        $skp->delete();
        return redirect()->route('skp.index')->with('success', 'Surat Perintah Kerja berhasil dihapus!');
    }

    public function edit(Skp $skp)
    {
        $skp->load('offer');
        return view('skp.edit', ['skp' => $skp, 'offer' => $skp->offer]);
    }
}
