import http from './http';

export function fetchProductSpecifications(params = {}) {
  return http.get('/product/specifications', { params });
}

export function createProductSpecification(data) {
  return http.post('/product/specifications', data);
}

export function updateProductSpecification(id, data) {
  return http.put(`/product/specifications/${id}`, data);
}

export function updateProductSpecificationSort(id, data) {
  return http.patch(`/product/specifications/${id}/sort`, data);
}

export function updateProductSpecificationStatus(id, data) {
  return http.patch(`/product/specifications/${id}/status`, data);
}

export function deleteProductSpecification(id) {
  return http.delete(`/product/specifications/${id}`);
}

export function fetchSpecValues(specId) {
  return http.get(`/product/specifications/${specId}/values`);
}

export function createSpecValue(specId, data) {
  return http.post(`/product/specifications/${specId}/values`, data);
}

export function updateSpecValue(id, data) {
  return http.put(`/product/specification-values/${id}`, data);
}

export function updateSpecValueSort(id, data) {
  return http.patch(`/product/specification-values/${id}/sort`, data);
}

export function updateSpecValueStatus(id, data) {
  return http.patch(`/product/specification-values/${id}/status`, data);
}

export function deleteSpecValue(id) {
  return http.delete(`/product/specification-values/${id}`);
}
