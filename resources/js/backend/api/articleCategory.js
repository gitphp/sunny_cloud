import http from './http';

export function fetchArticleCategories(params = {}) {
  return http.get('/news/article-categories', { params });
}

export function createArticleCategory(data) {
  return http.post('/news/article-categories', data);
}

export function updateArticleCategory(id, data) {
  return http.put(`/news/article-categories/${id}`, data);
}

export function updateArticleCategorySort(id, data) {
  return http.patch(`/news/article-categories/${id}/sort`, data);
}

export function updateArticleCategoryStatus(id, data) {
  return http.patch(`/news/article-categories/${id}/status`, data);
}

export function deleteArticleCategory(id) {
  return http.delete(`/news/article-categories/${id}`);
}
