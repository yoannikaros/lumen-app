<?php

namespace App\Http\Controllers;

use App\Models\PenjualanDetailBatch;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PenjualanDetailBatchController extends Controller
{
    /**
     * Get all penjualan detail batch with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PenjualanDetailBatch::with(['penjualan', 'dataSayur']);

            // Filter by penjualan_id
            if ($request->has('penjualan_id')) {
                $query->where('penjualan_id', $request->penjualan_id);
            }

            // Filter by data_sayur_id
            if ($request->has('data_sayur_id')) {
                $query->where('data_sayur_id', $request->data_sayur_id);
            }

            // Filter by qty range
            if ($request->has('min_qty')) {
                $query->where('qty_kg', '>=', $request->min_qty);
            }
            if ($request->has('max_qty')) {
                $query->where('qty_kg', '<=', $request->max_qty);
            }

            // Filter by date range (based on penjualan date)
            if ($request->has('start_date') || $request->has('end_date')) {
                $query->whereHas('penjualan', function($q) use ($request) {
                    if ($request->has('start_date')) {
                        $q->whereDate('tanggal_penjualan', '>=', $request->start_date);
                    }
                    if ($request->has('end_date')) {
                        $q->whereDate('tanggal_penjualan', '<=', $request->end_date);
                    }
                });
            }

            // Filter by jenis sayur
            if ($request->has('jenis_sayur')) {
                $query->whereHas('dataSayur', function($q) use ($request) {
                    $q->where('jenis_sayur', 'like', '%' . $request->jenis_sayur . '%');
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $data = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data penjualan detail batch berhasil diambil',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penjualan detail batch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new penjualan detail batch
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'penjualan_id' => 'required|integer|exists:penjualan_sayur,id',
                'data_sayur_id' => 'required|integer|exists:data_sayur,id',
                'qty_kg' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for duplicate combination
            $exists = PenjualanDetailBatch::where('penjualan_id', $request->penjualan_id)
                ->where('data_sayur_id', $request->data_sayur_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kombinasi penjualan dan batch sayur sudah ada'
                ], 422);
            }

            $data = PenjualanDetailBatch::create($validator->validated());
            $data->load(['penjualan', 'dataSayur']);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'create',
                'description' => 'Menambah detail penjualan batch: ' . $data->qty_kg . ' kg',
                'table_name' => 'penjualan_detail_batch',
                'record_id' => $data->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail penjualan batch berhasil ditambahkan',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan detail penjualan batch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific penjualan detail batch
     */
    public function show($id): JsonResponse
    {
        try {
            $data = PenjualanDetailBatch::with(['penjualan', 'dataSayur'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail penjualan batch berhasil diambil',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail penjualan batch tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update penjualan detail batch
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = PenjualanDetailBatch::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'penjualan_id' => 'sometimes|required|integer|exists:penjualan_sayur,id',
                'data_sayur_id' => 'sometimes|required|integer|exists:data_sayur,id',
                'qty_kg' => 'sometimes|required|numeric|min:0',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for duplicate combination if penjualan_id or data_sayur_id is being updated
            if ($request->has('penjualan_id') || $request->has('data_sayur_id')) {
                $penjualanId = $request->get('penjualan_id', $data->penjualan_id);
                $dataSayurId = $request->get('data_sayur_id', $data->data_sayur_id);
                
                $exists = PenjualanDetailBatch::where('penjualan_id', $penjualanId)
                    ->where('data_sayur_id', $dataSayurId)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kombinasi penjualan dan batch sayur sudah ada'
                    ], 422);
                }
            }

            $data->update($validator->validated());
            $data->load(['penjualan', 'dataSayur']);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'update',
                'description' => 'Mengubah detail penjualan batch: ' . $data->qty_kg . ' kg',
                'table_name' => 'penjualan_detail_batch',
                'record_id' => $data->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail penjualan batch berhasil diperbarui',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui detail penjualan batch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete penjualan detail batch
     */
    public function destroy($id): JsonResponse
    {
        try {
            $data = PenjualanDetailBatch::findOrFail($id);
            $qty = $data->qty_kg;

            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'delete',
                'description' => 'Menghapus detail penjualan batch: ' . $qty . ' kg',
                'table_name' => 'penjualan_detail_batch',
                'record_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail penjualan batch berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus detail penjualan batch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get penjualan detail batch by penjualan
     */
    public function getByPenjualan($penjualan_id): JsonResponse
    {
        try {
            $data = PenjualanDetailBatch::with(['penjualan', 'dataSayur'])
                ->where('penjualan_id', $penjualan_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data detail penjualan batch berhasil diambil',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail penjualan batch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get penjualan detail batch by data sayur
     */
    public function getByDataSayur($data_sayur_id): JsonResponse
    {
        try {
            $data = PenjualanDetailBatch::with(['penjualan', 'dataSayur'])
                ->where('data_sayur_id', $data_sayur_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data detail penjualan batch berhasil diambil',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail penjualan batch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary statistics
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $query = PenjualanDetailBatch::with(['penjualan', 'dataSayur']);

            // Filter by date range if provided
            if ($request->has('start_date') || $request->has('end_date')) {
                $query->whereHas('penjualan', function($q) use ($request) {
                    if ($request->has('start_date')) {
                        $q->whereDate('tanggal_penjualan', '>=', $request->start_date);
                    }
                    if ($request->has('end_date')) {
                        $q->whereDate('tanggal_penjualan', '<=', $request->end_date);
                    }
                });
            }

            $totalRecords = $query->count();
            $totalQty = $query->sum('qty_kg');

            // Group by jenis sayur
            $byJenisSayur = $query->join('data_sayur', 'penjualan_detail_batch.data_sayur_id', '=', 'data_sayur.id')
                ->selectRaw('data_sayur.jenis_sayur, COUNT(*) as count, SUM(qty_kg) as total_qty')
                ->groupBy('data_sayur.jenis_sayur')
                ->orderBy('total_qty', 'desc')
                ->get();

            // Group by varietas
            $byVarietas = $query->join('data_sayur', 'penjualan_detail_batch.data_sayur_id', '=', 'data_sayur.id')
                ->selectRaw('data_sayur.varietas, COUNT(*) as count, SUM(qty_kg) as total_qty')
                ->groupBy('data_sayur.varietas')
                ->orderBy('total_qty', 'desc')
                ->limit(10)
                ->get();

            // Monthly trends
            $monthlyTrends = $query->join('penjualan_sayur', 'penjualan_detail_batch.penjualan_id', '=', 'penjualan_sayur.id')
                ->selectRaw('
                    YEAR(penjualan_sayur.tanggal_penjualan) as year,
                    MONTH(penjualan_sayur.tanggal_penjualan) as month,
                    COUNT(*) as count,
                    SUM(qty_kg) as total_qty
                ')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();

            // Recent entries
            $recentEntries = $query->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan data penjualan detail batch berhasil diambil',
                'data' => [
                    'total_records' => $totalRecords,
                    'total_qty' => round($totalQty, 2),
                    'by_jenis_sayur' => $byJenisSayur,
                    'by_varietas' => $byVarietas,
                    'monthly_trends' => $monthlyTrends,
                    'recent_entries' => $recentEntries
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get batch performance analysis
     */
    public function getBatchPerformance(Request $request): JsonResponse
    {
        try {
            $query = PenjualanDetailBatch::with(['penjualan', 'dataSayur']);

            // Filter by date range if provided
            if ($request->has('start_date') || $request->has('end_date')) {
                $query->whereHas('penjualan', function($q) use ($request) {
                    if ($request->has('start_date')) {
                        $q->whereDate('tanggal_penjualan', '>=', $request->start_date);
                    }
                    if ($request->has('end_date')) {
                        $q->whereDate('tanggal_penjualan', '<=', $request->end_date);
                    }
                });
            }

            // Top performing batches
            $topBatches = $query->join('data_sayur', 'penjualan_detail_batch.data_sayur_id', '=', 'data_sayur.id')
                ->selectRaw('
                    data_sayur.id,
                    data_sayur.jenis_sayur,
                    data_sayur.varietas,
                    data_sayur.tanggal_tanam,
                    COUNT(*) as sales_count,
                    SUM(qty_kg) as total_sold,
                    AVG(qty_kg) as avg_qty_per_sale
                ')
                ->groupBy('data_sayur.id', 'data_sayur.jenis_sayur', 'data_sayur.varietas', 'data_sayur.tanggal_tanam')
                ->orderBy('total_sold', 'desc')
                ->limit(20)
                ->get();

            // Sales frequency by batch age
            $batchAgeAnalysis = $query->join('data_sayur', 'penjualan_detail_batch.data_sayur_id', '=', 'data_sayur.id')
                ->join('penjualan_sayur', 'penjualan_detail_batch.penjualan_id', '=', 'penjualan_sayur.id')
                ->selectRaw('
                    CASE 
                        WHEN DATEDIFF(penjualan_sayur.tanggal_penjualan, data_sayur.tanggal_tanam) <= 30 THEN "0-30 hari"
                        WHEN DATEDIFF(penjualan_sayur.tanggal_penjualan, data_sayur.tanggal_tanam) <= 60 THEN "31-60 hari"
                        WHEN DATEDIFF(penjualan_sayur.tanggal_penjualan, data_sayur.tanggal_tanam) <= 90 THEN "61-90 hari"
                        ELSE "90+ hari"
                    END as age_group,
                    COUNT(*) as count,
                    SUM(qty_kg) as total_qty,
                    AVG(qty_kg) as avg_qty
                ')
                ->groupBy('age_group')
                ->orderBy('age_group')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Analisis performa batch berhasil diambil',
                'data' => [
                    'top_batches' => $topBatches,
                    'batch_age_analysis' => $batchAgeAnalysis
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil analisis performa batch',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}