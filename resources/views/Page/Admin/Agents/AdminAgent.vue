<template>
    <div>
        <h3 class="text-2xl font-bold mb-4">👨‍⚕️ Agent (Personne)</h3>

        <button
            @click="$router.push('/admin/agent/create')"
            class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
            Créez un Agent
        </button>

        <!-- Tableau pour les zones de type "personne" -->
        <table class="min-w-full border-collapse border border-gray-300 mt-2">
            <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">Image</th>
                <th class="border border-gray-300 px-4 py-2">Nom</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="zone in paginatedZonesPersonne" :key="zone.id">
                <td class="border border-gray-300 px-4 py-2">
                    <img v-if="zone.image_url" :src="`${baseUrl}/storage/${zone.image_url}`" alt="Image" class="w-16 h-16 object-cover rounded">
                </td>
                <td class="border border-gray-300 px-4 py-2">{{ zone.nom }}</td>
                <td class="border-t border-gray-300 px-4 py-2 flex space-x-4">
                    <button
                        @click="$router.push(`/admin/zones/edit/${zone.id}`)"
                        class="text-blue-600 hover:text-blue-800"
                    >
                        Modifier
                    </button>
                    <button
                        @click="confirmDeletion(zone.id)"
                        class="text-red-600 hover:text-red-800"
                    >
                        Supprimer
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="flex justify-between items-center mt-4">
            <button
                @click="previousPagePersonne"
                :disabled="currentPagePersonne === 1"
                class="px-4 py-2 bg-gray-200 rounded-l hover:bg-gray-300"
            >
                Précédent
            </button>
            <select
                v-model="currentPagePersonne"
                @change="changePagePersonne"
                class="px-4 py-2 border rounded mx-2"
            >
                <option v-for="page in totalPagesPersonne" :key="page" :value="page">
                    {{ page }}
                </option>
            </select>
            <button
                @click="nextPagePersonne"
                :disabled="currentPagePersonne === totalPagesPersonne"
                class="px-4 py-2 bg-gray-200 rounded-r hover:bg-gray-300"
            >
                Suivant
            </button>
        </div>



        <!-- Modal de confirmation de suppression -->
        <div v-if="showConfirmationModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
                <h3 class="text-xl font-bold mb-4">Confirmer la suppression</h3>
                <p>Êtes-vous sûr de vouloir supprimer cette zone ?</p>
                <div class="flex justify-between mt-4">
                    <button
                        @click="deleteZone"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                    >
                        Oui, Supprimer
                    </button>
                    <button
                        @click="cancelDeletion"
                        class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400"
                    >
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";

export default {
    name: "AdminAgent",
    data() {
        return {
            zones: [],
            currentPagePersonne: 1,
            currentPageZone: 1,
            zonesPerPage: 10,
            baseUrl: import.meta.env.VITE_APP_URL,
            showConfirmationModal: false,
            zoneToDeleteId: null,
        };
    },
    computed: {
        paginatedZonesPersonne() {
            const start = (this.currentPagePersonne - 1) * this.zonesPerPage;
            const end = start + this.zonesPerPage;
            return this.zones.filter(zone => zone.type === "personne").slice(start, end);
        },

        totalPagesPersonne() {
            return Math.ceil(this.zones.filter(zone => zone.type === "personne").length / this.zonesPerPage);
        },

    },
    methods: {
        confirmDeletion(zoneId) {
            this.zoneToDeleteId = zoneId;
            this.showConfirmationModal = true;
        },
        cancelDeletion() {
            this.showConfirmationModal = false;
            this.zoneToDeleteId = null;
        },
        deleteZone() {
            axios
                .delete(`/api/zones/${this.zoneToDeleteId}`)
                .then(() => {
                    this.showSuccessToast("Zone supprimée avec succès !");
                    this.fetchzones();
                    this.showConfirmationModal = false;
                    this.zoneToDeleteId = null;
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la suppression de la zone.");
                    this.showConfirmationModal = false;
                    this.zoneToDeleteId = null;
                });
        },
        previousPagePersonne() {
            if (this.currentPagePersonne > 1) {
                this.currentPagePersonne--;
            }
        },
        nextPagePersonne() {
            if (this.currentPagePersonne < this.totalPagesPersonne) {
                this.currentPagePersonne++;
            }
        },
        changePagePersonne() {
            this.fetchzones();
        },

        fetchzones() {
            axios
                .get("/api/zones")
                .then((response) => {
                    this.zones = response.data;
                })
                .catch((error) => {
                    console.error("Erreur lors de la récupération des zones:", error);
                });
        },
        showSuccessToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#4CAF50",
                stopOnFocus: true,
            }).showToast();
        },
        showErrorToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#F44336",
                stopOnFocus: true,
            }).showToast();
        },
    },
    mounted() {
        this.fetchzones();
    },
};
</script>

<style scoped>
.fixed {
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
}

.bg-opacity-50 {
    background-color: rgba(0, 0, 0, 0.5);
}

.bg-white {
    background-color: #fff;
}

button {
    padding: 6px 12px;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #f0f0f0;
}


.table th,
.table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

.table th {
    background-color: #f4f4f4;
}

.table td img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}
</style>
