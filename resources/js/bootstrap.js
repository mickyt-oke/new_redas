import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // Required to send/receive HttpOnly cookies

/**
 * Sanctum Session Management
 *
 * Access tokens are deliberately stored in a Secure/HttpOnly/Strict cookie
 * (`access_token`) so JavaScript cannot read them, mitigating XSS theft.
 * The refresh token lives in a similar cookie so the PWA session can survive
 * browser restarts.
 *
 * A small in-memory token path remains for explicit external/mobile API
 * consumers that set a token via window.REDAS_AUTH.setToken().
 */
const REFRESH_URL = '/api/refresh';
const REFRESH_EXPIRY_KEY = 'redas_refresh_expires_at';

let accessToken = null;
let isRefreshing = false;
let failedQueue = [];

function hasAccessCookie() {
    return document.cookie.split(';').some(c => c.trim().startsWith('access_token='));
}

function storeRefreshExpiry(data) {
    if (data && data.refresh_expires_at) {
        try {
            sessionStorage.setItem(REFRESH_EXPIRY_KEY, String(new Date(data.refresh_expires_at).getTime()));
        } catch (e) {}
    }
}

function clearRefreshExpiry() {
    try {
        sessionStorage.removeItem(REFRESH_EXPIRY_KEY);
    } catch (e) {}
}

const processQueue = (error, token = null) => {
    failedQueue.forEach(prom => {
        if (error) prom.reject(error);
        else prom.resolve(token);
    });
    failedQueue = [];
};

// Request interceptor: only attach an explicit bearer token when the HttpOnly
// cookie is not present. Same-origin requests rely on the cookie automatically.
window.axios.interceptors.request.use(config => {
    if (!hasAccessCookie() && accessToken) {
        config.headers.Authorization = `Bearer ${accessToken}`;
    }
    return config;
}, error => Promise.reject(error));

// Response interceptor: handle 401s and silent refresh via the refresh cookie.
window.axios.interceptors.response.use(
    response => response,
    async error => {
        const originalRequest = error.config;

        if (error.response?.status !== 401 || originalRequest._retry) {
            return Promise.reject(error);
        }

        if (originalRequest.url === REFRESH_URL) {
            accessToken = null;
            clearRefreshExpiry();
            window.location.href = '/login';
            return Promise.reject(error);
        }

        if (isRefreshing) {
            return new Promise((resolve, reject) => {
                failedQueue.push({ resolve, reject });
            })
            .then(() => window.axios(originalRequest))
            .catch(err => Promise.reject(err));
        }

        originalRequest._retry = true;
        isRefreshing = true;

        try {
            const { data } = await window.axios.post(REFRESH_URL);

            if (data.access_token && !hasAccessCookie()) {
                accessToken = data.access_token;
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
            }

            storeRefreshExpiry(data);
            processQueue(null, data.access_token);

            return window.axios(originalRequest);
        } catch (refreshError) {
            processQueue(refreshError, null);
            accessToken = null;
            clearRefreshExpiry();
            window.location.href = '/login';
            return Promise.reject(refreshError);
        } finally {
            isRefreshing = false;
        }
    }
);

function restoreSession() {
    return window.axios.post(REFRESH_URL)
        .then(({ data }) => {
            if (data.access_token && !hasAccessCookie()) {
                accessToken = data.access_token;
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;
            }
            storeRefreshExpiry(data);
            return data;
        })
        .catch(error => {
            accessToken = null;
            clearRefreshExpiry();
            return Promise.reject(error);
        });
}

// Proactive refresh: request a new access token shortly before expiry.
const accessTokenTtlMs = 15 * 60 * 1000; // mirrors SANCTUM_ACCESS_TTL=900s
const proactiveRefreshMs = Math.max(30 * 1000, accessTokenTtlMs - 60 * 1000);

function scheduleProactiveRefresh() {
    setInterval(() => {
        if (!accessToken && !hasAccessCookie()) return;
        window.axios.post(REFRESH_URL)
            .then(({ data }) => {
                if (data.access_token && !hasAccessCookie()) {
                    accessToken = data.access_token;
                    window.axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
                }
                storeRefreshExpiry(data);
            })
            .catch(() => {
                accessToken = null;
                clearRefreshExpiry();
            });
    }, proactiveRefreshMs);
}

window.REDAS_AUTH = {
    restoreSession,
    setToken: token => { accessToken = token; },
    getToken: () => accessToken,
    clearToken: () => {
        accessToken = null;
        clearRefreshExpiry();
    },
};

if (document.cookie.includes('refresh_token=')) {
    restoreSession().catch(() => {});
}

scheduleProactiveRefresh();
