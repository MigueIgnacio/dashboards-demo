<template>
    <div class="chart-container">
        <div ref="chart" class="chart"></div>
        <div v-if="loading" class="loading-overlay">
            <div class="loading-spinner"></div>
            <div class="loading-text">Cargando datos...</div>
        </div>
        <div v-else-if="!alertCountByCarrier.length" class="no-data-message">
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
            alertCountByCarrier: [],
            loading: true,
            chart: null,
            timeoutHandler: null,
        };
    },
    mounted() {
        this.initChart();
        this.getAlertCountByCarrier();
    },
    watch: {
        filters: {
            handler() {
                this.getAlertCountByCarrier();
            },
            deep: true,
        },
        alertCountByCarrier: {
            handler(newValue) {
                this.updateChart(newValue);
            },
            deep: true,
        },
    },
    computed: {
        colors() {
            return [
                "#515BA5", // Azul oscuro
                "#743586", // Púrpura
                "#d02f7f", // Rosa
                "#ff5743", // Rojo anaranjado
                "#e0aa00", // Amarillo dorado
                "#d61c4e", // Rojo
                "#2e8b57", // Verde Cristal
            ];
        },
    },
    methods: {
        getAlertCountByCarrier() {
            this.loading = true;
            axios
                .get("/reports/dashboard-excess-alert-speed/getAlertCountByChannel", { params: this.filters })
                .then((response) => {
                    this.alertCountByCarrier = response.data;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                    this.alertCountByCarrier = [];
                });
        },
        initChart() {
            if (!this.chart) {
                this.chart = echarts.init(this.$refs.chart);
            }
            this.updateChart(this.alertCountByCarrier);
        },
        updateChart(data) {
            if (!this.chart) return;

            if (!data.length) {
                this.loading = false;
                return;
            }

            const option = {
                tooltip: {
                    trigger: "item",
                    formatter: "<b>{b}:</b> {c} ({d}%)",
                },
                legend: {
                    orient: "horizontal",
                    bottom: "0%",
                    padding: [20, 10, 10, 10],
                    textStyle: {
                        fontSize: 12,
                        color: "#000000",
                    },
                    itemWidth: 12,
                    itemHeight: 12,
                },
                series: [
                    {
                        name: "Alertas Emitidas",
                        type: "pie",
                        radius: ["40%", "70%"],
                        avoidLabelOverlap: false,
                        bottom: "20%",
                        itemStyle: {
                            borderRadius: 5,
                            borderWidth: 1,
                            borderColor: "#fff",
                        },
                        label: {
                            show: true,
                            position: "inside",
                            formatter: (params) => {
                                return params.percent > 10
                                    ? `${params.percent.toFixed(0)}%`
                                    : "";
                            },
                            fontSize: 10,
                            color: "#fff",
                            fontWeight: "bold",
                        },
                        labelLine: {
                            show: true,
                            lineStyle: {
                                width: 1,
                                type: 'solid',
                                color: "#000000",
                            },
                        },
                        data: data.map((item, index) => ({
                            name: item.channel_name,
                            value: item.alert_count,
                            itemStyle: {
                                color: this.colors[index % this.colors.length],
                            },
                        })),
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
    }
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
    height: 350px;
    position: relative;
    padding-top: -30px;
}
</style>
