<script>
import axios from "../../../axios.config"
import PopupExit from "../Components/PopupExit.vue";


export default {
    name: "Product",
    components: { PopupExit },
    urlParts : window.location.pathname.split("/"),
    data() {
        return {
            produit: [],
            showPopup: false,
            id_produit : null,
        };
    },
    mounted() {
        this.id_produit = window.location.pathname.split("/").pop();
        axios
            .get(`/api/produits/${this.id_produit}`)

            .then((response) => {
                this.produit = response.data;
            })
            .catch((error) => {
                console.error("Erreur lors de la récupération des produits:", error);
            });
    },
    methods: {
        openPopup() {
            this.showPopup = true;
        },
        closePopup() {
            this.showPopup = false;
        }
    }

}
</script>

<template>
    <!-- La pop-up s'affiche si showPopup est vrai -->
    <popup-exit v-if="showPopup" @close="closePopup"></popup-exit>

    <div class="bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-wrap -mx-4">
                <!-- Product Images -->
                <div class="w-full md:w-1/2 px-4 mb-8">
                    <img :src="produit.image_url" alt="Product"
                         class="w-full h-auto rounded-lg shadow-md mb-4" id="mainImage">
                </div>

                <!-- Product Details -->
                <div class="w-full md:w-1/2 px-4">
                    <h2 class="text-3xl font-bold mb-2">{{ produit.nom }}</h2>
                    <p class="text-gray-600 mb-4">Quantité : {{ produit.quantité }}</p>
                    <p class="text-gray-700 mb-6">{{ produit.description }}</p>

                    <div class="flex space-x-4 mb-6">
                        <button @click="openPopup"
                                class="bg-indigo-600 flex gap-2 items-center text-white px-6 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-white" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                <path d="M 31.037109 5.5 A 1.50015 1.50015 0 0 0 30.097656 5.8007812 L 18.451172 14.535156 L 18.326172 14.484375 C 18.264172 14.458375 18.201672 14.444734 18.138672 14.427734 C 17.647672 14.164734 17.093094 14 16.496094 14 C 14.565094 14 12.996094 15.568 12.996094 17.5 C 12.996094 18.175 13.197297 18.798984 13.529297 19.333984 L 13.496094 19.359375 L 13.777344 19.677734 C 13.874344 19.798734 13.971984 19.917391 14.083984 20.025391 L 21.027344 27.900391 L 21.285156 35.5 L 18.324219 42.5 L 18.396484 42.400391 L 18.193359 42.755859 C 17.782359 43.474859 18.031953 44.391734 18.751953 44.802734 C 18.986953 44.936734 19.243094 45 19.496094 45 C 20.017094 45 20.522781 44.729141 20.800781 44.244141 L 24.800781 37.244141 C 24.898781 37.072141 24.961328 36.882547 24.986328 36.685547 L 25.734375 30.697266 L 30.277344 35 L 29.996094 42.294922 L 30.023438 42.195312 L 30.007812 42.3125 C 29.904812 43.1345 30.488547 43.885281 31.310547 43.988281 C 31.374547 43.996281 31.436047 44 31.498047 44 C 32.243047 44 32.890375 43.4455 32.984375 42.6875 L 33.984375 34.6875 C 34.030375 34.3205 33.939516 33.950484 33.728516 33.646484 L 30.037109 28.318359 L 40.894531 20.201172 A 1.50015 1.50015 0 0 0 41.197266 18.099609 L 32.197266 6.0996094 A 1.50015 1.50015 0 0 0 31.037109 5.5 z M 9.9960938 7 A 4 4 0 0 0 9.9960938 15 A 4 4 0 0 0 9.9960938 7 z M 30.695312 9.0996094 L 37.896484 18.699219 L 28.328125 25.851562 L 27.371094 24.470703 C 27.335094 24.419703 27.291953 24.376078 27.251953 24.330078 C 27.273438 24.3125 L 26.300781 23.21875 L 20.603516 20.775391 C 20.095516 20.557391 19.860125 19.970844 20.078125 19.464844 C 20.295125 18.954844 20.886625 18.719453 21.390625 18.939453 L 25.964844 20.898438 C 26.135844 20.962438 26.314094 21 26.496094 21 L 32.496094 21 C 33.324094 21 33.996094 20.328 33.996094 19.5 C 33.996094 18.672 33.324094 18 32.496094 18 L 26.796875 18 L 21.667969 15.871094 L 30.695312 9.0996094 z"></path>
                            </svg>
                            Effectuer une sortie
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-2">Catégorie:</h3>
                        <ul class="list-disc list-inside text-gray-700">
                            <li>En cours...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Vous pouvez ajouter des styles personnalisés pour votre composant ici */
</style>
