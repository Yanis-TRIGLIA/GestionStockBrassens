<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">📂 Liste des Catégories</h2>
        <button
            @click="$router.push('/admin/categorie/create')"
            class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
            Créez votre catégorie
        </button>

        <!-- Pagination -->
        <div class="flex justify-between items-center mb-4">
            <span class="text-sm text-gray-700">Page: {{ currentPage }} / {{ totalPages }}</span>
            <div class="flex items-center">
                <button
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="px-4 py-2 bg-gray-200 rounded-l hover:bg-gray-300"
                >
                    Précédent
                </button>
                <select
                    v-model="currentPage"
                    @change="changePage"
                    class="px-4 py-2 border rounded mx-2"
                >
                    <option v-for="page in totalPages" :key="page" :value="page">
                        {{ page }}
                    </option>
                </select>
                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="px-4 py-2 bg-gray-200 rounded-r hover:bg-gray-300"
                >
                    Suivant
                </button>
            </div>
        </div>

        <table class="min-w-full border-collapse border border-gray-300">
            <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">Image</th>
                <th class="border border-gray-300 px-4 py-2">Nom</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="categorie in paginatedCategories" :key="categorie.id">
                <td class="border border-gray-300 px-4 py-2">
                    <img v-if="categorie.image" :src="`/storage/${categorie.image}`" alt="Image" class="w-16 h-16 object-cover rounded">
                </td>
                <td class="border border-gray-300 px-4 py-2">{{ categorie.nom }}</td>
                <td class="border-b border-gray-300 px-4 py-2 flex space-x-4">
                    <button
                        @click="$router.push(`/admin/categorie/edit/${categorie.id}`)"
                        class="text-blue-600 hover:text-blue-800"
                    >
                        Modifier
                    </button>
                    <button
                        @click="confirmDeletion(categorie.id)"
                        class="text-red-600 hover:text-red-800"
                    >
                        Supprimer
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Modal de confirmation de suppression -->
        <div v-if="showConfirmationModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
                <h3 class="text-xl font-bold mb-4">Confirmer la suppression</h3>
                <p>Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
                <div class="flex justify-between mt-4">
                    <button
                        @click="deleteCategorie"
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
    name: "AdminCategories",
    data() {
        return {
            categories: [],
            currentPage: 1,
            categoriesPerPage: 10,
            showConfirmationModal: false,
            categoryToDeleteId: null,
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
                .delete(`/api/categorie/${this.categoryToDeleteId}`)
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
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        changePage() {
            this.fetchCategories();
        },
    },
    mounted() {
        this.fetchCategories();
    },
};
</script>
