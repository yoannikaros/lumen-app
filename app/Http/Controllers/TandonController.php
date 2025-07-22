<?php

namespace App\Http\Controllers;

use App\Models\Tandon;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TandonController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Tandon::with('area');

            // Filter by area
            if ($request->has('area_id')) {
                $query->where('area_id', $request->area_id);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $data = $query->orderBy('kode_tandon')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tandon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'area_id' => 'required|exists:area_kebun,id',
            'kode_tandon' => 'required|string|unique:tandon,kode_tandon',
            'nama_tandon' => 'nullable|string|max:100',
            'kapasitas_liter' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:aktif,nonaktif',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = Tandon::create([
                'area_id' => $request->area_id,
                'kode_tandon' => $request->kode_tandon,
                'nama_tandon' => $request->nama_tandon,
                'kapasitas_liter' => $request->kapasitas_liter,
                'status' => $request->status ?? 'aktif',
                'keterangan' => $request->keterangan
            ]);

            $data->load('area');

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'create',
                'table_name' => 'tandon',
                'record_id' => $data->id,
                'details' => 'Created tandon: ' . $data->kode_tandon . ' - ' . $data->nama_tandon,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data tandon berhasil disimpan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data tandon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = Tandon::with('area')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tandon tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'area_id' => 'required|exists:area_kebun,id',
            'kode_tandon' => 'required|string|unique:tandon,kode_tandon,' . $id,
            'nama_tandon' => 'nullable|string|max:100',
            'kapasitas_liter' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:aktif,nonaktif',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = Tandon::findOrFail($id);
            $oldData = $data->toArray();

            $data->update([
                'area_id' => $request->area_id,
                'kode_tandon' => $request->kode_tandon,
                'nama_tandon' => $request->nama_tandon,
                'kapasitas_liter' => $request->kapasitas_liter,
                'status' => $request->status ?? 'aktif',
                'keterangan' => $request->keterangan
            ]);

            $data->load('area');

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'update',
                'table_name' => 'tandon',
                'record_id' => $data->id,
                'details' => 'Updated tandon: ' . json_encode(['old' => $oldData, 'new' => $data->toArray()]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data tandon berhasil diupdate',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data tandon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = Tandon::findOrFail($id);
            $dataInfo = $data->kode_tandon . ' - ' . $data->nama_tandon;
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'delete',
                'table_name' => 'tandon',
                'record_id' => $id,
                'details' => 'Deleted tandon: ' . $dataInfo,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data tandon berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data tandon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByArea($areaId)
    {
        try {
            $data = Tandon::where('area_id', $areaId)
                         ->where('status', 'aktif')
                         ->orderBy('kode_tandon')
                         ->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tandon',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}