<template>
    <div>
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h3 id="topagent" class="text-2xl font-extrabold text-gray-800">👨‍⚕️ Agents</h3>
                <p class="text-sm text-gray-500">Gestion des agents — Vue administrateur</p>
            </div>
            <button @click="$router.push('/admin/agent/create')"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Créer un agent
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total agents</p>
                <p class="text-2xl font-bold text-gray-800">{{ agentsFiltered.length }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Résultats</p>
                <p class="text-2xl font-bold text-green-700">{{ agentsFiltered.length }}</p>
            </div>
        </div>

        <!-- Recherche -->
        <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
            <div class="relative w-full md:w-1/3">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input v-model="recherche" type="text" placeholder="Rechercher par nom..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <span class="text-sm text-gray-500">{{ agentsFiltered.length }} résultat(s)</span>
        </div>

        <!-- Mobile cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:hidden">
            <div v-for="zone in agentsFiltered" :key="zone.id" class="bg-white p-4 shadow-md rounded-lg flex flex-col items-center text-center">
                <img v-if="zone.image_url" :src="`${baseUrl}/${zone.image_url}`" alt="Agent" class="w-40 h-40 object-cover rounded-full mb-3 cursor-pointer shadow-sm" @click="openLightbox(`${baseUrl}/${zone.image_url}`)">
                <div v-else class="w-20 h-20 rounded-full bg-purple-100 flex items-center justify-center mb-3">
                    <span class="text-2xl font-bold text-purple-600">{{ zone.nom.charAt(0).toUpperCase() }}</span>
                </div>
                <h4 class="font-semibold text-lg mb-2">{{ zone.nom }}</h4>
                <div class="flex gap-3">
                    <button @click="$router.push(`/admin/zones/edit/${zone.id}`)"
                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition text-sm">✏ Modifier</button>
                    <button @click="confirmDeletion(zone.id)"
                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition text-sm">🗑 Supprimer</button>
                </div>
            </div>
        </div>

        <!-- Table desktop -->
        <div class="overflow-x-auto hidden md:block">
            <div class="mt-2 bg-white rounded-lg shadow-md p-4">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b">
                            <th class="px-3 py-3">Photo</th>
                            <th class="px-3 py-3">Nom</th>
                            <th class="px-3 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="zone in agentsFiltered" :key="zone.id" class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="px-3 py-3 align-middle" style="width:100px;">
                                <div class="w-16 h-16 rounded-full overflow-hidden bg-purple-100 flex items-center justify-center cursor-pointer">
                                    <img v-if="zone.image_url" :src="`${baseUrl}/${zone.image_url}`" alt="Image" class="w-full h-full object-cover" @click="openLightbox(`${baseUrl}/${zone.image_url}`)" />
                                    <span v-else class="text-xl font-bold text-purple-600">{{ zone.nom.charAt(0).toUpperCase() }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 align-middle font-medium text-gray-800">{{ zone.nom }}</td>
                            <td class="px-3 py-3 align-middle text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button @click="$router.push(`/admin/zones/edit/${zone.id}`)"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/><path d="M20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                        Modifier
                                    </button>
                                    <button @click="confirmDeletion(zone.id)"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="agentsFiltered.length === 0">
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400">Aucun agent trouvé.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lightbox -->
        <div v-if="lightbox.visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70" @click.self="closeLightbox">
            <div class="max-w-[90%] max-h-[90%]">
                <img :src="lightbox.src" alt="Aperçu" class="w-full h-auto max-h-[90vh] rounded-lg shadow-2xl" />
            </div>
            <button @click="closeLightbox" class="absolute top-6 right-6 text-white bg-black bg-opacity-30 rounded-full p-2 hover:bg-opacity-50">✕</button>
        </div>

        <!-- Modal suppression -->
        <div v-if="showConfirmationModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-xl shadow-xl max-w-sm w-full mx-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Supprimer l'agent</h3>
                </div>
                <p class="text-gray-600 mb-6">Cette action est irréversible. Êtes-vous sûr de vouloir supprimer cet agent ?</p>
                <div class="flex justify-end gap-3">
                    <button @click="cancelDeletion"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Annuler</button>
                    <button @click="deleteZone"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Supprimer</button>
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
            recherche: "",
            baseUrl: import.meta.env.VITE_APP_URL,
            showConfirmationModal: false,
            zoneToDeleteId: null,
            lightbox: { visible: false, src: null },
        };
    },
    computed: {
        agentsFiltered() {
            let filtered = this.zones.filter(z => z.type === "personne");
            if (this.recherche) {
                filtered = filtered.filter(z => z.nom.toLowerCase().includes(this.recherche.toLowerCase()));
            }
            return filtered;
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
                .post(`/api/zones/${this.zoneToDeleteId}/delete`, {}, {
                    _method: 'DELETE'
                })
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

        scrollToTop() {
            const element = document.getElementById("topagent");
            if (element) {
                element.scrollIntoView({ behavior: "smooth" }); 
            }
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
        openLightbox(src) {
            this.lightbox.src = src;
            this.lightbox.visible = true;
            document.addEventListener('keydown', this._handleEsc);
        },
        closeLightbox() {
            this.lightbox.visible = false;
            this.lightbox.src = null;
            document.removeEventListener('keydown', this._handleEsc);
        },
        _handleEsc(e) {
            if (e.key === 'Escape') this.closeLightbox();
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
</style>
