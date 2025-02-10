<template>
    <div>

        <h2 id="topprod" class="text-2xl font-bold mb-4">🧴 Liste des Produits</h2>
        <button @click="$router.push('/admin/produits/create')"
            class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Créez votre produit
        </button>
        <!-- Filtres et recherche -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <input v-model="rechercheNom" type="text" placeholder="Rechercher par nom"
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <select v-model="triCategorie"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Trier par catégorie</option>
                <option v-for="categorie in categories" :key="categorie.id" :value="categorie.id">
                    {{ categorie.nom }}
                </option>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:hidden">
            <div v-for="produit in paginatedProduits" :key="produit.id"
                class="bg-white p-4 shadow-md rounded-lg w-[80%] justify-self-center ">

                <!-- Image -->
                <div class="flex justify-center">
                    <img v-if="produit.image_url" :src="`${baseUrl}/${produit.image_url}`" alt="Produit"
                        class="w-32 h-32 object-cover rounded">
                </div>

                <!-- Infos -->
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-semibold">{{ produit.nom }}</h3>
                    <p class="text-gray-600">Quantité : {{ produit.quantité }}</p>

                    <div class="mt-2">
                        <span v-for="categorie in produit.categories" :key="categorie.id"
                            class="text-sm bg-blue-100 text-blue-600 px-2 py-1 rounded-full mr-1">
                            {{ categorie.nom }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex justify-around">
                    <button @click="$router.push(`/admin/produits/edit/${produit.id}`)"
                        class="text-blue-600 hover:text-blue-800 ">
                        ✏ Modifier
                    </button>
                    <button @click="confirmDeletion(produit.id)" class="text-red-600 hover:text-red-800">
                        🗑 Supprimer
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto hidden md:block">
            <!-- Tableau des produits -->
            <table class="w-full border-collapse border border-gray-300 mt-2">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Image</th>
                        <th class="border border-gray-300 px-4 py-2">Nom</th>
                        <th class="border border-gray-300 px-4 py-2">Quantité</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="produit in paginatedProduits" :key="produit.id">
                        <td class="border border-gray-300 px-4 py-2">
                            <img v-if="produit.image_url" :src="`${baseUrl}/${produit.image_url}`" alt="Image"
                                class="w-16 h-16 object-cover rounded">
                        </td>
                        <td class="border border-gray-300 px-4 py-2">{{ produit.nom }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ produit.quantité }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <div class="flex justify-center items-center space-x-4">
                                <button @click="$router.push(`/admin/produits/edit/${produit.id}`)"
                                    class="text-blue-600 hover:text-blue-800 text-base ">
                                    Modifier
                                </button>
                                <button @click="confirmDeletion(produit.id)"
                                    class="text-red-600 hover:text-red-800 text-base ">
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <button @click="prevPage" :disabled="currentPage === 1"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Précédent</button>
            <span>Page {{ currentPage }} sur {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage === totalPages"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Suivant</button>
        </div>

        <!-- Formulaire de création/édition -->
        <formulaire-produit v-if="isFormVisible" :produit="currentProduit" @submit="handleFormSubmit"
            @close="closeForm"></formulaire-produit>

        <!-- Modal de confirmation de suppression -->
        <div v-if="showConfirmationModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded shadow-lg">
                <p>Êtes-vous sûr de vouloir supprimer ce produit ?</p>
                <div class="flex justify-end mt-4">
                    <button @click="cancelDeletion"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Annuler</button>
                    <button @click="deleteProduct"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 ml-2">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import FormulaireProduit from "./FormulaireProduit.vue";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";

export default {
    name: "AdminProduits",
    components: { FormulaireProduit },
    data() {
        return {
            produits: [],
            categories: [],
            rechercheNom: "",
            triCategorie: "",
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
        produitsFiltres() {
            let filtres = this.produits;

            if (this.rechercheNom) {
                filtres = filtres.filter((produit) =>
                    produit.nom.toLowerCase().includes(this.rechercheNom.toLowerCase())
                );
            }

            if (this.triCategorie) {
                filtres = filtres.filter((produit) =>
                    produit.categories.some(categorie => categorie.id === parseInt(this.triCategorie))
                );
            }

            return filtres;
        },
        paginatedProduits() {
            const start = (this.currentPage - 1) * this.productsPerPage;
            const end = start + this.productsPerPage;
            return this.produitsFiltres.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.produitsFiltres.length / this.productsPerPage);
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
        deleteProduct() {
            axios
                .delete(`/api/produits/${this.productToDeleteId}`)
                .then(() => {
                    this.showSuccessToast("Produit supprimé avec succès !");
                    this.fetchProduits();
                    this.cancelDeletion();
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la suppression du produit.");
                });
        },
        fetchProduits() {
            axios.get("/api/produits")
                .then((response) => { this.produits = response.data; })
                .catch((error) => { console.error("Erreur produits:", error); });

            axios.get("/api/categorie")
                .then((response) => { this.categories = response.data; })
                .catch((error) => { console.error("Erreur catégories:", error); });
        },
        showSuccessToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#4CAF50",
                stopOnFocus: true
            }).showToast();
        },
        showErrorToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#FF0000",
                stopOnFocus: true
            }).showToast();
        },
        scrollToTop() {
            const element = document.getElementById("topprod");
            if (element) {
                element.scrollIntoView({ behavior: "smooth" });
            }
        },
        prevPage() {
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
        }
    },
    mounted() {
        this.fetchProduits();
    }
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
