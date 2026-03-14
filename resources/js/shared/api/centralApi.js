import http from './http';

const centralApi = {
    login(payload) {
        return http.post('/login', payload);
    },
    logout() {
        return http.post('/logout');
    },
    dashboard() {
        return http.get('/dashboard');
    },
    companies(params) {
        return http.get('/companies', { params: params || {} });
    },
    createCompany(payload) {
        return http.post('/companies', payload);
    },
    getCompany(id) {
        return http.get('/companies/' + id);
    },
    getCompanyEdit(id) {
        return http.get('/companies/' + id + '/edit');
    },
    updateCompany(id, payload) {
        return http.patch('/companies/' + id, payload);
    },
    deleteCompany(id) {
        return http.delete('/companies/' + id);
    },
    provisionCompany(id) {
        return http.post('/companies/' + id + '/provision');
    },
    provisionStatus(id) {
        return http.get('/companies/' + id + '/provision-status');
    },
};

export default centralApi;
