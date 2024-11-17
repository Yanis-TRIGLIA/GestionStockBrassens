<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Créer un nouveau produit</h2>
        <FormulaireProduit @submit="createProduit" />
    </div>
</template>

<script>
import FormulaireProduit from "./FormulaireProduit.vue";
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";

export default {
    name: "CreateProduit",
    components: { FormulaireProduit },
    methods: {
        createProduit(formData) {
            axios
                .post("/api/produits", formData)
                .then(() => {
                    this.showSuccessToast("Produit créé avec succès !");
                    this.$router.push("/admin/produits");
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la création du produit.");
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
    },
};
</script>

<style scoped>
/* Ajoutez vos styles ici */
</style>
