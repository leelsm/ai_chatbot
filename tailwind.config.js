/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./*.{html,js,php}"],
  theme: {
    extend: {
      colors: {
        'kakao-yellow': '#FEE500',
        'kakao-gray': '#F2F3F5',
        'kakao-dark': '#1E1E1E',
        'kakao-light-gray': '#F8F9FA',
        'kakao-border': '#E5E5E5'
      }
    }
  },
  plugins: [],
}
