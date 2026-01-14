<?php

/**
 * HELPER SCRAPER ULTIMATE V2 (Fix Session & Regex)
 * Fokus: Deep Search untuk Instagram Views dengan Authenticated Session
 */

function getRandomUserAgent() {
    $agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36'
    ];
    return $agents[array_rand($agents)];
}

function curlRequest($url) {
    // MENYUSUN COOKIE DARI DATA YANG ANDA BERIKAN
    // Ini membuat server mengira request berasal dari browser Anda yang sudah login
    $cookieData = [
        'sessionid'  => '79799481333%3AfIEyYmC5bdngom%3A9%3AAYjz0rEeu_w4IHvmoZiOEFXiKz1-JRU4x4Rh1lTqHQ',
        'ds_user_id' => '79799481333',
        'csrftoken'  => 'ELdReNol2oWvlhf0pHtFCO68b6XpAm9I',
        'datr'       => 'ZwNmaQwKCYpzNnKf-M0Ppf-E',
        'ig_did'     => 'CB6C8066-C485-4DB9-B826-46B9A046CC1B',
        'mid'        => 'aWYDaAALAAEvwT_DZ_7XZ86SptvS',
        'rur'        => '"HIL\05479799481333\0541799833829:01fecb5e87422183eae6c1048d557c2f9ae3b14a2a815365bee290785863d3b077064d79"'
    ];

    $cookieString = "";
    foreach ($cookieData as $key => $val) {
        $cookieString .= "$key=$val; ";
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "", 
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => false,
        // Hapus CookieJar/File, kita inject manual
        CURLOPT_USERAGENT => getRandomUserAgent(),
        CURLOPT_HTTPHEADER => [
            "Cookie: " . $cookieString, // INJECT COOKIE DI SINI
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.9",
            "Cache-Control: max-age=0",
            "Sec-Ch-Ua: \"Google Chrome\";v=\"123\", \"Not:A-Brand\";v=\"8\", \"Chromium\";v=\"123\"",
            "Sec-Ch-Ua-Mobile: ?0",
            "Sec-Ch-Ua-Platform: \"Windows\"",
            "Sec-Fetch-Dest: document",
            "Sec-Fetch-Mode: navigate",
            "Sec-Fetch-Site: same-origin", // Ubah ke same-origin agar lebih dipercaya
            "Sec-Fetch-User: ?1",
            "Upgrade-Insecure-Requests: 1"
        ],
    ]);
    
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) return false;
    return $response;
}

/**
 * SCRAPE TIKTOK (Tetap pakai TikWM karena paling stabil)
 */
function scrapeTikTok($url) {
    $cleanUrl = strtok($url, '?');
    $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($cleanUrl);
    $response = curlRequest($apiUrl);
    
    if ($response) {
        $json = json_decode($response, true);
        if (isset($json['data'])) {
            $d = $json['data'];
            return [
                'views'    => $d['play_count'] ?? 0,
                'likes'    => $d['digg_count'] ?? 0,
                'comments' => $d['comment_count'] ?? 0,
                'shares'   => $d['share_count'] ?? 0,
            ];
        }
    }
    return ['views' => 0, 'likes' => 0, 'comments' => 0, 'shares' => 0];
}

/**
 * SCRAPE INSTAGRAM (Deep Search Pattern with Session)
 */
function scrapeInstagram($url) {
    $cleanUrl = strtok($url, '?');
    
    // Request HTML dengan Cookie Login
    $html = curlRequest($cleanUrl);
    
    $data = [
        'views'    => 0,
        'likes'    => 0,
        'comments' => 0
    ];

    if ($html) {
        // Cek apakah cookie expired (redirect ke login page)
        if (strpos($html, 'Login • Instagram') !== false) {
            // Jika masuk sini, berarti cookie/sessionid sudah mati/logout
            return $data; 
        }

        // --- TEKNIK JSON (PRIORITAS UTAMA) ---
        // Karena login, IG memberikan data di dalam script JSON lebih lengkap
        
        // 1. Cek Likes (Pola: "like_count":1234)
        if (preg_match('/"like_count":(\d+)/', $html, $m)) {
            $data['likes'] = (int)$m[1];
        }

        // 2. Cek Comments (Pola: "comment_count":1234)
        if (preg_match('/"comment_count":(\d+)/', $html, $m)) {
            $data['comments'] = (int)$m[1];
        }

        // 3. Cek Views (Pola: "play_count":1234 atau "video_view_count":1234)
        if (preg_match('/"play_count":(\d+)/', $html, $m)) {
            $data['views'] = (int)$m[1];
        } elseif (preg_match('/"video_view_count":(\d+)/', $html, $m)) {
            $data['views'] = (int)$m[1];
        }

        // --- TEKNIK FALLBACK (META TAGS) ---
        // Jika JSON gagal atau struktur berubah, coba ambil dari Meta Tag OGP
        if ($data['likes'] == 0) {
            if (preg_match('/<meta\s+content="([^"]*)"\s+name="description"/i', $html, $matches) || 
                preg_match('/<meta\s+property="og:description"\s+content="([^"]*)"/i', $html, $matches)) {
                
                $content = $matches[1];
                
                if (preg_match('/([\d,.]+)\s*likes/i', $content, $l)) {
                    $data['likes'] = (int)str_replace([',', '.'], '', $l[1]);
                }
                if (preg_match('/([\d,.]+)\s*comments/i', $content, $c)) {
                    $data['comments'] = (int)str_replace([',', '.'], '', $c[1]);
                }
            }
        }
    }

    return $data;
}
?>