import http from './http';

export function fetchProductCategories(params = {}) {
  return http.get('/product/categories', { params });
}

export function createProductCategory(data) {
  return http.post('/product/categories', data);
}

export function updateProductCategory(id, data) {
  return http.put(`/product/categories/${id}`, data);
}

export function updateProductCategorySort(id, data) {
  return http.patch(`/product/categories/${id}/sort`, data);
}

export function updateProductCategoryStatus(id, data) {
  return http.patch(`/product/categories/${id}/status`, data);
}

export function deleteProductCategory(id) {
  return http.delete(`/product/categories/${id}`);
}
