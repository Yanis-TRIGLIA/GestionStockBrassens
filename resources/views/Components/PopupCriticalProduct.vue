<script>
import axios from "../../../axios.config";

export default {
    name: "PopupCriticalProduct",
    data() {
        return {
            produits: [],
            isAdmin: false,
            showAlert: false,
            song: false,
            interaction: false,
            baseUrl: import.meta.env.VITE_APP_URL,
        };
    },
    mounted() {
        this.checkAdmin();


    },
    methods: {
        async checkAdmin() {
            const token = localStorage.getItem("auth_token");
            if (!token) return;

            try {
                const response = await axios.get("/api/user", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                if (response.data.is_admin == 1) {
                    this.isAdmin = true;
                    this.fetchProduits();
                }
            } catch (error) {
                console.error("Erreur lors de la vérification de l'admin :", error);
            }
        },

        async fetchProduits() {

            document.addEventListener('click', () => {
                console.log("UserInteracted");
                this.interaction = true;
            });


            try {
                const response = await axios.get("/api/produits");
                this.produits = response.data;

                const hasOutOfStock = this.produits.some(prod => prod.quantité === 0);

                if (this.isAdmin && hasOutOfStock && this.shouldShowPopup()) {
                    console.log("Produits en rupture de stock !");
                    this.showAlert = true;
                    this.setPopupTimestamp();
                }
            } catch (error) {
                console.error("Erreur lors de la récupération des produits :", error);
            }
        },

        shouldShowPopup() {
            const lastPopupTime = localStorage.getItem("lastPopupTime");
            if (!lastPopupTime) return true;

            const lastTime = new Date(parseInt(lastPopupTime, 10));
            const now = new Date();

            // On affiche la pop-up toutes les 1 heures
            return now - lastTime > 3600000;

        },

        setPopupTimestamp() {
            console.log(Date.now().toString());
            localStorage.setItem("lastPopupTime", Date.now().toString());
        },

        closePopup() {
            this.showAlert = false;
        },









        goToProductPage(id) {
            this.$router.push(`/prod/${id}`);
        }
    },
};
</script>

<template>
    <div v-if="showAlert" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg text-center relative">
            <h2 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">⚠️ Stock Critique</h2>
            <p class="text-gray-700 dark:text-gray-300">Les produits suivants sont en rupture de stock :</p>

            <div class="mt-4 space-y-4 max-h-[400px] overflow-y-auto">
                <div v-for="prod in produits.filter(p => p.quantité === 0)" :key="prod.id"
                    class="flex items-center p-3 bg-gray-100 dark:bg-gray-700 rounded-lg shadow cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                    @click="goToProductPage(prod.id)">

                    <img :src="`${baseUrl}/${prod.image_url}`" alt="Image produit"
                        class="w-16 h-16 object-cover rounded-md mr-4">

                    <div class="text-left">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ prod.nom }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ prod.description.length > 60 ? prod.description.substring(0, 60) + '...' :
                                prod.description }}
                        </p>
                        <p class="text-red-500 font-bold">Stock : {{ prod.quantité }}</p>
                    </div>
                </div>
            </div>

            <button @click="closePopup"
                class="mt-6 bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-700 transition duration-300">
                Fermer
            </button>
        </div>
    </div>

</template>

<style scoped>
.fixed {
    z-index: 50;
}

.bg-white {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
