<template>
    <div class="detalle-alertas-container">
        <div v-if="isLoading" class="loading-overlay">
            <div class="loading-spinner"></div>
            <div class="loading-text">Cargando datos...</div>
        </div>
        <div v-else>
            <h2 class="titulo">DETALLE DE ALERTAS - INFORMACIÓN COMPLETA</h2>
            <div class="title0">
                <div class="title1">
                    <button @click="exportToExcel" class="btn pulse-effect">EXPORTAR</button>
                </div>
            </div>
            <div class="pagination-controls">
                <label for="rowsPerPage">Filas por página:</label>
                <select v-model="rowsPerPage" id="rowsPerPage">
                    <option v-for="option in rowsPerPageOptions" :key="option" :value="option">{{ option }}</option>
                </select>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th @click="sortBy('vehicle_plate')">Patente <span>{{ getSortSymbol('vehicle_plate') }}</span>
                        </th>
                        <th @click="sortBy('vehicle_code')">Código <span>{{ getSortSymbol('vehicle_code') }}</span>
                        </th>
                        <th @click="sortBy('driver_name')">Conductor <span>{{ getSortSymbol('driver_name') }}</span>
                        </th>
                        <th @click="sortBy('driver_phone')">Teléfono <span>{{ getSortSymbol('driver_phone') }}</span>
                        </th>
                        <th @click="sortBy('avg_speed')">Promedio <span>{{ getSortSymbol('avg_speed') }}</span></th>
                        <th @click="sortBy('max_speed')">Máxima <span>{{ getSortSymbol('max_speed') }}</span></th>
                        <th @click="sortBy('speed_deviation')">Desviación <span>{{ getSortSymbol('speed_deviation')
                                }}</span></th>
                        <th @click="sortBy('alert_count')">Cantidad de Alertas <span>{{ getSortSymbol('alert_count')
                                }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="paginatedData.length === 0">
                        <td colspan="8" class="no-data-row">
                            <i class="fa fa-info-circle info-icon"></i>
                            No hay resultados.
                        </td>
                    </tr>
                    <tr v-else v-for="(item, index) in paginatedData" :key="index"
                        :class="{ even: index % 2 === 0, odd: index % 2 !== 0, 'cursor-pointer': true }"
                        @click="sendVehicleData(item)">
                        <td>{{ item.vehicle_plate }}</td>
                        <td>{{ item.vehicle_code }}</td>
                        <td>{{ item.driver?.driver_name || '--' }}</td>
                        <td>{{ formatPhoneNumber(item.driver?.driver_phone) }}</td>
                        <td>{{ parseNumericValue(item.avg_speed) }} km</td>
                        <td>{{ parseNumericValue(item.max_speed) }} km</td>
                        <td>{{ item.speed_deviation }}</td>
                        <td>{{ item.alert_count }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="pagination-controls">
                <button @click="prevPage" :disabled="currentPage === 1" class="rounded-button">Anterior</button>
                <span>Página {{ currentPage }} de {{ totalPages }}</span>
                <button @click="nextPage" :disabled="currentPage === totalPages"
                    class="rounded-button">Siguiente</button>
            </div>
        </div>
    </div>
</template>

<script>
import * as XLSX from 'xlsx';
import axios from 'axios';

export default {
    name: "DetalleAlertas",
    props: {
        filters: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            rowsPerPage: 10,
            rowsPerPageOptions: [10, 20, 50, 100, 200],
            currentPage: 1,
            sortKey: 'alert_count',
            sortOrders: { 'alert_count': 'desc' },
            isLoading: true,
            allSpeedAlertDetails: [],
        };
    },
    computed: {
        dataArray() {
            return Array.isArray(this.allSpeedAlertDetails) ? this.allSpeedAlertDetails : Object.values(this.allSpeedAlertDetails).flat();
        },
        sortedData() {
            if (!this.sortKey) return this.dataArray;
            return [...this.dataArray].sort((a, b) => {
                let valueA = a[this.sortKey] != null ? a[this.sortKey] : '';
                let valueB = b[this.sortKey] != null ? b[this.sortKey] : '';

                // Remove units (like 'km') for numeric comparison
                if (this.sortKey === 'avg_speed' || this.sortKey === 'max_speed') {
                    if (typeof valueA === 'string' && valueA.match(/\d+/)) {
                        valueA = parseFloat(valueA.replace(/[^\d.]/g, ''));
                    }
                    if (typeof valueB === 'string' && valueB.match(/\d+/)) {
                        valueB = parseFloat(valueB.replace(/[^\d.]/g, ''));
                    }
                }

                if (typeof valueA === 'string' && typeof valueB === 'string') {
                    return this.sortOrders[this.sortKey] === 'asc' ? valueA.localeCompare(valueB) : valueB.localeCompare(valueA);
                }

                if (typeof valueA === 'number' && typeof valueB === 'number') {
                    return this.sortOrders[this.sortKey] === 'asc' ? valueA - valueB : valueB - valueA;
                }
                if (valueA < valueB) return this.sortOrders[this.sortKey] === 'asc' ? -1 : 1;
                if (valueA > valueB) return this.sortOrders[this.sortKey] === 'asc' ? 1 : -1;
                return 0;
            });
        },
        paginatedData() {
            const start = (this.currentPage - 1) * this.rowsPerPage;
            const end = start + this.rowsPerPage;
            return this.sortedData.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.sortedData.length / this.rowsPerPage);
        }
    },
    watch: {
        filters: {
            handler() {
                this.getAllSpeedAlertDetails();
            },
            deep: true,
        },
    },
    mounted() {
        this.getAllSpeedAlertDetails();
    },
    methods: {
        exportToExcel() {
            const worksheet = XLSX.utils.json_to_sheet(this.dataArray.map(item => ({
                "Patente": item.vehicle_plate,
                "Código": item.vehicle_code,
                "Conductor": item.driver?.driver_name || '--',
                "Teléfono": this.formatPhoneNumber(item.driver?.driver_phone),
                "Promedio": `${item.avg_speed} km`,
                "Máxima": `${item.max_speed} km`,
                "Desviación": item.speed_deviation,
                "Cantidad de Alertas": item.alert_count,
            })));
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Alertas");
            XLSX.writeFile(workbook, "DetalleAlertas.xlsx");
        },
        sendVehicleData(item) {
            this.$emit('vehicle-selected', {
                vehicle_plate: item.vehicle_plate,
                vehicle_code: item.vehicle_code,
            });
        },
        formatPhoneNumber(phone) {
            if (!phone) return '--';
            phone = phone.toString();
            if (phone.startsWith('56')) {
                phone = phone.replace(/^56/, '');
            }
            if (phone.startsWith('9')) {
                phone = phone.replace(/^9/, '');
            }
            if (phone.length === 8) {
                return `+56 9 ${phone.slice(0, 4)} ${phone.slice(4)}`;
            }
            return phone;
        },
        sortBy(key) {
            if (this.sortKey === key) {
                // Toggle sort order if the same column is clicked again
                this.sortOrders[key] = this.sortOrders[key] === 'asc' ? 'desc' : 'asc';
            } else {
                // Set the new sort key and default to ascending order
                this.sortKey = key;
                this.sortOrders = { [key]: 'asc' }; // Reset other sort orders
            }
        },
        getSortSymbol(key) {
            if (this.sortKey !== key) return '';
            return this.sortOrders[key] === 'asc' ? '▲' : '▼';
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        parseNumericValue(value) {
            if (typeof value === 'string' && value.match(/\d+/)) {
                return parseFloat(value.replace(/[^\d.]/g, ''));
            }
            return value;
        },
        getAllSpeedAlertDetails() {
            this.isLoading = true;
            axios.get('/reports/dashboard-excess-alert-speed/getAlertDetailsComplete', { params: this.filters })
                .then(response => {
                    this.allSpeedAlertDetails = response.data;
                    this.isLoading = false;
                })
                .catch(() => {
                    this.isLoading = false;
                });
        },
    },
};
</script>

<style scoped>
.detalle-alertas-container {
    border: 1px solid #ccc;
    padding: 10px;
    background-color: #F8F8FC;
    border-radius: 10px;
    position: relative;
}

.table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 10px;
    overflow: hidden;
}

.table th,
.table td {
    padding: 8px;
    text-align: center;
}

.table th {
    background-color: #515BA5;
    color: white;
    cursor: pointer;
}

.table td {
    border-bottom: 1px solid #ddd;
}

.table .even {
    background-color: #F8F8FC;
}

.table .odd {
    background-color: white;
}

.cursor-pointer {
    cursor: pointer;
}

h2.titulo {
    text-align: left;
    font-size: 20px;
    font-weight: bold;
    font-family: Poppins;
    color: #454D8C;
}

.pulse-effect {
    border: none;
    color: white;
    padding: 3px 8px;
    cursor: pointer;
    border-radius: 10px;
    background-color: #515BA5;
    transition: transform 0.3s ease-in-out;
}

.pulse-effect:hover {
    background-color: #06305d;
}

.pulse-effect:active {
    transform: scale(0.9);
}

.pagination-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}

.pagination-controls select {
    margin-left: 10px;
}

.rounded-button {
    border: none;
    color: white;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 10px;
    background-color: #515BA5;
    transition: transform 0.3s ease-in-out;
}

.rounded-button:hover {
    background-color: #06305d;
}

.rounded-button:disabled {
    background-color: #b2b2b2;
    cursor: not-allowed;
}

.total-datos {
    text-align: center;
    margin-top: 10px;
    font-weight: bold;
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
    border-radius: 10px;
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
</style>
