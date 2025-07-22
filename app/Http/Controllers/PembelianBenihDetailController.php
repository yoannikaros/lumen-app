<?php

namespace App\Http\Controllers;

use App\Models\PembelianBenihDetail;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembelianBenihDetailController extends Controller
{
    /**
     * Get all pembelian benih detail with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PembelianBenihDetail::with(['belanjaModal']);

            // Filter by belanja_modal_id
            if ($request->has('belanja_modal_id')) {
                $query->where('belanja_modal_id', $request->belanja_modal_id);
            }

            // Filter by nama_benih
            if ($request->has('nama_benih')) {
                $query->where('nama_benih', 'like', '%' . $request->nama_benih . '%');
            }

            // Filter by varietas
            if ($request->has('varietas')) {
                $query->where('varietas', 'like', '%' . $request->varietas . '%');
            }

            // Filter by unit
            if ($request->has('unit')) {
                $query->where('unit', $request->unit);
            }

            // Filter by qty range
            if ($request->has('min_qty')) {
                $query->where('qty', '>=', $request->min_qty);
            }
            if ($request->has('max_qty')) {
                $query->where('qty', '<=', $request->max_qty);
            }

            // Filter by harga range
            if ($request->has('min_harga')) {
                $query->where('harga_per_unit', '>=', $request->min_harga);
            }
            if ($request->has('max_harga')) {
                $query->where('harga_per_unit', '<=', $request->max_harga);
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
                'message' => 'Data pembelian benih detail berhasil diambil',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembelian benih detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new pembelian benih detail
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'belanja_modal_id' => 'required|integer|exists:belanja_modal,id',
                'nama_benih' => 'required|string|max:100',
                'varietas' => 'nullable|string|max:100',
                'qty' => 'required|numeric|min:0',
                'unit' => 'required|in:gram,biji,pak,lainnya',
                'harga_per_unit' => 'nullable|numeric|min:0',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = PembelianBenihDetail::create($validator->validated());
            $data->load(['belanjaModal']);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'create',
                'description' => 'Menambah detail pembelian benih: ' . $data->nama_benih,
                'table_name' => 'pembelian_benih_detail',
                'record_id' => $data->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail pembelian benih berhasil ditambahkan',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan detail pembelian benih',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific pembelian benih detail
     */
    public function show($id): JsonResponse
    {
        try {
            $data = PembelianBenihDetail::with(['belanjaModal'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail pembelian benih berhasil diambil',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail pembelian benih tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update pembelian benih detail
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = PembelianBenihDetail::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'belanja_modal_id' => 'sometimes|required|integer|exists:belanja_modal,id',
                'nama_benih' => 'sometimes|required|string|max:100',
                'varietas' => 'nullable|string|max:100',
                'qty' => 'sometimes|required|numeric|min:0',
                'unit' => 'sometimes|required|in:gram,biji,pak,lainnya',
                'harga_per_unit' => 'nullable|numeric|min:0',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data->update($validator->validated());
            $data->load(['belanjaModal']);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'update',
                'description' => 'Mengubah detail pembelian benih: ' . $data->nama_benih,
                'table_name' => 'pembelian_benih_detail',
                'record_id' => $data->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail pembelian benih berhasil diperbarui',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui detail pembelian benih',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete pembelian benih detail
     */
    public function destroy($id): JsonResponse
    {
        try {
            $data = PembelianBenihDetail::findOrFail($id);
            $nama_benih = $data->nama_benih;

            $data->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'delete',
                'description' => 'Menghapus detail pembelian benih: ' . $nama_benih,
                'table_name' => 'pembelian_benih_detail',
                'record_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail pembelian benih berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus detail pembelian benih',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pembelian benih detail by belanja modal
     */
    public function getByBelanjaModal($belanja_modal_id): JsonResponse
    {
        try {
            $data = PembelianBenihDetail::with(['belanjaModal'])
                ->where('belanja_modal_id', $belanja_modal_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data detail pembelian benih berhasil diambil',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail pembelian benih',
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
            $query = PembelianBenihDetail::query();

            // Filter by date range if provided
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $totalRecords = $query->count();
            $totalQty = $query->sum('qty');
            $totalValue = $query->selectRaw('SUM(qty * COALESCE(harga_per_unit, 0)) as total')->value('total');
            $avgPrice = $query->whereNotNull('harga_per_unit')->avg('harga_per_unit');

            // Group by unit
            $byUnit = $query->selectRaw('unit, COUNT(*) as count, SUM(qty) as total_qty')
                ->groupBy('unit')
                ->get();

            // Group by nama_benih
            $byBenih = $query->selectRaw('nama_benih, COUNT(*) as count, SUM(qty) as total_qty')
                ->groupBy('nama_benih')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            // Recent entries
            $recentEntries = $query->with(['belanjaModal'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan data pembelian benih detail berhasil diambil',
                'data' => [
                    'total_records' => $totalRecords,
                    'total_qty' => round($totalQty, 2),
                    'total_value' => round($totalValue, 2),
                    'avg_price' => round($avgPrice, 2),
                    'by_unit' => $byUnit,
                    'by_benih' => $byBenih,
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
     * Get price analysis
     */
    public function getPriceAnalysis(Request $request): JsonResponse
    {
        try {
            $query = PembelianBenihDetail::whereNotNull('harga_per_unit');

            // Filter by nama_benih if provided
            if ($request->has('nama_benih')) {
                $query->where('nama_benih', $request->nama_benih);
            }

            // Filter by unit if provided
            if ($request->has('unit')) {
                $query->where('unit', $request->unit);
            }

            // Price trends by month
            $priceTrends = $query->selectRaw('
                    YEAR(created_at) as year,
                    MONTH(created_at) as month,
                    AVG(harga_per_unit) as avg_price,
                    MIN(harga_per_unit) as min_price,
                    MAX(harga_per_unit) as max_price,
                    COUNT(*) as count
                ')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();

            // Price comparison by benih
            $priceComparison = PembelianBenihDetail::selectRaw('
                    nama_benih,
                    unit,
                    AVG(harga_per_unit) as avg_price,
                    MIN(harga_per_unit) as min_price,
                    MAX(harga_per_unit) as max_price,
                    COUNT(*) as purchase_count
                ')
                ->whereNotNull('harga_per_unit')
                ->groupBy('nama_benih', 'unit')
                ->orderBy('avg_price', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Analisis harga berhasil diambil',
                'data' => [
                    'price_trends' => $priceTrends,
                    'price_comparison' => $priceComparison
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil analisis harga',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}