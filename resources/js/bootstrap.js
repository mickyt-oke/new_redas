import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // Required to send/receive HttpOnly cookies

/**
 * JWT Session Management
 *
 * Access tokens are deliberately kept in memory only (never localStorage or
 * sessionStorage) to mitigate XSS theft. The refresh token lives in a Secure,
 * HttpOnly cookie so the session can survive browser/PWA restarts.
 */
const REFRESH_URL = '/api/refresh';
const REFRESH_EXPIRY_KEY = 'redas_refresh_expires_at';

let accessToken = null;
let isRefreshing = false;
let failedQueue = [];

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

// Request Interceptor: Attach Access Token
window.axios.interceptors.request.use(config => {
    if (accessToken) {
        config.headers.Authorization = `Bearer ${accessToken}`;
    }
    return config;
}, error => Promise.reject(error));

// Response Interceptor: Handle 401s and Silent Refresh
window.axios.interceptors.response.use(
    response => response,
    async error => {
        const originalRequest = error.config;

        // Only attempt refresh if it's a 401 and not already a refresh attempt
        if (error.response?.status === 401 && !originalRequest._retry) {
            if (originalRequest.url === REFRESH_URL) {
                // Refresh token itself is expired or invalid -> Force Logout
                accessToken = null;
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
                const { data } = await window.axios.post(REFRESH_URL);
                const newToken = data.access_token;

                storeRefreshExpiry(data);
                accessToken = newToken;
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;

                processQueue(null, newToken);

                originalRequest.headers.Authorization = `Bearer ${newToken}`;
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

        return Promise.reject(error);
    }
);

/**
 * Restore the session on app start / PWA launch. If a refresh token cookie is
 * present, the server will issue a new access token and the user can continue
 * without re-entering credentials.
 */
function restoreSession() {
    return window.axios.post(REFRESH_URL)
        .then(({ data }) => {
            if (data.access_token) {
                accessToken = data.access_token;
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;
            }
            storeRefreshExpiry(data);
            return data;
        })
        .catch(error => {
            // No valid refresh cookie: clear any stale token and remain logged out.
            accessToken = null;
            clearRefreshExpiry();
            return Promise.reject(error);
        });
}

/**
 * Proactive refresh: request a new access token shortly before the current one
 * expires (default 14 minutes). This keeps the PWA session alive during active
 * use without waiting for a 401.
 */
const accessTokenTtlMs = 15 * 60 * 1000; // mirrors JWT_ACCESS_TTL=900s
const proactiveRefreshMs = Math.max(30 * 1000, accessTokenTtlMs - 60 * 1000);

function scheduleProactiveRefresh() {
    setInterval(() => {
        if (!accessToken) return;
        window.axios.post(REFRESH_URL)
            .then(({ data }) => {
                if (data.access_token) {
                    accessToken = data.access_token;
                    window.axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;
                }
                storeRefreshExpiry(data);
            })
            .catch(() => {
                accessToken = null;
                clearRefreshExpiry();
            });
    }, proactiveRefreshMs);
}

// Expose helpers for PWA/inline usage.
window.REDAS_AUTH = {
    restoreSession,
    setToken: token => { accessToken = token; },
    getToken: () => accessToken,
    clearToken: () => {
        accessToken = null;
        clearRefreshExpiry();
    },
};

// Attempt to restore the session on startup if the PWA was launched from a
// previous authenticated session. This is a "best effort" call: failure simply
// means the user must log in.
if (document.cookie.includes('refresh_token=')) {
    restoreSession().catch(() => {});
}

scheduleProactiveRefresh();
