<?php

namespace App\Http\Controllers;

use App\Models\JenisPupuk;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class JenisPupukController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = JenisPupuk::query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search by nama pupuk
            if ($request->has('search')) {
                $query->where('nama_pupuk', 'like', '%' . $request->search . '%');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $data = $query->orderBy('nama_pupuk', 'asc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jenis pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_pupuk' => 'required|string|max:100|unique:jenis_pupuk,nama_pupuk',
            'deskripsi' => 'nullable|string',
            'satuan' => 'required|string|max:20',
            'harga_per_satuan' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        try {
            $data = JenisPupuk::create([
                'nama_pupuk' => $request->nama_pupuk,
                'deskripsi' => $request->deskripsi,
                'satuan' => $request->satuan,
                'harga_per_satuan' => $request->harga_per_satuan,
                'status' => $request->status
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'create',
                'table_name' => 'jenis_pupuk',
                'record_id' => $data->id,
                'details' => 'Created jenis pupuk: ' . $data->nama_pupuk . ' - ' . $data->satuan . ' - Rp ' . number_format($data->harga_per_satuan ?? 0),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jenis pupuk berhasil disimpan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jenis pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = JenisPupuk::with('pencatatanPupuk')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis pupuk tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nama_pupuk' => 'required|string|max:100|unique:jenis_pupuk,nama_pupuk,' . $id,
            'deskripsi' => 'nullable|string',
            'satuan' => 'required|string|max:20',
            'harga_per_satuan' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        try {
            $data = JenisPupuk::findOrFail($id);
            $oldData = $data->toArray();

            $data->update([
                'nama_pupuk' => $request->nama_pupuk,
                'deskripsi' => $request->deskripsi,
                'satuan' => $request->satuan,
                'harga_per_satuan' => $request->harga_per_satuan,
                'status' => $request->status
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'update',
                'table_name' => 'jenis_pupuk',
                'record_id' => $data->id,
                'details' => 'Updated jenis pupuk: ' . json_encode(['old' => $oldData, 'new' => $data->toArray()]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jenis pupuk berhasil diupdate',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate jenis pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = JenisPupuk::findOrFail($id);
            
            // Check if jenis pupuk is being used
            $pencatatanCount = $data->pencatatanPupuk()->count();
            
            if ($pencatatanCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis pupuk tidak dapat dihapus karena masih digunakan dalam pencatatan pupuk'
                ], 400);
            }
            
            $dataInfo = $data->nama_pupuk . ' - ' . $data->satuan . ' - Rp ' . number_format($data->harga_per_satuan ?? 0);
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'delete',
                'table_name' => 'jenis_pupuk',
                'record_id' => $id,
                'details' => 'Deleted jenis pupuk: ' . $dataInfo,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jenis pupuk berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jenis pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $summary = [
                'total_jenis_pupuk' => JenisPupuk::count(),
                'total_aktif' => JenisPupuk::where('status', 'aktif')->count(),
                'total_tidak_aktif' => JenisPupuk::where('status', 'tidak_aktif')->count(),
                'rata_rata_harga' => JenisPupuk::whereNotNull('harga_per_satuan')->avg('harga_per_satuan'),
                'harga_tertinggi' => JenisPupuk::whereNotNull('harga_per_satuan')->max('harga_per_satuan'),
                'harga_terendah' => JenisPupuk::whereNotNull('harga_per_satuan')->min('harga_per_satuan'),
                'pupuk_termahal' => JenisPupuk::whereNotNull('harga_per_satuan')
                    ->orderBy('harga_per_satuan', 'desc')
                    ->first(['nama_pupuk', 'harga_per_satuan', 'satuan']),
                'pupuk_termurah' => JenisPupuk::whereNotNull('harga_per_satuan')
                    ->orderBy('harga_per_satuan', 'asc')
                    ->first(['nama_pupuk', 'harga_per_satuan', 'satuan']),
                'usage_stats' => JenisPupuk::with('pencatatanPupuk')
                    ->get()
                    ->map(function ($pupuk) {
                        return [
                            'id' => $pupuk->id,
                            'nama_pupuk' => $pupuk->nama_pupuk,
                            'satuan' => $pupuk->satuan,
                            'harga_per_satuan' => $pupuk->harga_per_satuan,
                            'total_pencatatan' => $pupuk->pencatatanPupuk->count(),
                            'total_jumlah_digunakan' => $pupuk->pencatatanPupuk->sum('jumlah_pupuk'),
                            'status' => $pupuk->status
                        ];
                    })
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary jenis pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getActive()
    {
        try {
            $data = JenisPupuk::where('status', 'aktif')
                ->orderBy('nama_pupuk', 'asc')
                ->get(['id', 'nama_pupuk', 'satuan', 'harga_per_satuan']);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jenis pupuk aktif',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}