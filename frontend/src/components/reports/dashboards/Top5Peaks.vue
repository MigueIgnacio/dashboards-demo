<template>
    <div class="top-speeding-container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="titulo">
                {{ isDriverView ? "TOP 5 CONDUCTORES CON MAYOR EXCESO DE VELOCIDAD" : "TOP 5 PATENTES CON MAYOR EXCESO
                DE VELOCIDAD" }}
            </h2>

            <label class="popup">
                <input type="checkbox" @change="checkOverflow">
                <div class="burger" tabindex="0">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <nav class="popup-window"
                    :class="{ 'adjust-left': isOverflowingRight, 'adjust-right': !isOverflowingRight }">
                    <legend>Acciones</legend>
                    <ul>
                        <li>
                            <button @click="toggleView(false)" :disabled="!isDriverView"
                                :class="{ 'disabled-button': !isDriverView }">
                                <i class="fa fa-car"></i>
                                <span>Ver Vehículos</span>
                            </button>
                        </li>
                        <li>
                            <button @click="toggleView(true)" :disabled="isDriverView"
                                :class="{ 'disabled-button': isDriverView }">
                                <i class="fa fa-user"></i>
                                <span>Ver Conductores</span>
                            </button>
                        </li>
                        <hr>
                        <li>
                            <button @click="openInNewTab('/reports/excess-alerts')">
                                <i class="fas fa-file-alt"></i>
                                <span>Ver Reporte</span>
                            </button>
                        </li>
                        <li>
                            <button @click="exportToExcel">
                                <i class="fas fa-file-excel"></i>
                                <span>Exportar Excel</span>
                            </button>
                        </li>
                    </ul>
                </nav>
            </label>
        </div>

        <div class="base">
            <div v-if="isLoading" class="loading-overlay">
                <div class="loading-spinner"></div>
                <div class="loading-text">Cargando datos...</div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th v-if="!isDriverView">Patente</th>
                        <th v-if="isDriverView">Conductor</th>
                        <th v-if="isDriverView">RUT</th>
                        <th v-if="!isDriverView">Código de Vehículo</th>
                        <th>Velocidad Máxima (KM/H)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filteredData.length === 0">
                        <td colspan="6" class="no-data-row">
                            <i class="fa fa-info-circle info-icon"></i> No hay resultados.
                        </td>
                    </tr>

                    <tr v-else v-for="(item, index) in filteredData" :key="index" @click="sendData(item)"
                        class="cursor-pointer">
                        <td class="circle-number">{{ index + 1 }}</td>
                        <td v-if="!isDriverView">{{ item.vehicle_plate }}</td>
                        <td v-if="isDriverView">{{ item.driver_name }}</td>
                        <td v-if="isDriverView">{{ formatRut(item.driver_rut) }}</td>
                        <td v-if="!isDriverView">{{ item.vehicle_code }}</td>
                        <td class="speed-cell">
                            <span :class="{ 'bg-red': item.max_speed > 100 }" class="speed-value">
                                {{ item.max_speed }}
                            </span>
                            <span class="speed-limit-icon">
                                <span class="speed-number">{{ item.limit_speed }}</span>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: {
        filters: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {
            topSpeedingData: [],
            isLoading: true,
            isDriverView: false,
            isOverflowingRight: false
        };
    },
    computed: {
        filteredData() {
            return this.topSpeedingData.filter(item =>
                this.isDriverView
                    ? item.driver_name && item.driver_rut && item.max_speed !== undefined
                    : item.vehicle_plate && item.vehicle_code && item.max_speed !== undefined
            );
        }
    },
    mounted() {
        this.fetchTopSpeedingData();
        this.checkOverflow();
    },
    methods: {
        checkOverflow() {
            this.$nextTick(() => {
                const menu = this.$refs.popupWindow;
                if (menu) {
                    const rect = menu.getBoundingClientRect();
                    const windowWidth = window.innerWidth;
                    this.isOverflowingRight = rect.right > windowWidth;
                }
            });
        },
        openInNewTab(url) {
            window.open(url, '_blank');
        },
        isValidDateRange() {
            const date1 = new Date(this.filters.dates[0]);
            const date2 = new Date(this.filters.dates[1]);
            const Difference_In_Time = date2.getTime() - date1.getTime();
            const Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
            return Difference_In_Days <= 32;
        },
        exportToExcel() {
            this.isLoading = true;
            const params = this.filters;


            const endpoint = this.isDriverView
                ? "/dashboard/getExcelTopMaxSpeedingDrivers"
                : "/dashboard/getExcelTopMaxSpeedingVehicles";

            const filename = this.isDriverView
                ? "top_5_conductores_mayor_velocidad.xlsx"
                : "top_5_vehiculos_mayor_velocidad.xlsx";

            axios({
                url: endpoint,
                method: "GET",
                params: params,
                responseType: "blob",
            })
                .then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", filename);
                    document.body.appendChild(link);
                    link.click();
                    this.isLoading = false;
                })
                .catch((error) => {
                    console.error("Error exportando a Excel:", error);
                    this.isLoading = false;
                });
        },
        fetchTopSpeedingData() {
            if (this.isValidDateRange()) {
                this.isLoading = true;
                const params = this.filters;

                const endpoint = this.isDriverView
                    ? "/reports/dashboard-excess-alert-speed/topDriversBySpeedExcess"
                    : "/reports/dashboard-excess-alert-speed/getTopVehiclesBySpeedExcess";


                axios
                    .get(endpoint, { params })
                    .then((response) => {
                        this.topSpeedingData = response.data || [];
                        this.isLoading = false;
                    })
                    .catch((error) => {
                        console.error("Error fetching top speeding data:", error);
                        this.isLoading = false;
                    });
            } else {
                this.$notify({
                    title: "Límite de fechas excedido",
                    text: "El rango de fechas no puede exceder los 32 días",
                    type: "warn",
                });
            }
        },
        sendData(item) {
            this.$emit('data-selected', item);
        },
        toggleView(isDriver) {
            this.isDriverView = isDriver;
            this.fetchTopSpeedingData();
        },
        formatRut(rut) {
            if (!rut) return "SIN RUT";
            const clean = String(rut).replace(/[.\s]/g, '');
            if (clean.includes('-')) return clean.toUpperCase();
            const cuerpo = clean.slice(0, -1);
            const dv = clean.slice(-1);
            return `${cuerpo}-${dv}`.toUpperCase();
        }

    },
    watch: {
        filters: {
            handler() {
                this.fetchTopSpeedingData();
            },
            deep: true,
        }
    }
};
</script>

<style scoped>
.popup-window.adjust-right {
    right: 0;
    left: auto;
}

.popup-window.adjust-left {
    left: 0;
    right: auto;
}

.top-speeding-vehicles-container {
    position: relative;
}

.base {
    width: 100%;
    position: relative;
    height: 100%;
    padding: 15px;
}

.table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 10px;
    overflow: hidden;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.table th {
    color: white;
    background-color: #6770B4;
}

.circle-number {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background-color: #FD9E11;
    color: rgb(0, 0, 0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin: auto;
    line-height: 35px;
    font-size: 14px;
}

.cursor-pointer {
    cursor: pointer;
}

.bg-red {
    color: red;
    font-weight: bold;
}

.no-data-row {
    text-align: center;
    font-size: 16px;
    color: #6c757d;
    padding: 20px;
}

.info-icon {
    font-size: 24px;
    margin-right: 8px;
    vertical-align: middle;
    color: #6c757d;
}

/* Estilos para el spinner de carga */
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
    background: rgba(0, 0, 0, 0.7);
    z-index: 10;
    color: #ffffff;
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

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.loading-text {
    margin-top: 10px;
    font-size: 14px;
    color: #ffffff;
    font-weight: bold;
}

h2.titulo {
    text-align: left;
    font-size: 22px;
    font-weight: bold;
    padding-top: 5px;
    padding-bottom: -13px;
    padding-left: 7px;
}

/* Footer de la gráfica */
.chart-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 10px;
    border-top: 1px solid #ddd;
    background-color: #f9f9f9;
    font-size: 14px;
    color: #6c757d;
}

.report-link {
    cursor: pointer;
    display: flex;
    align-items: center;
    text-decoration: underline;
    color: #6c757d;
}

.report-icon {
    font-size: 20px;
    color: #6c757d;
}

.report-link i {
    margin-left: 5px;
}

.disabled-button {
    opacity: 0.5;
    pointer-events: none;
    background-color: #ccc;
    cursor: not-allowed;
}

/* The design is inspired from the mockapi.io */

.popup {
    --burger-line-width: 1.125em;
    --burger-line-height: 0.125em;
    --burger-offset: 0.625em;
    --burger-bg: rgba(0, 0, 0, .15);
    --burger-color: #333;
    --burger-line-border-radius: 0.1875em;
    --burger-diameter: 2.125em;
    --burger-btn-border-radius: calc(var(--burger-diameter) / 2);
    --burger-line-transition: .3s;
    --burger-transition: all .1s ease-in-out;
    --burger-hover-scale: 1.1;
    --burger-active-scale: .95;
    --burger-enable-outline-color: var(--burger-bg);
    --burger-enable-outline-width: 0.125em;
    --burger-enable-outline-offset: var(--burger-enable-outline-width);
    /* nav */
    --nav-padding-x: 0.25em;
    --nav-padding-y: 0.625em;
    --nav-border-radius: 0.375em;
    --nav-border-color: #ccc;
    --nav-border-width: 0.0625em;
    --nav-shadow-color: rgba(0, 0, 0, .2);
    --nav-shadow-width: 0 1px 5px;
    --nav-bg: #eee;
    --nav-font-family: Menlo, Roboto Mono, monospace;
    --nav-default-scale: .8;
    --nav-active-scale: 1;
    --nav-position-left: 0;
    --nav-position-right: unset;
    /* if you want to change sides just switch one property */
    /* from properties to "unset" and the other to 0 */
    /* title */
    --nav-title-size: 0.625em;
    --nav-title-color: #777;
    --nav-title-padding-x: 1rem;
    --nav-title-padding-y: 0.25em;
    /* nav button */
    --nav-button-padding-x: 1rem;
    --nav-button-padding-y: 0.375em;
    --nav-button-border-radius: 0.375em;
    --nav-button-font-size: 12px;
    --nav-button-hover-bg: #6495ed;
    --nav-button-hover-text-color: #fff;
    --nav-button-distance: 0.875em;
    /* underline */
    --underline-border-width: 0.0625em;
    --underline-border-color: #ccc;
    --underline-margin-y: 0.3125em;
}

/* popup settings  */

.popup {
    display: inline-block;
    text-rendering: optimizeLegibility;
    position: relative;
    z-index: 8;
}

.popup input {
    display: none;
}

.burger {
    display: flex;
    position: relative;
    align-items: center;
    justify-content: center;
    background: var(--burger-bg);
    width: var(--burger-diameter);
    height: var(--burger-diameter);
    border-radius: var(--burger-btn-border-radius);
    border: none;
    cursor: pointer;
    overflow: hidden;
    transition: var(--burger-transition);
    outline: var(--burger-enable-outline-width) solid transparent;
    outline-offset: 0;
}

.burger span {
    height: var(--burger-line-height);
    width: var(--burger-line-width);
    background: var(--burger-color);
    border-radius: var(--burger-line-border-radius);
    position: absolute;
    transition: var(--burger-line-transition);
}

.burger span:nth-child(1) {
    top: var(--burger-offset);
}

.burger span:nth-child(2) {
    bottom: var(--burger-offset);
}

.burger span:nth-child(3) {
    top: 50%;
    transform: translateY(-50%);
}

.popup-window {
    transform: scale(var(--nav-default-scale));
    visibility: hidden;
    opacity: 0;
    position: absolute;
    padding: var(--nav-padding-y) var(--nav-padding-x);
    background: var(--nav-bg);
    font-family: var(--nav-font-family);
    color: var(--nav-text-color);
    border-radius: var(--nav-border-radius);
    box-shadow: var(--nav-shadow-width) var(--nav-shadow-color);
    border: var(--nav-border-width) solid var(--nav-border-color);
    top: calc(var(--burger-diameter) + var(--burger-enable-outline-width) + var(--burger-enable-outline-offset));
    left: var(--nav-position-left);
    right: var(--nav-position-right);
    transition: var(--burger-transition);
}

.popup-window legend {
    padding: var(--nav-title-padding-y) var(--nav-title-padding-x);
    margin: 0;
    color: var(--nav-title-color);
    font-size: var(--nav-title-size);
    text-transform: uppercase;
}

.popup-window ul {
    margin: 0;
    padding: 0;
    list-style-type: none;
}

.popup-window ul button {
    outline: none;
    width: 100%;
    border: none;
    background: none;
    display: flex;
    align-items: center;
    color: var(--burger-color);
    font-size: var(--nav-button-font-size);
    padding: var(--nav-button-padding-y) var(--nav-button-padding-x);
    white-space: nowrap;
    border-radius: var(--nav-button-border-radius);
    cursor: pointer;
    column-gap: var(--nav-button-distance);
}

.popup-window ul li:nth-child(1) svg,
.popup-window ul li:nth-child(2) svg {
    color: cornflowerblue;
}

.popup-window ul li:nth-child(4) svg,
.popup-window ul li:nth-child(5) svg {
    color: rgb(153, 153, 153);
}

.popup-window ul li:nth-child(7) svg {
    color: red;
}

.popup-window hr {
    margin: var(--underline-margin-y) 0;
    border: none;
    border-bottom: var(--underline-border-width) solid var(--underline-border-color);
}

/* actions */

.popup-window ul button:hover,
.popup-window ul button:focus-visible,
.popup-window ul button:hover svg,
.popup-window ul button:focus-visible svg {
    color: var(--nav-button-hover-text-color);
    background: var(--nav-button-hover-bg);
}

.burger:hover {
    transform: scale(var(--burger-hover-scale));
}

.burger:active {
    transform: scale(var(--burger-active-scale));
}

.burger:focus:not(:hover) {
    outline-color: var(--burger-enable-outline-color);
    outline-offset: var(--burger-enable-outline-offset);
}

.popup input:checked+.burger span:nth-child(1) {
    top: 50%;
    transform: translateY(-50%) rotate(45deg);
}

.popup input:checked+.burger span:nth-child(2) {
    bottom: 50%;
    transform: translateY(50%) rotate(-45deg);
}

.popup input:checked+.burger span:nth-child(3) {
    transform: translateX(calc(var(--burger-diameter) * -1 - var(--burger-line-width)));
}

.popup input:checked~nav {
    transform: scale(var(--nav-active-scale));
    visibility: visible;
    opacity: 1;
}

.titulo {
    text-align: left;
    font-size: 20px;
    font-weight: bold;
    font-family: Poppins;
    color: #454D8C;
}

.speed-cell {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.speed-value {
    font-weight: bold;
    font-size: 16px;
}

.speed-limit-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    border: 2px solid red;
    background-color: white;
    color: black;
    text-align: center;
    width: 2.2em;
    height: 2.2em;
    aspect-ratio: 1 / 1;
    font-size: 0.7em;
    transform: scale(0.85);
    margin-left: 5px;
}

.speed-number {
    font-size: 1em;
    font-weight: bold;
    line-height: 1em;
}
</style>
