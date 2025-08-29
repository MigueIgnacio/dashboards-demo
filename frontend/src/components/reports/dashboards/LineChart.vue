<template>
    <div class="chart-container">
        <div ref="chart" class="chart"></div>
        <div v-if="isLoading" class="loading-overlay">
            <div class="loading-spinner"></div>
            <div class="loading-text">Cargando datos...</div>
        </div>
        <div v-else-if="eventsByDate.length === 0" class="no-data-message">
            <i class="fa fa-info-circle info-icon"></i>
            <div class="no-data-text">No hay resultados para el período seleccionado.</div>
        </div>
    </div>
</template>

<script>
import * as echarts from "echarts";
import axios from "axios";


export default {
    props: {
        filters: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            eventsByDate: [],
            chart: null,
            isLoading: true,
        };
    },
    mounted() {
        this.initChart();
        this.getEventsByDate();
    },
    watch: {
        filters: {
            handler() {
                this.getEventsByDate();
            },
            deep: true,
        },
        eventsByDate: {
            handler(newValue) {
                this.updateChart(newValue);
            },
            deep: true,
        },
    },
    methods: {
        getEventsByDate() {
            this.isLoading = true;
            axios.get('/reports/dashboard-excess-alert-speed/getEventsByDate', { params: this.filters })
                .then(response => {
                    this.eventsByDate = response.data;
                    this.isLoading = false;
                    this.initChart();
                })
                .catch(() => {
                    this.isLoading = false;
                });
        },
        initChart() {
            if (!this.chart) {
                this.chart = echarts.init(this.$refs.chart);
            }
            this.updateChart(this.eventsByDate);
        },
        updateChart(data) {
            if (!this.chart) {
                return;
            }

            const countedData = data.map((item) => ({
                key: item.date,
                count: item.event_count,
            }));

            countedData.sort((a, b) => new Date(a.key) - new Date(b.key));

            const option = {
                tooltip: {
                    trigger: "axis",
                    formatter: function (params) {
                        const key = params[0].axisValue;
                        const value = params[0].data;
                        return `${key}<br/>Cantidad de eventos: <b>${value}</b>`;
                    },
                },
                xAxis: {
                    type: "category",
                    name: "Fechas",
                    nameTextStyle: {
                        fontStyle: "italic",
                    },
                    data: countedData.map((item) => item.key),
                    axisLine: {
                        show: true,
                    },
                    splitLine: {
                        show: true,
                    },
                },
                yAxis: {
                    type: "value",
                    name: "Cantidad de Eventos",
                    nameTextStyle: {
                        fontStyle: "italic",
                    },
                    axisLine: {
                        show: true,
                    },
                    splitLine: {
                        show: true,
                    },
                },
                grid: {
                    show: true,
                    borderColor: "#ccc",
                    containLabel: true,
                },
                series: [
                    {
                        data: countedData.map((item) => item.count),
                        type: "line",
                        itemStyle: {
                            color: "#FD9E11",
                            borderWidth: 2,
                        },
                        symbol: "circle",
                        symbolSize: 5,
                        smooth: true,
                        lineStyle: {
                            color: "#FD9E11",
                        },
                        showSymbol: false,
                        markPoint: {
                            data: countedData.map((item) => ({
                                coord: [item.key, item.count],
                                value: item.count,
                            })),
                            label: {
                                show: true,
                                formatter: "{@[1]}",
                            },
                        },
                    },
                ],
            };
            this.chart.setOption(option);
        },
    },
    beforeDestroy() {
        if (this.chart) {
            this.chart.dispose();
        }
    },
};
</script>

<style scoped>
.chart-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: rgba(0, 0, 0, 0.8);
    z-index: 10;
    border-radius: 15px;
}

.loading-spinner {
    border: 4px solid rgba(255, 255, 255, 0.1);
    border-left-color: #ffffff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

.loading-text {
    margin-top: 10px;
    font-size: 14px;
    color: #ffffff;
    font-weight: bold;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.no-data-message {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: #666;
    font-size: 16px;
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    padding: 20px;
    border-radius: 8px;
}

.no-data-text {
    display: flex;
    align-items: center;
    justify-content: center;
}

.info-icon {
    margin-right: 8px;
    color: #ccc;
    font-size: 24px;
}

.chart {
    width: 100%;
    height: 100%;
}
</style>
