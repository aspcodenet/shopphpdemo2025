
<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../Models/Database.php');
require_once(__DIR__ . '/../Models/Product.php');
$db = new Database();
$products = $db->getPopularProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopDemo - Välkommen!</title>
    <link href="/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-blue-700 text-white p-6 shadow">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">ShopDemo</h1>
            <nav>
                <a href="/" class="ml-4 hover:underline">Hem</a>
                <a href="/category" class="ml-4 hover:underline">Kategorier</a>
                <a href="/search" class="ml-4 hover:underline">Sök</a>
                <a href="/user/login" class="ml-4 hover:underline">Logga in</a>
            </nav>
        </div>
    </header>
    <main class="flex-1 container mx-auto py-16 px-4">
        <h2 class="text-4xl font-extrabold mb-4 text-gray-800 text-center">Välkommen till ShopDemo!</h2>
        <p class="text-lg text-gray-600 mb-8 text-center">Handla smart, snabbt och enkelt. Upptäck våra senaste produkter och erbjudanden.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php foreach($products as $product): ?>
                <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center transform transition duration-300 hover:-translate-y-2 hover:scale-105 hover:shadow-2xl group">
                    <div class="w-24 h-24 bg-gradient-to-tr from-blue-200 to-blue-400 rounded-full flex items-center justify-center mb-4 group-hover:rotate-6 transition-transform">
                        <span class="text-3xl font-bold text-blue-700">
                            <?php echo strtoupper(substr($product->title, 0, 2)); ?>
                        </span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800 group-hover:text-blue-700 transition-colors"><?php echo htmlspecialchars($product->title); ?></h3>
                    <p class="text-gray-500 mb-2">Kategori: <?php echo htmlspecialchars($product->categoryName); ?></p>
                    <p class="text-lg font-bold text-blue-700 mb-4"><?php echo number_format($product->price, 0, ',', ' '); ?> kr</p>
                    <p class="text-xs text-gray-400 mb-2">I lager: <?php echo $product->stockLevel; ?></p>
                    <a href="/category?cat=<?php echo urlencode($product->categoryName); ?>" class="mt-auto inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800 transition">Se fler</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <footer class="bg-gray-200 text-gray-600 text-center p-4 mt-auto">
        &copy; 2025 ShopDemo. Alla rättigheter förbehållna.
    </footer>
</body>
</html>
