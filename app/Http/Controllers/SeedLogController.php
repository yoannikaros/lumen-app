<?php

namespace App\Http\Controllers;

use App\Models\SeedLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeedLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = SeedLog::with(['user', 'dataSayur']);

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal_semai', [$request->start_date, $request->end_date]);
            }

            // Filter by nama_benih
            if ($request->has('nama_benih')) {
                $query->where('nama_benih', 'like', '%' . $request->nama_benih . '%');
            }

            // Filter by varietas
            if ($request->has('varietas')) {
                $query->where('varietas', 'like', '%' . $request->varietas . '%');
            }

            // Filter by satuan
            if ($request->has('satuan')) {
                $query->where('satuan', $request->satuan);
            }

            // Filter by data_sayur_id
            if ($request->has('data_sayur_id')) {
                $query->where('data_sayur_id', $request->data_sayur_id);
            }

            $data = $query->orderBy('tanggal_semai', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data seed log'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tanggal_semai' => 'required|date',
            'hari' => 'nullable|string|max:20',
            'nama_benih' => 'required|string|max:100',
            'varietas' => 'nullable|string|max:100',
            'satuan' => 'required|in:tray,hampan,pak,biji,gram,lainnya',
            'jumlah' => 'required|numeric|min:0',
            'sumber_benih' => 'nullable|string|max:255',
            'data_sayur_id' => 'nullable|exists:data_sayur,id',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = SeedLog::create([
                'tanggal_semai' => $request->tanggal_semai,
                'hari' => $request->hari,
                'nama_benih' => $request->nama_benih,
                'varietas' => $request->varietas,
                'satuan' => $request->satuan,
                'jumlah' => $request->jumlah,
                'sumber_benih' => $request->sumber_benih,
                'data_sayur_id' => $request->data_sayur_id,
                'keterangan' => $request->keterangan,
                'user_id' => $request->auth->id
            ]);

            $data->load(['user', 'dataSayur']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'create',
                'table_name' => 'seed_logs',
                'record_id' => $data->id,
                'details' => 'Menambah data seed log: ' . $data->nama_benih . ' (' . $data->varietas . ')',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data seed log berhasil ditambahkan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah data seed log'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = SeedLog::with(['user', 'dataSayur'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data seed log tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tanggal_semai' => 'required|date',
            'hari' => 'nullable|string|max:20',
            'nama_benih' => 'required|string|max:100',
            'varietas' => 'nullable|string|max:100',
            'satuan' => 'required|in:tray,hampan,pak,biji,gram,lainnya',
            'jumlah' => 'required|numeric|min:0',
            'sumber_benih' => 'nullable|string|max:255',
            'data_sayur_id' => 'nullable|exists:data_sayur,id',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = SeedLog::findOrFail($id);
            
            $data->update([
                'tanggal_semai' => $request->tanggal_semai,
                'hari' => $request->hari,
                'nama_benih' => $request->nama_benih,
                'varietas' => $request->varietas,
                'satuan' => $request->satuan,
                'jumlah' => $request->jumlah,
                'sumber_benih' => $request->sumber_benih,
                'data_sayur_id' => $request->data_sayur_id,
                'keterangan' => $request->keterangan
            ]);

            $data->load(['user', 'dataSayur']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'update',
                'table_name' => 'seed_logs',
                'record_id' => $data->id,
                'details' => 'Mengubah data seed log: ' . $data->nama_benih . ' (' . $data->varietas . ')',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data seed log berhasil diubah',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah data seed log'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = SeedLog::findOrFail($id);
            $seedName = $data->nama_benih . ' (' . $data->varietas . ')';
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'delete',
                'table_name' => 'seed_logs',
                'record_id' => $id,
                'details' => 'Menghapus data seed log: ' . $seedName,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data seed log berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data seed log'
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $query = SeedLog::query();

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal_semai', [$request->start_date, $request->end_date]);
            }

            $summary = [
                'total_entries' => $query->count(),
                'total_jumlah' => $query->sum('jumlah'),
                'by_satuan' => $query->select('satuan', DB::raw('COUNT(*) as count'), DB::raw('SUM(jumlah) as total_jumlah'))
                    ->groupBy('satuan')
                    ->get(),
                'by_nama_benih' => $query->select('nama_benih', DB::raw('COUNT(*) as count'), DB::raw('SUM(jumlah) as total_jumlah'))
                    ->groupBy('nama_benih')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'recent_entries' => $query->with(['user', 'dataSayur'])
                    ->orderBy('tanggal_semai', 'desc')
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan data seed log'
            ], 500);
        }
    }

    public function getByDataSayur($dataSayurId)
    {
        try {
            $data = SeedLog::with(['user'])
                ->where('data_sayur_id', $dataSayurId)
                ->orderBy('tanggal_semai', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data seed log'
            ], 500);
        }
    }
}