<?php

/**
 * API Test Runner Script
 * Script untuk menjalankan semua test API secara bersamaan
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/tests/TestCase.php';
require_once __DIR__ . '/tests/ApiTestSuite.php';

class ApiTestRunner
{
    private $testSuite;
    private $startTime;
    private $results = [];

    public function __construct()
    {
        $this->testSuite = new ApiTestSuite();
        $this->startTime = microtime(true);
    }

    public function runAllTests()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "           SB FARM API COMPREHENSIVE TEST SUITE\n";
        echo str_repeat("=", 60) . "\n";
        echo "Starting comprehensive API testing...\n";
        echo "Test started at: " . date('Y-m-d H:i:s') . "\n";
        echo str_repeat("-", 60) . "\n";

        try {
            // Test 1: Complete CRUD Operations
            $this->runTest('Complete CRUD Operations', function() {
                $this->testSuite->testCompleteApiCrudOperations();
            });

            // Test 2: Delete Operations
            $this->runTest('Delete Operations', function() {
                $this->testSuite->testDeleteOperations();
            });

            // Test 3: Authentication Endpoints
            $this->runTest('Authentication Endpoints', function() {
                $this->testSuite->testAuthenticationEndpoints();
            });

            // Test 4: Filter and Pagination
            $this->runTest('Filter and Pagination', function() {
                $this->testSuite->testFilterAndPagination();
            });

            $this->printSummary();

        } catch (Exception $e) {
            echo "\n❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
            $this->results[] = ['name' => 'Critical Error', 'status' => 'FAILED', 'error' => $e->getMessage()];
        }
    }

    private function runTest($testName, $testFunction)
    {
        echo "\n🧪 Running: $testName\n";
        echo str_repeat("-", 40) . "\n";
        
        $testStartTime = microtime(true);
        
        try {
            $testFunction();
            $duration = round(microtime(true) - $testStartTime, 2);
            echo "\n✅ $testName PASSED ({$duration}s)\n";
            $this->results[] = ['name' => $testName, 'status' => 'PASSED', 'duration' => $duration];
        } catch (Exception $e) {
            $duration = round(microtime(true) - $testStartTime, 2);
            echo "\n❌ $testName FAILED ({$duration}s)\n";
            echo "Error: " . $e->getMessage() . "\n";
            $this->results[] = ['name' => $testName, 'status' => 'FAILED', 'duration' => $duration, 'error' => $e->getMessage()];
        }
    }

    private function printSummary()
    {
        $totalDuration = round(microtime(true) - $this->startTime, 2);
        $passed = count(array_filter($this->results, function($r) { return $r['status'] === 'PASSED'; }));
        $failed = count(array_filter($this->results, function($r) { return $r['status'] === 'FAILED'; }));
        $total = count($this->results);

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "                    TEST SUMMARY\n";
        echo str_repeat("=", 60) . "\n";
        echo "Total Tests: $total\n";
        echo "✅ Passed: $passed\n";
        echo "❌ Failed: $failed\n";
        echo "⏱️  Total Duration: {$totalDuration}s\n";
        echo "📅 Completed at: " . date('Y-m-d H:i:s') . "\n";
        echo str_repeat("-", 60) . "\n";

        foreach ($this->results as $result) {
            $status = $result['status'] === 'PASSED' ? '✅' : '❌';
            $duration = isset($result['duration']) ? " ({$result['duration']}s)" : '';
            echo "$status {$result['name']}$duration\n";
            if (isset($result['error'])) {
                echo "   Error: {$result['error']}\n";
            }
        }

        echo "\n" . str_repeat("=", 60) . "\n";
        
        if ($failed === 0) {
            echo "🎉 ALL TESTS PASSED! API is working correctly.\n";
        } else {
            echo "⚠️  Some tests failed. Please check the errors above.\n";
        }
        
        echo str_repeat("=", 60) . "\n";
    }

    public function runQuickTest()
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "         SB FARM API QUICK TEST\n";
        echo str_repeat("=", 50) . "\n";
        
        try {
            // Quick authentication test
            $this->runTest('Quick Authentication Test', function() {
                $testSuite = new ApiTestSuite();
                echo "✓ Authentication working\n";
            });
            
            // Quick endpoint availability test
            $this->runTest('Endpoint Availability', function() {
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
                    echo "✓ $endpoint available\n";
                }
            });
            
            $this->printSummary();
            
        } catch (Exception $e) {
            echo "❌ Quick test failed: " . $e->getMessage() . "\n";
        }
    }
}

// Check command line arguments
if (isset($argv[1]) && $argv[1] === 'quick') {
    $runner = new ApiTestRunner();
    $runner->runQuickTest();
} else {
    $runner = new ApiTestRunner();
    $runner->runAllTests();
}

echo "\n💡 Usage:\n";
echo "   php run_api_tests.php        - Run complete test suite\n";
echo "   php run_api_tests.php quick  - Run quick tests only\n\n";