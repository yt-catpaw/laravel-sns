import { loadEcharts } from '../echarts-base';

export async function initFunnel(container, funnelData) {
    if (!container || !funnelData?.steps) return;

    const echarts = await loadEcharts();
    const chart = echarts.init(container, null, { renderer: 'svg' });

    const steps = funnelData.steps.map((step) => ({
        name: step.label,
        value: step.value,
    }));

    const option = {
        color: ['#2563eb', '#22c55e', '#fb923c', '#8b5cf6'],
        tooltip: {
            trigger: 'item',
            formatter: (p) => `${p.name}: ${p.value.toLocaleString()}`,
        },
        toolbox: { show: false },
        series: [
            {
                type: 'funnel',
                left: '10%',
                top: 10,
                bottom: 10,
                width: '80%',
                sort: 'none',
                gap: 6,
                label: {
                    show: true,
                    position: 'inside',
                    color: '#fff',
                    fontWeight: '600',
                    formatter: '{b}: {c}',
                },
                labelLine: { show: false },
                itemStyle: {
                    borderWidth: 0,
                },
                data: steps,
            },
        ],
    };

    chart.setOption(option);

    const resizeObserver = new ResizeObserver(() => chart.resize());
    resizeObserver.observe(container.parentElement ?? container);
}
