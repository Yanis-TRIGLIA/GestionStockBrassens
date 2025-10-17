<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h2 class="text-3xl font-bold text-gray-700 mb-6">📊 Statistiques Admin</h2>

        <div id="newProductsChart" class="shadow-lg bg-white rounded-lg p-4" style="height: 400px;"></div>

        <div class="mt-8">
            <h3 class="text-2xl font-bold text-gray-700 mb-4">📌 Détails des Sorties</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="person in personsData" :key="person.id" @click="openPopup(person.id)"
                    class="flex items-center bg-white p-4 shadow-md rounded-lg cursor-pointer hover:shadow-xl transition">

                    <img :src="person.photo" alt="Utilisateur"
                        class="w-16 h-16 object-cover rounded-full border border-gray-300" />
                    <div class="ml-4">
                        <p class="text-lg font-bold text-gray-800">{{ person.name }}</p>
                        <p class="text-gray-600">🔄 {{ person.sorties }} sorties</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showPopup" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
            @click="handleOverlayClick">
            <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-3/4 md:w-1/2 lg:w-1/3 p-6 relative max-h-[80vh] overflow-y-auto"
                @click.stop>
                <button @click="closePopup"
                    class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl">&times;</button>

                <h3 class="text-xl font-bold text-gray-800 mb-4">📜 Sorties de {{ selectedPerson?.name }}</h3>

                <div v-if="userSorties.length">
                    <ul class="space-y-3">
                        <li v-for="sortie in userSorties" :key="sortie.id" class="p-3 border rounded-lg bg-gray-100">
                            <p><strong>🛒 Produit:</strong> {{ sortie.produit.nom }}</p>
                            <p><strong>📦 Quantité:</strong> {{ sortie.quantité }}</p>
                            <p><strong>📍 Zone:</strong> {{ sortie.zone.nom }}</p>
                            <p><strong>📅 Date:</strong> {{ new Date(sortie.created_at).toLocaleString() }}</p>
                        </li>
                    </ul>
                </div>
                <p v-else class="text-gray-500">Aucune sortie enregistrée.</p>
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
            personsData: [],
            sortiesData: [],
            showPopup: false,
            selectedPerson: null,
            userSorties: []
        };
    },
    mounted() {
        this.loadStats();
    },
    methods: {

        async loadStats() {
            try {
                const { data } = await axios.get("/api/sorties");
                console.log(data);

                this.sortiesData = data;

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

                this.personsData = Object.values(sortiesParPersonne);

                this.initChart();
            } catch (error) {
                console.error("Erreur lors de la récupération des statistiques :", error);
            }
        },

        handleOverlayClick(event) {
            if (event.target === event.currentTarget) {
                this.closePopup();
            }
        },

        initChart() {
            const names = this.personsData.map(person => person.name);
            const sorties = this.personsData.map(person => person.sorties);

            const chartDom = document.getElementById("newProductsChart");
            const chart = echarts.init(chartDom);

            const options = {
                title: {
                    text: "Nombre de Sorties par Personne",
                    left: "center",
                    textStyle: { fontSize: 18, fontWeight: "bold", color: "#333" },
                },
                tooltip: {
                    trigger: "axis",
                    axisPointer: { type: "shadow" },
                    formatter: (params) => `${params[0].name} : ${params[0].value} sorties`,
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
                        itemStyle: { color: "#3498db" },
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
            window.addEventListener("resize", () => chart.resize());
        },

        openPopup(personId) {
            this.selectedPerson = this.personsData.find(person => person.id === personId);
            this.userSorties = this.sortiesData.filter(sortie => sortie.personne.id === personId);
            this.showPopup = true;
        },

        closePopup() {
            this.showPopup = false;
            this.selectedPerson = null;
            this.userSorties = [];
        }
    }
};
</script>


<style scoped>
#newProductsChart {
    margin: 0 auto;
    max-width: 900px;
}

.grid div {
    transition: transform 0.2s;
}

.grid div:hover {
    transform: scale(1.05);
}

.bg-gray-900 {
    backdrop-filter: blur(5px);
}

.fixed {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
