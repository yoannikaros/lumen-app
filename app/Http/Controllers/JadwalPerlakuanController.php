<?php

namespace App\Http\Controllers;

use App\Models\JadwalPerlakuan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalPerlakuanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = JadwalPerlakuan::with(['user', 'area', 'tandon', 'perlakuan']);

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            }

            // Filter by area_id
            if ($request->has('area_id')) {
                $query->where('area_id', $request->area_id);
            }

            // Filter by tandon_id
            if ($request->has('tandon_id')) {
                $query->where('tandon_id', $request->tandon_id);
            }

            // Filter by perlakuan_id
            if ($request->has('perlakuan_id')) {
                $query->where('perlakuan_id', $request->perlakuan_id);
            }

            // Filter by minggu_ke
            if ($request->has('minggu_ke')) {
                $query->where('minggu_ke', $request->minggu_ke);
            }

            // Filter by hari_dalam_minggu
            if ($request->has('hari_dalam_minggu')) {
                $query->where('hari_dalam_minggu', $request->hari_dalam_minggu);
            }

            $data = $query->orderBy('tanggal', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal perlakuan'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tanggal' => 'required|date',
            'minggu_ke' => 'nullable|integer|min:1|max:5',
            'hari_dalam_minggu' => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'area_id' => 'nullable|exists:area_kebun,id',
            'tandon_id' => 'nullable|exists:tandon,id',
            'perlakuan_id' => 'required|exists:perlakuan_master,id',
            'dosis' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = JadwalPerlakuan::create([
                'tanggal' => $request->tanggal,
                'minggu_ke' => $request->minggu_ke,
                'hari_dalam_minggu' => $request->hari_dalam_minggu,
                'area_id' => $request->area_id,
                'tandon_id' => $request->tandon_id,
                'perlakuan_id' => $request->perlakuan_id,
                'dosis' => $request->dosis,
                'satuan' => $request->satuan,
                'keterangan' => $request->keterangan,
                'user_id' => $request->auth->id
            ]);

            $data->load(['user', 'area', 'tandon', 'perlakuan']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'activity' => 'create',
                'description' => 'Menambah jadwal perlakuan: ' . $data->perlakuan->nama_perlakuan . ' pada ' . $data->tanggal,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal perlakuan berhasil ditambahkan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah jadwal perlakuan'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = JadwalPerlakuan::with(['user', 'area', 'tandon', 'perlakuan'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal perlakuan tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tanggal' => 'required|date',
            'minggu_ke' => 'nullable|integer|min:1|max:5',
            'hari_dalam_minggu' => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'area_id' => 'nullable|exists:area_kebun,id',
            'tandon_id' => 'nullable|exists:tandon,id',
            'perlakuan_id' => 'required|exists:perlakuan_master,id',
            'dosis' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = JadwalPerlakuan::findOrFail($id);
            
            $data->update([
                'tanggal' => $request->tanggal,
                'minggu_ke' => $request->minggu_ke,
                'hari_dalam_minggu' => $request->hari_dalam_minggu,
                'area_id' => $request->area_id,
                'tandon_id' => $request->tandon_id,
                'perlakuan_id' => $request->perlakuan_id,
                'dosis' => $request->dosis,
                'satuan' => $request->satuan,
                'keterangan' => $request->keterangan
            ]);

            $data->load(['user', 'area', 'tandon', 'perlakuan']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'activity' => 'update',
                'description' => 'Mengubah jadwal perlakuan: ' . $data->perlakuan->nama_perlakuan . ' pada ' . $data->tanggal,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal perlakuan berhasil diubah',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah jadwal perlakuan'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = JadwalPerlakuan::with(['perlakuan'])->findOrFail($id);
            $description = $data->perlakuan->nama_perlakuan . ' pada ' . $data->tanggal;
            
            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => $request->auth->id,
                'activity' => 'delete',
                'description' => 'Menghapus jadwal perlakuan: ' . $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal perlakuan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal perlakuan'
            ], 500);
        }
    }

    public function getByMonth(Request $request, $year, $month)
    {
        try {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            $data = JadwalPerlakuan::with(['user', 'area', 'tandon', 'perlakuan'])
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderBy('tanggal', 'asc')
                ->get();

            // Group by date for calendar view
            $groupedData = $data->groupBy(function($item) {
                return $item->tanggal->format('Y-m-d');
            });

            return response()->json([
                'success' => true,
                'data' => $groupedData,
                'month_info' => [
                    'year' => $year,
                    'month' => $month,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'total_schedules' => $data->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal perlakuan bulanan'
            ], 500);
        }
    }

    public function getByArea($areaId)
    {
        try {
            $data = JadwalPerlakuan::with(['user', 'tandon', 'perlakuan'])
                ->where('area_id', $areaId)
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal perlakuan'
            ], 500);
        }
    }

    public function getByPerlakuan($perlakuanId)
    {
        try {
            $data = JadwalPerlakuan::with(['user', 'area', 'tandon'])
                ->where('perlakuan_id', $perlakuanId)
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal perlakuan'
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $query = JadwalPerlakuan::query();

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            }

            $summary = [
                'total_jadwal' => $query->count(),
                'by_perlakuan' => $query->join('perlakuan_master', 'jadwal_perlakuan.perlakuan_id', '=', 'perlakuan_master.id')
                    ->select('perlakuan_master.nama_perlakuan', 'perlakuan_master.tipe', DB::raw('COUNT(*) as count'))
                    ->groupBy('perlakuan_master.nama_perlakuan', 'perlakuan_master.tipe')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'by_area' => $query->join('area_kebun', 'jadwal_perlakuan.area_id', '=', 'area_kebun.id')
                    ->select('area_kebun.nama_area', DB::raw('COUNT(*) as count'))
                    ->groupBy('area_kebun.nama_area')
                    ->orderBy('count', 'desc')
                    ->get(),
                'by_hari' => $query->select('hari_dalam_minggu', DB::raw('COUNT(*) as count'))
                    ->whereNotNull('hari_dalam_minggu')
                    ->groupBy('hari_dalam_minggu')
                    ->orderBy('count', 'desc')
                    ->get(),
                'upcoming_schedules' => JadwalPerlakuan::with(['area', 'tandon', 'perlakuan'])
                    ->where('tanggal', '>=', Carbon::now()->format('Y-m-d'))
                    ->orderBy('tanggal', 'asc')
                    ->limit(10)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan jadwal perlakuan'
            ], 500);
        }
    }

    public function getRotationSchedule(Request $request)
    {
        try {
            $query = JadwalPerlakuan::with(['area', 'tandon', 'perlakuan']);

            // Filter by area if provided
            if ($request->has('area_id')) {
                $query->where('area_id', $request->area_id);
            }

            // Get current month if no date range provided
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
            
            $query->whereBetween('tanggal', [$startDate, $endDate]);

            // Group by week and day for rotation view
            $data = $query->orderBy('tanggal', 'asc')->get();
            
            $rotationData = $data->groupBy(['minggu_ke', 'hari_dalam_minggu']);

            return response()->json([
                'success' => true,
                'data' => $rotationData,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_schedules' => $data->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal rotasi perlakuan'
            ], 500);
        }
    }
}