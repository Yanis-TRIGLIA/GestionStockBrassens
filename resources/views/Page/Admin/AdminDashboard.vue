<template>
    <div>
        <h2>Statistiques Admin</h2>
        <div class="chart-container">
            <canvas id="productDistributionChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="newProductsChart"></canvas>
        </div>
    </div>
</template>

<script>
import {
    Chart,
    PieController,
    LineController,
    ArcElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend,
} from "chart.js";
import axios from "axios";

Chart.register(
    PieController,
    LineController,
    ArcElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend
);

export default {
    name: "AdminStats",
    mounted() {
        this.loadStats();
    },
    methods: {
        async loadStats() {
            try {
                const { data } = await axios.get("/api/admin/stats");

                // Graphique circulaire pour la distribution des produits
                const distributionCtx = document
                    .getElementById("productDistributionChart")
                    .getContext("2d");
                new Chart(distributionCtx, {
                    type: "pie",
                    data: {
                        labels: Object.keys(data.productDistribution),
                        datasets: [
                            {
                                data: Object.values(data.productDistribution),
                                backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56"],
                            },
                        ],
                    },
                });

                // Graphique linéaire pour les nouveaux produits
                const newProductsCtx = document
                    .getElementById("newProductsChart")
                    .getContext("2d");
                new Chart(newProductsCtx, {
                    type: "line",
                    data: {
                        labels: data.newProducts.map((item) => item.date),
                        datasets: [
                            {
                                label: "Nouveaux produits",
                                data: data.newProducts.map((item) => item.count),
                                borderColor: "#42A5F5",
                                backgroundColor: "rgba(66, 165, 245, 0.2)",
                                fill: true,
                            },
                        ],
                    },
                });
            } catch (error) {
                console.error("Erreur lors de la récupération des statistiques :", error);
            }
        },
    },
};
</script>

<style scoped>
.chart-container {
    margin: 20px 0;
    /* Définit la taille du conteneur, par exemple 80% de la largeur du parent */
    width: 40%;
    max-width: 20%; /* Limiter la taille maximale */

}

canvas {
    width: 100% !important; /* Assurer que le canvas prend toute la largeur du conteneur */
    height: 100% !important; /* Assurer que le canvas prend toute la hauteur du conteneur */
}
</style>
