<script>
import axios from "../../../axios.config.js";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";

export default {
    name: "PopupExit",
    props: {
        id_prod: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            allZones: [],
            allPersons: [],
            selectedZone: '',
            selectedPerson: '',
            quantity: 1,
            id_produit: null,
            observation: '',
        };
    },
    mounted() {

            if (this.id_prod == null || this.id_prod == undefined ) {
                console.log(this.id_prod)
                this.id_produit = window.location.pathname.split("/").pop();
            } else {
                this.id_produit = this.id_prod;
                
            }


        // Récupérer les zones
        axios
            .get("/api/zones")
            .then((response) => {
                this.allZones = response.data.filter(zone => zone.type === 'zone');
                this.allPersons = response.data.filter(person => person.type === 'personne');
            })
            .catch((error) => {
                console.error("Erreur lors de la récupération des données:", error);
            });
    },
    methods: {
        closePopup() {
            document.getElementById('modelConfirm').classList.add('hidden');
        },
        validateForm() {
            if (!this.selectedZone || !this.selectedPerson) {
                this.showErrorToast("Veuillez sélectionner une zone et une personne.");
                return;
            }

            axios
                .post(`/api/produits/${this.id_produit}/retirer-stock`, {
            "zone_id": this.selectedZone,
            "personne_id": this.selectedPerson,
            "quantité": this.quantity,
            "observation": this.observation
        })
            .then(() => {
                this.showSuccessToast("Stock retiré avec succès !");
                this.closePopup();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            })
            .catch((error) => {
                console.error("Erreur lors de la mise à jour des données:", error);
                this.showErrorToast("Erreur lors du retrait du stock !");
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
            backgroundColor: "#F44336",
            stopOnFocus: true
        }).showToast();
    }
}
};
</script>

<template>
    <div id="modelConfirm" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4">
        <div class="relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-md">
            <!-- Bouton de fermeture -->
            <div class="flex justify-end p-2">
                <button type="button" @click="closePopup"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414 1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 pt-0">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Sortie de produit</h3>

                <form @submit.prevent="validateForm">

                    <div class="mb-4">
                        <label for="zone" class="block text-gray-600 font-medium mb-2">Sélectionnez une zone</label>
                        <select required id="zone" v-model="selectedZone"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Sélectionnez une zone</option>
                            <option v-for="zone in allZones" :key="zone.id" :value="zone.id">{{ zone.nom }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="person" class="block text-gray-600 font-medium mb-2">Sélectionnez la personne
                            responsable</label>
                        <select required id="person" v-model="selectedPerson"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Sélectionnez une personne</option>
                            <option v-for="person in allPersons" :key="person.id" :value="person.id">{{ person.nom }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="block text-gray-600 font-medium mb-2">Quantité à enlever</label>
                        <input type="number" id="quantity" v-model="quantity" min="1"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div class="mb-4">
                        <label for="observation" class="block text-gray-600 font-medium mb-2">Observation(s)
                            (facultatif)</label>
                        <input type="text" id="observation" v-model="observation"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Valider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Vous pouvez personnaliser ici les styles de la popup si nécessaire */
</style>
