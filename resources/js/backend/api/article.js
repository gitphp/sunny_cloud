import http from './http';

export function fetchArticles(params = {}) {
  return http.get('/news/articles', { params });
}

export function fetchArticle(id) {
  return http.get(`/news/articles/${id}`);
}

export function createArticle(data) {
  return http.post('/news/articles', data);
}

export function updateArticle(id, data) {
  return http.put(`/news/articles/${id}`, data);
}

export function updateArticleStatus(id, data) {
  return http.patch(`/news/articles/${id}/status`, data);
}

export function updateArticleTop(id, data) {
  return http.patch(`/news/articles/${id}/top`, data);
}

export function deleteArticle(id) {
  return http.delete(`/news/articles/${id}`);
}
