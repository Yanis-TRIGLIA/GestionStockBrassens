<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h2 class="text-3xl font-bold text-gray-700 mb-6"> 📊 Statistiques Admin</h2>

        <!-- Graphique -->
        <div id="newProductsChart" class="shadow-lg bg-white rounded-lg p-4" style="height: 400px;"></div>

        <!-- Section des utilisateurs sous le graphique -->
        <div class="mt-8">
            <h3 class="text-2xl font-bold text-gray-700 mb-4">📌 Détails des Sorties</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="person in personsData" :key="person.id"
                    class="flex items-center bg-white p-4 shadow-md rounded-lg">
                    <img :src="person.photo" alt="Utilisateur"
                        class="w-16 h-16 object-cover rounded-full border border-gray-300" />
                    <div class="ml-4">
                        <p class="text-lg font-bold text-gray-800">{{ person.name }}</p>
                        <p class="text-gray-600">🔄 {{ person.sorties }} sorties</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import * as echarts from "echarts";
import axios from "axios";

export default {
    name: "AdminStats",
    data() {
        return {
            personsData: [], // Liste des utilisateurs et leurs sorties
        };
    },
    mounted() {
        this.loadStats();
    },
    methods: {
        async loadStats() {
            try {
                // Récupération des sorties
                const { data } = await axios.get("/api/sorties");

                // Initialiser un objet pour compter les sorties par utilisateur
                const sortiesParPersonne = {};

                data.forEach((sortie) => {
                    const personneId = sortie.personne.id;
                    const personneNom = sortie.personne.nom;
                    const personnePhoto = sortie.personne.image_url
                        ? `${import.meta.env.VITE_APP_URL}/${sortie.personne.image_url}`
                        : "https://via.placeholder.com/150";

                    if (!sortiesParPersonne[personneId]) {
                        sortiesParPersonne[personneId] = {
                            id: personneId,
                            name: personneNom,
                            photo: personnePhoto,
                            sorties: 0,
                        };
                    }
                    sortiesParPersonne[personneId].sorties++;
                });

                // Convertir en tableau pour le graphique
                const names = [];
                const sorties = [];
                this.personsData = Object.values(sortiesParPersonne);

                this.personsData.forEach((person) => {
                    names.push(person.name);
                    sorties.push(person.sorties);
                });

                // Initialiser le graphique
                const chartDom = document.getElementById("newProductsChart");
                const chart = echarts.init(chartDom);

                // Configuration du graphique
                const options = {
                    title: {
                        text: "Nombre de Sorties par Personne",
                        left: "center",
                        textStyle: { fontSize: 18, fontWeight: "bold", color: "#333" },
                    },
                    tooltip: {
                        trigger: "axis",
                        axisPointer: { type: "shadow" },
                        formatter: (params) => {
                            return `${params[0].name} : ${params[0].value} sorties`;
                        },
                    },
                    xAxis: {
                        type: "category",
                        data: names,
                        axisLabel: { rotate: 45, fontSize: 12, color: "#666", fontWeight: "bold" },
                    },
                    yAxis: {
                        type: "value",
                        splitLine: { lineStyle: { type: "dashed", color: "#ddd" } },
                    },
                    series: [
                        {
                            name: "Sorties",
                            type: "bar",
                            data: sorties,
                            barWidth: "20%",
                            itemStyle: { color: "#3498db" }, // Couleur bleue
                            label: {
                                show: true,
                                position: "top",
                                fontSize: 12,
                                color: "#333",
                            },
                        },
                    ],
                };

                chart.setOption(options);

                // Adapter le graphique au redimensionnement
                window.addEventListener("resize", () => chart.resize());
            } catch (error) {
                console.error("Erreur lors de la récupération des statistiques :", error);
            }
        },
    },
};
</script>

<style scoped>
/* Style du graphique */
#newProductsChart {
    margin: 0 auto;
    max-width: 900px;
}

/* Style des cartes utilisateurs */
@media (max-width: 768px) {
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>
