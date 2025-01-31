import { createApp } from 'vue';
import { createWebHistory, createRouter } from 'vue-router';

// Importation des pages
import Produits from '../views/Page/ProductPage.vue';
import Produit from '../views/Page/Product.vue';
import Header from '../views/Components/Header.vue';
import Login from '../views/Page/Login.vue';
import Home from '../views/Page/Home.vue';
import TableauSortie from '../views/Page/TableauSortie.vue';
import TableauProduits from '../views/Page/TableauProduit.vue';

// Importation des pages Admin
import AdminLayout from '../views/Page/Admin/AdminLayout.vue';

import AdminDashboard from '../views/Page/Admin/AdminDashboard.vue';

import AdminProduits from '../views/Page/Admin/Produit/AdminProduit.vue';
import CreateProduit from '../views/Page/Admin/Produit/CreateProduit.vue';
import EditProduit from '../views/Page/Admin/Produit/EditProduit.vue';

import AdminZones from '../views/Page/Admin/Zone/AdminZone.vue';
import EditZones from '../views/Page/Admin/Zone/EditZone.vue';
import CreateZones from '../views/Page/Admin/Zone/CreateZone.vue';

import AdminAgent from '../views/Page/Admin/Agents/AdminAgent.vue';
import CreateAgent from '../views/Page/Admin/Agents/CreateAgent.vue';

import AdminUsers from '../views/Page/Admin/User/AdminUser.vue';
import EditUser from '../views/Page/Admin/User/EditUser.vue';
import CreateUser from '../views/Page/Admin/User/CreateUser.vue';


import AdminCategories from '../views/Page/Admin/Categories/AdminCategories.vue';
import EditCategorie from '../views/Page/Admin/Categories/EditCategorie.vue';
import CreateCategorie from '../views/Page/Admin/Categories/CreateCategorie.vue';


import Cart from '../views/Page/Cart.vue';


import UserPage from '../views/Page/UserPage.vue';

// Componenet
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from "primevue/column";
import PrimeVue from 'primevue/config';

import PopupCriticalProduct from '../views/Components/PopupCriticalProduct.vue';

import NotFound from '../views/Page/NotFound.vue';



let is_admin = false;

// Définir les routes
const routes = [
    {
        path: '/',
        component: Home,
        meta: { requiresAuth: false },
    },
    {
        path: '/connexion',
        component: Login,
        meta: { requiresAuth: false },
    },
    {
        path: '/page_compte',
        component: UserPage,
        meta: { requiresAuth: true },
    },
    {
        path: '/panier',
        component: Cart,
        meta: { requiresAuth: true },
    },
    {
        path: '/liste_produit',
        component: Produits,
        meta: { requiresAuth: false },
    },
    {
        path: '/prod/:id',
        component: Produit,
        meta: { requiresAuth: false },
    },
    {
        path: '/sortie_produit',
        component: TableauSortie,
        meta: { requiresAuth: false },
    },
    {
        path: '/tableau_produit',
        component: TableauProduits,
        meta: { requiresAuth: false },
    },
    {
        path: "/admin",
        component: AdminLayout,
        meta: { is_admin: true },
        children: [
            { path: "", component: AdminDashboard },
            { path: "produits", component: AdminProduits },
            {
                path: 'produits/create',
                component: CreateProduit,
            },
            {
                path: 'produits/edit/:id',
                component: EditProduit,
            },
            { path: "zones", component: AdminZones },
            {
                path: 'zones/create',
                component: CreateZones,
            },
            {
                path: 'zones/edit/:id',
                component: EditZones,
            },
            { path: "agent", component: AdminAgent },
            {
                path: 'agent/create',
                component: CreateAgent,
            },
            { path: "utilisateurs", component: AdminUsers },
            {
                path: 'utilisateur/create',
                component: CreateUser,
            },
            {
                path: 'utilisateur/edit/:id',
                component: EditUser,
            },
            { path: "categories", component: AdminCategories },
            {
                path: 'categorie/create',
                component: CreateCategorie,
            },
            {
                path: 'categorie/edit/:id',
                component: EditCategorie,
            },




        ],
    },
    {
        path: "/:pathMatch(.*)*",
        component: NotFound,
    },
];
import axios from 'axios';
async function fetchUserData(token) {
    try {
        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` },
        });

        const user = response.data;
        console.log(user);
        localStorage.setItem('user', JSON.stringify(user)); 
        return user; 
    } catch (error) {
        console.error('Erreur lors de la récupération des données utilisateur:', error);
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        return null;
    }
}



// Configuration du routeur
const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.onError((error) => {
    console.error("Erreur de navigation :", error);
});

router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem('auth_token');
    const tokenExpiration = localStorage.getItem('auth_token_expiration');
    const isLoggedIn = token && Date.now() < tokenExpiration;

    let user = JSON.parse(localStorage.getItem('user')); 
    if (!user && token) {
        user = await fetchUserData(token); 
    }

    console.log(user);

    const isAdmin = user && user.is_admin === 1;

    if (to.meta.is_admin && !isAdmin) {
        return next('/404');
    }

    if (to.meta.requiresAuth && !isLoggedIn) {
        return next('/connexion');
    }

    next();
});




const App = {
    components: { Header, PopupCriticalProduct  },
    template: `
   
        <div> 
            <Header />
            <PopupCriticalProduct />
            <router-view />
        </div>
        
    `,
};


const app = createApp(App);

app.use(PrimeVue);
app.component('Button', Button);
app.component('DataTable', DataTable);
app.component('Column', Column);

// Utiliser le routeur
app.use(router);



// Monter l'application
app.mount('#app');
