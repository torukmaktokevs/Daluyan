// Admin layout interactivity: sidebar toggle, dropdown, theme toggle, charts demo
(function(){
  const sidebar = document.getElementById('sidebar');
  const btnToggle = document.getElementById('btnToggle');
  const profileBtn = document.getElementById('profileBtn');
  const profileMenu = document.getElementById('profileMenu');
  const themeToggle = document.getElementById('themeToggle');

  if (btnToggle) btnToggle.addEventListener('click', ()=> sidebar.classList.toggle('open'));
  if (profileBtn) profileBtn.addEventListener('click', ()=> profileMenu.style.display = (profileMenu.style.display==='block'?'none':'block'));
  if (themeToggle) themeToggle.addEventListener('click', ()=> document.body.classList.toggle('theme-dark'));

  // Close profile menu on outside click
  document.addEventListener('click', (e)=>{
    if (!profileBtn || !profileMenu) return;
    if (!profileBtn.contains(e.target)) profileMenu.style.display = 'none';
  });

  // Demo charts if present
  if (window.Chart) {
    const regCtx = document.getElementById('regChart');
    if (regCtx) {
      new Chart(regCtx, {
        type: 'line',
        data: { labels: Array.from({length: 12}, (_,i)=>`M${i+1}`), datasets: [{
          label: 'Registrations', data: Array.from({length: 12}, ()=> Math.floor(Math.random()*50)+10),
          borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,.2)', tension:.3, fill:true,
        }]},
        options: { plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
      });
    }

    const verCtx = document.getElementById('verChart');
    if (verCtx) {
      new Chart(verCtx, {
        type: 'doughnut',
        data: {
          labels:['Verified','Unverified'],
          datasets:[{ data:[65,35], backgroundColor:['#22c55e','#ef4444'] }]
        },
        options:{ plugins:{legend:{position:'bottom'}} }
      });
    }
  }
})();
/* -------------------------
  script.js - shared for all pages
  - uses localStorage to persist demo data
  - creates modals/toasts dynamically
------------------------- */

/* Utilities */
const $ = (s, root = document) => root.querySelector(s);
const $$ = (s, root = document) => Array.from(root.querySelectorAll(s));

function toast(msg, time = 3000) {
  let wrap = document.getElementById('toastWrap');
  if (!wrap) {
    wrap = document.createElement('div'); wrap.id = 'toastWrap'; wrap.className = 'toast-wrap';
    document.body.appendChild(wrap);
  }
  const el = document.createElement('div'); el.className = 'toast'; el.textContent = msg;
  wrap.appendChild(el);
  setTimeout(() => el.classList.add('show'), 10);
  setTimeout(() => { el.remove(); }, time);
}

function confirmDialog(title, message) {
  return new Promise(resolve => {
    const modal = createModal(title, `
      <p>${message}</p>
      <div class="modal-actions">
        <button id="__confirm_cancel" class="btn ghost">Cancel</button>
        <button id="__confirm_ok" class="btn">OK</button>
      </div>`);
    modal.querySelector('#__confirm_cancel').onclick = () => { modal.remove(); resolve(false); };
    modal.querySelector('#__confirm_ok').onclick = () => { modal.remove(); resolve(true); };
  });
}

function createModal(title, html) {
  const outer = document.createElement('div'); outer.className = 'modal';
  outer.innerHTML = `<div class="modal-body"><h3>${title}</h3>${html}</div>`;
  document.body.appendChild(outer);
  return outer;
}

/* -------------------------
  Seed & storage helpers
------------------------- */
const LS_USERS = 'rental_users_v1';
const LS_PROPS = 'rental_properties_v1';
const LS_TASKS = 'rental_tasks_v1';
const LS_THEME = 'rental_theme_v1';

function uuid(prefix='id') { return prefix + '_' + Math.random().toString(36).slice(2,9); }

function seedData() {
  if (!localStorage.getItem(LS_USERS)) {
    const users = [
      { id: 'u_admin', name: 'Admin User', email: 'admin@daluyan.com', role: 'admin', phone:'09170001111', address:'HQ, Quezon City', verified: true, avatar: 'https://i.pravatar.cc/150?img=12', created: Date.now() - (60*24*3600*1000) },
      { id: 'u_maria', name: 'Maria Cruz', email: 'maria@example.com', role: 'owner', phone:'09171234567', address:'Marikina', verified: false, avatar: 'https://i.pravatar.cc/150?img=3', created: Date.now() - (3*24*3600*1000) },
      { id: 'u_john', name: 'John Santos', email: 'john@example.com', role: 'tenant', phone:'09179876543', address:'Quezon City', verified: true, avatar: 'https://i.pravatar.cc/150?img=5', created: Date.now() - (10*24*3600*1000) },
    ];
    localStorage.setItem(LS_USERS, JSON.stringify(users));
  }
  if (!localStorage.getItem(LS_PROPS)) {
    const props = [
      { id: 'p_1', ownerId:'u_maria', title:'3-bed House in Pangasinan', desc:'Cozy family home near beach. 3BR, 2BA.', images: ['https://images.unsplash.com/photo-1560448070-c6aee3d4f0d3?w=1200&q=60','https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200&q=60'], lat:14.666, lng:121.022, status:'pending', submittedAt: Date.now() - 2*3600*1000 },
      { id: 'p_2', ownerId:'u_john', title:'Studio apt QC', desc:'Compact studio, ideal for students.', images: ['https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1200&q=60'], lat:14.657, lng:121.032, status:'approved', submittedAt: Date.now() - 26*3600*1000 },
      { id: 'p_3', ownerId:'u_maria', title:'Lot listing - Taguig', desc:'Vacant lot ideal for small development.', images: [], lat:14.671, lng:121.015, status:'pending', submittedAt: Date.now() - 3*24*3600*1000 },
    ];
    localStorage.setItem(LS_PROPS, JSON.stringify(props));
  }
  if (!localStorage.getItem(LS_TASKS)) {
    // tasks map to pending properties that need admin review
    const props = JSON.parse(localStorage.getItem(LS_PROPS));
    const tasks = props.filter(p => p.status === 'pending').map(p => ({
      id: uuid('t'), propertyId: p.id, ownerId: p.ownerId, type:'submission', createdAt: p.submittedAt
    }));
    localStorage.setItem(LS_TASKS, JSON.stringify(tasks));
  }
}
seedData();

function readUsers(){ return JSON.parse(localStorage.getItem(LS_USERS) || '[]'); }
function saveUsers(u){ localStorage.setItem(LS_USERS, JSON.stringify(u)); }
function readProps(){ return JSON.parse(localStorage.getItem(LS_PROPS) || '[]'); }
function saveProps(p){ localStorage.setItem(LS_PROPS, JSON.stringify(p)); }
function readTasks(){ return JSON.parse(localStorage.getItem(LS_TASKS) || '[]'); }
function saveTasks(t){ localStorage.setItem(LS_TASKS, JSON.stringify(t)); }

/* -------------------------
  Shared UI: sidebar, theme, profile
------------------------- */
document.addEventListener('DOMContentLoaded', ()=> {
  // dropdowns: handle any .nav-link.dropdown -> toggle next .submenu
  document.querySelectorAll('.nav .nav-link.dropdown').forEach(link => {
    link.addEventListener('click', () => {
      const menu = link.nextElementSibling;
      if (menu && menu.classList.contains('submenu')) {
        menu.classList.toggle('show');
      }
    });
  });

  // sidebar toggle
  document.querySelectorAll('#btnToggle').forEach(b=>{
    b.addEventListener('click', ()=>{
      const sb = document.getElementById('sidebar');
      if (!sb) return;
      if (window.innerWidth < 900) sb.classList.toggle('open');
      else sb.classList.toggle('collapsed');
    });
  });

  // theme toggle
  document.querySelectorAll('#themeToggle').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const dark = document.body.classList.toggle('dark');
      btn.textContent = dark ? '☀️ Light' : '🌙 Dark';
      localStorage.setItem(LS_THEME, dark ? 'dark' : 'light');
      // apply some CSS variables for dark
      if (dark) {
        document.documentElement.style.setProperty('--bg', '#071129');
        document.documentElement.style.setProperty('--card', '#0b1220');
        document.documentElement.style.setProperty('--text', '#e6eefb');
        document.documentElement.style.setProperty('--muted', '#9aa6b2');
      } else {
        document.documentElement.style.removeProperty('--bg');
        document.documentElement.style.removeProperty('--card');
        document.documentElement.style.removeProperty('--text');
        document.documentElement.style.removeProperty('--muted');
      }
    });
  });

  // restore theme
  const saved = localStorage.getItem(LS_THEME);
  if (saved === 'dark') {
    document.body.classList.add('dark');
    document.querySelectorAll('#themeToggle').forEach(btn => btn.textContent = '☀️ Light');
    document.documentElement.style.setProperty('--bg', '#071129');
    document.documentElement.style.setProperty('--card', '#0b1220');
    document.documentElement.style.setProperty('--text', '#e6eefb');
    document.documentElement.style.setProperty('--muted', '#9aa6b2');
  }

  // profile menu
  document.querySelectorAll('#profileBtn').forEach(pb=>{
    pb.addEventListener('click', (e)=> {
      e.stopPropagation();
      pb.classList.toggle('active');
    });
  });
  document.addEventListener('click', ()=> {
    document.querySelectorAll('.profile').forEach(p=>p.classList.remove('active'));
  });

  // page-specific inits
  if (document.getElementById('regChart')) initDashboard();
  if (document.getElementById('pendingList')) initTasksPage();
  if (document.getElementById('peopleList')) initPeoplePage();
  if (document.getElementById('mapProperties')) initPropertiesPage();
  if (document.getElementById('settingsUserList')) initSettingsPage();

  // global search quickfinder (simple)
  const gs = document.getElementById('globalSearch');
  if (gs) gs.addEventListener('input', ()=> {
    // simple: just show a toast with number of matches across users/properties
    const q = gs.value.trim().toLowerCase();
    if (!q) return;
    const users = readUsers();
    const props = readProps();
    const uMatches = users.filter(u=> (u.name+u.email).toLowerCase().includes(q)).length;
    const pMatches = props.filter(p=> (p.title + p.desc).toLowerCase().includes(q)).length;
    toast(`Found ${uMatches} users and ${pMatches} properties matching "${q}"`, 2500);
  });
});

/* -------------------------
  Dashboard init
------------------------- */
function initDashboard(){
  const users = readUsers(), props = readProps(), tasks = readTasks();
  // counts
  const pendingCount = props.filter(p => p.status === 'pending').length;
  const newUsers7d = users.filter(u => (Date.now() - (u.created||0)) <= (7*24*3600*1000)).length;
  const verified = users.filter(u => u.verified).length;
  document.getElementById('cardPending').textContent = pendingCount;
  document.getElementById('cardNewUsers').textContent = newUsers7d;
  document.getElementById('cardVerified').textContent = verified;
  document.getElementById('cardProperties').textContent = props.length;

  // charts
  const ctx = document.getElementById('regChart').getContext('2d');
  const months = ['Nov','Dec','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct'];
  const regs = [12,9,15,11,14,20,18,22,17,19,23,16]; // sample
  new Chart(ctx, {
    type:'line',
    data:{ labels: months, datasets:[{ data: regs, label:'New Users', borderColor: getComputedStyle(document.documentElement).getPropertyValue('--accent') || '#2563eb', backgroundColor:'rgba(37,99,235,0.08)', fill:true, tension:0.3 }]},
    options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
  });

  const ctx2 = document.getElementById('verChart').getContext('2d');
  new Chart(ctx2, {
    type:'doughnut',
    data:{ labels:['Verified','Awaiting','Rejected'], datasets:[{ data: [verified, Math.max(0, users.length-verified-1), 1], backgroundColor:['#2563eb','#60a5fa','#f97316'] }]},
    options:{plugins:{legend:{position:'bottom'}}}
  });

  // recent activity
  const recent = document.getElementById('recentActivity');
  if (recent) {
    const activities = [
      'User maria@example.com submitted a property for approval',
      'Tenant john@domain.com completed verification',
      'Owner anne@example.com updated listing photos',
      'System backup completed'
    ];
    recent.innerHTML = '';
    activities.forEach(a => {
      const li = document.createElement('li'); li.textContent = a; recent.appendChild(li);
    });
  }

  // quick action: add property - opens properties add modal if available
  const btnAddProp = document.getElementById('btnNewProperty');
  if (btnAddProp) btnAddProp.addEventListener('click', ()=> {
    // open property add modal through createModal with a small form
    openPropertyForm();
  });

  const btnExport = document.getElementById('btnExport');
  if (btnExport) btnExport.addEventListener('click', ()=> {
    exportCSV();
  });
}

/* -------------------------
  Tasks page init
------------------------- */
function initTasksPage() {
  let tasks = readTasks();
  const props = readProps(), users = readUsers();

  const pendingList = document.getElementById('pendingList');
  const verifyList = document.getElementById('verifyList');
  const taskSearch = document.getElementById('taskSearch');

  function renderPending(filter = '') {
    pendingList.innerHTML = '';
    const filtered = tasks.filter(t => {
      if (filter.trim() === '') return true;
      const p = props.find(x => x.id === t.propertyId);
      return (p.title + p.desc).toLowerCase().includes(filter.toLowerCase());
    });
    if (filtered.length === 0) pendingList.innerHTML = '<p class="muted">No pending tasks</p>';
    filtered.forEach(t => {
      const p = props.find(x => x.id === t.propertyId);
      const owner = users.find(u => u.id === t.ownerId) || {name:'Unknown',email:''};
      const div = document.createElement('div'); div.className = 'item';
      div.innerHTML = `<div class="meta">
        <div style="display:flex;gap:12px;align-items:center">
          <div>
            <strong>${p.title}</strong>
            <div class="muted">${owner.name} • ${owner.email}</div>
            <div class="muted" style="font-size:12px">Submitted: ${new Date(p.submittedAt).toLocaleString()}</div>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <button class="btn small view-details" data-prop="${p.id}"><i class="fa fa-eye"></i></button>
        <button class="btn small approve" data-prop="${p.id}"><i class="fa fa-check"></i></button>
        <button class="btn ghost small reject" data-prop="${p.id}"><i class="fa fa-times"></i></button>
      </div>`;
      pendingList.appendChild(div);

      // small thumb row under description
      if (p.images && p.images.length) {
        const thumbs = document.createElement('div'); thumbs.className = 'thumb-row'; thumbs.style.marginTop = '8px';
        p.images.forEach((img, idx) => {
          const iel = document.createElement('img'); iel.className = 'thumb small'; iel.src = img; iel.alt = p.title+' img';
          iel.onclick = ()=> openImageGallery(p.images, idx);
          thumbs.appendChild(iel);
        });
        div.appendChild(thumbs);
      }
    });
  }

  function renderVerifications(filter='') {
    verifyList.innerHTML = '';
    // show users who are not verified and have pending verification flag or recently submitted
    const candidates = users.filter(u => !u.verified);
    if (candidates.length === 0) verifyList.innerHTML = '<p class="muted">No verification requests</p>';
    candidates.forEach(u => {
      if (filter && !(u.name.toLowerCase().includes(filter.toLowerCase()) || u.email.toLowerCase().includes(filter.toLowerCase()))) return;
      const div = document.createElement('div'); div.className = 'item';
      div.innerHTML = `<div class="meta">
        <div style="display:flex;gap:12px;align-items:center">
          <img src="${u.avatar||'https://i.pravatar.cc/80'}" alt="" style="width:48px;height:48px;border-radius:8px;object-fit:cover"/>
          <div>
            <strong>${u.name}</strong>
            <div class="muted">${u.email} • ${u.role}</div>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <button class="btn small view-user" data-user="${u.id}"><i class="fa fa-eye"></i></button>
        <button class="btn small send-verify" data-user="${u.id}"><i class="fa fa-envelope"></i></button>
        <button class="btn small verify" data-user="${u.id}"><i class="fa fa-check"></i></button>
      </div>`;
      verifyList.appendChild(div);
    });
  }

  renderPending(); renderVerifications();

  // search
  if (taskSearch) taskSearch.addEventListener('input', ()=> { renderPending(taskSearch.value); renderVerifications(taskSearch.value); });

  // event delegation: clicks inside lists
  pendingList.addEventListener('click', async (e) => {
    const pBtn = e.target.closest('.view-details');
    const appr = e.target.closest('.approve');
    const rej = e.target.closest('.reject');
    if (pBtn) {
      const pid = pBtn.dataset.prop;
      viewPropertyDetails(pid);
    } else if (appr) {
      const pid = appr.dataset.prop;
      const ok = await confirmDialog('Approve property', 'Approve this property for publishing?');
      if (!ok) return;
      // set prop status approved and remove corresponding task
      const props = readProps();
      const prop = props.find(pp => pp.id === pid);
      if (prop) { prop.status = 'approved'; saveProps(props); }
      tasks = readTasks().filter(t => t.propertyId !== pid); saveTasks(tasks);
      renderPending(taskSearch.value); renderPending(); toast('Property approved');
    } else if (rej) {
      const pid = rej.dataset.prop;
      const ok = await confirmDialog('Reject property', 'Reject this property submission?');
      if (!ok) return;
      const props = readProps();
      const prop = props.find(pp => pp.id === pid);
      if (prop) { prop.status = 'rejected'; saveProps(props); }
      tasks = readTasks().filter(t => t.propertyId !== pid); saveTasks(tasks);
      renderPending(taskSearch.value); toast('Property rejected');
    }
  });

  // verify list events
  verifyList.addEventListener('click', async (e) => {
    const view = e.target.closest('.view-user');
    const sendv = e.target.closest('.send-verify');
    const verify = e.target.closest('.verify');
    if (view) {
      const uid = view.dataset.user;
      viewUserDetails(uid);
    } else if (sendv) {
      const uid = sendv.dataset.user;
      // simulate sending a verification email
      const users = readUsers();
      const u = users.find(x=>x.id===uid);
      if (u) {
        toast(`Verification email sent to ${u.email}`);
        // set a flag that emailSent (optional)
        u.verificationEmailSent = Date.now();
        saveUsers(users);
      }
    } else if (verify) {
      const uid = verify.dataset.user;
      const ok = await confirmDialog('Verify user', 'Confirm verification for this user?');
      if (!ok) return;
      const users = readUsers();
      const u = users.find(x=>x.id===uid);
      if (u) { u.verified = true; saveUsers(users); toast(`${u.name} verified`); renderVerifications(taskSearch.value); }
    }
  });

  // helper UI actions
  function openImageGallery(images = [], startIndex = 0) {
    if (!images || images.length === 0) { toast('No images to show'); return; }
    const html = `<div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center">
      ${images.map((src, idx) => `<img src="${src}" data-idx="${idx}" style="max-width:220px;border-radius:8px;margin:6px;object-fit:cover" />`).join('')}
    </div>
    <div style="text-align:right;margin-top:8px">
      <button id="closeGallery" class="btn">Close</button>
    </div>`;
    const modal = createModal('Images', html);
    modal.querySelector('#closeGallery').onclick = ()=> modal.remove();
  }

  function viewPropertyDetails(pid) {
    const props = readProps(), users = readUsers();
    const p = props.find(x=>x.id===pid);
    if (!p) return toast('Property not found');
    const owner = users.find(u => u.id === p.ownerId) || {};
    const html = `
      <div style="display:flex;gap:16px">
        <div style="flex:1">
          <p><strong>${p.title}</strong></p>
          <p class="muted">${p.desc}</p>
          <p class="muted">Status: <strong>${p.status}</strong></p>
          <p class="muted">Submitted: ${new Date(p.submittedAt).toLocaleString()}</p>
          <p class="muted">Owner: <strong>${owner.name || 'N/A'}</strong><br/>${owner.email || ''}<br/>${owner.phone || ''}<br/>${owner.address || ''}</p>
        </div>
        <div style="width:320px">
          ${p.images && p.images.length ? p.images.map(img => `<img src="${img}" style="width:100%;border-radius:8px;margin-bottom:8px" />`).join('') : '<p class="muted">No images submitted</p>'}
        </div>
      </div>
      <div class="modal-actions">
        <button id="detailClose" class="btn ghost">Close</button>
      </div>`;
    const modal = createModal('Property Details', html);
    modal.querySelector('#detailClose').onclick = ()=> modal.remove();
  }

  function viewUserDetails(uid) {
    const users = readUsers();
    const u = users.find(x=>x.id === uid);
    if (!u) return toast('User not found');
    const html = `<div style="display:flex;gap:12px">
      <img src="${u.avatar || 'https://i.pravatar.cc/150'}" style="width:120px;height:120px;border-radius:8px;object-fit:cover" />
      <div>
        <p><strong>${u.name}</strong></p>
        <p class="muted">${u.email}</p>
        <p class="muted">${u.phone || ''}</p>
        <p class="muted">${u.address || ''}</p>
        <p class="muted">Role: ${u.role}</p>
        <p class="muted">Verified: ${u.verified ? 'Yes' : 'No'}</p>
      </div>
    </div>
    <div class="modal-actions">
      <button id="closeUser" class="btn ghost">Close</button>
    </div>`;
    const modal = createModal('User Details', html);
    modal.querySelector('#closeUser').onclick = ()=> modal.remove();
  }
}

/* -------------------------
  People page init (add/edit/delete)
------------------------- */
function initPeoplePage() {
  let users = readUsers();
  const peopleList = document.getElementById('peopleList');
  const userPreview = document.getElementById('userPreview');
  const roleFilter = document.getElementById('roleFilter');
  const peopleSearch = document.getElementById('peopleSearch');
  const btnAddUser = document.getElementById('btnAddUser');

  function renderList() {
    users = readUsers();
    peopleList.innerHTML = '';
    const role = roleFilter.value;
    const q = (peopleSearch.value || '').toLowerCase();

    const set = users.filter(u => (role === 'all' ? true : u.role === role) && (u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q)));
    if (set.length === 0) peopleList.innerHTML = '<p class="muted">No users found</p>';
    set.forEach(u => {
      const r = document.createElement('div'); r.className = 'row';
      r.innerHTML = `<div style="display:flex;align-items:center;gap:12px;flex:1">
        <img src="${u.avatar||'https://i.pravatar.cc/80'}" style="width:48px;height:48px;border-radius:8px;object-fit:cover"/>
        <div><strong>${u.name}</strong><div class="muted">${u.email} • ${u.role}</div></div>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <button class="btn small edit-user" data-id="${u.id}"><i class="fa fa-edit"></i></button>
        <button class="btn ghost small del-user" data-id="${u.id}"><i class="fa fa-trash"></i></button>
      </div>`;
      r.addEventListener('click', (e) => {
        if (e.target.closest('.edit-user') || e.target.closest('.del-user')) return;
        showUserPreview(u.id);
      });
      peopleList.appendChild(r);
    });
  }

  function showUserPreview(id) {
    const u = users.find(x => x.id === id);
    if (!u) return userPreview.innerHTML = '<p class="muted">User not found</p>';
    userPreview.innerHTML = `<div style="display:flex;gap:12px">
      <img src="${u.avatar||'https://i.pravatar.cc/150'}" style="width:120px;height:120px;border-radius:8px;object-fit:cover"/>
      <div>
        <h3 style="margin:0">${u.name}</h3>
        <p class="muted">${u.email}</p>
        <p>${u.phone || ''}</p>
        <p class="muted">${u.address || ''}</p>
        <p>Role: <strong>${u.role}</strong> • Verified: <strong>${u.verified ? 'Yes' : 'No'}</strong></p>
        <div style="margin-top:8px;display:flex;gap:8px;">
          <button class="btn" id="msgUser">Message</button>
          <button class="btn ghost" id="resetUserPW">Reset PW</button>
        </div>
      </div>
    </div>`;
    // button actions
    $('#msgUser', userPreview).onclick = () => toast('Open chat / send message (not implemented)');
    $('#resetUserPW', userPreview).onclick = async () => {
      const ok = await confirmDialog('Reset Password', `Send password reset for ${u.email}? (simulated)`);
      if (!ok) return;
      // simulate password reset email
      toast(`Password reset email sent to ${u.email}`);
    };
  }

  // add user modal
  function openAddUser(initial = {}) {
    const modalHtml = `<div style="display:grid;gap:8px">
      <label>Name <input id="mu_name" value="${initial.name||''}" /></label>
      <label>Email <input id="mu_email" value="${initial.email||''}" /></label>
      <label>Phone <input id="mu_phone" value="${initial.phone||''}" /></label>
      <label>Address <input id="mu_address" value="${initial.address||''}" /></label>
      <label>Role
        <select id="mu_role">
          <option value="admin">admin</option>
          <option value="owner">owner</option>
          <option value="tenant">tenant</option>
        </select>
      </label>
      <label>Avatar <input id="mu_avatar" type="file" accept="image/*" /></label>
      <div class="modal-actions">
        <button id="mu_cancel" class="btn ghost">Cancel</button>
        <button id="mu_save" class="btn">Save</button>
      </div>
    </div>`;
    const modal = createModal('Add / Edit User', modalHtml);
    if (initial.role) modal.querySelector('#mu_role').value = initial.role;
    modal.querySelector('#mu_cancel').onclick = () => modal.remove();
    modal.querySelector('#mu_save').onclick = async () => {
      const name = modal.querySelector('#mu_name').value.trim();
      const email = modal.querySelector('#mu_email').value.trim();
      const phone = modal.querySelector('#mu_phone').value.trim();
      const address = modal.querySelector('#mu_address').value.trim();
      const role = modal.querySelector('#mu_role').value;
      const avatarFile = modal.querySelector('#mu_avatar').files[0];
      let avatar = initial.avatar || '';
      if (avatarFile) {
        avatar = await fileToDataUrl(avatarFile);
      }
      if (!name || !email) { toast('Name and email required'); return; }
      // create or update
      const all = readUsers();
      if (initial.id) {
        const idx = all.findIndex(x=>x.id===initial.id);
        if (idx>=0) all[idx] = {...all[idx], name, email, phone, address, role, avatar};
        saveUsers(all);
        toast('User updated');
      } else {
        const id = uuid('u');
        all.push({ id, name, email, phone, address, role, avatar, verified:false, created: Date.now() });
        saveUsers(all);
        toast('User created');
      }
      modal.remove();
      renderList();
    };
  }

  // file helper
  function fileToDataUrl(file) {
    return new Promise(resolve => {
      const r = new FileReader();
      r.onload = () => resolve(r.result);
      r.readAsDataURL(file);
    });
  }

  // events: filter/search
  roleFilter.addEventListener('change', renderList);
  peopleSearch.addEventListener('input', renderList);
  btnAddUser.addEventListener('click', ()=> openAddUser());

  // delegation for edit/delete
  peopleList.addEventListener('click', (e) => {
    const edit = e.target.closest('.edit-user');
    const del = e.target.closest('.del-user');
    if (edit) {
      const id = edit.dataset.id; const u = users.find(x => x.id===id);
      openAddUser(u);
    } else if (del) {
      const id = del.dataset.id;
      confirmDialog('Delete user', 'Delete this user?').then(ok => {
        if (!ok) return;
        let all = readUsers(); all = all.filter(x=>x.id!==id); saveUsers(all); renderList(); toast('User deleted');
      });
    }
  });

  renderList();
}

/* -------------------------
  Properties page init (leaflet map)
------------------------- */
function initPropertiesPage() {
  let props = readProps(), users = readUsers();
  const propertyList = document.getElementById('propertyList');
  const propFilter = document.getElementById('propFilter');
  const propSearch = document.getElementById('propSearch');
  const btnAddProp = document.getElementById('btnAddProperty');

  // initialize map
  const map = L.map('mapProperties').setView([14.665,121.022], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
  let markers = {};

  function renderListAndMarkers() {
  props = readProps();
  users = readUsers();
  propertyList.innerHTML = '';

  // clear markers
  Object.values(markers).forEach(m => map.removeLayer(m));
  markers = {};

  const status = propFilter.value;
  const q = (propSearch.value || '').toLowerCase();
  const set = props.filter(p =>
    (status === 'all' ? true : p.status === status) &&
    (p.title.toLowerCase().includes(q) || p.desc.toLowerCase().includes(q))
  );

  if (set.length === 0) {
    propertyList.innerHTML = '<p class="muted">No properties</p>';
    return;
  }

  set.forEach(p => {
    const owner = users.find(u => u.id === p.ownerId) || { name: 'Unknown' };
    const r = document.createElement('div');
    r.className = 'row';
    r.innerHTML = `
      <div style="flex:1">
        <strong>${p.title}</strong>
        <div class="muted">${owner.name} • ${p.status}</div>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <button class="btn small view-prop" data-id="${p.id}"><i class="fa fa-eye"></i></button>
        <button class="btn ghost small edit-prop" data-id="${p.id}"><i class="fa fa-edit"></i></button>
        <button class="btn del small del-prop" data-id="${p.id}"><i class="fa fa-trash"></i></button>
      </div>
    `;
    propertyList.appendChild(r);

    // add marker
    try {
      const m = L.marker([p.lat || 14.665, p.lng || 121.022])
        .addTo(map)
        .bindPopup(`<strong>${p.title}</strong><div class="muted">${owner.name}</div>`);
      markers[p.id] = m;
    } catch (e) {
      console.error(e);
    }

    // click list -> center
    r.addEventListener('click', (ev) => {
      if (ev.target.closest('.edit-prop') || ev.target.closest('.view-prop') || ev.target.closest('.del-prop')) return;
      map.setView([p.lat || 14.665, p.lng || 121.022], 15);
      if (markers[p.id]) markers[p.id].openPopup();
    });
  });
}

  renderListAndMarkers();

  // events
  propFilter.addEventListener('change', renderListAndMarkers);
  propSearch.addEventListener('input', renderListAndMarkers);

  // add property modal
  btnAddProp.addEventListener('click', ()=> openPropertyForm());

  // delegated actions
  propertyList.addEventListener('click', (e) => {
  const view = e.target.closest('.view-prop');
  const edit = e.target.closest('.edit-prop');
  const del = e.target.closest('.del-prop');

  if (view) {
    const id = view.dataset.id;
    viewProperty(id);
  } else if (edit) {
    const id = edit.dataset.id;
    openPropertyForm(id);
  } else if (del) {
    const id = del.dataset.id;
    if (!id) return;

    if (confirm("Are you sure you want to delete this property?")) {
      let props = readProps();
      props = props.filter(p => p.id !== id);
      saveProps(props);
      toast("Property deleted");
      renderListAndMarkers();
    }
  }
});
  // open property details
  function viewProperty(id) {
    const p = props.find(x => x.id === id);
    const owner = users.find(u => u.id === p.ownerId) || {};
    const html = `<div style="display:flex;gap:12px">
      <div style="flex:1">
        <h4 style="margin:0">${p.title}</h4>
        <p class="muted">${p.desc}</p>
        <p class="muted">Owner: ${owner.name} • ${owner.email}</p>
        <p class="muted">Status: ${p.status}</p>
      </div>
      <div style="width:300px">
        ${p.images && p.images.length ? p.images.map(i=>`<img src="${i}" style="width:100%;border-radius:8px;margin-bottom:8px" />`).join('') : '<p class="muted">No images</p>'}
      </div>
    </div>
    <div class="modal-actions">
      <button id="closeProp" class="btn ghost">Close</button>
    </div>`;
    const modal = createModal('Property Info', html);
    modal.querySelector('#closeProp').onclick = () => modal.remove();
  }

  // property form for add/edit
  function openPropertyForm(editId) {
    const users = readUsers();
    const owners = users.filter(u => u.role === 'owner' || u.role === 'admin');
    const p = props.find(x => x.id === editId) || {};
    const html = `<div style="display:grid;gap:8px">
      <label>Title <input id="pf_title" value="${p.title || ''}" /></label>
      <label>Description <textarea id="pf_desc">${p.desc || ''}</textarea></label>
      <label>Owner
        <select id="pf_owner">${owners.map(o=>`<option value="${o.id}" ${o.id===p.ownerId?'selected':''}>${o.name} (${o.email})</option>`).join('')}</select>
      </label>
      <label>Images <input id="pf_images" type="file" accept="image/*" multiple /></label>
      <div class="form-row">
        <label style="flex:1">Latitude <input id="pf_lat" value="${p.lat||14.665}" /></label>
        <label style="flex:1">Longitude <input id="pf_lng" value="${p.lng||121.022}" /></label>
      </div>
      <div style="display:flex;gap:8px;align-items:center;">
        <button id="pf_pickmap" class="btn ghost">Pick from Map</button>
        <div class="muted" style="font-size:13px">Click on the map to set coordinates</div>
      </div>
      <div class="modal-actions">
        <button id="pf_cancel" class="btn ghost">Cancel</button>
        <button id="pf_save" class="btn">Save</button>
      </div>
    </div>`;
    const modal = createModal(editId ? 'Edit Property' : 'Add Property', html);
    modal.querySelector('#pf_cancel').onclick = () => modal.remove();
    // map picker - allow click on map to set
    let pickerListener = null;
    modal.querySelector('#pf_pickmap').onclick = () => {
      toast('Click on the map to set coordinates');
      pickerListener = function(ev) {
        const latlng = ev.latlng;
        modal.querySelector('#pf_lat').value = latlng.lat.toFixed(6);
        modal.querySelector('#pf_lng').value = latlng.lng.toFixed(6);
        map.off('click', pickerListener);
        pickerListener = null;
        toast('Coordinates set');
      };
      map.on('click', pickerListener);
    };

    modal.querySelector('#pf_save').onclick = async () => {
      const title = modal.querySelector('#pf_title').value.trim();
      const desc = modal.querySelector('#pf_desc').value.trim();
      const ownerId = modal.querySelector('#pf_owner').value;
      const lat = parseFloat(modal.querySelector('#pf_lat').value) || 14.665;
      const lng = parseFloat(modal.querySelector('#pf_lng').value) || 121.022;
      const files = modal.querySelector('#pf_images').files;
      let images = p.images ? [...p.images] : [];
      if (files && files.length) {
        for (let f of files) {
          images.push(await fileToDataUrl(f));
        }
      }
      if (!title) { toast('Title required'); return; }
      let all = readProps();
      if (editId) {
        const idx = all.findIndex(x=>x.id===editId);
        if (idx>=0) all[idx] = {...all[idx], title, desc, ownerId, lat, lng, images};
      } else {
        const id = uuid('p');
        all.push({ id, ownerId, title, desc, images, lat, lng, status:'pending', submittedAt: Date.now() });
      }
      saveProps(all);
      modal.remove();
      renderListAndMarkers();
      toast('Property saved');
    };
  }

  // small helper for file to data URL (used earlier)
  function fileToDataUrl(file) {
    return new Promise(resolve => {
      const r = new FileReader();
      r.onload = () => resolve(r.result);
      r.readAsDataURL(file);
    });
  }
}

/* -------------------------
  Settings page init
------------------------- */
function initSettingsPage() {
  const settingsList = document.getElementById('settingsUserList');
  const pwSelect = document.getElementById('pwUserSelect');
  const btnAdd = document.getElementById('btnAddUserSettings');
  const btnReset = document.getElementById('btnResetPassword');

  function render() {
    const users = readUsers();
    settingsList.innerHTML = '';
    users.forEach(u => {
      const r = document.createElement('div'); r.className = 'row';
      r.innerHTML = `<div style="flex:1">
        <strong>${u.name}</strong>
        <div class="muted">${u.email} • ${u.role}</div>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <button class="btn small edit-set" data-id="${u.id}"><i class="fa fa-edit"></i></button>
        <button class="btn ghost small del-set" data-id="${u.id}"><i class="fa fa-trash"></i></button>
      </div>`;
      settingsList.appendChild(r);
    });

    // pw select
    pwSelect.innerHTML = users.map(u => `<option value="${u.id}">${u.name} (${u.email})</option>`).join('');
  }

  // add
  btnAdd.addEventListener('click', () => {
    // reuse people add user modal by creating a simple form here
    const html = `<div style="display:grid;gap:8px">
      <label>Name <input id="s_name" /></label>
      <label>Email <input id="s_email" /></label>
      <label>Role <select id="s_role"><option value="admin">admin</option><option value="owner">owner</option><option value="tenant">tenant</option></select></label>
      <div class="modal-actions">
        <button id="s_cancel" class="btn ghost">Cancel</button>
        <button id="s_save" class="btn">Create</button>
      </div>
    </div>`;
    const modal = createModal('Create User', html);
    modal.querySelector('#s_cancel').onclick = () => modal.remove();
    modal.querySelector('#s_save').onclick = () => {
      const name = modal.querySelector('#s_name').value.trim();
      const email = modal.querySelector('#s_email').value.trim();
      const role = modal.querySelector('#s_role').value;
      if (!name || !email) { toast('Fill required'); return; }
      const all = readUsers(); all.push({ id: uuid('u'), name, email, role, verified:false, created: Date.now() }); saveUsers(all);
      modal.remove(); render(); toast('User created');
    };
  });

  // delegation for edit/delete
  settingsList.addEventListener('click', (e) => {
    const edit = e.target.closest('.edit-set');
    const del = e.target.closest('.del-set');
    if (edit) {
      const id = edit.dataset.id; const all = readUsers();
      const u = all.find(x => x.id === id);
      if (!u) return;
      const html = `<div style="display:grid;gap:8px">
        <label>Name <input id="se_name" value="${u.name}" /></label>
        <label>Email <input id="se_email" value="${u.email}" /></label>
        <label>Role <select id="se_role"><option value="admin">admin</option><option value="owner">owner</option><option value="tenant">tenant</option></select></label>
        <div class="modal-actions">
          <button id="se_cancel" class="btn ghost">Cancel</button>
          <button id="se_save" class="btn">Save</button>
        </div>
      </div>`;
      const modal = createModal('Edit User', html);
      modal.querySelector('#se_role').value = u.role;
      modal.querySelector('#se_cancel').onclick = () => modal.remove();
      modal.querySelector('#se_save').onclick = () => {
        u.name = modal.querySelector('#se_name').value.trim();
        u.email = modal.querySelector('#se_email').value.trim();
        u.role = modal.querySelector('#se_role').value;
        saveUsers(all); modal.remove(); render(); toast('User updated');
      };
    } else if (del) {
      const id = del.dataset.id;
      confirmDialog('Delete user', 'Delete this user?').then(ok => {
        if (!ok) return;
        let all = readUsers(); all = all.filter(x => x.id !== id); saveUsers(all); render(); toast('User deleted');
      });
    }
  });

  btnReset.addEventListener('click', () => {
    const uid = pwSelect.value;
    if (!uid) return toast('Select a user');
    // simulate sending reset link
    const users = readUsers(); const u = users.find(x=>x.id === uid);
    toast(`Password reset email sent to ${u.email} (simulated)`);
  });

  render();
}

/* -------------------------
  Helpers: CSV export
------------------------- */
function exportCSV(){
  const props = readProps();
  let csv = 'id,title,owner,lat,lng,status,submittedAt\\n';
  const users = readUsers();
  props.forEach(p => {
    const owner = users.find(u=>u.id===p.ownerId) || {};
    csv += `"${p.id}","${p.title.replace(/"/g,'""')}","${owner.email || ''}",${p.lat || ''},${p.lng || ''},"${p.status}",${p.submittedAt}\\n`;
  });
  const blob = new Blob([csv], {type: 'text/csv'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a'); a.href = url; a.download = 'properties_export.csv'; a.click();
  URL.revokeObjectURL(url);
  toast('CSV exported');
}
// (dropdown toggles handled in DOMContentLoaded via event listeners on .nav-link.dropdown)
// Image preview on upload
const photoInput = document.getElementById('photoUpload');
const profilePic = document.getElementById('profilePic');
const removeBtn = document.querySelector('.remove-link');

photoInput?.addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (event) => {
      profilePic.src = event.target.result;
    };
    reader.readAsDataURL(file);
  }
});

removeBtn?.addEventListener('click', () => {
  profilePic.src = 'https://via.placeholder.com/100';
});
