<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h2 class="text-3xl font-bold text-gray-700 mb-6"> 📊 Statistiques Admin</h2>

        <div
            id="newProductsChart"
            class="shadow-lg bg-white rounded-lg p-4"
            style="height: 400px;"
        ></div>
    </div>
</template>

<script>
import * as echarts from "echarts";
import axios from "axios";

export default {
    name: "AdminStats",
    mounted() {
        this.loadStats();
    },
    methods: {
        async loadStats() {
            try {
                // Récupérer toutes les sorties
                const { data } = await axios.get("/api/sorties");

                // Initialiser un objet pour compter le nombre de sorties par personne
                const sortiesParPersonne = {};

                // Traiter les données des sorties
                data.forEach((sortie) => {
                    const personneId = sortie.personne.id; 
                    const personneNom = sortie.personne.nom; 
                    const personnePhoto = sortie.personne.image_url ? `${import.meta.env.VITE_APP_URL}/storage/${sortie.personne.image_url}` : "";

                    // Si la personne n'existe pas encore dans l'objet, on l'ajoute
                    if (!sortiesParPersonne[personneId]) {
                        sortiesParPersonne[personneId] = {
                            name: personneNom,
                            photo: personnePhoto,
                            sorties: 0,
                        };
                    }

                    // Incrémenter le nombre de sorties
                    sortiesParPersonne[personneId].sorties++;
                });

                // Convertir l'objet en tableau pour le graphique
                const names = [];
                const photos = [];
                const sorties = [];
                for (let id in sortiesParPersonne) {
                    names.push(sortiesParPersonne[id].name);
                    photos.push(sortiesParPersonne[id].photo);
                    sorties.push(sortiesParPersonne[id].sorties);
                }

                // Initialiser le graphique
                const chartDom = document.getElementById("newProductsChart");
                const chart = echarts.init(chartDom);

                // Options du graphique
                const options = {
                    title: {
                        text: "Nombre de Sorties par Personne",
                        left: "center",
                        textStyle: {
                            fontSize: 18,
                            fontWeight: "bold",
                            color: "#333",
                        },
                    },
                    tooltip: {
                        trigger: "item",
                        formatter: (params) => {
                            const person = sortiesParPersonne[params.dataIndex];
                            return `${person.name} : ${person.sorties} sorties`;
                        },
                    },
                    xAxis: {
                        type: "category",
                        data: names,
                        axisLine: {
                            lineStyle: {
                                color: "#ccc",
                            },
                        },
                        axisLabel: {
                            rotate: 45,
                            fontSize: 12,
                            color: "#666",
                            fontWeight: "bold",
                        },
                    },
                    yAxis: {
                        type: "value",
                        axisLine: {
                            lineStyle: {
                                color: "#ccc",
                            },
                        },
                        axisLabel: {
                            fontSize: 12,
                            color: "#666",
                        },
                        splitLine: {
                            lineStyle: {
                                type: "dashed",
                                color: "#ddd",
                            },
                        },
                    },
                    series: [
                        {
                            name: "Sorties",
                            type: "bar",
                            data: sorties,
                            barWidth: "20%",
                            
                            itemStyle: {
                                // Appliquer l'image comme fond de la barre, étirée et centrée
                                color: (params) => {
                                    return {
                                        type: "image",
                                        image: photos[params.dataIndex],
                                   
                                        //on centre l'image
                                        x: -50,
                                        y: -50,
                                        //on étire l'image
                                        width: 100,
                                        
                                    
                                    };
                                },
                            },
                            label: {
                                show: true,
                                position: "top",
                                fontSize: 0,
                                color: "#333",
                            },
                        },
                    ],
                };

                // Charger les options dans le graphique
                chart.setOption(options);

                // Réagir à la redimension
                window.addEventListener("resize", () => {
                    chart.resize();
                });
            } catch (error) {
                console.error("Erreur lors de la récupération des statistiques :", error);
            }
        },
    },
};
</script>

<style scoped>
/* Améliorer la présentation du graphique */
#newProductsChart {
    margin: 0 auto;
    max-width: 800px;
}
</style>
