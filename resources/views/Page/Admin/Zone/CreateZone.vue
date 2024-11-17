<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Créer une zone</h2>
        <FormulaireZone @submit="handleSubmit" />
    </div>
</template>

<script>
import FormulaireZone from "./FormulaireZone.vue";
import axios from "../../../../../axios.config.js";

export default {
    name: "CreateZone",
    components: { FormulaireZone },
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
