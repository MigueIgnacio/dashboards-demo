<template>
    <div class="contenedor">
        <div ref="mapContainer" class="map-container"></div>
        <div class="legends-container">
            <div class="legend legend-sizes">
                <div class="legend-title"><b>Exceso de Velocidad</b></div>
                <div class="legend-items">
                    <div class="legend-item">
                        <div class="legend-circle" style="
                                width: 20px;
                                height: 20px;
                                background-color: black;
                            "></div>
                        <span class="legend-text">Alto</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-circle" style="
                                width: 10px;
                                height: 10px;
                                background-color: black;
                            "></div>
                        <span class="legend-text">Medio</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-circle" style="
                                width: 3px;
                                height: 3px;
                                background-color: black;
                            "></div>
                        <span class="legend-text">Bajo</span>
                    </div>
                </div>
            </div>
            <div class="legend legend-excess">
                <div class="legend-title"><b>Velocidad</b></div>
                <div class="legend-items">
                    <div class="legend-item">
                        <div class="legend-square" style="background-color: #ff0000"></div>
                        <span class="legend-text">Alta</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-square" style="background-color: #FE9900"></div>
                        <span class="legend-text">Media</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-square" style="background-color: #FFDE59"></div>
                        <span class="legend-text">Baja</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import axios from 'axios';

export default {
    props: {
        filters: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            map: null,
            data: [],
            visibleMarkers: [],
            minSpeed: 0,
            maxSpeed: 0,
        };
    },
    mounted() {
        this.initializeMap();
        this.fetchMapData();
    },
    watch: {
        filters: {
            handler() {
                this.fetchMapData();
            },
            deep: true,
        },
    },
    methods: {
        initializeMap() {
            this.map = L.map(this.$refs.mapContainer).setView([-35.6751, -71.5430], 4);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(this.map);
        },
        fetchMapData() {
            axios.get("/reports/dashboard-excess-alert-speed/getAllSpeedAlertDetails", {
                params: this.filters,
            }).then((response) => {
                if (response.data && response.data.data) {
                    this.data = response.data.data.filter(item => item.lat !== 0 && item.lng !== 0);
                    this.processSpeedData();
                } else {
                    this.resetData();
                }
                this.updateMarkers();
            }).catch(() => {
                this.resetData();
                this.updateMarkers();
            });
        },
        processSpeedData() {
            const speeds = this.data.map(item => item.speed);
            if (speeds.length > 0) {
                this.minSpeed = Math.min(...speeds);
                this.maxSpeed = Math.max(...speeds);
            } else {
                this.resetData();
            }
        },
        updateMarkers() {
            if (!this.map) return;

            this.clearMarkers();

            if (this.data.length === 0) {
                return;
            }

            this.data.forEach(item => {
                const { lat, lng, speed, max, delta, vehicle_plate, driver, begin_at } = item;

                const markerColor = this.getColorBySpeed(speed);
                const markerSize = this.calculateMarkerSize(delta);

                const circleMarker = L.circleMarker([parseFloat(lat), parseFloat(lng)], {
                    radius: markerSize,
                    fillColor: markerColor,
                    color: "#000",
                    weight: 0.8,
                    opacity: 0.7,
                    fillOpacity: 0.8,
                }).addTo(this.map);

                circleMarker.on('mouseover', function () {
                    const tooltipContent = `
                        <div style="font-size: 10px; line-height: 1;">
                            <strong>Vehículo:</strong> ${vehicle_plate}<br>
                            <strong>Conductor:</strong> ${driver.driver_name ? driver.driver_name : '--'}<br>
                            <strong>Fecha del Evento:</strong> ${begin_at}<br>
                            <strong>Velocidad:</strong> ${speed} km/h<br>
                            <strong>Velocidad Máxima:</strong> ${max} km/h
                        </div>
                    `;
                    this.bindTooltip(tooltipContent, { className: 'custom-tooltip' }).openTooltip();
                });

                circleMarker.on('mouseout', function () {
                    this.closeTooltip();
                });

                this.visibleMarkers.push(circleMarker);
            });

            this.fitBoundsToMarkers();
        },
        getColorBySpeed(speed) {
            const range = this.maxSpeed - this.minSpeed;
            if (range === 0) {
                return "#FFDE59"; // Pale Yellow for uniform speeds
            }
            const normalizedSpeed = (speed - this.minSpeed) / range;
            if (normalizedSpeed < 0.33) {
                return "#FFDE59"; // Pale Yellow
            } else if (normalizedSpeed < 0.66) {
                return "#FE9900"; // Yellow
            } else {
                return "#FF0000"; // Red
            }
        },
        calculateMarkerSize(delta) {
            const minSize = 5;
            const maxSize = 20;
            return minSize + (delta / this.maxSpeed) * (maxSize - minSize);
        },
        clearMarkers() {
            if (!this.map) return;
            this.visibleMarkers.forEach(marker => marker.removeFrom(this.map));
            this.visibleMarkers = [];
        },
        resetData() {
            this.data = [];
            this.minSpeed = 0;
            this.maxSpeed = 0;
        },
        fitBoundsToMarkers() {
            if (this.visibleMarkers.length > 0) {
                const bounds = L.latLngBounds(this.visibleMarkers.map(marker => marker.getLatLng()));
                if (bounds.isValid()) {
                    this.map.fitBounds(bounds);
                }
            }
        }
    },
};
</script>

<style scoped>
.contenedor {
    height: 500px;
    width: 100%;
    position: relative;
    z-index: 8;
}

.map-container {
    height: 500px;
    width: 100%;
    position: relative;
    border: 2px solid rgb(138, 131, 131);
    border-radius: 15px;
}

.legends-container {
    position: absolute;
    bottom: 10px;
    left: 10px;
    display: flex;
    justify-content: space-between;
    z-index: 1000;
}

.legend {
    display: flex;
    flex-direction: column;
    background-color: rgba(255, 255, 255, 0.8);
    padding: 8px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    width: 250px;
}

.legend-title {
    font-weight: bold;
    margin-bottom: 5px;
}

.legend-items {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-right: 10px;
}

.legend-circle {
    border-radius: 50%;
    margin-right: 5px;
    flex-shrink: 0;
}

.legend-square {
    width: 15px;
    height: 15px;
    margin-right: 5px;
    flex-shrink: 0;
}

.legend-text {
    font-size: 14px;
}
</style>
