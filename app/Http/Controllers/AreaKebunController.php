<?php

namespace App\Http\Controllers;

use App\Models\AreaKebun;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AreaKebunController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = AreaKebun::query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search by nama area
            if ($request->has('search')) {
                $query->where('nama_area', 'like', '%' . $request->search . '%');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $data = $query->orderBy('nama_area', 'asc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data area kebun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_area' => 'required|string|max:100|unique:area_kebun,nama_area',
            'deskripsi' => 'nullable|string',
            'luas_m2' => 'nullable|numeric|min:0',
            'kapasitas_tanaman' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        try {
            $data = AreaKebun::create([
                'nama_area' => $request->nama_area,
                'deskripsi' => $request->deskripsi,
                'luas_m2' => $request->luas_m2,
                'kapasitas_tanaman' => $request->kapasitas_tanaman,
                'status' => $request->status
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->user_id,
                'action' => 'create',
                'table_name' => 'area_kebun',
                'record_id' => $data->id,
                'details' => 'Created area kebun: ' . $data->nama_area . ' - ' . $data->luas_m2 . ' m²',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Area kebun berhasil disimpan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan area kebun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = AreaKebun::with(['nutrisiPupuk', 'dataSayur'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Area kebun tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nama_area' => 'required|string|max:100|unique:area_kebun,nama_area,' . $id,
            'deskripsi' => 'nullable|string',
            'luas_m2' => 'nullable|numeric|min:0',
            'kapasitas_tanaman' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        try {
            $data = AreaKebun::findOrFail($id);
            $oldData = $data->toArray();

            $data->update([
                'nama_area' => $request->nama_area,
                'deskripsi' => $request->deskripsi,
                'luas_m2' => $request->luas_m2,
                'kapasitas_tanaman' => $request->kapasitas_tanaman,
                'status' => $request->status
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->user_id,
                'action' => 'update',
                'table_name' => 'area_kebun',
                'record_id' => $data->id,
                'details' => 'Updated area kebun: ' . json_encode(['old' => $oldData, 'new' => $data->toArray()]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Area kebun berhasil diupdate',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate area kebun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = AreaKebun::findOrFail($id);
            
            // Check if area is being used
            $nutrisiCount = $data->nutrisiPupuk()->count();
            $sayurCount = $data->dataSayur()->count();
            
            if ($nutrisiCount > 0 || $sayurCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area kebun tidak dapat dihapus karena masih digunakan dalam pencatatan nutrisi atau data sayur'
                ], 400);
            }
            
            $dataInfo = $data->nama_area . ' - ' . $data->luas_m2 . ' m²';
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->user_id,
                'action' => 'delete',
                'table_name' => 'area_kebun',
                'record_id' => $id,
                'details' => 'Deleted area kebun: ' . $dataInfo,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Area kebun berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus area kebun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $summary = [
                'total_area' => AreaKebun::count(),
                'total_area_aktif' => AreaKebun::where('status', 'aktif')->count(),
                'total_area_tidak_aktif' => AreaKebun::where('status', 'tidak_aktif')->count(),
                'total_luas_m2' => AreaKebun::sum('luas_m2'),
                'total_kapasitas_tanaman' => AreaKebun::sum('kapasitas_tanaman'),
                'rata_rata_luas_m2' => AreaKebun::avg('luas_m2'),
                'rata_rata_kapasitas_tanaman' => AreaKebun::avg('kapasitas_tanaman'),
                'area_terbesar' => AreaKebun::orderBy('luas_m2', 'desc')->first(['nama_area', 'luas_m2']),
                'area_terkecil' => AreaKebun::orderBy('luas_m2', 'asc')->first(['nama_area', 'luas_m2']),
                'utilization' => AreaKebun::with(['nutrisiPupuk', 'dataSayur'])
                    ->get()
                    ->map(function ($area) {
                        return [
                            'id' => $area->id,
                            'nama_area' => $area->nama_area,
                            'luas_m2' => $area->luas_m2,
                            'kapasitas_tanaman' => $area->kapasitas_tanaman,
                            'total_pencatatan_nutrisi' => $area->nutrisiPupuk->count(),
                            'total_penanaman_sayur' => $area->dataSayur->count(),
                            'status' => $area->status
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
                'message' => 'Gagal mengambil summary area kebun',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}