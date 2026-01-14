/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./public/**/*.{php,html,js}"],
  theme: {
    extend: {
      colors: {
        'soft-bg': '#FDFBF7',       // Background Putih Tulang (Lebih bersih)
        'soft-dark': '#2D2D2A',     // Hitam Arang (Bukan hitam pekat, lebih halus)
        'soft-white': '#FFFFFF',
        'soft-gray': '#F3F4F6',
        
        // Aksen Pastel Halus
        'pastel-yellow': '#FEF08A', 
        'pastel-blue': '#BFDBFE',
        'pastel-pink': '#FBCFE8',
        'pastel-green': '#BBF7D0',
        'pastel-purple': '#E9D5FF',
      },
      borderWidth: {
        '2': '2px', // Border tipis (sebelumnya 3px)
      },
      boxShadow: {
        // Shadow halus (offset lebih kecil)
        'soft': '3px 3px 0px 0px #2D2D2A', 
        'soft-hover': '1px 1px 0px 0px #2D2D2A',
      },
      borderRadius: {
        'xl': '0.75rem', // 12px
        '2xl': '1rem',   // 16px
      }
    },
  },
  plugins: [],
}