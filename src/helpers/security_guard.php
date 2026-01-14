<?php
// Cek Login: Wajib dipanggil di setiap halaman public
function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../public/index.php");
        exit;
    }
}

// Cek Role: Membatasi akses (Misal Editor tidak boleh masuk halaman Admin)
function check_role($allowed_roles = []) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        echo "⛔ AKSES DITOLAK: Anda tidak memiliki izin ke halaman ini.";
        exit;
    }
}

// Sanitize Input (Mencegah XSS)
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>