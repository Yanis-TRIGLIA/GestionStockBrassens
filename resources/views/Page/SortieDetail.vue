<template>
    <div class="min-h-screen bg-gray-50 p-6">

        <!-- Bouton retour -->
        <button @click="$router.back()"
            class="mb-6 flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
            <i class="pi pi-arrow-left"></i>
            <span>Retour au tableau</span>
        </button>

        <!-- Chargement -->
        <div v-if="isLoading" class="flex items-center justify-center h-64">
            <i class="pi pi-spin pi-spinner text-4xl text-blue-500"></i>
        </div>

        <!-- Erreur -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center text-red-600">
            <i class="pi pi-exclamation-triangle text-3xl mb-2 block"></i>
            <p>{{ error }}</p>
        </div>

        <!-- Contenu -->
        <div v-else-if="sortie" class="max-w-4xl mx-auto">

            <!-- En-tête de la fiche -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6 flex flex-col md:flex-row items-start gap-6">
                <!-- Image produit -->
                <div class="flex-shrink-0">
                    <img v-if="sortie.produit?.image_url"
                        :src="`${baseUrl}/${sortie.produit.image_url}`"
                        :alt="sortie.produit.nom"
                        class="w-40 h-40 object-cover rounded-xl shadow" />
                    <div v-else
                        class="w-40 h-40 bg-gray-200 rounded-xl flex items-center justify-center text-gray-400">
                        <i class="pi pi-image text-4xl"></i>
                    </div>
                </div>

                <!-- Titre et badges -->
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-gray-800">{{ sortie.produit?.nom || 'Produit inconnu' }}</h1>
                        <span v-if="sortie.produit?.reference"
                            class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-mono border">
                            Réf. {{ sortie.produit.reference }}
                        </span>
                    </div>

                    <!-- Catégories -->
                    <div v-if="sortie.produit?.categories?.length" class="flex flex-wrap gap-2 mb-3">
                        <span v-for="cat in sortie.produit.categories" :key="cat.id"
                            class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">
                            {{ cat.nom }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-500">Fiche de sortie #{{ sortie.id }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="pi pi-calendar mr-1"></i>
                        {{ formatDate(sortie.updated_at) }}
                    </p>
                </div>
            </div>

            <!-- Grille des informations clés -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

                <!-- Quantité sortie -->
                <div class="bg-white rounded-xl shadow-md p-5">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1 flex items-center gap-1">
                        <i class="pi pi-box"></i> Quantité sortie
                    </p>
                    <p class="text-3xl font-bold text-blue-600">{{ sortie.quantité }}</p>
                </div>

                <!-- Stock restant (si connecté) -->
                <div v-if="token" class="bg-white rounded-xl shadow-md p-5">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1 flex items-center gap-1">
                        <i class="pi pi-chart-bar"></i> Stock après sortie
                    </p>
                    <p class="text-3xl font-bold"
                        :class="sortie.number_after_reduce <= 5 ? 'text-red-500' : 'text-green-600'">
                        {{ sortie.number_after_reduce ?? '—' }}
                    </p>
                    <p v-if="sortie.number_after_reduce <= 5" class="text-xs text-red-400 mt-1">Stock critique</p>
                </div>

                <!-- Agent -->
                <div class="bg-white rounded-xl shadow-md p-5">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1 flex items-center gap-1">
                        <i class="pi pi-user"></i> Effectué par
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            {{ agentInitials }}
                        </div>
                        <p class="font-semibold text-gray-800">{{ sortie.personne?.nom || '—' }}</p>
                    </div>
                </div>

                <!-- Zone -->
                <div class="bg-white rounded-xl shadow-md p-5">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1 flex items-center gap-1">
                        <i class="pi pi-map-marker"></i> Zone concernée
                    </p>
                    <p v-if="zoneName" class="font-semibold text-gray-800 mt-1">{{ zoneName }}</p>
                    <p v-else class="text-gray-400 italic mt-1">Aucune zone</p>
                </div>

                <!-- Date de création -->
                <div class="bg-white rounded-xl shadow-md p-5">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1 flex items-center gap-1">
                        <i class="pi pi-clock"></i> Date de création
                    </p>
                    <p class="font-semibold text-gray-800 mt-1">{{ formatDate(sortie.created_at) }}</p>
                </div>

            </div>

            <!-- Observation -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-sm text-gray-400 uppercase font-semibold mb-3 flex items-center gap-1">
                    <i class="pi pi-comment"></i> Observation
                </h2>
                <p v-if="sortie.observation" class="text-gray-700 whitespace-pre-line leading-relaxed">
                    {{ sortie.observation }}
                </p>
                <p v-else class="text-gray-400 italic">Aucune observation renseignée.</p>
            </div>

            <!-- Actions (admin) -->
            <div v-if="token" class="mt-6 flex justify-end">
                <button @click="confirmDelete"
                    class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    <i class="pi pi-trash"></i>
                    Supprimer cette sortie
                </button>
            </div>
        </div>

        <!-- Modal de confirmation suppression -->
        <div v-if="showDeleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Confirmer la suppression</h3>
                <p class="text-gray-600 mb-6">
                    Êtes-vous sûr de vouloir supprimer cette sortie ? Cette action est irréversible.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="showDeleteModal = false"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Annuler
                    </button>
                    <button @click="deleteSortie"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../../../axios.config.js';

export default {
    name: 'SortieDetail',

    setup() {
        const route = useRoute();
        const router = useRouter();

        const sortie = ref(null);
        const isLoading = ref(true);
        const error = ref(null);
        const showDeleteModal = ref(false);
        const token = localStorage.getItem('auth_token');
        const baseUrl = import.meta.env.VITE_APP_URL;

        const fetchSortie = () => {
            isLoading.value = true;
            error.value = null;
            axios.get(`/api/sorties/${route.params.id}`)
                .then(response => {
                    sortie.value = response.data;
                })
                .catch(() => {
                    error.value = 'Impossible de charger cette sortie. Elle n\'existe peut-être plus.';
                })
                .finally(() => {
                    isLoading.value = false;
                });
        };

        const formatDate = (dateStr) => {
            if (!dateStr) return '—';
            return new Date(dateStr).toLocaleString('fr-FR', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
        };

        const agentInitials = computed(() => {
            const nom = sortie.value?.personne?.nom;
            if (!nom) return '?';
            return nom.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
        });

        const zoneName = computed(() => {
            const z = sortie.value?.zone;
            if (!z) return null;
            return z.type === 'zone' ? z.nom : null;
        });

        const confirmDelete = () => {
            showDeleteModal.value = true;
        };

        const deleteSortie = () => {
            axios.post(`/api/cancel_sortie/${sortie.value.id}/delete`, {}, { _method: 'DELETE' })
                .then(() => {
                    router.push('/sortie_produit');
                })
                .catch(() => {
                    error.value = 'Erreur lors de la suppression.';
                    showDeleteModal.value = false;
                });
        };

        onMounted(fetchSortie);

        return {
            sortie, isLoading, error, baseUrl, token,
            showDeleteModal, agentInitials, zoneName,
            formatDate, confirmDelete, deleteSortie,
        };
    },
};
</script>
