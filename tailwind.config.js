/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: "mz-",
  content: [
    "./**/*.php",
    './woocommerce/**/*.php',
    "./src/**/*.{js,css}",
  ],
  theme: {
    extend: {
      fontFamily: {
        heading: ["Plus Jakarta Sans", "system-ui", "sans-serif"],
        body: ["Poppins", "system-ui", "sans-serif"],
      },
      colors: {
        brand: {
          primary: "#C58BAA",   // Nude Rose
          secondary: "#F6EFEA", // Soft Beige
          accent: "#9B4A6A",    // Deep Rose
        },
        text: {
          heading: "#2B1C23",
          body: "#5A4A4F",
        },
      },
    },
  },
  plugins: [],
};
