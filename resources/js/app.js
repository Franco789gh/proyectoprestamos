import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';
import QRCode from 'qrcode';

window.Alpine = Alpine;
window.Chart = Chart;
window.QRCode = QRCode;

// Helper global para renderizar Lucide icons
window.renderLucideIcons = () => {
    createIcons({ icons });
};

// Helper global para generar QR en un elemento canvas o img
window.generateQRCode = (elementId, text, options = {}) => {
    const el = document.getElementById(elementId);
    if (!el) return;
    const defaultOptions = {
        width: 160,
        margin: 1,
        color: {
            dark: '#0f172a',
            light: '#ffffff'
        },
        ...options
    };

    if (el.tagName === 'CANVAS') {
        QRCode.toCanvas(el, text, defaultOptions, (error) => {
            if (error) console.error('Error generating QR Canvas:', error);
        });
    } else if (el.tagName === 'IMG') {
        QRCode.toDataURL(text, defaultOptions, (error, url) => {
            if (error) console.error('Error generating QR URL:', error);
            else el.src = url;
        });
    }
};

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    window.renderLucideIcons();
});
