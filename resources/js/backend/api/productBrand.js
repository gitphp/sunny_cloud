import http from './http';

export function fetchProductBrands(params = {}) {
  return http.get('/product/brands', { params });
}

export function createProductBrand(data) {
  return http.post('/product/brands', data);
}

export function updateProductBrand(id, data) {
  return http.put(`/product/brands/${id}`, data);
}

export function updateProductBrandSort(id, data) {
  return http.patch(`/product/brands/${id}/sort`, data);
}

export function updateProductBrandStatus(id, data) {
  return http.patch(`/product/brands/${id}/status`, data);
}

export function deleteProductBrand(id) {
  return http.delete(`/product/brands/${id}`);
}
