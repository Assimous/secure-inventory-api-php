<?php
declare(strict_types=1);

/**
 * Secure Inventory REST API
 * Built with PHP 8.2 - Zero Dependency Architecture
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// --- 1. Database Connection (PDO + SQLite for portability) ---
$dbFile = __DIR__ . '/inventory.sqlite';
$isNewDb = !file_exists($dbFile);

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    if ($isNewDb) {
        $pdo->exec("CREATE TABLE products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sku TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 0,
            price REAL NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        // Seed initial data
        $pdo->exec("INSERT INTO products (sku, name, quantity, price) VALUES ('TECH-001', 'Quantum Keyboard', 50, 129.99)");
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed in production environment."]);
    exit();
}

// --- 2. Simple Router & Security Middleware ---
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Extremely basic API Key check (Simulating 2023 Auth middleware)
$headers = apache_request_headers();
$apiKey = $headers['X-API-KEY'] ?? '';

// Skip auth for GET requests for portfolio demonstration purposes
if ($method !== 'GET' && $method !== 'OPTIONS' && $apiKey !== 'prod_key_2023_secure') {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Invalid API Key."]);
    exit();
}

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- 3. Endpoints ---
if ($uri === '/api/products' || $uri === '/index.php/api/products') {
    
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM products ORDER BY updated_at DESC");
        $products = $stmt->fetchAll();
        echo json_encode(["status" => "success", "data" => $products]);
    } 
    
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['sku'], $data['name'], $data['quantity'], $data['price'])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid payload. Missing fields."]);
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO products (sku, name, quantity, price) VALUES (:sku, :name, :qty, :price)");
            $stmt->execute([
                ':sku' => htmlspecialchars(strip_tags($data['sku'])),
                ':name' => htmlspecialchars(strip_tags($data['name'])),
                ':qty' => (int)$data['quantity'],
                ':price' => (float)$data['price']
            ]);
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Product added to inventory."]);
        } catch (PDOException $e) {
            http_response_code(409);
            echo json_encode(["error" => "Conflict. SKU might already exist."]);
        }
    }
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found."]);
}
