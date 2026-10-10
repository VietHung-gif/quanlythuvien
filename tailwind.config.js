import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Be Vietnam Pro"', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'ui-monospace', 'SFMono-Regular', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // Lớp phân tầng nền Canvas & Surface (Stripe & Linear style)
                canvas: {
                    DEFAULT: '#f8fafc', // Nền tổng thể trang (slate-50)
                    surface: '#ffffff', // Nền Card & Bảng biểu
                    subtle: '#f1f5f9',  // Khối phụ trợ (slate-100)
                },
                // Hệ màu nhận diện thương hiệu Deep Royal Indigo
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5', // Primary CTA chính
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                },
                // Accent Sky ngọc thanh thoát
                accent: {
                    sky: '#0ea5e9',
                    'sky-light': '#f0f9ff',
                },
                // Backward-compatibility: Ánh xạ an toàn sang Light Mode
                cyber: {
                    950: '#f8fafc',
                    900: '#f1f5f9',
                    800: '#e2e8f0',
                    700: '#cbd5e1',
                    600: '#94a3b8',
                },
                neon: {
                    cyan: '#0284c7',
                    pink: '#e11d48',
                    green: '#059669',
                    amber: '#d97706',
                    purple: '#4f46e5',
                },
            },
            boxShadow: {
                'subtle': '0 1px 2px 0 rgba(0, 0, 0, 0.03)',
                'card': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04)',
                'card-hover': '0 10px 25px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                'popover': '0 10px 38px -10px rgba(22, 23, 24, 0.15), 0 10px 20px -15px rgba(22, 23, 24, 0.1)',
                'card-sm': '0 1px 2px 0 rgba(0, 0, 0, 0.03)',
                'card-md': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04)',
            },
            borderColor: {
                subtle: 'rgba(226, 232, 240, 0.8)',
                'subtle-hover': 'rgba(203, 213, 225, 1)',
            },
        },
    },

    plugins: [forms],
};
