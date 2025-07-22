<?php

namespace App\Http\Controllers;

use App\Models\PerlakuanMaster;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerlakuanMasterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = PerlakuanMaster::query();

            // Filter by tipe
            if ($request->has('tipe')) {
                $query->where('tipe', $request->tipe);
            }

            // Search by nama_perlakuan
            if ($request->has('search')) {
                $query->where('nama_perlakuan', 'LIKE', '%' . $request->search . '%');
            }

            $data = $query->orderBy('nama_perlakuan', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data perlakuan master'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_perlakuan' => 'required|string|max:100|unique:perlakuan_master,nama_perlakuan',
            'tipe' => 'required|in:pupuk,fungisida,insektisida,biopestisida,kultur,lainnya',
            'deskripsi' => 'nullable|string',
            'satuan_default' => 'nullable|string|max:20'
        ]);

        try {
            $data = PerlakuanMaster::create([
                'nama_perlakuan' => $request->nama_perlakuan,
                'tipe' => $request->tipe,
                'deskripsi' => $request->deskripsi,
                'satuan_default' => $request->satuan_default
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'activity' => 'create',
                'description' => 'Menambah data perlakuan master: ' . $data->nama_perlakuan,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data perlakuan master berhasil ditambahkan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah data perlakuan master'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = PerlakuanMaster::with(['jadwalPerlakuan.area', 'jadwalPerlakuan.tandon', 'jadwalPerlakuan.user'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data perlakuan master tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nama_perlakuan' => 'required|string|max:100|unique:perlakuan_master,nama_perlakuan,' . $id,
            'tipe' => 'required|in:pupuk,fungisida,insektisida,biopestisida,kultur,lainnya',
            'deskripsi' => 'nullable|string',
            'satuan_default' => 'nullable|string|max:20'
        ]);

        try {
            $data = PerlakuanMaster::findOrFail($id);
            
            $data->update([
                'nama_perlakuan' => $request->nama_perlakuan,
                'tipe' => $request->tipe,
                'deskripsi' => $request->deskripsi,
                'satuan_default' => $request->satuan_default
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'activity' => 'update',
                'description' => 'Mengubah data perlakuan master: ' . $data->nama_perlakuan,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data perlakuan master berhasil diubah',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah data perlakuan master'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = PerlakuanMaster::findOrFail($id);
            $nama = $data->nama_perlakuan;
            
            // Check if there are related jadwal_perlakuan records
            $jadwalCount = $data->jadwalPerlakuan()->count();
            if ($jadwalCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus perlakuan master karena masih digunakan dalam jadwal perlakuan'
                ], 400);
            }
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'activity' => 'delete',
                'description' => 'Menghapus data perlakuan master: ' . $nama,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data perlakuan master berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data perlakuan master'
            ], 500);
        }
    }

    public function getByTipe($tipe)
    {
        try {
            $data = PerlakuanMaster::where('tipe', $tipe)
                ->orderBy('nama_perlakuan', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data perlakuan master'
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $summary = [
                'total_perlakuan' => PerlakuanMaster::count(),
                'by_tipe' => PerlakuanMaster::select('tipe', DB::raw('COUNT(*) as count'))
                    ->groupBy('tipe')
                    ->orderBy('count', 'desc')
                    ->get(),
                'recent_added' => PerlakuanMaster::orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'nama_perlakuan', 'tipe', 'created_at'])
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan data perlakuan master'
            ], 500);
        }
    }
}