import { loadEcharts } from '../echarts-base';

export async function initTrendChart(canvas, trendData) {
    if (!canvas || !trendData) return;

    const echarts = await loadEcharts();
    const chart = echarts.init(canvas, null, { renderer: 'svg' });

    const labels = trendData.labels ?? [];
    const posts = trendData.posts ?? [];
    const likes = trendData.likes ?? [];

    const option = {
        color: ['#93c5fd', '#2563eb'],
        tooltip: { trigger: 'axis' },
        grid: {
            left: 50,
            right: 30,
            top: 50,
            bottom: 80,
        },
        legend: { data: ['投稿数', 'いいね数'], top: 6, itemGap: 12 },
        xAxis: {
            type: 'category',
            data: labels,
            axisLabel: {
                formatter: (value) => value.slice(5), // MM-DD 表示
                rotate: 45,
                margin: 12,
            },
        },
        yAxis: {
            type: 'value',
            axisLabel: { margin: 10 },
            splitLine: { lineStyle: { color: '#e5e7eb' } },
        },
        series: [
            {
                name: '投稿数',
                type: 'bar',
                data: posts,
                itemStyle: { color: '#93c5fd' },
                barMaxWidth: 24,
            },
            {
                name: 'いいね数',
                type: 'line',
                data: likes,
                smooth: false,
                lineStyle: { width: 3, color: '#2563eb' },
                itemStyle: { color: '#2563eb' },
                symbol: 'circle',
                symbolSize: 6,
                z: 3,
            },
        ],
    };

    chart.setOption(option);

    const resizeObserver = new ResizeObserver(() => chart.resize());
    resizeObserver.observe(canvas.parentElement ?? canvas);
}
