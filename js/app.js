import * as api from './api.js';

const msgEl = document.getElementById('message');
const usersTbody = document.querySelector('#usersTable tbody');

const createUserForm = document.getElementById('createUserForm');
const createUsername = document.getElementById('createUsername');
const createEmail = document.getElementById('createEmail');
const createPassword = document.getElementById('createPassword');
const createUserClear = document.getElementById('createUserClear');

const refreshUsersBtn = document.getElementById('refreshUsersBtn');
const filterInput = document.getElementById('filterUsers');

const editUserForm = document.getElementById('editUserForm');
const editId = document.getElementById('editId');
const editUsername = document.getElementById('editUsername');
const editEmail = document.getElementById('editEmail');
const editPassword = document.getElementById('editPassword');
const cancelUserEdit = document.getElementById('cancelUserEdit');

function showMessage(text, ok = true) {
  msgEl.textContent = text;
  msgEl.className = 'message ' + (ok ? 'ok' : 'err');
  msgEl.classList.remove('hide');
  setTimeout(() => msgEl.classList.add('hide'), 5000);
}

function escapeHtml(s){
  if (s === null || s === undefined) return '';
  return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
}

async function loadUsers() {
  try {
    const users = await api.listUsers();
    renderUsers(users || []);
  } catch (err) {
    showMessage('Error loading users: ' + (err.message || err), false);
    usersTbody.innerHTML = '';
  }
}

function renderUsers(users) {
  const q = (filterInput.value || '').toLowerCase().trim();
  usersTbody.innerHTML = '';
  users.forEach(u => {
    if (q) {
      const match = (u.username || '').toLowerCase().includes(q) || (u.email || '').toLowerCase().includes(q);
      if (!match) return;
    }
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${escapeHtml(u.id)}</td>
      <td>${escapeHtml(u.username)}</td>
      <td>${escapeHtml(u.email)}</td>
      <td>${escapeHtml(u.created_at || '')}</td>
      <td>
        <button class="action view" data-id="${u.id}">View</button>
        <button class="action edit" data-id="${u.id}">Edit</button>
        <button class="action del" data-id="${u.id}">Delete</button>
      </td>
    `;
    usersTbody.appendChild(tr);
  });
}

usersTbody.addEventListener('click', async (ev) => {
  const btn = ev.target.closest('button');
  if (!btn) return;
  const id = btn.getAttribute('data-id');
  if (btn.classList.contains('view')) {
    try {
      const u = await api.getUser(id);
      showMessage(JSON.stringify(u));
    } catch (err) {
      showMessage('View error: ' + (err.message || err), false);
    }
  } else if (btn.classList.contains('edit')) {
    try {
      const u = await api.getUser(id);
      editId.value = u.id;
      editUsername.value = u.username || '';
      editEmail.value = u.email || '';
      editPassword.value = '';
      editId.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } catch (err) {
      showMessage('Load for edit failed: ' + (err.message || err), false);
    }
  } else if (btn.classList.contains('del')) {
    if (!confirm('Delete user ' + id + '?')) return;
    try {
      await api.deleteUser(id);
      showMessage('Deleted user ' + id);
      await loadUsers();
    } catch (err) {
      showMessage('Delete failed: ' + (err.message || err), false);
    }
  }
});

createUserForm.addEventListener('submit', async (ev) => {
  ev.preventDefault();
  const payload = {
    username: createUsername.value.trim(),
    email: createEmail.value.trim(),
    password: createPassword.value
  };
  try {
    const res = await api.createUser(payload);
    showMessage('Created user id ' + (res && res.id ? res.id : '?'));
    createForm.reset();
    await loadUsers();
  } catch (err) {
    showMessage('Create failed: ' + (err.message || err), false);
  }
});

createUserClear.addEventListener('click', () => createForm.reset());
refreshUsersBtn.addEventListener('click', loadUsers);
filterInput.addEventListener('input', () => loadUsers());

editUserForm.addEventListener('submit', async (ev) => {
  ev.preventDefault();
  const id = editId.value;
  if (!id) return showMessage('Select a user to edit', false);
  const payload = {};
  if (editUsername.value) payload.username = editUsername.value.trim();
  if (editEmail.value) payload.email = editEmail.value.trim();
  if (editPassword.value) payload.password = editPassword.value;
  try {
    await api.updateUser(id, payload);
    showMessage('Updated user ' + id);
    editForm.reset();
    await loadUsers();
  } catch (err) {
    showMessage('Update failed: ' + (err.message || err), false);
  }
});

cancelUserEdit.addEventListener('click', () => editForm.reset());

// initial load
loadUsers();