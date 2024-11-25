<template>
    <div>
        <FormulaireProduit
            :produit="produit"
            isEditMode
            @submit="updateProduit"
        />
    </div>
</template>

<script>
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";
import FormulaireProduit from "./FormulaireProduit.vue";

export default {
    name: "EditProduit",
    components: { FormulaireProduit },
    data() {
        return {
            produit: null,
        };
    },
    methods: {
        fetchProduit() {
            const id = this.$route.params.id;
            axios
                .get(`/api/produits/${id}`)
                .then((response) => {
                    console.log("Produit récupéré :", response.data);
                    this.produit = response.data; // Vérifiez ici si "categories" existe
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors du chargement du produit.");
                });
        },

        updateProduit(formData) {
            const id = this.$route.params.id;
            console.log("Updating product with ID:", id);

            // Vérifier si file_product est vide et, dans ce cas, l'exclure
            if (typeof formData.get("file_product") == "string") {
                console.log("file_product is empty or not a string, it will be excluded from the request");
                formData.delete("file_product");
            }

            // Envoyer le FormData directement
            axios
                .post(`/api/produits/${id}?_method=POST`, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data", // Spécifier multipart/form-data pour le traitement des fichiers
                    },
                })
                .then((response) => {
                    console.log("Produit mis à jour :", response.data);
                    this.produit = response.data;
                    this.showSuccessToast("Produit modifié avec succès !");
                    this.$router.push("/admin/produits");
                })
                .catch((error) => {
                    console.error("Erreur lors de la modification du produit :", error.response || error);
                    this.showErrorToast("Erreur lors de la modification du produit.");
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
    mounted() {
        this.fetchProduit();
    },
};
</script>

<style scoped>
/* Ajoutez vos styles ici */
</style>
