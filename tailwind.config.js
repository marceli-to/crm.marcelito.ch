/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/livewire/flux-pro/stubs/**/*.blade.php",
    "./vendor/livewire/flux/stubs/**/*.blade.php",
    "./resources/views/vendor/livewire-dropzone/livewire/dropzone.blade.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
