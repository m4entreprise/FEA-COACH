import '../css/app.css';
import Alpine from 'alpinejs';

window.coachSiteLightbox = () => ({
    lightboxOpen: false,
    lightboxSrc: null,
    lightboxLabel: '',
    openLightbox(src, label = '') {
        if (!src) {
            return;
        }

        this.lightboxSrc = src;
        this.lightboxLabel = label;
        this.lightboxOpen = true;
        document.documentElement.classList.add('overflow-hidden');
    },
    closeLightbox() {
        this.lightboxOpen = false;
        this.lightboxSrc = null;
        this.lightboxLabel = '';
        document.documentElement.classList.remove('overflow-hidden');
    },
});

// Initialize Alpine
window.Alpine = Alpine;
Alpine.start();
