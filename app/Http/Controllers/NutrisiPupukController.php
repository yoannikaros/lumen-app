<?php

namespace App\Http\Controllers;

use App\Models\NutrisiPupuk;
use App\Models\AreaKebun;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class NutrisiPupukController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = NutrisiPupuk::with(['area', 'user']);

            // Filter by date range
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $query->whereBetween('tanggal_pencatatan', [$request->tanggal_mulai, $request->tanggal_selesai]);
            }

            // Filter by area
            if ($request->has('area_id')) {
                $query->where('area_id', $request->area_id);
            }

            // Filter by kondisi cuaca
            if ($request->has('kondisi_cuaca')) {
                $query->where('kondisi_cuaca', $request->kondisi_cuaca);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $data = $query->orderBy('tanggal_pencatatan', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data nutrisi pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tanggal_pencatatan' => 'required|date',
            'area_id' => 'required|exists:area_kebun,id',
            'jumlah_tanda_air' => 'required|numeric|min:0',
            'jumlah_pupuk' => 'required|numeric|min:0',
            'jumlah_air' => 'required|numeric|min:0',
            'ppm_sebelum' => 'nullable|numeric|min:0',
            'ppm_sesudah' => 'nullable|numeric|min:0',
            'ph_sebelum' => 'nullable|numeric|min:0|max:14',
            'ph_sesudah' => 'nullable|numeric|min:0|max:14',
            'suhu_air' => 'nullable|numeric',
            'kondisi_cuaca' => 'nullable|in:cerah,berawan,hujan,mendung',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = NutrisiPupuk::create([
                'tanggal_pencatatan' => $request->tanggal_pencatatan,
                'area_id' => $request->area_id,
                'jumlah_tanda_air' => $request->jumlah_tanda_air,
                'jumlah_pupuk' => $request->jumlah_pupuk,
                'jumlah_air' => $request->jumlah_air,
                'ppm_sebelum' => $request->ppm_sebelum,
                'ppm_sesudah' => $request->ppm_sesudah,
                'ph_sebelum' => $request->ph_sebelum,
                'ph_sesudah' => $request->ph_sesudah,
                'suhu_air' => $request->suhu_air,
                'kondisi_cuaca' => $request->kondisi_cuaca,
                'keterangan' => $request->keterangan,
                'user_id' => $request->auth->id
            ]);

            $data->load(['area', 'user']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'create',
                'table_name' => 'nutrisi_pupuk',
                'record_id' => $data->id,
                'details' => 'Created nutrisi pupuk for area: ' . $data->area->nama_area . ' - Pupuk: ' . $data->jumlah_pupuk . ', Air: ' . $data->jumlah_air,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data nutrisi pupuk berhasil disimpan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data nutrisi pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = NutrisiPupuk::with(['area', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data nutrisi pupuk tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tanggal_pencatatan' => 'required|date',
            'area_id' => 'required|exists:area_kebun,id',
            'jumlah_tanda_air' => 'required|numeric|min:0',
            'jumlah_pupuk' => 'required|numeric|min:0',
            'jumlah_air' => 'required|numeric|min:0',
            'ppm_sebelum' => 'nullable|numeric|min:0',
            'ppm_sesudah' => 'nullable|numeric|min:0',
            'ph_sebelum' => 'nullable|numeric|min:0|max:14',
            'ph_sesudah' => 'nullable|numeric|min:0|max:14',
            'suhu_air' => 'nullable|numeric',
            'kondisi_cuaca' => 'nullable|in:cerah,berawan,hujan,mendung',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = NutrisiPupuk::findOrFail($id);
            $oldData = $data->toArray();

            $data->update([
                'tanggal_pencatatan' => $request->tanggal_pencatatan,
                'area_id' => $request->area_id,
                'jumlah_tanda_air' => $request->jumlah_tanda_air,
                'jumlah_pupuk' => $request->jumlah_pupuk,
                'jumlah_air' => $request->jumlah_air,
                'ppm_sebelum' => $request->ppm_sebelum,
                'ppm_sesudah' => $request->ppm_sesudah,
                'ph_sebelum' => $request->ph_sebelum,
                'ph_sesudah' => $request->ph_sesudah,
                'suhu_air' => $request->suhu_air,
                'kondisi_cuaca' => $request->kondisi_cuaca,
                'keterangan' => $request->keterangan
            ]);

            $data->load(['area', 'user']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'update',
                'table_name' => 'nutrisi_pupuk',
                'record_id' => $data->id,
                'details' => 'Updated nutrisi pupuk: ' . json_encode(['old' => $oldData, 'new' => $data->toArray()]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data nutrisi pupuk berhasil diupdate',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data nutrisi pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = NutrisiPupuk::with('area')->findOrFail($id);
            $dataInfo = 'Area: ' . $data->area->nama_area . ' - Pupuk: ' . $data->jumlah_pupuk . ', Air: ' . $data->jumlah_air;
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'delete',
                'table_name' => 'nutrisi_pupuk',
                'record_id' => $id,
                'details' => 'Deleted nutrisi pupuk: ' . $dataInfo,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data nutrisi pupuk berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data nutrisi pupuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $query = NutrisiPupuk::query();

            // Filter by date range
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $query->whereBetween('tanggal_pencatatan', [$request->tanggal_mulai, $request->tanggal_selesai]);
            }

            $summary = [
                'total_pupuk_digunakan' => $query->sum('jumlah_pupuk'),
                'total_air_digunakan' => $query->sum('jumlah_air'),
                'total_pencatatan' => $query->count(),
                'rata_rata_pupuk' => $query->avg('jumlah_pupuk'),
                'rata_rata_air' => $query->avg('jumlah_air'),
                'rata_rata_ppm_sebelum' => $query->whereNotNull('ppm_sebelum')->avg('ppm_sebelum'),
                'rata_rata_ppm_sesudah' => $query->whereNotNull('ppm_sesudah')->avg('ppm_sesudah'),
                'rata_rata_ph_sebelum' => $query->whereNotNull('ph_sebelum')->avg('ph_sebelum'),
                'rata_rata_ph_sesudah' => $query->whereNotNull('ph_sesudah')->avg('ph_sesudah'),
                'rata_rata_suhu_air' => $query->whereNotNull('suhu_air')->avg('suhu_air'),
                'nutrisi_per_area' => $query->with('area')
                    ->selectRaw('
                        area_id,
                        SUM(jumlah_pupuk) as total_pupuk,
                        SUM(jumlah_air) as total_air,
                        COUNT(*) as total_pencatatan,
                        AVG(jumlah_pupuk) as rata_rata_pupuk,
                        AVG(jumlah_air) as rata_rata_air
                    ')
                    ->groupBy('area_id')
                    ->get(),
                'kondisi_cuaca_stats' => $query->selectRaw('
                    kondisi_cuaca,
                    COUNT(*) as total_pencatatan,
                    AVG(jumlah_pupuk) as rata_rata_pupuk,
                    AVG(jumlah_air) as rata_rata_air
                ')
                ->whereNotNull('kondisi_cuaca')
                ->groupBy('kondisi_cuaca')
                ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary nutrisi pupuk',
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