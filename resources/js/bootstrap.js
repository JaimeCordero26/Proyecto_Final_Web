// resources/js/bootstrap.js
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Importa Alpine.js si lo necesitas
import Alpine from 'alpinejs';
window.Alpine = Alpine;
