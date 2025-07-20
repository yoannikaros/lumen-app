<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseTransactions;

class ApiCrudTest extends TestCase
{
    use DatabaseTransactions;

    private $token;
    private $testData = [];
    private $testUserEmail;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticate();
    }

    private function authenticate()
    {
        $timestamp = time();
        $this->testUserEmail = 'testapi' . $timestamp . '@example.com';
        $registerData = [
            'username' => 'testapi' . $timestamp,
            'nama' => 'Test API User',
            'email' => $this->testUserEmail,
            'password' => 'password123'
        ];

        $response = $this->json('POST', '/api/register', $registerData);
        
        if ($response->response->getStatusCode() !== 201) {
            throw new \Exception('Failed to register test user');
        }

        $loginData = [
            'username' => 'testapi' . $timestamp,
            'password' => 'password123'
        ];

        $response = $this->json('POST', '/api/login', $loginData);
        
        if ($response->response->getStatusCode() !== 200) {
            throw new \Exception('Failed to login test user');
        }

        $responseData = json_decode($response->response->getContent(), true);
        
        if (!isset($responseData['data']['token'])) {
            throw new \Exception('Failed to get authentication token');
        }

        $this->token = $responseData['data']['token'];
    }

    private function getHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    public function testAreaKebunCrud()
    {
        // CREATE
        $createData = [
            'nama_area' => 'Test Area PHPUnit',
            'deskripsi' => 'Area test untuk PHPUnit testing',
            'luas_m2' => 100.5,
            'kapasitas_tanaman' => 500,
            'status' => 'aktif'
        ];

        $response = $this->json('POST', '/api/area-kebun', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->testData['area_kebun_id'] = $responseData['data']['id'];

        // READ (Index)
        $response = $this->json('GET', '/api/area-kebun', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // READ (Show)
        $response = $this->json('GET', '/api/area-kebun/' . $this->testData['area_kebun_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Test Area PHPUnit', $responseData['data']['nama_area']);

        // UPDATE
        $updateData = [
            'nama_area' => 'Test Area Updated',
            'deskripsi' => 'Area test updated untuk PHPUnit testing',
            'luas_m2' => 120.0,
            'kapasitas_tanaman' => 600,
            'status' => 'aktif'
        ];

        $response = $this->json('PUT', '/api/area-kebun/' . $this->testData['area_kebun_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Summary
        $response = $this->json('GET', '/api/area-kebun/summary', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // DELETE
        $response = $this->json('DELETE', '/api/area-kebun/' . $this->testData['area_kebun_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    public function testJenisPupukCrud()
    {
        // CREATE
        $createData = [
            'nama_pupuk' => 'NPK Test PHPUnit',
            'deskripsi' => 'Pupuk NPK untuk testing',
            'satuan' => 'kg',
            'harga_per_satuan' => 25000,
            'status' => 'aktif'
        ];

        $response = $this->json('POST', '/api/jenis-pupuk', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->testData['jenis_pupuk_id'] = $responseData['data']['id'];

        // READ (Index)
        $response = $this->json('GET', '/api/jenis-pupuk', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // READ (Show)
        $response = $this->json('GET', '/api/jenis-pupuk/' . $this->testData['jenis_pupuk_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('NPK Test PHPUnit', $responseData['data']['nama_pupuk']);

        // UPDATE
        $updateData = [
            'nama_pupuk' => 'NPK Test Updated',
            'deskripsi' => 'Pupuk NPK updated untuk testing',
            'satuan' => 'kg',
            'harga_per_satuan' => 30000,
            'status' => 'aktif'
        ];

        $response = $this->json('PUT', '/api/jenis-pupuk/' . $this->testData['jenis_pupuk_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Get Active
        $response = $this->json('GET', '/api/jenis-pupuk/active', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Summary
        $response = $this->json('GET', '/api/jenis-pupuk/summary', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // DELETE
        $response = $this->json('DELETE', '/api/jenis-pupuk/' . $this->testData['jenis_pupuk_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    public function testPencatatanPupukCrud()
    {
        // First create dependencies
        $this->createTestDependencies();

        // CREATE
        $createData = [
            'tanggal' => date('Y-m-d'),
            'jenis_pupuk_id' => $this->testData['jenis_pupuk_id'],
            'jumlah_pupuk' => 5.5,
            'satuan' => 'kg',
            'keterangan' => 'Pemupukan rutin test PHPUnit'
        ];

        $response = $this->json('POST', '/api/pencatatan-pupuk', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->testData['pencatatan_pupuk_id'] = $responseData['data']['id'];

        // READ (Index)
        $response = $this->json('GET', '/api/pencatatan-pupuk', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // READ (Show)
        $response = $this->json('GET', '/api/pencatatan-pupuk/' . $this->testData['pencatatan_pupuk_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // UPDATE
        $updateData = [
            'tanggal' => date('Y-m-d'),
            'jenis_pupuk_id' => $this->testData['jenis_pupuk_id'],
            'jumlah_pupuk' => 6.0,
            'satuan' => 'kg',
            'keterangan' => 'Pemupukan rutin test updated'
        ];

        $response = $this->json('PUT', '/api/pencatatan-pupuk/' . $this->testData['pencatatan_pupuk_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Summary
        $response = $this->json('GET', '/api/pencatatan-pupuk/summary', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // DELETE
        $response = $this->json('DELETE', '/api/pencatatan-pupuk/' . $this->testData['pencatatan_pupuk_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Clean up dependencies
        $this->cleanupTestDependencies();
    }

    public function testPenjualanSayurCrud()
    {
        // CREATE
        $createData = [
            'tanggal_penjualan' => date('Y-m-d'),
            'nama_pembeli' => 'Toko Test',
            'tipe_pembeli' => 'pasar',
            'alamat_pembeli' => 'Alamat Test',
            'jenis_sayur' => 'Selada Test',
            'jumlah_kg' => 5.5,
            'harga_per_kg' => 15000,
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'lunas',
            'keterangan' => 'Penjualan test PHPUnit'
        ];

        $response = $this->json('POST', '/api/penjualan-sayur', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->testData['penjualan_sayur_id'] = $responseData['data']['id'];

        // READ (Index)
        $response = $this->json('GET', '/api/penjualan-sayur', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // READ (Show)
        $response = $this->json('GET', '/api/penjualan-sayur/' . $this->testData['penjualan_sayur_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Selada Test', $responseData['data']['jenis_sayur']);

        // UPDATE
        $updateData = [
            'tanggal_penjualan' => date('Y-m-d'),
            'nama_pembeli' => 'Pasar Test',
            'tipe_pembeli' => 'hotel',
            'alamat_pembeli' => 'Alamat Test Updated',
            'jenis_sayur' => 'Selada Test Updated',
            'jumlah_kg' => 6.0,
            'harga_per_kg' => 16000,
            'metode_pembayaran' => 'transfer',
            'status_pembayaran' => 'lunas',
            'keterangan' => 'Penjualan test updated'
        ];

        $response = $this->json('PUT', '/api/penjualan-sayur/' . $this->testData['penjualan_sayur_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Summary
        $response = $this->json('GET', '/api/penjualan-sayur/summary', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // DELETE
        $response = $this->json('DELETE', '/api/penjualan-sayur/' . $this->testData['penjualan_sayur_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    public function testBelanjaModalCrud()
    {
        // CREATE
        $createData = [
            'tanggal_belanja' => date('Y-m-d'),
            'kategori' => 'benih',
            'deskripsi' => 'Benih Test PHPUnit',
            'jumlah' => 1000,
            'satuan' => 'biji',
            'nama_toko' => 'Toko Test',
            'alamat_toko' => 'Alamat Toko Test',
            'metode_pembayaran' => 'tunai',
            'bukti_pembayaran' => 'Bukti Test',
            'keterangan' => 'Pembelian test PHPUnit'
        ];

        $response = $this->json('POST', '/api/belanja-modal', $createData, $this->getHeaders());
        $this->assertEquals(201, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->testData['belanja_modal_id'] = $responseData['data']['id'];

        // READ (Index)
        $response = $this->json('GET', '/api/belanja-modal', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // READ (Show)
        $response = $this->json('GET', '/api/belanja-modal/' . $this->testData['belanja_modal_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Benih Test PHPUnit', $responseData['data']['deskripsi']);

        // UPDATE
        $updateData = [
            'tanggal_belanja' => date('Y-m-d'),
            'kategori' => 'pupuk',
            'deskripsi' => 'Benih Test Updated',
            'jumlah' => 1200,
            'satuan' => 'biji',
            'nama_toko' => 'Toko Test Updated',
            'alamat_toko' => 'Alamat Toko Test Updated',
            'metode_pembayaran' => 'transfer',
            'bukti_pembayaran' => 'Bukti Test Updated',
            'keterangan' => 'Pembelian test updated'
        ];

        $response = $this->json('PUT', '/api/belanja-modal/' . $this->testData['belanja_modal_id'], $updateData, $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Get Kategori
        $response = $this->json('GET', '/api/belanja-modal/kategori', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Summary
        $response = $this->json('GET', '/api/belanja-modal/summary', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // DELETE
        $response = $this->json('DELETE', '/api/belanja-modal/' . $this->testData['belanja_modal_id'], [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    public function testAuthenticationEndpoints()
    {
        // Test /me endpoint
        $response = $this->json('GET', '/api/me', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals($this->testUserEmail, $responseData['data']['email']);

        // Test logout
        $response = $this->json('POST', '/api/logout', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    public function testFilterAndPagination()
    {
        // Test pagination
        $response = $this->json('GET', '/api/area-kebun?page=1&per_page=5', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('current_page', $responseData['data']);
        $this->assertArrayHasKey('per_page', $responseData['data']);

        // Test date filter
        $today = date('Y-m-d');
        $response = $this->json('GET', '/api/pencatatan-pupuk?tanggal_dari=' . $today . '&tanggal_sampai=' . $today, [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Test search
        $response = $this->json('GET', '/api/area-kebun?search=test', [], $this->getHeaders());
        $this->assertEquals(200, $response->response->getStatusCode());
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    private function createTestDependencies()
    {
        // Create Jenis Pupuk for testing
        $createData = [
            'nama_pupuk' => 'NPK Dependency Test',
            'deskripsi' => 'Pupuk NPK untuk dependency testing',
            'satuan' => 'kg',
            'harga_per_satuan' => 25000,
            'status' => 'aktif'
        ];

        $response = $this->json('POST', '/api/jenis-pupuk', $createData, $this->getHeaders());
        $responseData = json_decode($response->response->getContent(), true);
        $this->testData['jenis_pupuk_id'] = $responseData['data']['id'];
    }

    private function cleanupTestDependencies()
    {
        if (isset($this->testData['jenis_pupuk_id'])) {
            $this->json('DELETE', '/api/jenis-pupuk/' . $this->testData['jenis_pupuk_id'], [], $this->getHeaders());
        }
    }
}