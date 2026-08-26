<?php

namespace App\Http\Controllers;

use App\Models\Bast;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BastController extends Controller
{
    // === Menampilkan Form Pembuatan BAST ===
    public function create(Offer $offer)
    {
        // Cek apakah BAST sudah ada
        if ($offer->bast) {
             return redirect()->back()->with('error', 'BAST untuk penawaran ini sudah dibuat.');
        }

        // Generate Nomor Surat Otomatis
        $bulanRomawi = ["", "I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII"];
        $nextId = Bast::max('id') + 1;
        $noSurat = sprintf('%04d/SK/TGI-1/%s/%s', $nextId, $bulanRomawi[date('n')], date('Y'));

        // Default Pihak Pertama
        $defaultPihakPertama = [
            'nama' => 'Samsu Rizal',
            'jabatan' => 'General Manager'
        ];

        return view('bast.create', [
            'offer' => $offer,
            'noSurat' => $noSurat,
            'pihakPertama' => $defaultPihakPertama
        ]);
    }

    // === Menyimpan Data BAST ===
    public function store(Request $request, Offer $offer)
    {
        // 1. Validasi Input
        $request->validate([
            'no_surat' => 'required|string',
            'pihak_pertama_nama' => 'required|string',
            'pihak_pertama_jabatan' => 'required|string',
            'pihak_kedua_nama' => 'required|string',
            'pihak_kedua_jabatan' => 'nullable|string',
            'pihak_kedua_perusahaan' => 'nullable|string',
            'pihak_kedua_alamat' => 'required|string',
            'deskripsi_pekerjaan' => 'required|string',
            'before_images' => 'required|array|min:1',
            'before_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'after_images' => 'required|array|min:1',
            'after_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Proses Upload Gambar Before
        $beforePaths = [];
        if($request->hasFile('before_images')) {
            foreach($request->file('before_images') as $file) {
                // Simpan di folder public/bast-images
                $path = $file->store('bast-images', 'public');
                $beforePaths[] = $path;
            }
        }

        // 3. Proses Upload Gambar After
        $afterPaths = [];
        if($request->hasFile('after_images')) {
            foreach($request->file('after_images') as $file) {
                $path = $file->store('bast-images', 'public');
                $afterPaths[] = $path;
            }
        }

        // 4. Simpan ke Database
        $bast = Bast::create([
            'offer_id' => $offer->id,
            'no_surat' => $request->no_surat,
            'tanggal_bast' => date('Y-m-d'), // Otomatis hari ini

            'pihak_pertama_nama' => $request->pihak_pertama_nama,
            'pihak_pertama_jabatan' => $request->pihak_pertama_jabatan,

            'pihak_kedua_nama' => $request->pihak_kedua_nama,
            'pihak_kedua_jabatan' => $request->pihak_kedua_jabatan ?? '-',
            'pihak_kedua_perusahaan' => $request->pihak_kedua_perusahaan ?? '-',
            'pihak_kedua_alamat' => $request->pihak_kedua_alamat,
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,

            // PENTING: Nama kolom harus konsisten (menggunakan _image_path sesuai store)
            'before_image_path' => json_encode($beforePaths),
            'after_image_path' => json_encode($afterPaths),
        ]);

        return redirect()->route('bast.index')->with('success', 'BAST berhasil disimpan!');
    }

    // === Method Print (YANG DIPERBAIKI) ===
    public function print($id)
    {
        $bast = \App\Models\Bast::findOrFail($id);

        // --- PERBAIKAN UTAMA ---
        // 1. Gunakan nama kolom yang benar: 'before_image_path' (bukan foto_before)
        // 2. Tambahkan logika keamanan decoding JSON

        // Proses Gambar Before
        $rawBefore = $bast->before_image_path; // <--- Ganti ke nama kolom yang benar
        if (is_array($rawBefore)) {
            $beforeImages = $rawBefore;
        } else {
            $decoded = json_decode($rawBefore);
            // Jika berhasil decode jadi array, pakai itu. Jika gagal/null, jadikan array kosong.
            $beforeImages = is_array($decoded) ? $decoded : [];
            // Fallback: Jika ternyata string tunggal (bukan array json), bungkus jadi array
            if (empty($beforeImages) && !empty($rawBefore) && is_string($rawBefore)) {
                $beforeImages = [$rawBefore];
            }
        }

        // Proses Gambar After
        $rawAfter = $bast->after_image_path; // <--- Ganti ke nama kolom yang benar
        if (is_array($rawAfter)) {
            $afterImages = $rawAfter;
        } else {
            $decoded = json_decode($rawAfter);
            $afterImages = is_array($decoded) ? $decoded : [];
            if (empty($afterImages) && !empty($rawAfter) && is_string($rawAfter)) {
                $afterImages = [$rawAfter];
            }
        }

        return view('bast.print', compact('bast', 'beforeImages', 'afterImages'));
    }

    // === Menampilkan Daftar Histori BAST ===
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Bast::query()->with('offer');

        if ($search) {
            $query->where('no_surat', 'like', '%' . $search . '%')
                  ->orWhereHas('offer', function ($q) use ($search) {
                      $q->where('nama_klien', 'like', '%' . $search . '%');
                  });
        }

        $basts = $query->latest()->paginate(15);

        return view('bast.index', compact('basts', 'search'));
    }

    // === Menampilkan Detail BAST ===
    public function show(Bast $bast)
    {
        $bast->load('offer');

        // Logika decode yang sama amannya dengan method print
        $beforeImages = json_decode($bast->before_image_path);
        if (!is_array($beforeImages)) {
            $beforeImages = $bast->before_image_path ? [$bast->before_image_path] : [];
        }

        $afterImages = json_decode($bast->after_image_path);
        if (!is_array($afterImages)) {
            $afterImages = $bast->after_image_path ? [$bast->after_image_path] : [];
        }

        return view('bast.show', compact('bast', 'beforeImages', 'afterImages'));
    }

    // === Menghapus BAST ===
    public function destroy(Bast $bast)
    {
        // 1. Hapus File Before
        $beforeImages = json_decode($bast->before_image_path);
        if (is_array($beforeImages)) {
            foreach ($beforeImages as $path) {
                if($path) Storage::disk('public')->delete($path);
            }
        }

        // 2. Hapus File After
        $afterImages = json_decode($bast->after_image_path);
        if (is_array($afterImages)) {
            foreach ($afterImages as $path) {
                if($path) Storage::disk('public')->delete($path);
            }
        }

        // 3. Hapus Record DB
        $bast->delete();

        return redirect()->route('bast.index')->with('success', 'BAST berhasil dihapus!');
    }
}