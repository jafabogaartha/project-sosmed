// Toggle Form Alur (Take vs Ready Stock)
function toggleProductionMode(mode) {
    const linkInput = document.getElementById('field-link-drive');
    const takeInfo = document.getElementById('field-take-info');
    
    if (mode === 'need_take') {
        linkInput.classList.add('hidden');
        takeInfo.classList.remove('hidden');
        document.getElementById('input_link_raw').required = false;
    } else {
        linkInput.classList.remove('hidden');
        takeInfo.classList.add('hidden');
        document.getElementById('input_link_raw').required = true;
    }
}

// Copy Script TikTok ke Instagram
function copyScript() {
    const ttScript = document.getElementById('script_tiktok').value;
    document.getElementById('caption_instagram').value = ttScript;
    alert('✅ Naskah TikTok berhasil disalin ke Caption Instagram!');
}

// Copy Text ke Clipboard dari ID element tertentu
function copyToClipboard(elementId) {
    const copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999); // Untuk mobile
    navigator.clipboard.writeText(copyText.value);
    alert('Copied to clipboard!');
}