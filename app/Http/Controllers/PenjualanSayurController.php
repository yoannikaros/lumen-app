<?php

namespace App\Http\Controllers;

use App\Models\PenjualanSayur;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PenjualanSayurController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = PenjualanSayur::with('user');

            // Filter by date range
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $query->whereBetween('tanggal_penjualan', [$request->tanggal_mulai, $request->tanggal_selesai]);
            }

            // Filter by tipe pembeli
            if ($request->has('tipe_pembeli')) {
                $query->where('tipe_pembeli', $request->tipe_pembeli);
            }

            // Filter by jenis sayur
            if ($request->has('jenis_sayur')) {
                $query->where('jenis_sayur', 'LIKE', '%' . $request->jenis_sayur . '%');
            }

            // Filter by status pembayaran
            if ($request->has('status_pembayaran')) {
                $query->where('status_pembayaran', $request->status_pembayaran);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $data = $query->orderBy('tanggal_penjualan', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penjualan sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tanggal_penjualan' => 'required|date',
            'nama_pembeli' => 'required|string|max:255',
            'tipe_pembeli' => 'required|in:restoran,hotel,pasar,individu,lainnya',
            'alamat_pembeli' => 'nullable|string',
            'jenis_sayur' => 'required|string|max:100',
            'jumlah_kg' => 'required|numeric|min:0',
            'harga_per_kg' => 'required|numeric|min:0',
            'metode_pembayaran' => 'nullable|in:tunai,transfer,kredit',
            'status_pembayaran' => 'nullable|in:lunas,belum_lunas,cicilan',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $totalHarga = $request->jumlah_kg * $request->harga_per_kg;

            $data = PenjualanSayur::create([
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'nama_pembeli' => $request->nama_pembeli,
                'tipe_pembeli' => $request->tipe_pembeli,
                'alamat_pembeli' => $request->alamat_pembeli,
                'jenis_sayur' => $request->jenis_sayur,
                'jumlah_kg' => $request->jumlah_kg,
                'harga_per_kg' => $request->harga_per_kg,
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $request->metode_pembayaran ?? 'tunai',
                'status_pembayaran' => $request->status_pembayaran ?? 'lunas',
                'keterangan' => $request->keterangan,
                'user_id' => $request->auth->id
            ]);

            $data->load('user');

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'create',
                'table_name' => 'penjualan_sayur',
                'record_id' => $data->id,
                'details' => 'Created penjualan sayur: ' . $data->jenis_sayur . ' - ' . $data->jumlah_kg . 'kg to ' . $data->nama_pembeli,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data penjualan sayur berhasil disimpan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data penjualan sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = PenjualanSayur::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan sayur tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tanggal_penjualan' => 'required|date',
            'nama_pembeli' => 'required|string|max:255',
            'tipe_pembeli' => 'required|in:restoran,hotel,pasar,individu,lainnya',
            'alamat_pembeli' => 'nullable|string',
            'jenis_sayur' => 'required|string|max:100',
            'jumlah_kg' => 'required|numeric|min:0',
            'harga_per_kg' => 'required|numeric|min:0',
            'metode_pembayaran' => 'nullable|in:tunai,transfer,kredit',
            'status_pembayaran' => 'nullable|in:lunas,belum_lunas,cicilan',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = PenjualanSayur::findOrFail($id);
            $oldData = $data->toArray();
            
            $totalHarga = $request->jumlah_kg * $request->harga_per_kg;

            $data->update([
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'nama_pembeli' => $request->nama_pembeli,
                'tipe_pembeli' => $request->tipe_pembeli,
                'alamat_pembeli' => $request->alamat_pembeli,
                'jenis_sayur' => $request->jenis_sayur,
                'jumlah_kg' => $request->jumlah_kg,
                'harga_per_kg' => $request->harga_per_kg,
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $request->metode_pembayaran ?? 'tunai',
                'status_pembayaran' => $request->status_pembayaran ?? 'lunas',
                'keterangan' => $request->keterangan
            ]);

            $data->load('user');

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'update',
                'table_name' => 'penjualan_sayur',
                'record_id' => $data->id,
                'details' => 'Updated penjualan sayur: ' . json_encode(['old' => $oldData, 'new' => $data->toArray()]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data penjualan sayur berhasil diupdate',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data penjualan sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = PenjualanSayur::findOrFail($id);
            $dataInfo = $data->jenis_sayur . ' - ' . $data->jumlah_kg . 'kg to ' . $data->nama_pembeli;
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'delete',
                'table_name' => 'penjualan_sayur',
                'record_id' => $id,
                'details' => 'Deleted penjualan sayur: ' . $dataInfo,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data penjualan sayur berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data penjualan sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $query = PenjualanSayur::query();

            // Filter by date range
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $query->whereBetween('tanggal_penjualan', [$request->tanggal_mulai, $request->tanggal_selesai]);
            }

            $summary = [
                'total_penjualan' => $query->sum('total_harga'),
                'total_kg_terjual' => $query->sum('jumlah_kg'),
                'total_transaksi' => $query->count(),
                'rata_rata_harga_per_kg' => $query->avg('harga_per_kg'),
                'penjualan_per_jenis' => $query->selectRaw('
                    jenis_sayur,
                    SUM(jumlah_kg) as total_kg,
                    SUM(total_harga) as total_pendapatan,
                    COUNT(*) as total_transaksi
                ')
                ->groupBy('jenis_sayur')
                ->get(),
                'penjualan_per_tipe_pembeli' => $query->selectRaw('
                    tipe_pembeli,
                    SUM(total_harga) as total_pendapatan,
                    COUNT(*) as total_transaksi
                ')
                ->groupBy('tipe_pembeli')
                ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary penjualan sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}