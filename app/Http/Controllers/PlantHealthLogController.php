<?php

namespace App\Http\Controllers;

use App\Models\PlantHealthLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlantHealthLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = PlantHealthLog::with(['user', 'dataSayur']);

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            }

            // Filter by data_sayur_id
            if ($request->has('data_sayur_id')) {
                $query->where('data_sayur_id', $request->data_sayur_id);
            }

            // Filter by gejala
            if ($request->has('gejala')) {
                $query->where('gejala', $request->gejala);
            }

            // Filter by minimum jumlah_tanaman_terdampak
            if ($request->has('min_terdampak')) {
                $query->where('jumlah_tanaman_terdampak', '>=', $request->min_terdampak);
            }

            $data = $query->orderBy('tanggal', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data plant health log'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tanggal' => 'required|date',
            'data_sayur_id' => 'required|exists:data_sayur,id',
            'gejala' => 'required|in:busuk,layu,jamur,serangga,nutrisi,lainnya',
            'jumlah_tanaman_terdampak' => 'required|integer|min:1',
            'tindakan' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = PlantHealthLog::create([
                'tanggal' => $request->tanggal,
                'data_sayur_id' => $request->data_sayur_id,
                'gejala' => $request->gejala,
                'jumlah_tanaman_terdampak' => $request->jumlah_tanaman_terdampak,
                'tindakan' => $request->tindakan,
                'keterangan' => $request->keterangan,
                'user_id' => $request->auth->id
            ]);

            $data->load(['user', 'dataSayur']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'create',
                'table_name' => 'plant_health_log',
                'record_id' => $data->id,
                'details' => 'Menambah data plant health log: ' . $data->gejala . ' (' . $data->jumlah_tanaman_terdampak . ' tanaman)',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data plant health log berhasil ditambahkan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah data plant health log'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = PlantHealthLog::with(['user', 'dataSayur'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data plant health log tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tanggal' => 'required|date',
            'data_sayur_id' => 'required|exists:data_sayur,id',
            'gejala' => 'required|in:busuk,layu,jamur,serangga,nutrisi,lainnya',
            'jumlah_tanaman_terdampak' => 'required|integer|min:1',
            'tindakan' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = PlantHealthLog::findOrFail($id);
            
            $data->update([
                'tanggal' => $request->tanggal,
                'data_sayur_id' => $request->data_sayur_id,
                'gejala' => $request->gejala,
                'jumlah_tanaman_terdampak' => $request->jumlah_tanaman_terdampak,
                'tindakan' => $request->tindakan,
                'keterangan' => $request->keterangan
            ]);

            $data->load(['user', 'dataSayur']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'update',
                'table_name' => 'plant_health_log',
                'record_id' => $data->id,
                'details' => 'Mengubah data plant health log: ' . $data->gejala . ' (' . $data->jumlah_tanaman_terdampak . ' tanaman)',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data plant health log berhasil diubah',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah data plant health log'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = PlantHealthLog::with(['dataSayur'])->findOrFail($id);
            $description = $data->gejala . ' pada ' . ($data->dataSayur ? $data->dataSayur->jenis_sayur : 'tanaman');
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'action' => 'delete',
                'table_name' => 'plant_health_log',
                'record_id' => $id,
                'details' => 'Menghapus data plant health log: ' . $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data plant health log berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data plant health log'
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $query = PlantHealthLog::query();

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            }

            $summary = [
                'total_entries' => $query->count(),
                'total_tanaman_terdampak' => $query->sum('jumlah_tanaman_terdampak'),
                'by_gejala' => $query->select('gejala', DB::raw('COUNT(*) as count'), DB::raw('SUM(jumlah_tanaman_terdampak) as total_terdampak'))
                    ->groupBy('gejala')
                    ->orderBy('total_terdampak', 'desc')
                    ->get(),
                'by_data_sayur' => $query->join('data_sayur', 'plant_health_log.data_sayur_id', '=', 'data_sayur.id')
                    ->select('data_sayur.jenis_sayur', 'data_sayur.varietas', DB::raw('COUNT(*) as count'), DB::raw('SUM(jumlah_tanaman_terdampak) as total_terdampak'))
                    ->groupBy('data_sayur.jenis_sayur', 'data_sayur.varietas')
                    ->orderBy('total_terdampak', 'desc')
                    ->limit(10)
                    ->get(),
                'recent_entries' => $query->with(['user', 'dataSayur'])
                    ->orderBy('tanggal', 'desc')
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
                'message' => 'Gagal mengambil ringkasan data plant health log'
            ], 500);
        }
    }

    public function getByDataSayur($dataSayurId)
    {
        try {
            $data = PlantHealthLog::with(['user'])
                ->where('data_sayur_id', $dataSayurId)
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data plant health log'
            ], 500);
        }
    }

    public function getHealthStats(Request $request)
    {
        try {
            $query = PlantHealthLog::query();

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            }

            // Get daily health statistics
            $dailyStats = $query->select(
                    'tanggal',
                    DB::raw('COUNT(*) as total_incidents'),
                    DB::raw('SUM(jumlah_tanaman_terdampak) as total_affected_plants')
                )
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'desc')
                ->limit(30)
                ->get();

            // Get gejala distribution
            $gejalaStats = $query->select(
                    'gejala',
                    DB::raw('COUNT(*) as incident_count'),
                    DB::raw('SUM(jumlah_tanaman_terdampak) as affected_plants'),
                    DB::raw('AVG(jumlah_tanaman_terdampak) as avg_affected_per_incident')
                )
                ->groupBy('gejala')
                ->orderBy('affected_plants', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'daily_stats' => $dailyStats,
                    'gejala_stats' => $gejalaStats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik kesehatan tanaman'
            ], 500);
        }
    }
}