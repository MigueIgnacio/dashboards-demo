<template>
    <div class="col-fg">
        <notifications position="top right" />
        <vue-confirm-dialog class="vue-confirm-dialog-style"></vue-confirm-dialog>
        <headpage-layout :headData="headData" :filterExcel="filters"></headpage-layout>
        <div class="header" v-if="true">
            <HeaderComponent />
        </div>
        <div class="dashboard-header">
            <p>Dashboard Excesos de Velocidad</p>
        </div>

        <div class="dashboard-subheader">
            <p>Este panel entrega una visión clara y oportuna sobre los excesos de velocidad registrados en la empresa XX, 
                facilitando la toma de decisiones para una gestión segura y eficiente de la flota.</p>
        </div>

        <div class="table-page">
            <div>
                <!--FILTTROS --->
                <span class="title-filter">
                    Filtros
                    <span class="mdi mdi-chevron-down-circle filter-icon" v-if="showFilter"
                        @click="showFilter = false"></span>
                    <span class="mdi mdi-chevron-right-circle filter-icon" v-else @click="showFilter = true"></span>
                    <span class="mdi mdi-close-circle filter-icon" @click="
                        filters.dates = [new Date(), new Date()];
                    filters.infringement = '';
                    filters.geofence = '';
                    filters.carrier = '';
                    filters.vehicleType = '';
                    filters.vehicle = '';
                    "></span>
                </span>
                <span class="special-label label-filter" @click="filters.dates = [new Date(), new Date()]"
                    v-if="filters.dates">fecha: {{ customFormatDate(filters.dates[0]) }} a
                    {{ customFormatDate(filters.dates[1]) }}</span>
                <span class="special-label label-filter cursor-no-drop" @click="filters.vehicle = ''"
                    v-if="filters.vehicle">codigo/patente: {{ filters.vehicle.code }}-{{
                        filters.vehicle.plate
                    }}</span>
                <span class="special-label label-filter cursor-no-drop" @click="
                    filters.carrier = '';
                changeCarrier();
                " v-if="filters.carrier">cliente: {{ filters.carrier.name }}</span>
            </div>
            <div class="row" v-show="showFilter">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-5 form-group">
                    <label>Periodo de búsqueda</label>
                    <div>
                        <date-picker style="width: 100%" v-model="filters.dates" format="DD-MM-YYYY" type="date" range
                            :clearable="false" :disabled-date="disabledDates" @change="changeDate">
                        </date-picker>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-5 form-group">
                    <label>Vehículo</label>
                    <multiselect v-model="filters.vehicle" :options="vehicles" selectLabel="" deselectLabel=""
                        selectedLabel="" placeholder="Seleccione" :custom-label="(option) => `${option.code} - ${option.plate}`
                            " track-by="id">
                        <template v-slot:noOptions>No hay resultados.</template>
                        <template v-slot:noResult>No hay resultados.</template>
                    </multiselect>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-5 form-group" v-if="carriers.length > 1">
                    <label>Cliente</label>
                    <multiselect v-model="filters.carrier" :options="carriers" selectLabel="" deselectLabel=""
                        selectedLabel="" placeholder="Seleccione" label="name" track-by="name" @input="changeCarrier">
                        <template v-slot:noOptions>No hay resultados.</template>
                        <template v-slot:noResult>No hay resultados.</template>
                    </multiselect>
                </div>
            </div>
        </div>
        <div class="cuerpo">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="analitica-tab" data-bs-toggle="tab" data-bs-target="#analitica"
                        type="button" role="tab" aria-controls="analitica" aria-selected="true">
                        KPI-Analítica
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mapa-calor" type="button" role="tab"
                        aria-controls="mapa-calor" aria-selected="false" @click="test2 = true">
                        Mapa de Calor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mapa-excesos-tab" data-bs-toggle="tab" data-bs-target="#mapa-excesos"
                        type="button" role="tab" aria-controls="mapa-excesos" aria-selected="false"
                        @click="test = true">
                        Mapa de Puntos
                    </button>
                </li>
            </ul>
            <br />
            <div v-if="isLoading" class="loading-overlay">
                <div class="loading-message">
                    <i class="fas fa-spinner fa-spin"></i> Cargando datos...
                </div>
            </div>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analitica" role="tabpanel" aria-labelledby="analitica-tab"
                    tabindex="0" style="">
                    <div class="parent">
                        <div class="div2" v-if="visibleChannels.length > 1">
                            <h2 class="titulo">ALERTAS POR GERENCIA</h2>
                            <DonutChart :filters="filters" />
                        </div>
                        <div class="div3">
                            <h2 class="titulo">
                                ZONAS CON EXCESOS DE VELOCIDAD
                            </h2>
                            <AnalyticsMap :filters="filters" />
                        </div>
                        <div class="div4">
                            <Top5Peaks :filters="filters" />
                        </div>
                        <div class="div5">
                            <h2 class="titulo">
                                CANTIDAD DE EXCESOS DE VELOCIDAD POR FECHAS
                            </h2>
                            <LineChart :filters="filters" />
                        </div>
                        <div class="div6">
                            <Top5AlertasPatentes :filters="filters" />
                        </div>
                        <div class="div7">
                            <DetalleAlertas :filters="filters" />
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="mapa-calor" role="tabpanel" aria-labelledby="mapa-calor-tab"
                    tabindex="0">
                    <div class="contenedor" v-if="test2">
                        <div class="columna">
                            <div class="fila">
                                <div class="titulo">
                                    <h2>
                                        ZONAS CON MAYOR CONCENTRACIÓN DE EXCESOS
                                        DE VELOCIDAD
                                    </h2>
                                </div>
                                <div class="grafico-maps">
                                    <HeatMap :filters="filters" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="mapa-excesos" role="tabpanel" aria-labelledby="mapa-excesos-tab"
                    tabindex="0">
                    <div class="contenedor" v-if="test">
                        <div class="columna">
                            <div class="fila">
                                <div class="titulo">
                                    <h2>
                                        ZONAS CON MAYOR CONCENTRACIÓN DE EXCESOS
                                        DE VELOCIDAD
                                    </h2>
                                </div>
                                <div class="grafico-maps">
                                    <MapComponent :filters="filters" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DonutChart from "./DonutChart.vue";
import LineChart from "./LineChart.vue";
import DetalleAlertas from "./DetalleAlertas.vue";
import Top5AlertasPatentes from "./Top5AlertasPatentes.vue";
import Top5Peaks from "./Top5Peaks.vue";
import AnalyticsMap from "./AnalyticsMap.vue";
import MapComponent from "./MapComponent.vue";
import HeatMap from "./HeatMap.vue";

import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";

import api from "@/services/api";


export default {
    name: "DashboardExcessAlertIndex",
    components: {
        DonutChart,
        LineChart,
        DetalleAlertas,
        Top5AlertasPatentes,
        Top5Peaks,
        AnalyticsMap,
        DatePicker,
        Multiselect,
        HeatMap,
        MapComponent
    },

    data() {
        this.show = true;
        return {
            test: false,
            test2: false,
            show: {
                LineChart: true,
                DonutChart: true,
                Top5AlertasPatentes: true,
                Top5Peaks: true,
                AnalyticsMap: true,
            },
            isLoading: false,
            showFilter: true,
            errors: [],
            selectedActionPlanTicket: {
                ticket: "",
                description: "",
                file: "",
                actionPlanList: [],
            },
            headData: {
                title: "Dashboard Excesos de Velocidad",
                icon: "fas fa-tachometer-alt-fast",
                pages: [
                    { name: "", route: "/", class: "prev" },
                    { name: "dashboard excesos de velocidad", route: "/dashboard", class: "current" },
                ],
                returnTo: "/reports",
                addButton: false,
                postButton: false,
                excelButton: true,
            },
            filters: {
                dates: [new Date(), new Date()],
                status: [],
                carrier: "",
                vehicle: "",
            },
            tableData: [],
            vehicleGroupsOptions: [],
            carriers: [],
            visibleChannels: [],
            vehicles: [],
            alertDetailsComplete: [],
            alertCountByCarrier: [],
            topVehiclesByAlerts: [],
            topVehiclesBySpeedExcess: [],
            eventsByDate: [],
        };
    },
    created() {
        this.filters.dates[0] = new Date(this.filters.dates[0].setDate(this.filters.dates[0].getDate() - 7));
    },
    mounted() {
        this.getCarriers();
        this.getVehicles();
        this.getVisibleChannels();
    },
    methods: {
        customFormatDate(d) {
            if (!d) return "";
            const dt = new Date(d);
            const pad = n => (n < 10 ? `0${n}` : `${n}`);
            return `${pad(dt.getDate())}-${pad(dt.getMonth() + 1)}-${dt.getFullYear()}`;
        },
        disabledDates(date) {
            const today = new Date();
            const end = new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime();
            return date.getTime() > end;
        },

        handleVehicleSelected(item) {
            const selectedVehicle = this.vehicles.find(v => v.plate === item.vehicle);
            if (selectedVehicle) this.filters.vehicle = selectedVehicle;
            else console.warn("Vehículo no encontrado en la lista de opciones");
        },
        onSelectedVehicleGroup() {
            this.getVehicles();
        },

        async getVehicleGroups() {
            this.vehicleGroupsOptions = [];
            const { data } = await api.get("/api/vehicle-groups", {
                params: { contractor: this.filters.carrier },
            }).catch(() => ({ data: { groups: [] } }));
            this.vehicleGroupsOptions = data.groups || [];
            this.vehicleGroupsOptions.unshift({ id: -1, name: "Flota Completa" });
            this.filters.vehicleGroup = this.vehicleGroupsOptions[0];
        },
        async getVisibleChannels() {
            const { data } = await api.get("/api/visible-channels", { params: this.filters }).catch(() => ({ data: {} }));
            this.visibleChannels = data.visibleChannels || [];
        },
        async getCarriers() {
            const { data } = await api.get("/api/contractors").catch(() => ({ data: [] }));
            this.carriers = data || [];
            if (this.carriers.length === 1) {
                this.filters.carrier = this.carriers[0];
                this.getVehicles();
            }
        },
        async getVehicles() {
            const { data } = await api.get("/api/vehicles", {
                params: {
                    contractor: this.filters.carrier,
                    vehicleGroup: this.filters.vehicleGroup?.id,
                    vehicleType: this.filters.vehicleType,
                    onlyActives: true,
                },
            }).catch(() => ({ data: [] }));
            this.vehicles = data || [];
        },

        changeCarrier() {
            this.filters.vehicle = "";
            this.getVehicles();
            this.getCarriers();
        },
        changeVehicleType() {
            this.filters.vehicle = "";
        },
        changeDate() {
            const [date1, date2] = this.filters.dates.map(d => new Date(d));
            const diffDays = (date2.getTime() - date1.getTime()) / (1000 * 3600 * 24);
            if (diffDays > 32) {
                this.$notify({ title: "Límite rango de fechas", text: "Filtre hasta un rango de 32 días", type: "warn" });
                const now = new Date();
                this.filters.dates = [new Date(now.getFullYear(), now.getMonth(), now.getDate() - 32), now];
            }
        },
    },
};
</script>


<style scoped>
.parent {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    grid-template-rows: repeat(4, minmax(300px, auto));
    gap: 8px;
    height: auto !important;
}

.parent>div {
    background-color: white;
    border-radius: 10px;
    padding: 10px;
    border: 2px solid #f0f0f9;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    min-height: 300px;
}

.div2 {
    grid-column: span 2 / span 2;
}

.div3 {
    grid-row: span 2 / span 2;
    grid-column-start: 5;
    grid-row-start: 1;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.div4 {
    grid-column: span 2 / span 2;
    grid-column-start: 3;
    grid-row-start: 1;
}

.div5 {
    grid-column: span 2 / span 2;
    grid-row-start: 2;
}

.div6 {
    grid-column: span 2 / span 2;
    grid-column-start: 3;
    grid-row-start: 2;
}

.div7 {
    grid-column: span 5 / span 5;
    grid-row-start: 3;
}

.div8 {
    grid-column: span 5 / span 5;
    grid-row-start: 4;
}

.div10 {
    grid-column: span 5 / span 5;
    grid-row-start: 5;
}

.div11 {
    grid-column: span 5 / span 5;
    grid-row-start: 6;
}

.titulo {
    font-size: 18px;
    font-weight: bold;
    color: #454d8c;
    margin-bottom: 5px;
}

.scheduler-border {
    border: none;
    padding: 0;
    margin-bottom: 20px;
}

.grid-container {
    display: grid;
    gap: 10px;
    margin-bottom: 20px;
}

.second-grid {
    grid-template-columns: repeat(2, 1fr);
}

.detalle-alertas-container {
    border: 4px solid var(--Primary-Primary400, #f0f0f9);
    padding: 10px;
    text-align: left;
    background-color: #ffffff;
    border-radius: 10px;
    flex: 1 1 calc(33.33% - 10px);
    box-sizing: border-box;
    min-height: 300px;
}

.line-chart-container {
    margin-bottom: 20px;
}

.detalle-alertas-container {
    margin-bottom: 20px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    padding: 8px;
    text-align: center;
    color: white;
    background-color: #27115b;
}

.table td {
    padding: 8px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.title-filter {
    display: inline-block;
    margin-right: 10px;
    font-weight: bold;
}

.special-label {
    display: inline-block;
    margin-right: 10px;
}

.form-group {
    margin-bottom: 15px;
}

.filter-icon {
    cursor: pointer;
    margin-left: 5px;
}

.line-chart-container,
.bar-chart-container,
.speeding-variation-container {
    margin-bottom: 20px;
}

.evolucion2,
.evolucion1 {
    margin-bottom: 20px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-message {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    text-align: center;
    font-size: 16px;
}

.loading-message i {
    margin-right: 8px;
}

.first-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.second-grid {
    grid-template-columns: 1fr 1fr;
}

h2 {
    text-align: left;
    font-size: 20px;
    font-weight: bold;
    font-family: Poppins;
    color: #454d8c;
}

.tabla-edit {
    background-color: #f8f8fc;
}

nav {
    width: 100%;
    max-width: 1200px;
}

nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
}

nav li {
    margin: 0 15px;
}

nav a {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    padding: 10px 15px;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

nav a:hover {
    background-color: #404f8f;
    color: #fff;
}

.nav nav-tabs {
    padding: 0;
    margin: 0;
}


.table-page {
    margin-top: 5px;
    border-radius: 1px 1px 1px 1px;
    -moz-border-radius: 1px 1px 1px 1px;
    -webkit-border-radius: 1px 1px 1px 1px;
    padding: 0px 10px 10px;
    margin-bottom: 20px;
    box-shadow: unset !important;
}

.dashboard-header p {
  font-weight: 700;        
  color: #004aad;          
  font-size: 25px;       
  margin: 0;
margin-left: 10px;
}

.dashboard-subheader p {
  font-weight: 500;
  color: #27115b;         
  font-size: 16px;
  margin-top: 4px;
  margin-left: 10px;
}

</style>
