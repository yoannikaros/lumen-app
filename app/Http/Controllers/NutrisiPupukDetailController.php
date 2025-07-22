<?php

namespace App\Http\Controllers;

use App\Models\NutrisiPupukDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class NutrisiPupukDetailController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = NutrisiPupukDetail::with(['nutrisiPupuk', 'tandon']);

            // Filter by nutrisi_pupuk_id
            if ($request->has('nutrisi_pupuk_id')) {
                $query->where('nutrisi_pupuk_id', $request->nutrisi_pupuk_id);
            }

            // Filter by tandon_id
            if ($request->has('tandon_id')) {
                $query->where('tandon_id', $request->tandon_id);
            }

            $data = $query->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail nutrisi pupuk'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nutrisi_pupuk_id' => 'required|exists:nutrisi_pupuk,id',
            'tandon_id' => 'required|exists:tandon,id',
            'ppm' => 'nullable|numeric|min:0',
            'nutrisi_ditambah_ml' => 'nullable|numeric|min:0',
            'air_ditambah_liter' => 'nullable|numeric|min:0',
            'ph' => 'nullable|numeric|min:0|max:14',
            'suhu_air' => 'nullable|numeric',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = NutrisiPupukDetail::create([
                'nutrisi_pupuk_id' => $request->nutrisi_pupuk_id,
                'tandon_id' => $request->tandon_id,
                'ppm' => $request->ppm,
                'nutrisi_ditambah_ml' => $request->nutrisi_ditambah_ml,
                'air_ditambah_liter' => $request->air_ditambah_liter,
                'ph' => $request->ph,
                'suhu_air' => $request->suhu_air,
                'keterangan' => $request->keterangan
            ]);

            $data->load(['nutrisiPupuk', 'tandon']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'activity' => 'create',
                'description' => 'Menambah detail nutrisi pupuk untuk tandon: ' . $data->tandon->nama_tandon,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail nutrisi pupuk berhasil ditambahkan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah detail nutrisi pupuk'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = NutrisiPupukDetail::with(['nutrisiPupuk', 'tandon'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data detail nutrisi pupuk tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nutrisi_pupuk_id' => 'required|exists:nutrisi_pupuk,id',
            'tandon_id' => 'required|exists:tandon,id',
            'ppm' => 'nullable|numeric|min:0',
            'nutrisi_ditambah_ml' => 'nullable|numeric|min:0',
            'air_ditambah_liter' => 'nullable|numeric|min:0',
            'ph' => 'nullable|numeric|min:0|max:14',
            'suhu_air' => 'nullable|numeric',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = NutrisiPupukDetail::findOrFail($id);
            
            $data->update([
                'nutrisi_pupuk_id' => $request->nutrisi_pupuk_id,
                'tandon_id' => $request->tandon_id,
                'ppm' => $request->ppm,
                'nutrisi_ditambah_ml' => $request->nutrisi_ditambah_ml,
                'air_ditambah_liter' => $request->air_ditambah_liter,
                'ph' => $request->ph,
                'suhu_air' => $request->suhu_air,
                'keterangan' => $request->keterangan
            ]);

            $data->load(['nutrisiPupuk', 'tandon']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'activity' => 'update',
                'description' => 'Mengubah detail nutrisi pupuk untuk tandon: ' . $data->tandon->nama_tandon,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail nutrisi pupuk berhasil diubah',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah detail nutrisi pupuk'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = NutrisiPupukDetail::with(['nutrisiPupuk', 'tandon'])->findOrFail($id);
            $tandonName = $data->tandon->nama_tandon;
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'activity' => 'delete',
                'description' => 'Menghapus detail nutrisi pupuk untuk tandon: ' . $tandonName,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail nutrisi pupuk berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus detail nutrisi pupuk'
            ], 500);
        }
    }

    public function getByNutrisiPupuk($nutrisiPupukId)
    {
        try {
            $data = NutrisiPupukDetail::with(['tandon'])
                ->where('nutrisi_pupuk_id', $nutrisiPupukId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail nutrisi pupuk'
            ], 500);
        }
    }
}