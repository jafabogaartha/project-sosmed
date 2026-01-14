<?php

/**
 * HELPER SCRAPER SEDERHANA
 * Menggunakan cURL untuk mengambil data publik.
 * Note: TikTok/IG sering update keamanan, jadi metode ini adalah 'best effort'.
 */

function randomUserAgent() {
    $agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
    ];
    return $agents[array_rand($agents)];
}

function curlGet($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => randomUserAgent(),
        CURLOPT_HTTPHEADER => [
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5"
        ]
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

// SCRAPE TIKTOK (Menggunakan Pola Regex JSON)
function scrapeTikTok($url) {
    $html = curlGet($url);
    if (!$html) return false;

    // Coba cari data JSON di dalam source code TikTok
    // Pola ini bisa berubah sewaktu-waktu tergantung update TikTok
    if (preg_match('/<script id="SIGI_STATE" type="application\/json">(.*?)<\/script>/s', $html, $matches) || 
        preg_match('/<script id="__UNIVERSAL_DATA_FOR_REHYDRATION__" type="application\/json">(.*?)<\/script>/s', $html, $matches)) {
        
        $json = json_decode($matches[1], true);
        
        // Parsing data (Struktur TikTok sering berubah, ini logic umum)
        $stats = $json['ItemModule'][array_key_first($json['ItemModule'])]['stats'] ?? null;
        
        if ($stats) {
            return [
                'views'    => $stats['playCount'] ?? 0,
                'likes'    => $stats['diggCount'] ?? 0,
                'comments' => $stats['commentCount'] ?? 0,
                'shares'   => $stats['shareCount'] ?? 0,
            ];
        }
    }
    
    // Fallback nilai 0 jika gagal scrape (karena proteksi TikTok)
    return ['views' => 0, 'likes' => 0, 'comments' => 0, 'shares' => 0];
}

// SCRAPE INSTAGRAM (Menggunakan oEmbed Resmi)
function scrapeInstagram($url) {
    // API Publik Instagram untuk embed (Lebih stabil daripada scrape HTML langsung)
    $apiUrl = "https://api.instagram.com/oembed/?url=" . urlencode($url);
    $json_str = curlGet($apiUrl);
    
    if ($json_str) {
        $data = json_decode($json_str, true);
        if ($data) {
            // Instagram oEmbed tidak memberikan data angka detail, 
            // jadi kita return 0 atau dummy data untuk demo.
            // Untuk data real IG, wajib pakai API Graph Facebook (Login required).
            return [
                'views' => 0, // IG oEmbed tidak kasih views
                'likes' => 0, 
                'comments' => 0
            ];
        }
    }
    return ['views' => 0, 'likes' => 0, 'comments' => 0];
}
?>