<template>
    <div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
            <div>
                <h2 id="topprod" class="text-2xl font-extrabold">🧴 Liste des Produits</h2>
                <p class="text-sm text-gray-500">Gestion des produits — Vue administrateur</p>
            </div>

            <div class="flex items-center gap-3">
                <button @click="$router.push('/admin/produits/create')"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">Créez un
                    produit</button>
            </div>
        </div>
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

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:hidden">
            <div v-for="produit in paginatedProduits" :key="produit.id"
                class="bg-white p-4 shadow-md rounded-xl w-[85%] justify-self-center transform hover:scale-[1.02] transition">

                <!-- Image -->
                <div class="flex justify-center">
                    <img v-if="produit.image_url" :src="`${baseUrl}/${produit.image_url}`" alt="Produit"
                        class="w-56 h-56 object-cover rounded cursor-pointer shadow-sm hover:shadow-lg transition"
                        @click="openLightbox(`${baseUrl}/${produit.image_url}`)">
                </div>

                <!-- Infos -->
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-semibold truncate">{{ produit.nom }}</h3>
                    <p class="text-gray-600">Quantité : <span :class="badgeClass(produit.quantité)">{{ produit.quantité
                    }}</span></p>

                    <div class="mt-3 flex flex-wrap justify-center gap-2">
                        <span v-for="categorie in produit.categories" :key="categorie.id"
                            class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full border">{{ categorie.nom
                            }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex justify-around">
                    <button @click="$router.push(`/admin/produits/edit/${produit.id}`)"
                        class="px-3 py-1 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition">✏
                        Modifier</button>
                    <button @click="confirmDeletion(produit.id)"
                        class="px-3 py-1 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition">🗑
                        Supprimer</button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto hidden md:block">
            <!-- Tableau des produits (card) -->
            <div class="mt-4 bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-700">Produits</h3>
                    <span class="text-sm text-gray-500">Total : {{ produits.length }}</span>
                </div>
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 border-b pb-2">
                            <th class="px-3 py-2">Produit</th>
                            <th class="px-3 py-2">Catégories</th>
                            <th class="px-3 py-2">Quantité</th>
                            <th class="px-3 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr v-for="produit in paginatedProduits" :key="produit.id"
                            class="transition transform hover:-translate-y-0.5 hover:shadow-lg">
                            <td class="px-3 py-3 align-top">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-28 h-28 rounded-md overflow-hidden bg-gray-100 flex items-center justify-center cursor-pointer">
                                        <img v-if="produit.image_url" :src="`${baseUrl}/${produit.image_url}`"
                                            alt="Image" class="w-full h-full object-cover"
                                            @click="openLightbox(`${baseUrl}/${produit.image_url}`)">
                                        <div v-else class="text-gray-400 text-xs">No image</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm text-gray-800">{{ produit.nom }}</div>
                                        <div class="text-sm text-gray-500 truncate max-w-[40ch]">{{ produit.description
                                            || '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 align-top w-48">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="categorie in produit.categories" :key="categorie.id"
                                        class="text-xs bg-gray-50 text-gray-700 px-2 py-0.5 rounded-full border">{{
                                            categorie.nom }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 align-top">
                                <span :class="badgeClass(produit.quantité)">{{ produit.quantité }}</span>
                            </td>
                            <td class="px-3 py-3 text-center align-top">
                                <div class="flex justify-center items-center gap-3">
                                    <button @click="$router.push(`/admin/produits/edit/${produit.id}`)"
                                        aria-label="Modifier"
                                        class="flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor">
                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" />
                                            <path
                                                d="M20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                        </svg>
                                        <span class="text-sm">Modifier</span>
                                    </button>
                                    <button @click="confirmDeletion(produit.id)" aria-label="Supprimer"
                                        class="flex items-center gap-2 px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor">
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
                        <tr v-if="paginatedProduits.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun produit trouvé.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center mt-4 gap-3">
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

        <!-- Lightbox modal -->
        <div v-if="lightbox.visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70"
            @click.self="closeLightbox">
            <div class="max-w-[90%] max-h-[90%]">
                <img :src="lightbox.src" alt="Aperçu" class="w-full h-auto max-h-[90vh] rounded-lg shadow-2xl" />
            </div>
            <button @click="closeLightbox"
                class="absolute top-6 right-6 text-white bg-black bg-opacity-30 rounded-full p-2 hover:bg-opacity-50">✕</button>
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
            lightbox: {
                visible: false,
                src: null,
            },
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
        badgeClass(quantite) {
            const base = 'inline-block px-3 py-1 rounded-full text-sm font-medium ';
            if (quantite <= 3) return base + 'bg-red-100 text-red-700 border border-red-200';
            if (quantite <= 10) return base + 'bg-yellow-100 text-yellow-800 border border-yellow-200';
            return base + 'bg-green-100 text-green-700 border border-green-200';
        },
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
        // Après (Correspond à la route protégée POST /produits/{id}/delete)
        deleteProduct() {
            // 1. Récupérer le token depuis le localStorage
            const token = localStorage.getItem('auth_token');

            if (!token) {
                this.showErrorToast("Erreur: Token d'authentification manquant. Reconnectez-vous.");
                return;
            }

            // 2. Préparer la requête avec l'en-tête d'autorisation (Bearer Token)
            axios
                .post(
                    `/api/produits/${this.productToDeleteId}/delete`,
                    {}, // Le corps de la requête est vide
                    {
                        headers: {
                            'Authorization': `Bearer ${token}` // ⬅️ Envoi du token
                        }
                    }
                )
                .then(() => {
                    this.showSuccessToast("Produit supprimé avec succès !");
                    this.fetchProduits();
                    this.cancelDeletion();
                })
                .catch((error) => {
                    console.error("Erreur de suppression:", error.response || error);
                    // Le 401 sera toujours géré ici
                    this.showErrorToast("Erreur lors de la suppression du produit. (Token ou route invalide)");
                });
        },
        openLightbox(src) {
            this.lightbox.src = src;
            this.lightbox.visible = true;
            // add key listener
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
