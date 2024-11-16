<script>
export default {
    name: "PopupExit",
    data() {
        return {
            selectedZone: '',
            quantity: 1
        };
    },
    methods: {
        closePopup() {
            // Cette méthode ferme la popup
            document.getElementById('modelConfirm').classList.add('hidden');
        },
        validateForm() {
            // Ajouter ici la logique pour valider le formulaire
            console.log("Zone:", this.selectedZone);
            console.log("Quantité:", this.quantity);
        }
    }
};
</script>

<template>
    <div id="modelConfirm" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4">
        <div class="relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-md">

            <!-- Bouton de fermeture -->
            <div class="flex justify-end p-2">
                <button type="button"
                        @click="closePopup"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <!-- Contenu de la popup -->
            <div class="p-6 pt-0">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Sortie de produit</h3>

                <!-- Formulaire -->
                <form @submit.prevent="validateForm">

                    <!-- Select pour choisir la zone -->
                    <div class="mb-4">
                        <label for="zone" class="block text-gray-600 font-medium mb-2">Sélectionner une zone</label>
                        <select id="zone" v-model="selectedZone" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Sélectionnez une zone</option>
                            <option value="zone1">Zone 1</option>
                            <option value="zone2">Zone 2</option>
                            <option value="zone3">Zone 3</option>
                            <option value="zone4">Zone 4</option>
                        </select>
                    </div>

                    <!-- Input pour la quantité -->
                    <div class="mb-4">
                        <label for="quantity" class="block text-gray-600 font-medium mb-2">Quantité à enlever</label>
                        <input type="number" id="quantity" v-model="quantity" min="1" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <!-- Bouton de validation -->
                    <div class="flex justify-center">
                        <button type="submit" class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
