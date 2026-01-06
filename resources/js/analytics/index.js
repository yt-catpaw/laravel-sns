import { initTrendChart } from './charts/trend';
import { initHeatmap } from './charts/heatmap';
import { initFunnel } from './charts/funnel';

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

    const heatmapContainer = document.querySelector('[data-analytics-heatmap]');
    const heatmapDataEl = document.getElementById('heatmap-data');
    if (heatmapContainer && heatmapDataEl) {
        try {
            const heatmapData = JSON.parse(heatmapDataEl.textContent || '{}');
            initHeatmap(heatmapContainer, heatmapData);
        } catch (e) {
            console.error('Failed to parse heatmap data', e);
        }
    }

    const funnelCanvas = document.querySelector('[data-analytics-funnel]');
    const funnelDataEl = document.getElementById('funnel-data');
    if (funnelCanvas && funnelDataEl) {
        try {
            const funnelData = JSON.parse(funnelDataEl.textContent || '{}');
            initFunnel(funnelCanvas, funnelData);
        } catch (e) {
            console.error('Failed to parse funnel data', e);
        }
    }
}
