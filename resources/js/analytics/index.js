import { initTrendChart } from './charts/trend';

function getTrendData() {
    const el = document.getElementById('trend-data');
    if (!el) return null;
    try {
        return JSON.parse(el.textContent || '{}');
    } catch (e) {
        console.error('Failed to parse trend data', e);
        return null;
    }
}

export function initAnalytics() {
    const trendCanvas = document.querySelector('[data-analytics-trend]');
    const trendData = getTrendData();
    if (trendCanvas && trendData) {
        initTrendChart(trendCanvas, trendData);
    }
}
