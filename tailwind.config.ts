import type { Config } from 'tailwindcss';

export default {
  content: ['./src/**/*.{astro,html,js,jsx,md,mdx,svelte,ts,tsx,vue}'],
  theme: {
    extend: {
      fontFamily: {
        inter: ['Inter', 'sans-serif'],
        montserrat: ['Montserrat', 'sans-serif'],
      },
      colors: {
        'primary-naranja': '#d57629',
        'primary-azul': '#2f656d',
        'primary-rojo': '#c02745',
        'primary-negro': '#333437',
      },
    },
  },
  plugins: [],
} satisfies Config;
