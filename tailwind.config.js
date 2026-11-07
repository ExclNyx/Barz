/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./**/*.html",
    "./**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        bg: '#F9F7F2',
        surface: '#FFF8F0',
        ink: '#354024',
        muted: '#5A5A5A',
        accent: '#889063',
        'accent-hi': '#767E54',
        taupe: '#C9A876',
        cafe: '#4C3D19',
        bone: '#E5D7C4',
        line: '#CFBB99',
        ok: '#6FA86F',
        bad: '#D97F6E',
      },
      borderRadius: {
        card: '16px',
        btn: '16px',
        input: '8px',
      },
      boxShadow: {
        card: '0 2px 8px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.03)',
        'card-hover': '0 12px 32px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.05)',
      },
    },
  },
  plugins: [],
}