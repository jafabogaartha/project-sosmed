<?php

/**
 * Helper untuk merender badge status dengan gaya Soft Neobrutalism.
 * Digunakan secara konsisten di seluruh aplikasi (Dashboard, Detail Brand, Detail Task).
 */
function ui_badge_status($status) {
    // Style Dasar: Border 1.5px, Shadow Halus, Font Bold, No Emoticon
    // Padding, rounded, dan shadow disesuaikan untuk gaya Soft Neobrutalism
    $base = "px-3 py-1 border-[1.5px] border-stone-900 rounded-lg text-[10px] font-bold shadow-[2px_2px_0px_0px_#1c1917] inline-block tracking-wide uppercase whitespace-nowrap transition-all";

    switch ($status) {
        // --- 1. TAHAP PERENCANAAN / SHOOTING ---
        case 'planning':
        case 'waiting_footage':
            return "<span class='$base bg-stone-100 text-stone-600'>Proses Planning</span>";
        
        // --- 2. TAHAP VOICE OVER ---
        case 'process_vo':
            return "<span class='$base bg-yellow-100 text-yellow-800'>Proses VO</span>";
        
        // --- 3. TAHAP EDITING ---
        case 'editing':
        case 'ready_to_edit': // Mendukung data lama
            return "<span class='$base bg-blue-100 text-blue-800'>Proses Editing</span>";
        
        // --- 4. TAHAP QUALITY CONTROL (Editor Selesai) ---
        case 'ready_upload':
        case 'ready_to_post': // Mendukung data lama
            return "<span class='$base bg-purple-100 text-purple-800'>Siap Upload</span>";
        
        // --- 5. TAHAP POSTING (Oleh Specialist) ---
        case 'uploaded':
            return "<span class='$base bg-indigo-100 text-indigo-800'>Uploaded</span>";

        // --- 6. TAHAP FINAL (Sudah Input URL) ---
        case 'published':
            return "<span class='$base bg-green-100 text-green-800'>Final Published</span>";
            
        // --- FALLBACK JIKA STATUS TIDAK DIKENALI ---
        default:
            return "<span class='$base bg-red-50 text-red-600 uppercase italic'>Unmapped: $status</span>";
    }
}

/**
 * Opsional: Helper tambahan untuk warna avatar user agar konsisten
 */
function ui_avatar_style($color_class) {
    return "w-10 h-10 rounded-xl border-[1.5px] border-stone-900 shadow-[2px_2px_0px_0px_#1c1917] flex items-center justify-center text-white font-bold " . $color_class;
}
