<script>
import axios from "axios";
import jsPDF from "jspdf";
import "jspdf-autotable";

export default {
    name: "Cart",
    data() {
        return {
            panier: [],
            total: 0,
            baseUrl: import.meta.env.VITE_APP_URL,
        };
    },
    methods: {
        loadPanier() {
            const token = localStorage.getItem("auth_token");
            if (!token) return;

            axios.get("/api/panier", {
                headers: { Authorization: `Bearer ${token}` }
            })
                .then(response => {
                    this.panier = response.data.produits || [];
                    this.calculateTotal();
                })
                .catch(error => {
                    console.error("Erreur chargement panier:", error);
                });
        },
        updateQuantity(produit, increment) {
            const token = localStorage.getItem("auth_token");
            if (!token) return;

            const nouvelleQuantite = Math.max(1, produit.pivot.quantite + increment);

            axios.put(`/api/panier/update/${produit.id}`, { quantite: nouvelleQuantite }, {
                headers: { Authorization: `Bearer ${token}` }
            })
                .then(response => {
                    this.panier = response.data.panier.produits; // Mettre à jour le panier
                    this.calculateTotal();
                })
                .catch(error => {
                    console.error("Erreur mise à jour quantité:", error);
                });
        },

        removeProduct(produitId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce produit du panier ?")) {
                axios
                    .post(`/panier/supprimer/${produitId}`, {
                        _method: "DELETE"
                    })
                    .then(() => {
                        this.showSuccessToast("Produit supprimé du panier !");
                        this.panier = this.panier.filter(p => p.id !== produitId);
                        this.calculateTotal();
                    })
                    .catch((error) => {
                        console.error(error);  // More detailed error logging
                        this.showErrorToast("Erreur lors de la suppression du produit.");
                    });
            }
        },




        calculateTotal() {
            this.total = this.panier.reduce((sum, produit) => sum + produit.prix * produit.pivot.quantite, 0);
        },
        generatePDF() {
            const doc = new jsPDF();
            doc.text("Récapitulatif de votre panier", 20, 10);

            const rows = this.panier.map(produit => [
                produit.nom,
                produit.prix + "€",
                produit.pivot.quantite,
                (produit.prix * produit.pivot.quantite) + "€"
            ]);

            doc.autoTable({
                head: [["Produit", "Prix unitaire", "Quantité", "Total"]],
                body: rows
            });

            doc.text(`Total: ${this.total.toFixed(2)}€`, 20, doc.autoTable.previous.finalY + 10);
            doc.save("Panier.pdf");
        }
    },
    mounted() {
        this.loadPanier();
    }
};
</script>

<template>
    <div class="cart-container ">
        <h1>🛒 Votre panier</h1>

        <div v-if="panier.length" class="cart-items">
            <div v-for="produit in panier" :key="produit.id" class="cart-item">
                <img :src="`${baseUrl}/${produit.image_url}`" :alt="produit.nom" class="product-image">

                <div class="product-info">
                    <h3>{{ produit.nom }}</h3>
                    <p class="price">{{ produit.prix }}€</p>

                    <div class="quantity-controls">
                        <button @click="updateQuantity(produit, -1)">➖</button>
                        <span class="quantity">{{ produit.pivot.quantite }}</span>
                        <button @click="updateQuantity(produit, 1)">➕</button>
                    </div>

                    <button @click="removeProduct(produit.id)" class="remove">🗑️ Supprimer</button>
                </div>
            </div>
        </div>

        <p v-else class="empty-message">Votre panier est vide.</p>

        <div class="cart-summary">
            <h2>Total: <span class="total">{{ total.toFixed(2) }}€</span></h2>
            <button @click="generatePDF" class="generate-pdf">📄 Générer PDF</button>
        </div>
    </div>
</template>

<style scoped>
/* Container principal */
.cart-container {
    max-width: 900px;
    margin: auto;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Titre */
h1 {
    text-align: center;
    font-size: 24px;
    color: #333;
}

/* Liste des articles */
.cart-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 20px;
}

/* Un article du panier */
.cart-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    background: #f9f9f9;
    transition: transform 0.2s;
}

.cart-item:hover {
    transform: scale(1.02);
}

/* Image du produit */
.product-image {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
}

/* Infos du produit */
.product-info {
    flex-grow: 1;
}

/* Prix */
.price {
    font-size: 18px;
    font-weight: bold;
    color: #ff9900;
}

/* Contrôles de quantité */
.quantity-controls {
    display: flex;
    align-items: center;
    margin-top: 10px;
}

.quantity-controls button {
    padding: 8px 12px;
    font-size: 16px;
    border: none;
    background: #007bff;
    color: white;
    cursor: pointer;
    border-radius: 5px;
    transition: 0.2s;
}

.quantity-controls button:hover {
    background: #0056b3;
}

.quantity {
    margin: 0 10px;
    font-size: 16px;
    font-weight: bold;
}

/* Bouton de suppression */
.remove {
    display: block;
    margin-top: 10px;
    background: none;
    border: none;
    color: red;
    font-size: 14px;
    cursor: pointer;
}

.remove:hover {
    text-decoration: underline;
}

/* Message panier vide */
.empty-message {
    text-align: center;
    font-size: 18px;
    color: #777;
}

/* Résumé du panier */
.cart-summary {
    text-align: right;
    margin-top: 30px;
}

.total {
    font-size: 22px;
    font-weight: bold;
    color: #ff9900;
}

/* Bouton de génération PDF */
.generate-pdf {
    background: #28a745;
    color: white;
    padding: 12px 15px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    transition: 0.2s;
}

.generate-pdf:hover {
    background: #218838;
}

/* RESPONSIVE DESIGN */
@media (max-width: 768px) {
    .cart-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .product-image {
        width: 100%;
        height: auto;
        margin-bottom: 10px;
    }

    .product-info {
        width: 100%;
    }
}
</style>
