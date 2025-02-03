<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">🧴 Liste des Produits</h2>
        <button @click="$router.push('/admin/produits/create')"
            class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Créez votre produit
        </button>

       

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:hidden">
            <div v-for="produit in paginatedProduits" :key="produit.id" class="bg-white p-4 shadow-md rounded-lg w-[80%] justify-self-center ">
                
                <!-- Image -->
                <div class="flex justify-center">
                    <img v-if="produit.image_url" :src="`${baseUrl}/${produit.image_url}`" alt="Produit" class="w-32 h-32 object-cover rounded">
                </div>

                <!-- Infos -->
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-semibold">{{ produit.nom }}</h3>
                    <p class="text-gray-600">Quantité : {{ produit.quantité }}</p>
                    
                    <div class="mt-2">
                        <span v-for="categorie in produit.categories" :key="categorie.id" class="text-sm bg-blue-100 text-blue-600 px-2 py-1 rounded-full mr-1">
                            {{ categorie.nom }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex justify-around">
                    <button @click="$router.push(`/admin/produits/edit/${produit.id}`)" class="text-blue-600 hover:text-blue-800">
                        ✏ Modifier
                    </button>
                    <button @click="confirmDeletion(produit.id)" class="text-red-600 hover:text-red-800">
                        🗑 Supprimer
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto hidden md:block">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Nom</th>
                        <th class="border border-gray-300 px-4 py-2">Quantité</th>
                        <th class="border border-gray-300 px-4 py-2">Image</th>
                        <th class="border border-gray-300 px-4 py-2">Categorie</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="produit in paginatedProduits" :key="produit.id">
                        <td class="border border-gray-300 px-4 py-2">{{ produit.nom }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ produit.quantité }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <img v-if="produit.image_url" :src="`${baseUrl}/${produit.image_url}`" alt="Image"
                                class="w-16 h-16 object-cover rounded">
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <ul class="list-disc pl-5">
                                <li v-for="categorie in produit.categories" :key="categorie.id">
                                    {{ categorie.nom }}
                                </li>
                            </ul>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <div class="flex justify-center items-center space-x-4">
                                <button @click="$router.push(`/admin/produits/edit/${produit.id}`)"
                                    class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm">
                                    Modifier
                                </button>
                                <button @click="confirmDeletion(produit.id)" class="text-red-600 hover:text-red-800 text-xs sm:text-sm">
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

         <!-- Pagination -->
         <div class="flex justify-between items-center mb-4 flex-wrap mt-10">
            <span class="text-sm text-gray-700 w-full sm:w-auto mb-2 sm:mb-0">Page: {{ currentPage }} / {{ totalPages }}</span>
            <div class="flex items-center w-full sm:w-auto justify-between sm:justify-start">
                <button @click="previousPage" :disabled="currentPage === 1"
                    class="px-4 py-2 bg-gray-200 rounded-l hover:bg-gray-300 w-full sm:w-auto mb-2 sm:mb-0">
                    Précédent
                </button>
                <select v-model="currentPage" @change="changePage" class="px-4 py-2 border rounded mx-2 w-full sm:w-auto mb-2 sm:mb-0">
                    <option v-for="page in totalPages" :key="page" :value="page">
                        {{ page }}
                    </option>
                </select>
                <button @click="nextPage" :disabled="currentPage === totalPages"
                    class="px-4 py-2 bg-gray-200 rounded-r hover:bg-gray-300 w-full sm:w-auto">
                    Suivant
                </button>
            </div>
        </div>

        <!-- Modal de confirmation de suppression -->
        <div v-if="showConfirmationModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
                <h3 class="text-xl font-bold mb-4">Confirmer la suppression</h3>
                <p>Êtes-vous sûr de vouloir supprimer ce produit ?</p>
                <div class="flex justify-between mt-4">
                    <button @click="deleteProduit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
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
import FormulaireProduit from "./FormulaireProduit.vue";

export default {
    name: "AdminProduits",
    components: { FormulaireProduit },
    data() {
        return {
            produits: [],
            isFormVisible: false,
            baseUrl: import.meta.env.VITE_APP_URL,
            isEditMode: false,
            currentProduit: null,
            currentPage: 1,
            productsPerPage: 10,
            showConfirmationModal: false,
            productToDeleteId: null,  // ID du produit à supprimer
        };
    },
    computed: {
        paginatedProduits() {
            const start = (this.currentPage - 1) * this.productsPerPage;
            const end = start + this.productsPerPage;
            return this.produits.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.produits.length / this.productsPerPage);
        },
    },
    methods: {
        openCreateForm() {
            this.isFormVisible = true;
            this.isEditMode = false;
            this.currentProduit = {
                nom: "",
                description: "",
                quantité: 0,
                image_url: "",
                file_product: null,
            };
        },
        openEditForm(produit) {
            this.isFormVisible = true;
            this.isEditMode = true;
            this.currentProduit = { ...produit };
        },
        closeForm() {
            this.isFormVisible = false;
            this.currentProduit = null;
        },
        handleFormSubmit(formData) {
            if (this.isEditMode) {
                axios
                    .put(`/api/produits/${formData.id}`, formData)
                    .then(() => {
                        this.showSuccessToast("Produit modifié avec succès !");
                        this.fetchProduits();
                        this.closeForm();
                    })
                    .catch(() => {
                        this.showErrorToast("Erreur lors de la modification du produit.");
                    });
            } else {
                axios
                    .post("/api/produits", formData)
                    .then(() => {
                        this.showSuccessToast("Produit créé avec succès !");
                        this.fetchProduits();
                        this.closeForm();
                    })
                    .catch(() => {
                        this.showErrorToast("Erreur lors de la création du produit.");
                    });
            }
        },
        confirmDeletion(productId) {
            this.productToDeleteId = productId;
            this.showConfirmationModal = true;
        },
        cancelDeletion() {
            this.showConfirmationModal = false;
            this.productToDeleteId = null;
        },
        deleteProduit() {
            axios
                .post(`/api/produits/${this.productToDeleteId}/delete`, {}, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(() => {
                    this.showSuccessToast("Produit supprimé avec succès !");
                    this.fetchProduits();
                    this.showConfirmationModal = false;
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la suppression du produit.");
                    this.showConfirmationModal = false;
                });
        },

        fetchProduits() {
            axios
                .get("/api/produits")
                .then((response) => {
                    this.produits = response.data;
                })
                .catch((error) => {
                    console.error("Erreur lors de la récupération des produits:", error);
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
            this.fetchProduits();
        },
    },
    mounted() {
        this.fetchProduits();
    },
};
</script>

<style scoped>
/* Styles modale de confirmation */
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
</style>
