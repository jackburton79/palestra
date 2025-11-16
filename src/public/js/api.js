import { API_BASE } from './config.js';

async function request(path, opts = {}) {
  const url = path.startsWith('http') ? path : `${API_BASE}${path}`;
  const headers = Object.assign({ 'Accept': 'application/json' }, opts.headers || {});
  let body = opts.body;
  if (body && typeof body === 'object') {
    headers['Content-Type'] = 'application/json';
    body = JSON.stringify(body);
  }
  const res = await fetch(url, Object.assign({}, opts, { headers, body }));
  let data = null;
  try { data = await res.json(); } catch (e) { /* ignore parse errors */ }
  if (!res.ok) {
    const errMsg = (data && data.error) ? data.error : (data && data.message) ? data.message : res.statusText;
    const err = new Error(errMsg || `HTTP ${res.status}`);
    err.status = res.status;
    err.body = data;
    throw err;
  }
  return data;
}

export const listUsers = () => request('/users', { method: 'GET' });
export const getUser = (id) => request(`/user/${encodeURIComponent(id)}`, { method: 'GET' });
export const createUser = (payload) => request('/user', { method: 'POST', body: payload });
export const updateUser = (id, payload) => request(`/user/${encodeURIComponent(id)}`, { method: 'PUT', body: payload });
export const deleteUser = (id) => request(`/user/${encodeURIComponent(id)}`, { method: 'DELETE' });