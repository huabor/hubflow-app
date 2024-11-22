import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.interceptors.request.use((config) => {
    if (window.location.pathname.startsWith('/hubspot/crm-card')) {
        config.params = {
            ...config.params,
            crm_card: true,
        };
    }

    return config;
});
