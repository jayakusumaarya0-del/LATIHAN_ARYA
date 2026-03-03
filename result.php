<?php
$query = isset($_GET['q']) ? $_GET['q'] : '';
$apiKey = "81b69acaeff11b72e12d55985ef9bbe8eed3bad0155da7dd72e6a38bcaf49e22";

// Pagination Logic
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

function getSerpData($params, $apiKey) {
    $allParams = array_merge($params, ["api_key" => $apiKey]);
    $apiUrl = "https://serpapi.com/search.json?" . http_build_query($allParams);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}

$ai_summary = "";
$products = [];
$videos = [];
$related_queries = [];
$total_items = 0;
$total_pages = 0;

if ($query) {
    $aiData = getSerpData([
        "engine" => "google_ai_mode", 
        "q" => $query,
        "hl" => "id",
        "gl" => "id"
    ], $apiKey);
    
    $ai_summary = $aiData['answer'] ?? $aiData['ai_overview'] ?? $aiData['generative_summary'] ?? "";

    $shoppingData = getSerpData([
        "engine" => "google_shopping", 
        "q" => $query, 
        "gl" => "id", 
        "hl" => "id"
    ], $apiKey);
    
    $all_products = $shoppingData['shopping_results'] ?? [];
    $related_queries = $shoppingData['related_searches'] ?? [];
    
    $total_items = count($all_products);
    $total_pages = ceil($total_items / $items_per_page);
    $products = array_slice($all_products, $offset, $items_per_page);

    $youtubeData = getSerpData([
        "engine" => "youtube", 
        "search_query" => $query . " review indonesia"
    ], $apiKey);
    $videos = $youtubeData['video_results'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartShopper - <?php echo htmlspecialchars($query); ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body onload="hideLoader()">

    <div id="loader-wrapper">
        <div class="loader"></div>
        <p style="color: var(--text-dim); margin-top: 15px; font-size: 0.8rem;">Mencari harga terbaik...</p>
    </div>

    <nav class="navbar">
        <div class="nav-content">
            <a href="index.php" class="logo">Smart<span>Shopper</span></a>
            <form action="result.php" method="GET" class="search-form-inline" onsubmit="showLoader()">
                <div class="input-group">
                    <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Cari barang..." required>
                    <button type="submit">Cari</button>
                </div>
            </form>
        </div>
    </nav>

    <main class="container">
        
        <?php if (!empty($ai_summary)): ?>
        <section class="ai-section">
            <div class="ai-card">
                <span class="ai-label">AI Overview</span>
                <div class="ai-content">
                    <p><?php echo nl2br(htmlspecialchars($ai_summary)); ?></p>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <div class="dashboard-layout">
            <section class="shopping-section">
                <div class="section-header">
                    <h2>Hasil Produk</h2>
                    <span class="badge"><?php echo $total_items; ?> ditemukan</span>
                </div>

                <div class="product-grid">
                    <?php if (empty($products)): ?>
                        <p class="empty-msg">Tidak ada produk ditemukan untuk "<?php echo htmlspecialchars($query); ?>"</p>
                    <?php else: ?>
                        <?php foreach($products as $item): ?>
                        <div class="product-card">
                            <div class="image-wrapper">
                                <img src="<?php echo $item['thumbnail']; ?>" alt="product" loading="lazy">
                            </div>
                            <div class="card-body">
                                <span class="source-label"><?php echo $item['source']; ?></span>
                                <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                <p class="price"><?php echo $item['price']; ?></p>
                                <a href="<?php echo $item['link']; ?>" target="_blank" class="btn-check">Cek Detail</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <?php if($current_page > 1): ?>
                        <a href="?q=<?php echo urlencode($query); ?>&page=<?php echo $current_page - 1; ?>" class="page-link" onclick="showLoader()">Sebelumnya</a>
                    <?php endif; ?>

                    <div class="page-numbers">
                        <?php 
                        $start = max(1, $current_page - 2);
                        $end = min($total_pages, $current_page + 2);
                        for($i = $start; $i <= $end; $i++): 
                        ?>
                            <a href="?q=<?php echo urlencode($query); ?>&page=<?php echo $i; ?>" 
                               class="page-num <?php echo ($i == $current_page) ? 'active' : ''; ?>" 
                               onclick="showLoader()"><?php echo $i; ?></a>
                        <?php endfor; ?>
                    </div>

                    <?php if($current_page < $total_pages): ?>
                        <a href="?q=<?php echo urlencode($query); ?>&page=<?php echo $current_page + 1; ?>" class="page-link" onclick="showLoader()">Berikutnya</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </section>

            <aside class="video-section">
                <div class="section-header"><h2>Video Review</h2></div>
                <div class="video-list">
                    <?php if (empty($videos)): ?>
                        <p class="empty-msg">Tidak ada video ditemukan.</p>
                    <?php else: ?>
                        <?php foreach(array_slice($videos, 0, 5) as $v): ?>
                        <a href="<?php echo $v['link']; ?>" target="_blank" class="video-card-mini">
                            <div class="thumb-container">
                                <img src="<?php echo $v['thumbnail']['static']; ?>" alt="video thumb">
                            </div>
                            <div class="video-meta">
                                <h4><?php echo htmlspecialchars($v['title']); ?></h4>
                                <p><?php echo htmlspecialchars($v['channel']['name']); ?></p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </aside>
        </div>

        <?php if(!empty($related_queries)): ?>
        <div class="related-section">
            <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Pencarian Terkait</h3>
            <div class="related-chips">
                <?php foreach($related_queries as $related): ?>
                    <a href="?q=<?php echo urlencode($related['query']); ?>" class="chip" onclick="showLoader()">
                        <?php echo htmlspecialchars($related['query']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <script>
        function showLoader() { document.getElementById('loader-wrapper').style.display = 'flex'; }
        function hideLoader() { document.getElementById('loader-wrapper').style.display = 'none'; }
    </script>
</body>
</html>