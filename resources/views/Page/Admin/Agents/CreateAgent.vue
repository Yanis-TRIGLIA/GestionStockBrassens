<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Créer un agent</h2>
        <FormulaireAgent @submit="handleSubmit" />
    </div>
</template>

<script>
import axios from "../../../../../axios.config.js";
import FormulaireAgent from "./FormulaireAgent.vue";

export default {
    name: "CreateAgent",
    components: {FormulaireAgent },
    methods: {
        handleSubmit(formData) {
            axios.post("/api/zones", formData)
                .then(() => {
                    this.$router.push("/admin/zones");
                })
                .catch(error => {
                    console.error("Erreur lors de la création de la zone :", error);
                });
        },
    },
};
</script>
