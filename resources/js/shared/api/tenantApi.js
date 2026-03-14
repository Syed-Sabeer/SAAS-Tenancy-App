import http from './http';

const tenantApi = {
    login(payload) {
        return http.post('/login', payload);
    },
    logout() {
        return http.post('/logout');
    },
    dashboard() {
        return http.get('/dashboard');
    },
    users(params) {
        return http.get('/users', { params: params || {} });
    },
    createUser(payload) {
        return http.post('/users', payload);
    },
    getUserEdit(id) {
        return http.get('/users/' + id + '/edit');
    },
    updateUser(id, payload) {
        return http.patch('/users/' + id, payload);
    },
    deleteUser(id) {
        return http.delete('/users/' + id);
    },
    profile() {
        return http.get('/profile');
    },
    updateProfile(payload) {
        return http.patch('/profile', payload);
    },
};

export default tenantApi;
