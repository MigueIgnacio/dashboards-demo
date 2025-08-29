<template>
    <div class="heat-map-container">
        <div ref="mapContainer" class="map-container"></div>
    </div>
</template>
<script>
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import "leaflet.heat";
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
            heatLayer: null,
            data: [],
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
            this.map = L.map(this.$refs.mapContainer).setView([-35.6751, -71.5430], 7);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(this.map);
        },
        fetchMapData() {
            axios.get("/reports/dashboard-excess-alert-speed/getPoints", {
                params: this.filters,
            }).then((response) => {
                if (response.data && response.data.data) {
                    this.data = response.data.data.filter(item => item.lat !== 0 && item.lng !== 0);
                } else {
                    this.data = [];
                }
                this.updateHeatMap();
            }).catch(() => {
                this.data = [];
                this.updateHeatMap();
            });
        },
        updateHeatMap() {
            const dataPoints = this.data.map(item => [parseFloat(item.lat), parseFloat(item.lng), 10]);

            if (this.heatLayer && this.map.hasLayer(this.heatLayer)) {
                this.map.removeLayer(this.heatLayer);
            }

            this.heatLayer = L.heatLayer(dataPoints, { radius: 25 }).addTo(this.map);

            const bounds = L.latLngBounds(this.data.map(item => [item.lat, item.lng]));
            if (bounds.isValid()) {
                this.map.fitBounds(bounds);
            }
        },
    },
};
</script>

<style scoped>
.heat-map-container {
    height: 500px;
    width: 100%;
    position: relative;
    overflow: hidden;
    z-index: 8;
}

.map-container {
    height: 100%;
    border: 2px solid black;
    border-radius: 15px;
}
</style>
