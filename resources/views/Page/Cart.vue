<script>
import axios from "axios";
import jsPDF from "jspdf";
import "jspdf-autotable";
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

export default {
    name: "Cart",
    data() {
        return {
            panier: [],
            total: 0,
            user: {},
            showEmptyCartModal: false,
            baseUrl: import.meta.env.VITE_APP_URL,
            showConfirmationModal: false,
            //on déclare un int 
            id_tmp: 0
        };
    },
    methods: {

        confirmDeletion(id_tmp) {
            this.showConfirmationModal = true;
            this.id_tmp = id_tmp;
        },
        cancelDeletion() {
            this.showConfirmationModal = false;
        },

        async fetchUserData(token) {
            try {
                const response = await axios.get('/api/user', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.user = response.data;
            } catch (error) {
                console.error('Erreur lors de la récupération des données utilisateur:', error);
                this.isLoggedIn = false;
                localStorage.removeItem('auth_token');
            }
        },

        emptyCart() {
            this.cancelEmptyCart();
            const token = localStorage.getItem("auth_token");

            axios.post(`/api/panier/vider`, {}, {
                headers: { "Authorization": `Bearer ${token}` }
            })
                .then(() => {
                    this.showSuccessToast("Le panier a été vidé !");
                    this.panier = [];
                    this.total = 0;
                })
                .catch(error => {
                    console.error("Erreur vidage du panier:", error);
                    this.showErrorToast("Erreur lors du vidage du panier.");
                });
        },

        calculateTotal() {
            this.total = this.panier.reduce((sum, produit) => sum + produit.prix * produit.pivot.quantite, 0);
        },

        confirmEmptyCart() {
            this.showEmptyCartModal = true;
        },
        cancelEmptyCart() {
            this.showEmptyCartModal = false;
        },


        showSuccessToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#4CAF50",
                stopOnFocus: true
            }).showToast();
            //on reload
            setTimeout(() => {
                location.reload();
            }, 1500);
        },
        showErrorToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#FF0000",
                stopOnFocus: true
            }).showToast();
        },
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
                    this.panier = response.data.panier.produits;
                    this.calculateTotal();
                })
                .catch(error => {
                    console.error("Erreur mise à jour quantité:", error);
                });
        },

        removeProduct() {
            this.cancelDeletion();
            const token = localStorage.getItem("auth_token");
            console.log(this.id_tmp);

            axios.post(`/api/panier/supprimer/${this.id_tmp}`, {}, {
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            })

                .then(() => {
                    this.showSuccessToast("Produit supprimé du panier !");
                    this.panier = this.panier.filter(p => p.id !== this.id_tmp);
                    this.calculateTotal();

                })
                .catch(error => {
                    console.error("Erreur suppression produit:", error);
                    this.showErrorToast("Erreur lors de la suppression du produit.");
                });

        },





        calculateTotal() {
            this.total = this.panier.reduce((sum, produit) => sum + produit.prix * produit.pivot.quantite, 0);
        },
        generatePDF() {
            const doc = new jsPDF();
            doc.text("Récapitulatif de votre panier", 20, 10);

            const rows = this.panier.map(produit => [
                produit.nom,
                produit.reference,
                produit.prix + "€",
                produit.pivot.quantite,
                (produit.prix * produit.pivot.quantite) + "€"
            ]);

            doc.autoTable({
                head: [["Produit", "Référence", "Prix unitaire", "Quantité", "Total"]],
                body: rows
            });

            doc.text(`Total: ${this.total.toFixed(2)}€`, 20, doc.autoTable.previous.finalY + 10);
            //on enregistre le panier avec la date du jour Panier_ladate_lenomdelutilisateur.pdf
            doc.save(`Panier_${new Date().toISOString().split('T')[0]}_${this.user.name}.pdf`);
        }
    },



    mounted() {
        this.fetchUserData(localStorage.getItem('auth_token'));
        this.loadPanier();


    }
};
</script>

<template>
    <div class="cart-container " style="margin-top: 2%;">
        <h1>🛒 Votre panier</h1>

        <div v-if="panier.length" class="cart-items">
            <div v-for="produit in panier" :key="produit.id" class="cart-item w-[80%] self-center md:w-full ">
                <img :src="`${baseUrl}/${produit.image_url}`" :alt="produit.nom" class="product-image">



                <div class="product-info">
                    <a :href="`/prod/${produit.id}`">
                        <h3 class="text-xl">{{ produit.nom }}</h3>
                    </a>
                    <p class="price text-end text-xl">{{ produit.prix }}€</p>
                    <p class="text-sm"><span class="font-bold ">réf :</span> {{ produit.reference }}</p>

                    <div class="quantity-controls">
                        <button @click="updateQuantity(produit, -1)">➖</button>
                        <span class="quantity">{{ produit.pivot.quantite }}</span>
                        <button @click="updateQuantity(produit, 1)">➕</button>
                    </div>

                    <button @click="confirmDeletion(produit.id)" class="remove">🗑️ Supprimer</button>
                </div>
            </div>


        </div>



        <p v-else class="empty-message">Votre panier est vide.</p>

        

        <div class="cart-summary">
            <h2>Total: <span class="total">{{ total.toFixed(2) }}€</span></h2>
            <button @click="generatePDF" class="generate-pdf">📄 Générer PDF</button>
            <button v-if="panier.length" @click="confirmEmptyCart" class="empty-cart">🗑️ Vider le panier</button>
        </div>
    </div>

    <div v-if="showConfirmationModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h3 class="text-xl font-bold mb-4">Confirmer la suppression</h3>
            <p>Êtes-vous sûr de vouloir supprimer ce produit du panier?</p>
            <div class="flex justify-between mt-4">
                <button @click="removeProduct" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Oui, Supprimer
                </button>
                <button @click="cancelDeletion" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                    Annuler
                </button>
            </div>
        </div>
    </div>

    <div v-if="showEmptyCartModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h3 class="text-xl font-bold mb-4">Vider le panier</h3>
            <p>Êtes-vous sûr de vouloir supprimer tous les produits du panier ?</p>
            <div class="flex justify-between mt-4">
                <button @click="emptyCart" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Oui, Vider
                </button>
                <button @click="cancelEmptyCart" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                    Annuler
                </button>
            </div>
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
    width: 150px;
    height: 150px;
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

.empty-cart {
    background: #dc3545;
    color: white;
    padding: 12px 15px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    transition: 0.2s;
    margin-left: 10px;
}

.empty-cart:hover {
    background: #c82333;
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
