export async function loadEcharts() {
    const echarts = await import('echarts');
    return echarts.default ?? echarts;
}
