<template>
    <div>
        <h2 id="topcat" class="text-2xl font-bold mb-4">📂 Liste des Catégories</h2>
        <button @click="$router.push('/admin/categorie/create')"
            class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Créez votre catégorie
        </button>

        <!-- Pagination -->
        <div class="flex justify-between items-center mb-4">
            <span class="text-sm text-gray-700">Page: {{ currentPage }} / {{ totalPages }}</span>
            <div class="flex items-center">
                <button @click="previousPage" :disabled="currentPage === 1"
                    class="px-4 py-2 bg-gray-200 rounded-l hover:bg-gray-300">
                    Précédent
                </button>
                <select v-model="currentPage" @change="changePage" class="px-4 py-2 border rounded mx-2">
                    <option v-for="page in totalPages" :key="page" :value="page">
                        {{ page }}
                    </option>
                </select>
                <button @click="nextPage" :disabled="currentPage === totalPages"
                    class="px-4 py-2 bg-gray-200 rounded-r hover:bg-gray-300">
                    Suivant
                </button>
            </div>
        </div>
        <!-- Lightbox modal -->
        <div v-if="lightbox.visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70" @click.self="closeLightbox">
            <div class="max-w-[90%] max-h-[90%]">
                <img :src="lightbox.src" alt="Aperçu" class="w-full h-auto max-h-[90vh] rounded-lg shadow-2xl" />
            </div>
            <button @click="closeLightbox" class="absolute top-6 right-6 text-white bg-black bg-opacity-30 rounded-full p-2 hover:bg-opacity-50">✕</button>
        </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:hidden">
            <div v-for="categorie in paginatedCategories" :key="categorie.id" class="bg-white p-4 shadow-md rounded-lg flex flex-col items-center text-center">
                
                <!-- Image -->
                <img v-if="categorie.image" :src="`/storage/${categorie.image}`" alt="Catégorie" class="w-40 h-40 object-cover rounded mb-3 cursor-pointer shadow-sm hover:shadow-lg transition" @click="openLightbox(`/storage/${categorie.image}`)">
                
                <!-- Nom -->
                <h3 class="text-lg font-semibold">{{ categorie.nom }}</h3>

                <!-- Actions -->
                <div class="mt-4 flex space-x-4">
                    <button @click="$router.push(`/admin/categorie/edit/${categorie.id}`)" class="text-blue-600 hover:text-blue-800">
                        ✏ Modifier
                    </button>
                    <button @click="confirmDeletion(categorie.id)" class="text-red-600 hover:text-red-800">
                        🗑 Supprimer
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto hidden md:block">
            <div class="mt-4 bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-700">Catégories</h3>
                    <span class="text-sm text-gray-500">Total : {{ categories.length }}</span>
                </div>
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 border-b pb-2">
                            <th class="px-3 py-2">Image</th>
                            <th class="px-3 py-2">Nom</th>
                            <th class="px-3 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="categorie in paginatedCategories" :key="categorie.id" class="transition transform hover:-translate-y-0.5 hover:shadow-lg">
                            <td class="px-3 py-3 align-top w-28">
                                <div class="w-24 h-24 overflow-hidden rounded-md bg-gray-100 flex items-center justify-center cursor-pointer">
                                    <img v-if="categorie.image" :src="`/storage/${categorie.image}`" alt="Image" class="w-full h-full object-cover" @click="openLightbox(`/storage/${categorie.image}`)">
                                </div>
                            </td>
                            <td class="px-3 py-3 align-top">
                                <div class="font-semibold text-sm text-gray-800">{{ categorie.nom }}</div>
                            </td>
                            <td class="px-3 py-3 text-center align-top">
                                <div class="flex justify-center items-center gap-3">
                                    <button @click="$router.push(`/admin/categorie/edit/${categorie.id}`)" class="flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow" aria-label="Modifier">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" />
                                            <path d="M20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                        </svg>
                                        <span class="text-sm">Modifier</span>
                                    </button>
                                    <button @click="confirmDeletion(categorie.id)" class="flex items-center gap-2 px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow" aria-label="Supprimer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                            <path d="M10 11v6M14 11v6" />
                                            <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
                                        </svg>
                                        <span class="text-sm">Supprimer</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="paginatedCategories.length === 0">
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">Aucune catégorie trouvée.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal de confirmation de suppression -->
        <div v-if="showConfirmationModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
                <h3 class="text-xl font-bold mb-4">Confirmer la suppression</h3>
                <p>Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
                <div class="flex justify-between mt-4">
                    <button @click="deleteCategorie" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Oui, Supprimer
                    </button>
                    <button @click="cancelDeletion" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
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
    name: "AdminCategories",
    data() {
        return {
            categories: [],
            currentPage: 1,
            categoriesPerPage: 10,
            showConfirmationModal: false,
            categoryToDeleteId: null,
            lightbox: { visible: false, src: null },
        };
    },
    computed: {
        paginatedCategories() {
            const start = (this.currentPage - 1) * this.categoriesPerPage;
            const end = start + this.categoriesPerPage;
            return this.categories.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.categories.length / this.categoriesPerPage);
        },
    },
    methods: {
        fetchCategories() {
            axios
                .get("/api/categorie")
                .then((response) => {
                    this.categories = response.data;
                })
                .catch((error) => {
                    console.error("Erreur lors de la récupération des catégories:", error);
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
        confirmDeletion(categoryId) {
            this.categoryToDeleteId = categoryId;
            this.showConfirmationModal = true;
        },
        cancelDeletion() {
            this.showConfirmationModal = false;
            this.categoryToDeleteId = null;
        },
        deleteCategorie() {
            axios
                .post(`/api/categorie/${this.categoryToDeleteId}/delete`, {}, {
                    _method: 'DELETE'
                })
                .then(() => {
                    this.showSuccessToast("Catégorie supprimée avec succès !");
                    this.fetchCategories();
                    this.showConfirmationModal = false;
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la suppression de la catégorie.");
                    this.showConfirmationModal = false;
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
        scrollToTop() {
            const element = document.getElementById("topcat");
            if (element) {
                element.scrollIntoView({ behavior: "smooth" }); 
            }
        },
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.scrollToTop();
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.scrollToTop();
            }
        },
        changePage() {
            this.fetchCategories();
            this.scrollToTop();
        },
    },
    mounted() {
        this.fetchCategories();
    },
};
</script>
