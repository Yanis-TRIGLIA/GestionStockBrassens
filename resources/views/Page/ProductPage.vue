<template>
    <SkeletonLoader v-if="isLoading" />
    <popup-exit :id_prod="idselected" v-if="showPopup" @close="closePopup"></popup-exit>

    <div v-else class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-gray-50 min-h-screen">
        <header class="text-center mb-8">
            <h1
                class="text-3xl sm:text-4xl font-extrabold text-gray-800 tracking-tight flex items-center justify-center gap-3">
               
                📋 Catalogue des Produits
            </h1>
            <p class="text-gray-600 mt-2">Gérez et explorez l'inventaire complet des produits.</p>
        </header>

        <div
            class="bg-white p-5 rounded-xl shadow-lg mb-8 flex flex-col md:flex-row justify-between items-center gap-4 border border-gray-100">
            <div class="relative w-full md:w-1/3">
                <input v-model="rechercheNom" type="text" placeholder="🔍 Rechercher par nom..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition duration-150" />
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd" />
                </svg>
            </div>

            <select v-model="triCategorie"
                class="w-full md:w-1/5 px-4 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 appearance-none focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition duration-150 cursor-pointer">
                <option value="">Toutes les catégories</option>
                <option v-for="categorie in categories" :key="categorie.id" :value="categorie.id">
                    {{ categorie.nom }}
                </option>
            </select>

            <select v-if="user" v-model="triQuantite"
                class="w-full md:w-1/5 px-4 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 appearance-none focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition duration-150 cursor-pointer">
                <option value="">Trier par quantité</option>
                <option value="asc">Quantité : 🔼 Croissante</option>
                <option value="desc">Quantité : 🔽 Décroissante</option>
            </select>

            <input v-if="user" v-model.number="quantiteMax" type="number" placeholder="Quantité Max ou Moins"
                class="w-full md:w-1/5 px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition duration-150" />
        </div>

        <div class="overflow-x-auto shadow-2xl rounded-xl hidden md:block border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-600 text-black sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-sm font-medium uppercase tracking-wider rounded-tl-xl">Image</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Nom du Produit</th>
                        <th v-if="user" class="px-6 py-3 text-sm font-medium uppercase tracking-wider">Stock</th>
                        <th v-if="user" class="px-6 py-3 text-sm font-medium uppercase tracking-wider">Sortie</th>
                        <th v-if="user" class="px-6 py-3 text-sm font-medium uppercase tracking-wider rounded-tr-xl">
                            Panier</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <tr v-for="(produit, index) in produitsFiltres" :key="produit.id"
                        :class="{ 'bg-gray-50': index % 2 === 1, 'hover:bg-blue-50 transition duration-150': true }"
                        @click="$router.push(`/prod/${produit.id}`)">

                        <td class="px-6 py-4 whitespace-nowrap">
                            <img :src="`${baseUrl}/${produit.image_url}`" :alt="`Image de ${produit.nom}`"
                                class="w-full h-72 sm:h-80 object-cover rounded-2xl mb-5 mx-auto shadow-lg border-2 border-gray-100 transition-transform duration-300 hover:scale-105" />

                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-left font-semibold text-gray-800 group-hover:text-blue-600">
                            {{ produit.nom }}
                        </td>
                        <td v-if="user" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span
                                :class="{ 'bg-green-100 text-green-800': produit.quantité > 5, 'bg-yellow-100 text-yellow-800': produit.quantité <= 5 && produit.quantité > 1, 'bg-red-100 text-red-800': produit.quantité <= 1 }"
                                class="px-3 inline-flex text-xs leading-5 font-bold rounded-full">
                                {{ produit.quantité }} en stock
                            </span>
                        </td>
                        <td v-if="user" class="px-6 py-4 whitespace-nowrap" @click.stop>
                            <button @click="openPopup(produit.id)"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                <svg class="h-5 w-5 mr-2 transform rotate-90" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 12L3.268 9.659a2 2 0 010-3.32C5.071 5.093 8.169 4 12 4c3.831 0 6.929 1.093 8.732 2.339a2 2 0 010 3.32L18 12m-6 6h.01M12 4v16m-4 0h8" />
                                </svg>
                                Sortie
                            </button>
                        </td>
                        <td v-if="user" class="px-6 py-4 whitespace-nowrap text-center" @click.stop>
                            <div class="flex flex-col items-center space-y-2">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="modifierQuantite(produit, -1)"
                                        class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 disabled:opacity-50 transition duration-150">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input type="number" v-model="panier[produit.id]" min="1"
                                        class="w-14 text-center border-2 border-gray-300 rounded-lg p-1 text-sm focus:border-blue-500" />
                                    <button @click="modifierQuantite(produit, 1)"
                                        class="bg-green-500 text-white p-2 rounded-lg hover:bg-green-600 transition duration-150">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <div v-if="panier_verif.includes(produit.id)">
                                    <span class="text-sm font-bold text-green-600 flex items-center gap-1">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Dans le panier
                                    </span>
                                </div>
                                <div v-else>
                                    <button @click="ajouterAuPanier(produit)"
                                        class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:hidden">
            <div v-for="produit in produitsFiltres" :key="produit.id"
                class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 p-5 border border-gray-100 cursor-pointer flex flex-col items-center text-center"
                @click="$router.push(`/prod/${produit.id}`)">

                <img :src="`${baseUrl}/${produit.image_url}`" :alt="`Image de ${produit.nom}`"
                    class="w-full h-full object-cover rounded-lg mb-4 mx-auto shadow-md border-2 border-gray-100" />

                <h2 class="text-xl font-extrabold text-gray-800 mb-2 truncate w-full">{{ produit.nom }}</h2>

                <p v-if="user" class="text-sm mb-4">
                    <span
                        :class="{ 'bg-green-100 text-green-800': produit.quantité > 5, 'bg-yellow-100 text-yellow-800': produit.quantité <= 5 && produit.quantité > 1, 'bg-red-100 text-red-800': produit.quantité <= 1 }"
                        class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full">
                        Stock: {{ produit.quantité }}
                    </span>
                </p>

                <div v-if="user" class="flex flex-col space-y-3 mt-4 w-full">
                    <button @click.stop="openPopup(produit.id)"
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition duration-200">
                        <svg class="h-5 w-5 mr-2 transform rotate-90" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 12L3.268 9.659a2 2 0 010-3.32C5.071 5.093 8.169 4 12 4c3.831 0 6.929 1.093 8.732 2.339a2 2 0 010 3.32L18 12m-6 6h.01M12 4v16m-4 0h8" />
                        </svg>
                        Effectuer une sortie
                    </button>

                    <div class="flex flex-col items-center space-y-2 pt-2 w-full" @click.stop>
                        <div class="flex items-center justify-center space-x-2">
                            <button @click="modifierQuantite(produit, -1)"
                                class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 disabled:opacity-50 transition duration-150">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="number" v-model="panier[produit.id]" min="1"
                                class="w-14 text-center border-2 border-gray-300 rounded-lg p-1 text-sm focus:border-blue-500" />
                            <button @click="modifierQuantite(produit, 1)"
                                class="bg-green-500 text-white p-2 rounded-lg hover:bg-green-600 transition duration-150">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        <div v-if="panier_verif.includes(produit.id)">
                            <span class="text-sm font-bold text-green-600 mt-2 flex items-center gap-1">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Dans le panier
                            </span>
                        </div>
                        <div v-else>
                            <button @click="ajouterAuPanier(produit)"
                                class="mt-2 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-500 hover:bg-blue-600 transition duration-150">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
import SkeletonLoader from './SkeletonLoader.vue';
import PopupExit from "../Components/PopupExit.vue";
import { number } from "echarts";



export default {
    name: "Produits",
    components: { SkeletonLoader, PopupExit },

    data() {
        return {
            produits: [],
            categories: [],
            rechercheNom: "",
            triQuantite: "",
            showPopup: false,
            idselected: number,
            isLoading: true,
            quantiteMax: null,
            triCategorie: "",
            baseUrl: import.meta.env.VITE_APP_URL,
            user: localStorage.getItem("auth_token"),
            panier: {},
            panier_verif: [],
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

            if (this.triQuantite === "asc") {
                filtres.sort((a, b) => a.quantité - b.quantité);
            } else if (this.triQuantite === "desc") {
                filtres.sort((a, b) => b.quantité - a.quantité);
            }

            if (this.quantiteMax !== null && this.quantiteMax !== "") {
                filtres = filtres.filter(produit => produit.quantité <= this.quantiteMax);
            }

            return filtres;
        }
    },
    methods: {
        modifierQuantite(produit, valeur) {
            let nouvelleQuantite = (this.panier[produit.id] || 1) + valeur;
            if (nouvelleQuantite < 1) nouvelleQuantite = 1;
            this.panier[produit.id] = nouvelleQuantite;
        },

        openPopup(id_select_produit) {
            this.showPopup = true;
            this.idselected = id_select_produit;

        },
        closePopup() {
            this.showPopup = false;
        },




        loadPanier() {
            const token = localStorage.getItem("auth_token");
            if (!token) return;

            axios.get("/api/panier", {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json"
                }
            })
                .then(response => {
                    this.panier_verif = response.data.produits.map(produit => produit.id);

                })
                .catch(error => {
                    console.error("Erreur chargement panier:", error);
                });


        },

        async ajouterAuPanier(produit) {
            try {
                const token = localStorage.getItem("auth_token");

                if (!token) {
                    console.error("Aucun token trouvé, l'utilisateur n'est pas authentifié.");
                    return;
                }

                await axios.post("/api/panier/ajouter",
                    {
                        produit_id: produit.id,
                        quantite: this.panier[produit.id] || 1
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            Accept: "application/json"
                        },
                        withCredentials: true
                    }
                );

                this.showSuccessToast("Produit ajouté au panier avec succès !");

            } catch (error) {
                console.error("Erreur ajout au panier:", error);
            }
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
            //on reload
            setTimeout(() => {
                location.reload();
            }, 1500);
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
        }

    },
    mounted() {
        axios.get("/api/produits")
            .then((response) => { this.produits = response.data; })
            .catch((error) => { console.error("Erreur produits:", error); });

        axios.get("/api/categorie")
            .then((response) => { this.categories = response.data; })
            .catch((error) => { console.error("Erreur catégories:", error); });

        this.loadPanier();


        this.isLoading = false;

    }
};
</script>

<style scoped>
/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 12px;
    text-align: center;
}

th {
    background-color: #f9fafb;
    font-weight: bold;
}

tr:hover {
    background-color: #e0e7ff;
}

/* Responsive */
@media (max-width: 768px) {
    table {
        font-size: 14px;
    }

    img {
        width: 50%;
        height: 40%;
    }
}


@media (min-width: 800px) {
    img {
        width: 100px;
        height: 100px;
    }
}
</style>
