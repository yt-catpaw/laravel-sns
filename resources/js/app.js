import './bootstrap';
import { initLikeButtons } from './like';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.site-header').forEach((header) => {
        const toggle = header.querySelector('[data-header-toggle]');
        const menu = header.querySelector('[data-header-menu]');
        if (!toggle || !menu) return;

        toggle.addEventListener('click', () => {
            const isOpen = header.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    initLikeButtons();

    if (document.querySelector('[data-analytics-trend]')) {
        import('./analytics/index').then(({ initAnalytics }) => initAnalytics());
    }
});
