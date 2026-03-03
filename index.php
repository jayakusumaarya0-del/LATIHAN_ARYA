<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartShopper | Pembanding Harga & Review</title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        /* CSS Tambahan khusus untuk halaman Index agar transisi smooth */
        body {
            overflow: hidden; /* Mencegah scroll di halaman hero */
        }
        
        #loader-wrapper p {
            margin-top: 15px;
            color: var(--text-dim);
            font-size: 0.9rem;
            letter-spacing: 1px;
        }
    </style>
</head>
<body onload="hideLoader()">

    <div id="loader-wrapper" style="display: none;">
        <div class="loader"></div>
        <p>Mempersiapkan data terbaik...</p>
    </div>

    <div class="hero-container">
        <div class="search-card">
            <div class="logo-large">
                <h1>Smart<span>Shopper</span></h1>
            </div>
            
            <p class="tagline">Temukan harga termurah, perbandingan toko, dan ulasan video dalam satu kali klik.</p>
            
            <form action="result.php" method="GET" onsubmit="showLoader()">
                <div class="input-group">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Cari gadget, sepatu, atau barang lainnya..." 
                        required 
                        autocomplete="off"
                        autofocus
                    >
                    <button type="submit">Cari Sekarang</button>
                </div>
            </form>

            <div class="trending-tags">
                <span class="trending-label">Populer Saat Ini:</span>
                <div class="tags-wrapper">
                    <a href="result.php?q=iPhone+16+Pro" onclick="showLoader()">iPhone 16 Pro</a>
                    <a href="result.php?q=Samsung+S24+Ultra" onclick="showLoader()">S24 Ultra</a>
                    <a href="result.php?q=Mechanical+Keyboard" onclick="showLoader()">Keyboard</a>
                    <a href="result.php?q=Sepatu+Running" onclick="showLoader()">Sepatu Running</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi memunculkan loader saat form disubmit atau link diklik
        function showLoader() {
            const loader = document.getElementById('loader-wrapper');
            loader.style.display = 'flex';
            
            // Efek memudar halus untuk konten hero saat loading
            document.querySelector('.hero-container').style.opacity = '0.4';
            document.querySelector('.hero-container').style.filter = 'blur(4px)';
        }
        
        // Fungsi menyembunyikan loader saat halaman siap (antisipasi tombol back)
        function hideLoader() {
            const loader = document.getElementById('loader-wrapper');
            loader.style.display = 'none';
            
            document.querySelector('.hero-container').style.opacity = '1';
            document.querySelector('.hero-container').style.filter = 'none';
        }

        // Shortcut: Tekan '/' untuk langsung fokus ke input pencarian
        document.addEventListener('keydown', function(e) {
            if (e.key === '/') {
                e.preventDefault();
                document.querySelector('input[name="q"]').focus();
            }
        });
    </script>
</body>
</html>

//oilah 