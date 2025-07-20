<?php

namespace App\Http\Controllers;

use App\Models\BelanjaModal;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class BelanjaModalController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = BelanjaModal::with('user');

            // Filter by date range
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $query->whereBetween('tanggal_belanja', [$request->tanggal_mulai, $request->tanggal_selesai]);
            }

            // Filter by kategori
            if ($request->has('kategori')) {
                $query->where('kategori', $request->kategori);
            }

            // Filter by metode pembayaran
            if ($request->has('metode_pembayaran')) {
                $query->where('metode_pembayaran', $request->metode_pembayaran);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $data = $query->orderBy('tanggal_belanja', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data belanja modal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tanggal_belanja' => 'required|date',
            'kategori' => 'required|in:listrik,bensin,benih,rockwool,pupuk,lain-lain',
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string',
            'nama_toko' => 'nullable|string|max:255',
            'alamat_toko' => 'nullable|string',
            'metode_pembayaran' => 'nullable|in:tunai,transfer,kredit',
            'bukti_pembayaran' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = BelanjaModal::create([
                'tanggal_belanja' => $request->tanggal_belanja,
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'satuan' => $request->satuan ?? 'rupiah',
                'nama_toko' => $request->nama_toko,
                'alamat_toko' => $request->alamat_toko,
                'metode_pembayaran' => $request->metode_pembayaran ?? 'tunai',
                'bukti_pembayaran' => $request->bukti_pembayaran,
                'keterangan' => $request->keterangan,
                'user_id' => $request->auth->id
            ]);

            $data->load('user');

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'create',
                'table_name' => 'belanja_modal',
                'record_id' => $data->id,
                'details' => 'Created belanja modal: ' . $data->kategori . ' - ' . $data->deskripsi . ' - Rp ' . number_format($data->jumlah),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data belanja modal berhasil disimpan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data belanja modal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = BelanjaModal::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data belanja modal tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tanggal_belanja' => 'required|date',
            'kategori' => 'required|in:listrik,bensin,benih,rockwool,pupuk,lain-lain',
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string',
            'nama_toko' => 'nullable|string|max:255',
            'alamat_toko' => 'nullable|string',
            'metode_pembayaran' => 'nullable|in:tunai,transfer,kredit',
            'bukti_pembayaran' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = BelanjaModal::findOrFail($id);
            $oldData = $data->toArray();

            $data->update([
                'tanggal_belanja' => $request->tanggal_belanja,
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'satuan' => $request->satuan ?? 'rupiah',
                'nama_toko' => $request->nama_toko,
                'alamat_toko' => $request->alamat_toko,
                'metode_pembayaran' => $request->metode_pembayaran ?? 'tunai',
                'bukti_pembayaran' => $request->bukti_pembayaran,
                'keterangan' => $request->keterangan
            ]);

            $data->load('user');

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'update',
                'table_name' => 'belanja_modal',
                'record_id' => $data->id,
                'details' => 'Updated belanja modal: ' . json_encode(['old' => $oldData, 'new' => $data->toArray()]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data belanja modal berhasil diupdate',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data belanja modal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = BelanjaModal::findOrFail($id);
            $dataInfo = $data->kategori . ' - ' . $data->deskripsi . ' - Rp ' . number_format($data->jumlah);
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'delete',
                'table_name' => 'belanja_modal',
                'record_id' => $id,
                'details' => 'Deleted belanja modal: ' . $dataInfo,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data belanja modal berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data belanja modal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $query = BelanjaModal::query();

            // Filter by date range
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $query->whereBetween('tanggal_belanja', [$request->tanggal_mulai, $request->tanggal_selesai]);
            }

            $summary = [
                'total_pengeluaran' => $query->sum('jumlah'),
                'total_transaksi' => $query->count(),
                'rata_rata_pengeluaran' => $query->avg('jumlah'),
                'pengeluaran_per_kategori' => $query->selectRaw('
                    kategori,
                    SUM(jumlah) as total_pengeluaran,
                    COUNT(*) as total_transaksi,
                    AVG(jumlah) as rata_rata_pengeluaran
                ')
                ->groupBy('kategori')
                ->get(),
                'pengeluaran_per_metode_pembayaran' => $query->selectRaw('
                    metode_pembayaran,
                    SUM(jumlah) as total_pengeluaran,
                    COUNT(*) as total_transaksi
                ')
                ->groupBy('metode_pembayaran')
                ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary belanja modal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getKategori()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'listrik',
                'bensin',
                'benih',
                'rockwool',
                'pupuk',
                'lain-lain'
            ]
        ]);
    }
}