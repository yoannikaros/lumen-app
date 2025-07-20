<?php

namespace App\Http\Controllers;

use App\Models\DataSayur;
use App\Models\AreaKebun;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DataSayurController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DataSayur::with(['area', 'user']);

            // Filter by date range (tanggal tanam)
            if ($request->has('tanggal_tanam_mulai') && $request->has('tanggal_tanam_selesai')) {
                $query->whereBetween('tanggal_tanam', [$request->tanggal_tanam_mulai, $request->tanggal_tanam_selesai]);
            }

            // Filter by jenis sayur
            if ($request->has('jenis_sayur')) {
                $query->where('jenis_sayur', 'like', '%' . $request->jenis_sayur . '%');
            }

            // Filter by area
            if ($request->has('area_id')) {
                $query->where('area_id', $request->area_id);
            }

            // Filter by status panen
            if ($request->has('status_panen')) {
                $query->where('status_panen', $request->status_panen);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $data = $query->orderBy('tanggal_tanam', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tanggal_tanam' => 'required|date',
            'jenis_sayur' => 'required|string|max:100',
            'varietas' => 'nullable|string|max:100',
            'area_id' => 'required|exists:area_kebun,id',
            'jumlah_bibit' => 'required|integer|min:1',
            'metode_tanam' => 'nullable|in:hidroponik,tanah,pot',
            'jenis_media' => 'nullable|string|max:100',
            'tanggal_panen_target' => 'nullable|date|after:tanggal_tanam',
            'tanggal_panen_aktual' => 'nullable|date',
            'status_panen' => 'required|in:belum_panen,panen_sukses,gagal_panen',
            'jumlah_panen_kg' => 'nullable|numeric|min:0',
            'penyebab_gagal' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = DataSayur::create([
                'tanggal_tanam' => $request->tanggal_tanam,
                'jenis_sayur' => $request->jenis_sayur,
                'varietas' => $request->varietas,
                'area_id' => $request->area_id,
                'jumlah_bibit' => $request->jumlah_bibit,
                'metode_tanam' => $request->metode_tanam ?? 'hidroponik',
                'jenis_media' => $request->jenis_media,
                'tanggal_panen_target' => $request->tanggal_panen_target,
                'tanggal_panen_aktual' => $request->tanggal_panen_aktual,
                'status_panen' => $request->status_panen,
                'jumlah_panen_kg' => $request->jumlah_panen_kg,
                'penyebab_gagal' => $request->penyebab_gagal,
                'keterangan' => $request->keterangan,
                'user_id' => $request->auth->user_id
            ]);

            $data->load(['area', 'user']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->user_id,
                'action' => 'create',
                'table_name' => 'data_sayur',
                'record_id' => $data->id,
                'details' => 'Created data sayur: ' . $data->jenis_sayur . ' - ' . $data->jumlah_bibit . ' bibit di area ' . $data->area->nama_area,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data sayur berhasil disimpan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = DataSayur::with(['area', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data sayur tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tanggal_tanam' => 'required|date',
            'jenis_sayur' => 'required|string|max:100',
            'varietas' => 'nullable|string|max:100',
            'area_id' => 'required|exists:area_kebun,id',
            'jumlah_bibit' => 'required|integer|min:1',
            'metode_tanam' => 'nullable|in:hidroponik,tanah,pot',
            'jenis_media' => 'nullable|string|max:100',
            'tanggal_panen_target' => 'nullable|date|after:tanggal_tanam',
            'tanggal_panen_aktual' => 'nullable|date',
            'status_panen' => 'required|in:belum_panen,panen_sukses,gagal_panen',
            'jumlah_panen_kg' => 'nullable|numeric|min:0',
            'penyebab_gagal' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = DataSayur::findOrFail($id);
            $oldData = $data->toArray();

            $data->update([
                'tanggal_tanam' => $request->tanggal_tanam,
                'jenis_sayur' => $request->jenis_sayur,
                'varietas' => $request->varietas,
                'area_id' => $request->area_id,
                'jumlah_bibit' => $request->jumlah_bibit,
                'metode_tanam' => $request->metode_tanam ?? 'hidroponik',
                'jenis_media' => $request->jenis_media,
                'tanggal_panen_target' => $request->tanggal_panen_target,
                'tanggal_panen_aktual' => $request->tanggal_panen_aktual,
                'status_panen' => $request->status_panen,
                'jumlah_panen_kg' => $request->jumlah_panen_kg,
                'penyebab_gagal' => $request->penyebab_gagal,
                'keterangan' => $request->keterangan
            ]);

            $data->load(['area', 'user']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->user_id,
                'action' => 'update',
                'table_name' => 'data_sayur',
                'record_id' => $data->id,
                'details' => 'Updated data sayur: ' . json_encode(['old' => $oldData, 'new' => $data->toArray()]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data sayur berhasil diupdate',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = DataSayur::with('area')->findOrFail($id);
            $dataInfo = $data->jenis_sayur . ' - ' . $data->jumlah_bibit . ' bibit di area ' . $data->area->nama_area;
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->user_id,
                'action' => 'delete',
                'table_name' => 'data_sayur',
                'record_id' => $id,
                'details' => 'Deleted data sayur: ' . $dataInfo,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data sayur berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $query = DataSayur::query();

            // Filter by date range
            if ($request->has('tanggal_tanam_mulai') && $request->has('tanggal_tanam_selesai')) {
                $query->whereBetween('tanggal_tanam', [$request->tanggal_tanam_mulai, $request->tanggal_tanam_selesai]);
            }

            $totalTanam = $query->count();
            $totalSukses = $query->where('status_panen', 'panen_sukses')->count();
            $totalGagal = $query->where('status_panen', 'gagal_panen')->count();
            $totalBelumPanen = $query->where('status_panen', 'belum_panen')->count();

            $summary = [
                'total_penanaman' => $totalTanam,
                'total_panen_sukses' => $totalSukses,
                'total_gagal_panen' => $totalGagal,
                'total_belum_panen' => $totalBelumPanen,
                'persentase_keberhasilan' => $totalTanam > 0 ? round(($totalSukses / $totalTanam) * 100, 2) : 0,
                'persentase_kegagalan' => $totalTanam > 0 ? round(($totalGagal / $totalTanam) * 100, 2) : 0,
                'total_bibit_ditanam' => $query->sum('jumlah_bibit'),
                'total_panen_kg' => $query->where('status_panen', 'panen_sukses')->sum('jumlah_panen_kg'),
                'rata_rata_panen_kg' => $query->where('status_panen', 'panen_sukses')->avg('jumlah_panen_kg'),
                'sayur_per_jenis' => $query->selectRaw('
                    jenis_sayur,
                    COUNT(*) as total_penanaman,
                    SUM(CASE WHEN status_panen = "panen_sukses" THEN 1 ELSE 0 END) as total_sukses,
                    SUM(CASE WHEN status_panen = "gagal_panen" THEN 1 ELSE 0 END) as total_gagal,
                    SUM(jumlah_bibit) as total_bibit,
                    SUM(CASE WHEN status_panen = "panen_sukses" THEN jumlah_panen_kg ELSE 0 END) as total_panen_kg,
                    ROUND((SUM(CASE WHEN status_panen = "panen_sukses" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as persentase_keberhasilan
                ')
                ->groupBy('jenis_sayur')
                ->get(),
                'sayur_per_area' => $query->with('area')
                    ->selectRaw('
                        area_id,
                        COUNT(*) as total_penanaman,
                        SUM(CASE WHEN status_panen = "panen_sukses" THEN 1 ELSE 0 END) as total_sukses,
                        SUM(CASE WHEN status_panen = "gagal_panen" THEN 1 ELSE 0 END) as total_gagal,
                        SUM(jumlah_bibit) as total_bibit,
                        SUM(CASE WHEN status_panen = "panen_sukses" THEN jumlah_panen_kg ELSE 0 END) as total_panen_kg,
                        ROUND((SUM(CASE WHEN status_panen = "panen_sukses" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as persentase_keberhasilan
                    ')
                    ->groupBy('area_id')
                    ->get(),
                'metode_tanam_stats' => $query->selectRaw('
                    metode_tanam,
                    COUNT(*) as total_penanaman,
                    SUM(CASE WHEN status_panen = "panen_sukses" THEN 1 ELSE 0 END) as total_sukses,
                    ROUND((SUM(CASE WHEN status_panen = "panen_sukses" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as persentase_keberhasilan
                ')
                ->whereNotNull('metode_tanam')
                ->groupBy('metode_tanam')
                ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary data sayur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAreas()
    {
        try {
            $areas = AreaKebun::where('status', 'aktif')->get(['id', 'nama_area', 'deskripsi']);

            return response()->json([
                'success' => true,
                'data' => $areas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data area kebun',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}