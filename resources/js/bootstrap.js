import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // Required to send/receive HttpOnly cookies

/**
 * JWT Session Management
 */
const AUTH_TOKEN_KEY = 'redas_access_token';

// Request Interceptor: Attach Access Token
window.axios.interceptors.request.use(config => {
    const token = localStorage.getItem(AUTH_TOKEN_KEY);
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => Promise.reject(error));

// Response Interceptor: Handle 401s and Silent Refresh
let isRefreshing = false;
let failedQueue = [];

const processQueue = (error, token = null) => {
    failedQueue.forEach(prom => {
        if (error) prom.reject(error);
        else prom.resolve(token);
    });
    failedQueue = [];
};

window.axios.interceptors.response.use(
    response => response,
    async error => {
        const originalRequest = error.config;

        // Only attempt refresh if it's a 401 and not already a refresh attempt
        if (error.response?.status === 401 && !originalRequest._retry) {
            if (originalRequest.url === '/api/auth/refresh') {
                // Refresh token itself is expired or invalid -> Force Logout
                localStorage.removeItem(AUTH_TOKEN_KEY);
                window.location.href = '/login';
                return Promise.reject(error);
            }

            if (isRefreshing) {
                // Queue requests while refreshing
                return new Promise((resolve, reject) => {
                    failedQueue.push({ resolve, reject });
                })
                .then(token => {
                    originalRequest.headers.Authorization = `Bearer ${token}`;
                    return window.axios(originalRequest);
                })
                .catch(err => Promise.reject(err));
            }

            originalRequest._retry = true;
            isRefreshing = true;

            try {
                // Call the refresh endpoint (browser sends HttpOnly cookie automatically)
                const { data } = await window.axios.post('/api/auth/refresh');
                const newToken = data.access_token;

                localStorage.setItem(AUTH_TOKEN_KEY, newToken);
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
                
                processQueue(null, newToken);
                
                originalRequest.headers.Authorization = `Bearer ${newToken}`;
                return window.axios(originalRequest);
            } catch (refreshError) {
                processQueue(refreshError, null);
                localStorage.removeItem(AUTH_TOKEN_KEY);
                window.location.href = '/login';
                return Promise.reject(refreshError);
            } finally {
                isRefreshing = false;
            }
        }

        return Promise.reject(error);
    }
);
