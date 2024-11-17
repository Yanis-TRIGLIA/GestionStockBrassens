<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h2 class="text-3xl font-bold text-gray-700 mb-6">Statistiques Admin</h2>

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
                const {data} = await axios.get("/api/admin/stats");

                // Initialiser le graphique
                const chartDom = document.getElementById("newProductsChart");
                const chart = echarts.init(chartDom);

                // Options du graphique
                const options = {
                    title: {
                        text: "Nouveaux Produits",
                        left: "center",
                        textStyle: {
                            fontSize: 18,
                            fontWeight: "bold",
                            color: "#333",
                        },
                    },
                    tooltip: {
                        trigger: "axis",
                        axisPointer: {
                            type: "line",
                        },
                    },
                    xAxis: {
                        type: "category",
                        boundaryGap: false,
                        data: data.newProducts.map((item) => item.date),
                        axisLine: {
                            lineStyle: {
                                color: "#ccc",
                            },
                        },
                        axisLabel: {
                            rotate: 45,
                            fontSize: 12,
                            color: "#666",
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
                            name: "Nouveaux produits",
                            type: "line",
                            data: data.newProducts.map((item) => item.count),
                            smooth: true,
                            lineStyle: {
                                color: "#42A5F5",
                                width: 3,
                            },
                            areaStyle: {
                                color: "rgba(66, 165, 245, 0.3)",
                            },
                            itemStyle: {
                                color: "#42A5F5",
                                borderColor: "#fff",
                                borderWidth: 2,
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
/* Ajout de styles pour améliorer la présentation */
#newProductsChart {
    margin: 0 auto;
    max-width: 600px;
}
</style>
