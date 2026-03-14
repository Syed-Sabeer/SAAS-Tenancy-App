import axios from 'axios';

const http = axios.create({
    baseURL: '/',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
});

http.interceptors.response.use(
    function onSuccess(response) {
        return response;
    },
    function onError(error) {
        if (error.response && error.response.data) {
            return Promise.reject(error.response.data);
        }

        return Promise.reject({
            success: false,
            message: 'Unexpected network error.',
            errors: null,
            data: null,
        });
    }
);

export default http;
