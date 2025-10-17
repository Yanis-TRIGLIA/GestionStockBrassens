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

  <div v-else class="bg-gradient-to-b from-gray-50 via-white to-gray-100 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 min-h-screen py-12 lg:py-20">
    <div class="container mx-auto px-4 lg:px-8 max-w-6xl">
      
      <div class="flex flex-col md:flex-row gap-10">
        <!-- Image Produit -->
        <div class="md:w-1/2 lg:w-5/12 sticky top-6 h-fit bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-4">
          <div class="overflow-hidden rounded-xl">
            <img
              :src="`${baseUrl}/${produit.image_url}`"
              :alt="produit.nom"
              class="w-full h-96 object-contain transition-transform duration-500 hover:scale-105"
              style="aspect-ratio: 4/3;"
            />
          </div>
        </div>

        <!-- Informations Produit -->
        <div class="md:w-1/2 lg:w-7/12">
          <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-gray-50 mb-3">
            {{ produit.nom }}
          </h1>

          <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-4">
            🎫 Référence : <span class="font-semibold">{{ produit.reference }}</span>
          </p>

          <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-6">
            {{ produit.prix }} €
          </p>

          <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2 flex items-center gap-2">
            <span class="text-blue-500 dark:text-blue-400">📄</span> Description :
          </h2>

          <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-6 p-5 rounded-xl bg-gray-100 dark:bg-gray-800 border-l-4 border-blue-400 dark:border-blue-500 shadow-sm">
            {{ produit.description }}
          </p>

          <div
            v-if="token"
            class="flex items-center gap-4 text-lg mb-8 p-4 bg-gray-100 dark:bg-gray-800 rounded-xl shadow-inner border border-gray-200 dark:border-gray-700"
          >
            <span class="text-blue-500 dark:text-blue-400">⚖️</span>
            <p class="text-gray-700 dark:text-gray-300 font-medium">
              Stock disponible :
              <span class="font-bold text-blue-600 dark:text-blue-400">{{ produit.quantité }}</span>
            </p>
          </div>

          <!-- Panier -->
          <div
            v-if="token"
            class="p-6 border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-gray-800 shadow-lg"
          >
            <h2 class="text-xl font-bold text-gray-900 mb-5">🧺 Panier</h2>

            <div v-if="!panier_verif.includes(produit.id)" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
              <!-- Quantity -->
              <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden dark:bg-gray-700">
                <button
                  @click="quantity > 1 ? quantity-- : null"
                  class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                >
                  <span class="text-2xl font-bold">−</span>
                </button>
                <input
                  type="number"
                  v-model.number="quantity"
                  :max="produit.quantité"
                  min="1"
                  class="w-16 text-center text-lg font-semibold bg-transparent focus:outline-none border-x border-gray-300 dark:border-gray-600 p-2 dark:text-white"
                  @input="quantity = Math.max(1, Math.min(produit.quantité, quantity))"
                />
                <button
                  @click="quantity < produit.quantité ? quantity++ : null"
                  class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                >
                  <span class="text-2xl font-bold">+</span>
                </button>
              </div>

              <!-- Bouton panier -->
              <button
                @click="ajouterAuPanier()"
                class="w-full sm:flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl font-extrabold text-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transition-transform duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                  ></path>
                </svg>
                Ajouter au panier
              </button>
            </div>
          </div>

          <!-- Autres informations -->
          <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-5">
            <button
              v-if="tokenExists"
              @click="openPopup"
              class="bg-gradient-to-r from-teal-600 to-blue-600 text-white px-6 py-3 rounded-xl font-bold shadow-md hover:from-teal-700 hover:to-blue-700 transition flex items-center justify-center gap-2"
            >
              ⚡ Effectuer une sortie
            </button>

            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-xl shadow-inner">
              <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-white">🔠 Catégorie :</h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="categorie in produit.categories"
                  :key="categorie.id"
                  class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-200"
                >
                  {{ categorie.nom }}
                </span>
              </div>
            </div>

            <div class="md:col-span-2 p-4 bg-gray-100 dark:bg-gray-800 rounded-xl shadow-inner">
              <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">📂 Fiches Techniques :</h3>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a
                  v-for="(file, index) in fileProduct"
                  :key="index"
                  :href="`${baseUrl}/${file}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-200 flex items-center justify-center text-sm shadow-sm"
                >
                  📎 Fiche {{ index + 1 }}
                </a>
              </div>
            </div>
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
