<?php

namespace App\Http\Controllers;

use App\Models\PencatatanPupuk;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PencatatanPupukController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = PencatatanPupuk::with(['jenisPupuk', 'user']);

            // Filter by date range
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
            }

            // Filter by jenis pupuk
            if ($request->has('jenis_pupuk_id')) {
                $query->where('jenis_pupuk_id', $request->jenis_pupuk_id);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $data = $query->orderBy('tanggal', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pencatatan pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tanggal' => 'required|date',
            'jenis_pupuk_id' => 'required|exists:jenis_pupuk,id',
            'jumlah_pupuk' => 'required|numeric|min:0',
            'satuan' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = PencatatanPupuk::create([
                'tanggal' => $request->tanggal,
                'jenis_pupuk_id' => $request->jenis_pupuk_id,
                'jumlah_pupuk' => $request->jumlah_pupuk,
                'satuan' => $request->satuan ?? 'kg',
                'keterangan' => $request->keterangan,
                'user_id' => $request->auth->user_id
            ]);

            $data->load(['jenisPupuk', 'user']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->user_id,
                'action' => 'create',
                'table_name' => 'pencatatan_pupuk',
                'record_id' => $data->id,
                'details' => 'Created pencatatan pupuk: ' . $data->jenisPupuk->nama_pupuk . ' - ' . $data->jumlah_pupuk . ' ' . $data->satuan,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data pencatatan pupuk berhasil disimpan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data pencatatan pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = PencatatanPupuk::with(['jenisPupuk', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pencatatan pupuk tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tanggal' => 'required|date',
            'jenis_pupuk_id' => 'required|exists:jenis_pupuk,id',
            'jumlah_pupuk' => 'required|numeric|min:0',
            'satuan' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = PencatatanPupuk::findOrFail($id);
            
            $oldData = $data->toArray();
            
            $data->update([
                'tanggal' => $request->tanggal,
                'jenis_pupuk_id' => $request->jenis_pupuk_id,
                'jumlah_pupuk' => $request->jumlah_pupuk,
                'satuan' => $request->satuan ?? 'kg',
                'keterangan' => $request->keterangan
            ]);

            $data->load(['jenisPupuk', 'user']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->user_id,
                'action' => 'update',
                'table_name' => 'pencatatan_pupuk',
                'record_id' => $data->id,
                'details' => 'Updated pencatatan pupuk: ' . json_encode(['old' => $oldData, 'new' => $data->toArray()]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data pencatatan pupuk berhasil diupdate',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data pencatatan pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = PencatatanPupuk::findOrFail($id);
            $dataInfo = $data->jenisPupuk->nama_pupuk . ' - ' . $data->jumlah_pupuk . ' ' . $data->satuan;
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->user_id,
                'action' => 'delete',
                'table_name' => 'pencatatan_pupuk',
                'record_id' => $id,
                'details' => 'Deleted pencatatan pupuk: ' . $dataInfo,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data pencatatan pupuk berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data pencatatan pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $query = PencatatanPupuk::with('jenisPupuk');

            // Filter by date range
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
            }

            $summary = $query->selectRaw('
                jenis_pupuk_id,
                SUM(jumlah_pupuk) as total_pupuk,
                COUNT(*) as total_pencatatan,
                AVG(jumlah_pupuk) as rata_rata_pupuk
            ')
            ->groupBy('jenis_pupuk_id')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary pencatatan pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}