import { loadEcharts } from '../echarts-base';

export async function initHeatmap(container, heatmapData) {
    if (!container || !heatmapData) return;

    const echarts = await loadEcharts();
    const chart = echarts.init(container, null, { renderer: 'svg' });

    const days = heatmapData.days ?? [];
    const slots = heatmapData.slots ?? [];
    const values = heatmapData.values ?? [];
    const max = heatmapData.max ?? 0;

    const option = {
        backgroundColor: '#fff',
        tooltip: {
            position: 'top',
            formatter: (p) => {
                const slot = slots[p.data[0]] ?? '';
                const day = days[p.data[1]] ?? '';
                const val = p.data[2] ?? 0;
                return `${day} ${slot}: ${val}`;
            },
        },
        grid: { top: 24, left: 80, right: 40, bottom: 90, containLabel: true },
        xAxis: {
            type: 'category',
            data: slots,
            splitArea: { show: true },
            axisLabel: { interval: 0, margin: 12, color: '#0f172a', fontSize: 12, rotate: 0 },
        },
        yAxis: {
            type: 'category',
            data: days,
            splitArea: { show: true },
            axisLabel: { interval: 0, color: '#0f172a', margin: 14, fontSize: 12, show: true },
            axisTick: { alignWithLabel: true },
        },
        visualMap: {
            min: 0,
            max: Math.max(max, 1),
            calculable: true,
            orient: 'horizontal',
            left: 'center',
            bottom: 10,
            text: ['多', '少'],
            textStyle: { color: '#0f172a' },
            inRange: {
                color: ['#e0f2fe', '#60a5fa', '#1d4ed8'],
            },
        },
        series: [
            {
                name: '反応数',
                type: 'heatmap',
                data: values,
                label: {
                    show: true,
                    formatter: ({ data }) => (data?.[2] ? data[2] : ''),
                    color: '#0f172a',
                    fontSize: 12,
                },
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 0, 0, 0.3)',
                    },
                },
            },
        ],
    };

    chart.setOption(option);

    const resizeObserver = new ResizeObserver(() => chart.resize());
    resizeObserver.observe(container.parentElement ?? container);
}
