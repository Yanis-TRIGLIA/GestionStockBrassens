<script>
import axios from "../../../axios.config";
import PopupExit from "../Components/PopupExit.vue";
import SkeletonLoader from './SkeletonLoader.vue';

export default {
    name: "Product",
    components: { PopupExit, SkeletonLoader },
    urlParts: window.location.pathname.split("/"),
    quantite: 1,
    addedToCart: false,
    token :"",
    data() {
        return {
            produit: [],
            baseUrl: import.meta.env.VITE_APP_URL,
            url_pdf: import.meta.env.VITE_APP_URL_PDF,
            showPopup: false,
            isLoading: true,
            id_produit: null,
            tokenExists: false,
            fileProduct: [],
            quantity: 1,  // Variable pour la quantité
            addedToCart: false,
            panier_verif: [],
        };
    },
    mounted() {
        this.token = localStorage.getItem('auth_token');
        this.tokenExists = !!this.token;
        this.id_produit = window.location.pathname.split("/").pop();
        axios
            .get(`/api/produits/${this.id_produit}`)
            .then((response) => {
                this.produit = response.data;
                this.fileProduct = JSON.parse(this.produit.file_product); // Parse the JSON string
            })
            .catch((error) => {
                console.error("Erreur lors de la récupération des produits:", error);
            });
        this.loadPanier();
        this.isLoading = false;
    },
    methods: {
        openPopup() {
            this.showPopup = true;
        },

        ajouterAuPanier() {
            this.addToCart(); // Appel de la méthode d'ajout au panier

            this.addedToCart = true; // Affiche le message de confirmation
            setTimeout(() => {
                this.addedToCart = false; // Cache le message après 3 secondes
            }, 3000);
            //on reload la page
            window.location.reload();
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

        closePopup() {
            this.showPopup = false;
        },

        async addToCart() {
            try {
                const token = localStorage.getItem("auth_token");

                if (!token) {
                    console.error("Aucun token trouvé, l'utilisateur n'est pas authentifié.");
                    return;
                }

                await axios.post("/api/panier/ajouter",
                    {
                        produit_id: this.id_produit,
                        quantite: this.quantity,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            Accept: "application/json"
                        },
                        withCredentials: true
                    }
                );

            } catch (error) {
                console.error("Erreur ajout au panier:", error);
            }
        }
    }

}

</script>

<template>
    <popup-exit v-if="showPopup" @close="closePopup"></popup-exit>

    <SkeletonLoader v-if="isLoading" />

    <div v-else class="bg-gray-100 dark:bg-gray-800 py-10">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- IMAGE PRODUIT -->
                <div class="md:w-1/2">
                    <div class="rounded-lg overflow-hidden bg-gray-300 dark:bg-gray-700 shadow-lg">
                        <img :src="`${baseUrl}/${produit.image_url}`" alt="Product"
                            class="w-full h-[460px] object-cover">
                    </div>
                </div>

                <!-- DÉTAILS PRODUIT -->
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">{{ produit.nom }}</h2>
                    <p v-if="token" class="text-gray-600 dark:text-gray-300 text-lg mb-2">⚖️ Quantité disponible : <span
                            class="font-semibold">{{ produit.quantité }}</span></p>
                    <p class="text-gray-700 dark:text-gray-300 text-lg mb-2">🎫 Référence : <span
                            class="font-semibold">{{ produit.reference }}</span></p>
                    <p class="text-gray-700 dark:text-gray-300 text-lg mb-4">📃 Description : <span
                            class="font-semibold">{{ produit.description }}</span></p>

                    <p class="text-xl font-bold text-green-600 dark:text-green-400 mb-6">💰 Prix : {{ produit.prix }} €
                    </p>
                    <div v-if="token">
                        <h2 class="text-white font-semibold mb-4">🧺 Panier :</h2>
                        <!-- MESSAGE "DÉJÀ DANS LE PANIER" -->
                        <div v-if="panier_verif && panier_verif.includes(produit.id)"
                            class="bg-green-100 text-green-700 dark:bg-green-700 dark:text-white font-bold p-3 rounded-lg mb-4 md:w-4/12 ">
                            ✅ Déjà dans le panier
                        </div>

                        <!-- AJOUTER AU PANIER -->

                        <div v-else
                            class="flex items-center gap-4 bg-white dark:bg-gray-700 p-4 rounded-lg shadow-md md:w-7/12">
                            <label for="quantity" class="text-lg font-semibold">📦 Quantité :</label>
                            <input type="number" v-model="quantity" :max="produit.quantité" min="1"
                                class="w-20 p-2 border rounded-md text-center" id="quantity">

                            <button @click="ajouterAuPanier()"
                                class="bg-blue-500 text-white px-6 py-2 rounded-lg font-bold hover:bg-green-600 transition duration-200 flex items-center gap-2">
                                🛒 Ajouter
                            </button>
                        </div>
                    </div>

                    <!-- MESSAGE DE CONFIRMATION -->
                    <div v-if="addedToCart"
                        class="mt-4 text-green-600 dark:text-green-400 font-semibold bg-green-100 dark:bg-green-700 p-3 rounded-lg">
                        ✅ Le produit <span class="font-bold">{{ produit.nom }}</span> a été ajouté au panier !
                    </div>

                    <!-- BOUTON SORTIE (SI CONNECTÉ) -->
                    <button v-if="tokenExists" @click="openPopup"
                        class="mt-6 bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition duration-200 flex items-center gap-2">
                        🚀 Effectuer une sortie
                    </button>

                    <!-- TÉLÉCHARGEMENT FICHES TECHNIQUES -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-2 text-white">📂 Fiches Techniques :</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <router-link v-for="(file, index) in fileProduct" :key="index" :to="`/${file}`"
                                target="_blank">
                                <button
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-indigo-700 transition duration-200 flex items-center gap-2">
                                    📄 Fiche Techniques {{ index + 1 }}
                                </button>
                            </router-link>
                        </div>
                    </div>

                    <!-- CATÉGORIE -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-2 text-white">🔠 Catégorie :</h3>
                        <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 px-4">
                            <li v-for="categorie in produit.categories" :key="categorie.id">
                                {{ categorie.nom }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>




<style scoped>
/* Personnalisation de la section ajouter au panier */
.bg-white {
    background-color: #ffffff;
}

button:hover {
    background-color: #4c6ef5;
}

button:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease-in-out;
}
</style>
