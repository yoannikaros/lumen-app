<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SimpleApiTest extends TestCase
{
    use DatabaseTransactions;
    public function testBasicEndpoint()
    {
        // Test basic endpoint without authentication
        $response = $this->get('/');
        $this->assertEquals(200, $response->response->getStatusCode());
    }

    public function testRegisterEndpoint()
    {
        // Test user registration
        $timestamp = time();
        $userData = [
            'username' => 'testuserSimple' . $timestamp,
            'nama' => 'Test User Simple',
            'email' => 'testsimple' . $timestamp . '@example.com',
            'password' => 'password123'
        ];

        $response = $this->post('/api/register', $userData);
        
        // Check if response is successful (201 or 200)
        $statusCode = $response->response->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 201]), 
            "Registration failed with status code: $statusCode. Response: " . $response->response->getContent());
        
        $responseData = json_decode($response->response->getContent(), true);
        $this->assertArrayHasKey('success', $responseData);
    }

    public function testLoginEndpoint()
    {
        // First register a user
        $timestamp = time();
        $userData = [
            'username' => 'testuserLogin' . $timestamp,
            'nama' => 'Test User Login',
            'email' => 'testlogin' . $timestamp . '@example.com',
            'password' => 'password123'
        ];

        $this->post('/api/register', $userData);

        // Then try to login
        $loginData = [
            'username' => 'testuserLogin' . $timestamp,
            'password' => 'password123'
        ];

        $response = $this->post('/api/login', $loginData);
        
        $statusCode = $response->response->getStatusCode();
        $responseContent = $response->response->getContent();
        
        echo "\nLogin Response Status: $statusCode\n";
        echo "Login Response Content: $responseContent\n";
        
        $this->assertTrue(in_array($statusCode, [200, 201]), 
            "Login failed with status code: $statusCode. Response: $responseContent");
        
        $responseData = json_decode($responseContent, true);
        $this->assertArrayHasKey('success', $responseData);
        
        if (isset($responseData['data']['token'])) {
            $this->assertNotEmpty($responseData['data']['token']);
            echo "\nToken received: " . substr($responseData['data']['token'], 0, 20) . "...\n";
        }
    }

    public function testApiEndpointsAvailability()
    {
        // Test if API endpoints are accessible (should return 401 for protected routes)
        $endpoints = [
            '/api/area-kebun',
            '/api/jenis-pupuk',
            '/api/pencatatan-pupuk',
            '/api/nutrisi-pupuk',
            '/api/data-sayur',
            '/api/penjualan-sayur',
            '/api/belanja-modal'
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->get($endpoint);
            $statusCode = $response->response->getStatusCode();
            
            // Should return 401 (Unauthorized) for protected routes
            $this->assertTrue(in_array($statusCode, [401, 403]), 
                "Endpoint $endpoint should return 401/403 but returned $statusCode");
            
            echo "✓ $endpoint returns $statusCode (as expected)\n";
        }
    }

    public function testDatabaseConnection()
    {
        // Test if we can connect to database by checking if we can create a simple query
        try {
            $pdo = new \PDO(
                'mysql:host=127.0.0.1;dbname=sb_farm_bigdata',
                'root',
                '',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            
            $stmt = $pdo->query('SELECT 1');
            $result = $stmt->fetch();
            
            $this->assertEquals(1, $result[0]);
            echo "\n✓ Database connection successful\n";
            
        } catch (\Exception $e) {
            $this->fail("Database connection failed: " . $e->getMessage());
        }
    }
}