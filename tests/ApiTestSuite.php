<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ApiTestSuite extends TestCase
{
    use DatabaseTransactions;

    private $token;
    private $testData = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticate();
    }

    private function authenticate()
    {
        // Register test user
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $this->post('/api/register', $userData);

        // Login to get token
        $response = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $data = json_decode($response->response->getContent(), true);
        $this->token = $data['data']['token'] ?? null;
    }

    private function getHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    public function testCompleteApiCrudOperations()
    {
        echo "\n=== Starting Complete API CRUD Testing ===\n";

        // Test all CRUD operations for each module
        $this->testAreaKebunCrud();
        $this->testJenisPupukCrud();
        $this->testPencatatanPupukCrud();
        $this->testNutrisiPupukCrud();
        $this->testDataSayurCrud();
        $this->testPenjualanSayurCrud();
        $this->testBelanjaModalCrud();

        echo "\n=== All API CRUD Tests Completed Successfully ===\n";
    }

    private function testAreaKebunCrud()
    {
        echo "\n--- Testing Area Kebun CRUD ---\n";

        // CREATE
        $createData = [
            'nama_area' => 'Area Test 1',
            'luas_area' => 100.5,
            'kapasitas_tanaman' => 500,
            'lokasi' => 'Greenhouse A',
            'status' => 'aktif'
        ];

        $response = $this->post('/api/area-kebun', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->testData['area_kebun_id'] = $responseData['data']['id'];
        echo "✓ Area Kebun Created: ID {$this->testData['area_kebun_id']}\n";

        // READ (Index)
        $response = $this->get('/api/area-kebun', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Area Kebun Index Retrieved\n";

        // READ (Show)
        $response = $this->get('/api/area-kebun/' . $this->testData['area_kebun_id'], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Area Kebun Detail Retrieved\n";

        // UPDATE
        $updateData = [
            'nama_area' => 'Area Test Updated',
            'luas_area' => 120.0,
            'kapasitas_tanaman' => 600,
            'lokasi' => 'Greenhouse A Updated',
            'status' => 'aktif'
        ];

        $response = $this->put('/api/area-kebun/' . $this->testData['area_kebun_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Area Kebun Updated\n";

        // Summary
        $response = $this->get('/api/area-kebun/summary', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Area Kebun Summary Retrieved\n";
    }

    private function testJenisPupukCrud()
    {
        echo "\n--- Testing Jenis Pupuk CRUD ---\n";

        // CREATE
        $createData = [
            'nama_pupuk' => 'NPK Test',
            'jenis' => 'cair',
            'kandungan_nutrisi' => 'N:15, P:15, K:15',
            'harga_per_kg' => 25000,
            'status' => 'aktif'
        ];

        $response = $this->post('/api/jenis-pupuk', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->testData['jenis_pupuk_id'] = $responseData['data']['id'];
        echo "✓ Jenis Pupuk Created: ID {$this->testData['jenis_pupuk_id']}\n";

        // READ (Index)
        $response = $this->get('/api/jenis-pupuk', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Jenis Pupuk Index Retrieved\n";

        // READ (Show)
        $response = $this->get('/api/jenis-pupuk/' . $this->testData['jenis_pupuk_id'], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Jenis Pupuk Detail Retrieved\n";

        // UPDATE
        $updateData = [
            'nama_pupuk' => 'NPK Test Updated',
            'jenis' => 'granul',
            'kandungan_nutrisi' => 'N:20, P:10, K:10',
            'harga_per_kg' => 30000,
            'status' => 'aktif'
        ];

        $response = $this->put('/api/jenis-pupuk/' . $this->testData['jenis_pupuk_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Jenis Pupuk Updated\n";

        // Get Active
        $response = $this->get('/api/jenis-pupuk/active', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Active Jenis Pupuk Retrieved\n";

        // Summary
        $response = $this->get('/api/jenis-pupuk/summary', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Jenis Pupuk Summary Retrieved\n";
    }

    private function testPencatatanPupukCrud()
    {
        echo "\n--- Testing Pencatatan Pupuk CRUD ---\n";

        // CREATE
        $createData = [
            'tanggal_pencatatan' => date('Y-m-d'),
            'jenis_pupuk_id' => $this->testData['jenis_pupuk_id'],
            'jumlah_kg' => 5.5,
            'harga_total' => 165000,
            'keterangan' => 'Pemupukan rutin test'
        ];

        $response = $this->post('/api/pencatatan-pupuk', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->testData['pencatatan_pupuk_id'] = $responseData['data']['id'];
        echo "✓ Pencatatan Pupuk Created: ID {$this->testData['pencatatan_pupuk_id']}\n";

        // READ (Index)
        $response = $this->get('/api/pencatatan-pupuk', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Pencatatan Pupuk Index Retrieved\n";

        // READ (Show)
        $response = $this->get('/api/pencatatan-pupuk/' . $this->testData['pencatatan_pupuk_id'], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Pencatatan Pupuk Detail Retrieved\n";

        // UPDATE
        $updateData = [
            'tanggal_pencatatan' => date('Y-m-d'),
            'jenis_pupuk_id' => $this->testData['jenis_pupuk_id'],
            'jumlah_kg' => 6.0,
            'harga_total' => 180000,
            'keterangan' => 'Pemupukan rutin test updated'
        ];

        $response = $this->put('/api/pencatatan-pupuk/' . $this->testData['pencatatan_pupuk_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Pencatatan Pupuk Updated\n";

        // Summary
        $response = $this->get('/api/pencatatan-pupuk/summary', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Pencatatan Pupuk Summary Retrieved\n";
    }

    private function testNutrisiPupukCrud()
    {
        echo "\n--- Testing Nutrisi Pupuk CRUD ---\n";

        // CREATE
        $createData = [
            'tanggal_pencatatan' => date('Y-m-d'),
            'area_kebun_id' => $this->testData['area_kebun_id'],
            'ppm_nutrisi' => 1200,
            'ph_air' => 6.5,
            'suhu_air' => 25.5,
            'volume_air_liter' => 100,
            'jumlah_pupuk_gram' => 120,
            'kondisi_cuaca' => 'cerah',
            'catatan' => 'Nutrisi optimal test'
        ];

        $response = $this->post('/api/nutrisi-pupuk', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->testData['nutrisi_pupuk_id'] = $responseData['data']['id'];
        echo "✓ Nutrisi Pupuk Created: ID {$this->testData['nutrisi_pupuk_id']}\n";

        // READ (Index)
        $response = $this->get('/api/nutrisi-pupuk', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Nutrisi Pupuk Index Retrieved\n";

        // READ (Show)
        $response = $this->get('/api/nutrisi-pupuk/' . $this->testData['nutrisi_pupuk_id'], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Nutrisi Pupuk Detail Retrieved\n";

        // UPDATE
        $updateData = [
            'tanggal_pencatatan' => date('Y-m-d'),
            'area_kebun_id' => $this->testData['area_kebun_id'],
            'ppm_nutrisi' => 1300,
            'ph_air' => 6.8,
            'suhu_air' => 26.0,
            'volume_air_liter' => 110,
            'jumlah_pupuk_gram' => 130,
            'kondisi_cuaca' => 'berawan',
            'catatan' => 'Nutrisi optimal test updated'
        ];

        $response = $this->put('/api/nutrisi-pupuk/' . $this->testData['nutrisi_pupuk_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Nutrisi Pupuk Updated\n";

        // Get Areas
        $response = $this->get('/api/nutrisi-pupuk/areas', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Nutrisi Pupuk Areas Retrieved\n";

        // Summary
        $response = $this->get('/api/nutrisi-pupuk/summary', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Nutrisi Pupuk Summary Retrieved\n";
    }

    private function testDataSayurCrud()
    {
        echo "\n--- Testing Data Sayur CRUD ---\n";

        // CREATE
        $createData = [
            'tanggal_tanam' => date('Y-m-d'),
            'jenis_sayur' => 'Selada',
            'area_kebun_id' => $this->testData['area_kebun_id'],
            'jumlah_bibit' => 100,
            'metode_tanam' => 'hidroponik',
            'target_panen' => date('Y-m-d', strtotime('+30 days')),
            'status_panen' => 'belum_panen',
            'catatan' => 'Penanaman selada test'
        ];

        $response = $this->post('/api/data-sayur', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->testData['data_sayur_id'] = $responseData['data']['id'];
        echo "✓ Data Sayur Created: ID {$this->testData['data_sayur_id']}\n";

        // READ (Index)
        $response = $this->get('/api/data-sayur', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Data Sayur Index Retrieved\n";

        // READ (Show)
        $response = $this->get('/api/data-sayur/' . $this->testData['data_sayur_id'], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Data Sayur Detail Retrieved\n";

        // UPDATE
        $updateData = [
            'tanggal_tanam' => date('Y-m-d'),
            'jenis_sayur' => 'Selada Hijau',
            'area_kebun_id' => $this->testData['area_kebun_id'],
            'jumlah_bibit' => 120,
            'metode_tanam' => 'hidroponik',
            'target_panen' => date('Y-m-d', strtotime('+35 days')),
            'status_panen' => 'sedang_tumbuh',
            'tanggal_panen' => null,
            'hasil_panen_kg' => null,
            'catatan' => 'Penanaman selada test updated'
        ];

        $response = $this->put('/api/data-sayur/' . $this->testData['data_sayur_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Data Sayur Updated\n";

        // Get Areas
        $response = $this->get('/api/data-sayur/areas', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Data Sayur Areas Retrieved\n";

        // Summary
        $response = $this->get('/api/data-sayur/summary', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Data Sayur Summary Retrieved\n";
    }

    private function testPenjualanSayurCrud()
    {
        echo "\n--- Testing Penjualan Sayur CRUD ---\n";

        // CREATE
        $createData = [
            'tanggal_penjualan' => date('Y-m-d'),
            'jenis_sayur' => 'Selada',
            'jumlah_kg' => 5.5,
            'harga_per_kg' => 15000,
            'tipe_pembeli' => 'retail',
            'nama_pembeli' => 'Toko Sayur Segar',
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'lunas',
            'catatan' => 'Penjualan selada test'
        ];

        $response = $this->post('/api/penjualan-sayur', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->testData['penjualan_sayur_id'] = $responseData['data']['id'];
        echo "✓ Penjualan Sayur Created: ID {$this->testData['penjualan_sayur_id']}\n";

        // READ (Index)
        $response = $this->get('/api/penjualan-sayur', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Penjualan Sayur Index Retrieved\n";

        // READ (Show)
        $response = $this->get('/api/penjualan-sayur/' . $this->testData['penjualan_sayur_id'], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Penjualan Sayur Detail Retrieved\n";

        // UPDATE
        $updateData = [
            'tanggal_penjualan' => date('Y-m-d'),
            'jenis_sayur' => 'Selada Hijau',
            'jumlah_kg' => 6.0,
            'harga_per_kg' => 16000,
            'tipe_pembeli' => 'grosir',
            'nama_pembeli' => 'Pasar Induk Sayur',
            'metode_pembayaran' => 'transfer',
            'status_pembayaran' => 'lunas',
            'catatan' => 'Penjualan selada test updated'
        ];

        $response = $this->put('/api/penjualan-sayur/' . $this->testData['penjualan_sayur_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Penjualan Sayur Updated\n";

        // Summary
        $response = $this->get('/api/penjualan-sayur/summary', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Penjualan Sayur Summary Retrieved\n";
    }

    private function testBelanjaModalCrud()
    {
        echo "\n--- Testing Belanja Modal CRUD ---\n";

        // CREATE
        $createData = [
            'tanggal_belanja' => date('Y-m-d'),
            'kategori' => 'benih',
            'nama_item' => 'Benih Selada Import',
            'jumlah' => 1000,
            'satuan' => 'biji',
            'harga_satuan' => 500,
            'metode_pembayaran' => 'tunai',
            'supplier' => 'Toko Benih Unggul',
            'keterangan' => 'Pembelian benih test'
        ];

        $response = $this->post('/api/belanja-modal', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->testData['belanja_modal_id'] = $responseData['data']['id'];
        echo "✓ Belanja Modal Created: ID {$this->testData['belanja_modal_id']}\n";

        // READ (Index)
        $response = $this->get('/api/belanja-modal', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Belanja Modal Index Retrieved\n";

        // READ (Show)
        $response = $this->get('/api/belanja-modal/' . $this->testData['belanja_modal_id'], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Belanja Modal Detail Retrieved\n";

        // UPDATE
        $updateData = [
            'tanggal_belanja' => date('Y-m-d'),
            'kategori' => 'benih',
            'nama_item' => 'Benih Selada Import Premium',
            'jumlah' => 1200,
            'satuan' => 'biji',
            'harga_satuan' => 600,
            'metode_pembayaran' => 'transfer',
            'supplier' => 'Toko Benih Unggul Premium',
            'keterangan' => 'Pembelian benih test updated'
        ];

        $response = $this->put('/api/belanja-modal/' . $this->testData['belanja_modal_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Belanja Modal Updated\n";

        // Get Kategori
        $response = $this->get('/api/belanja-modal/kategori', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Belanja Modal Kategori Retrieved\n";

        // Summary
        $response = $this->get('/api/belanja-modal/summary', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Belanja Modal Summary Retrieved\n";
    }

    public function testDeleteOperations()
    {
        echo "\n=== Testing Delete Operations ===\n";

        // Delete in reverse order to avoid foreign key constraints
        if (isset($this->testData['belanja_modal_id'])) {
            $response = $this->delete('/api/belanja-modal/' . $this->testData['belanja_modal_id'], [], $this->getHeaders());
            $this->assertEquals(200, $response->response->getStatusCode());
            echo "✓ Belanja Modal Deleted\n";
        }

        if (isset($this->testData['penjualan_sayur_id'])) {
            $response = $this->delete('/api/penjualan-sayur/' . $this->testData['penjualan_sayur_id'], [], $this->getHeaders());
            $this->assertEquals(200, $response->response->getStatusCode());
            echo "✓ Penjualan Sayur Deleted\n";
        }

        if (isset($this->testData['data_sayur_id'])) {
            $response = $this->delete('/api/data-sayur/' . $this->testData['data_sayur_id'], [], $this->getHeaders());
            $this->assertEquals(200, $response->response->getStatusCode());
            echo "✓ Data Sayur Deleted\n";
        }

        if (isset($this->testData['nutrisi_pupuk_id'])) {
            $response = $this->delete('/api/nutrisi-pupuk/' . $this->testData['nutrisi_pupuk_id'], [], $this->getHeaders());
            $this->assertEquals(200, $response->response->getStatusCode());
            echo "✓ Nutrisi Pupuk Deleted\n";
        }

        if (isset($this->testData['pencatatan_pupuk_id'])) {
            $response = $this->delete('/api/pencatatan-pupuk/' . $this->testData['pencatatan_pupuk_id'], [], $this->getHeaders());
            $this->assertEquals(200, $response->response->getStatusCode());
            echo "✓ Pencatatan Pupuk Deleted\n";
        }

        if (isset($this->testData['jenis_pupuk_id'])) {
            $response = $this->delete('/api/jenis-pupuk/' . $this->testData['jenis_pupuk_id'], [], $this->getHeaders());
            $this->assertEquals(200, $response->response->getStatusCode());
            echo "✓ Jenis Pupuk Deleted\n";
        }

        if (isset($this->testData['area_kebun_id'])) {
            $response = $this->delete('/api/area-kebun/' . $this->testData['area_kebun_id'], [], $this->getHeaders());
            $this->assertEquals(200, $response->response->getStatusCode());
            echo "✓ Area Kebun Deleted\n";
        }

        echo "\n=== All Delete Operations Completed ===\n";
    }

    public function testAuthenticationEndpoints()
    {
        echo "\n=== Testing Authentication Endpoints ===\n";

        // Test /me endpoint
        $response = $this->get('/api/me', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ User Profile Retrieved\n";

        // Test logout
        $response = $this->post('/api/logout', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ User Logged Out\n";

        echo "\n=== Authentication Tests Completed ===\n";
    }

    public function testFilterAndPagination()
    {
        echo "\n=== Testing Filter and Pagination ===\n";

        // Re-authenticate for this test
        $this->authenticate();

        // Test pagination
        $response = $this->get('/api/area-kebun?page=1&per_page=5', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Pagination Test Passed\n";

        // Test date filter
        $today = date('Y-m-d');
        $response = $this->get('/api/pencatatan-pupuk?tanggal_dari=' . $today . '&tanggal_sampai=' . $today, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Date Filter Test Passed\n";

        // Test search
        $response = $this->get('/api/area-kebun?search=test', $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        echo "✓ Search Filter Test Passed\n";

        echo "\n=== Filter and Pagination Tests Completed ===\n";
    }
}