import http from './http';

export function fetchProducts(params = {}) {
  return http.get('/product/products', { params });
}

export function fetchProduct(id) {
  return http.get(`/product/products/${id}`);
}

export function createProduct(data) {
  return http.post('/product/products', data);
}

export function updateProduct(id, data) {
  return http.put(`/product/products/${id}`, data);
}

export function updateProductStatus(id, data) {
  return http.patch(`/product/products/${id}/status`, data);
}

export function deleteProduct(id) {
  return http.delete(`/product/products/${id}`);
}

export function uploadProductFile(file, mediaType) {
  const form = new FormData();
  form.append('file', file);
  if (mediaType != null) form.append('media_type', mediaType);
  return http.post('/product/upload', form);
}
