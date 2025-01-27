import axios from 'axios';

const instance = axios.create({
    baseURL: import.meta.env.VITE_APP_URL,
});

instance.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    const tokenExpiration = localStorage.getItem('auth_token_expiration');
    console.log(token);

    if (token && Date.now() < tokenExpiration) {
        config.headers.Authorization = `Bearer ${token}`;
    } else {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_token_expiration');
    }

    return config;
});

export default instance;
