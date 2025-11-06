document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. CORRECTED SIDEBAR ACTIVE LINK LOGIC ---
    try {
        const currentPage = window.location.pathname.split('/').pop(); // Gets 'index.php' or 'details.php'
        const urlParams = new URLSearchParams(window.location.search);
        const currentMetric = urlParams.get('metric'); // Gets the metric from the URL, like 'total_users'
        const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

        navLinks.forEach(link => {
            link.classList.remove('active'); // First, remove active from all links

            const linkHref = link.getAttribute('href');

            // Case 1: We are on the main dashboard page
            if (currentPage === 'index.php' && linkHref === 'index.php') {
                link.classList.add('active');
            }
            // Case 2: We are on a details page
            else if (currentPage === 'details.php' && currentMetric && linkHref.includes(`metric=${currentMetric}`)) {
                link.classList.add('active');
            }
        });
    } catch (e) {
        console.error("Error setting active sidebar link:", e);
    }


    // --- 2. LOGOUT SIMULATION ---
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Are you sure you want to log out?')) {
                alert('You have been logged out.');
                // In a real app, you would redirect: window.location.href = '/login.php';
            }
        });
    }

    // --- 3. GRAPH MODAL LOGIC (for index.php) ---
    const dashboardCards = document.querySelector('.dashboard-cards');
    if (dashboardCards) {
        const graphModalOverlay = document.getElementById('graph-modal-overlay');
        const graphModalTitle = document.getElementById('graph-modal-title');
        const viewDetailsBtn = document.getElementById('view-details-btn');
        const closeGraphModalBtn = document.getElementById('close-graph-modal-btn');
        const chartCanvas = document.getElementById('summary-chart');
        const dashboardData = JSON.parse(document.getElementById('dashboard-data').textContent);
        let summaryChart = null;

        dashboardCards.addEventListener('click', (e) => {
            const card = e.target.closest('.card.clickable');
            if (!card) return;

            const metricKey = card.dataset.metric;
            const metricData = dashboardData[metricKey];

            if (!metricData.graph) {
                window.location.href = `details.php?metric=${metricKey}`;
                return;
            }

            graphModalTitle.textContent = `${metricData.label} Summary`;
            viewDetailsBtn.href = `details.php?metric=${metricKey}`;

            if (summaryChart) summaryChart.destroy();
            
            summaryChart = new Chart(chartCanvas, {
                type: metricData.graph.type,
                data: {
                    labels: metricData.graph.labels,
                    datasets: [{
                        label: metricData.label,
                        data: metricData.graph.dataPoints,
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        tension: metricData.graph.type === 'line' ? 0.3 : 0,
                        fill: true,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
            });

            graphModalOverlay.classList.remove('hidden');
        });
        
        const closeGraphModal = () => graphModalOverlay.classList.add('hidden');
        closeGraphModalBtn.addEventListener('click', closeGraphModal);
        graphModalOverlay.addEventListener('click', (e) => { if (e.target === graphModalOverlay) closeGraphModal(); });
    }

    // --- 4. CRUD MODAL LOGIC (for details.php) ---
    const table = document.getElementById('details-table');
    if (!table) return; 

    const modalOverlay = document.getElementById('modal-overlay');
    const modalTitle = document.getElementById('modal-title');
    const dataForm = document.getElementById('data-form');
    const formContent = document.getElementById('modal-form-content');
    const addNewBtn = document.getElementById('add-new-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');
    
    let editingRow = null;
    const metric = addNewBtn.dataset.metric;

    const openModal = () => modalOverlay.classList.remove('hidden');
    const closeModal = () => modalOverlay.classList.add('hidden');

    const createFormFields = (headers, rowData = {}) => {
        formContent.innerHTML = '';
        headers.forEach((header) => {
            const key = header.toLowerCase().replace(/[^a-z0-9]/gi, '_');
            const value = rowData[header] || (header.toLowerCase().includes('date') ? new Date().toISOString().slice(0, 10) : '');
            if (header.toLowerCase() === 'id') return;

            const formGroup = document.createElement('div');
            formGroup.className = 'form-group';
            formGroup.innerHTML = `<label for="field-${key}">${header}</label><input type="text" id="field-${key}" name="${header}" value="${value}" required>`;
            formContent.appendChild(formGroup);
        });
    };

    addNewBtn.addEventListener('click', () => {
        editingRow = null;
        modalTitle.textContent = 'Add New Entry';
        const headers = Array.from(table.querySelectorAll('thead th:not(:last-child)')).map(th => th.textContent);
        createFormFields(headers);
        openModal();
    });
    
    closeModalBtn.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', e => { if (e.target === modalOverlay) closeModal(); });

    table.addEventListener('click', async (e) => {
        const target = e.target.closest('button');
        if (!target) return;

        const row = target.closest('tr');
        const id = row.dataset.id;

        if (target.classList.contains('edit-btn')) {
            editingRow = row;
            modalTitle.textContent = 'Edit Entry';
            const headers = Array.from(table.querySelectorAll('thead th:not(:last-child)')).map(th => th.textContent);
            const cells = Array.from(row.querySelectorAll('td:not(:last-child)')).map(td => td.textContent);
            
            const rowData = {};
            headers.forEach((header, i) => { rowData[header] = cells[i]; });
            
            createFormFields(headers, rowData);
            openModal();
        }

        if (target.classList.contains('delete-btn')) {
            if (!confirm('Are you sure you want to delete this entry?')) return;
            
            const formData = new FormData();
            formData.append('action', 'deleteEntry'); 
            formData.append('id', id);
            formData.append('metric', metric);

            const response = await fetch('api.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (result.status === 'success') {
                row.remove();
            } else {
                alert('Error: ' + (result.message || 'Could not delete entry.'));
            }
        }
    });

    dataForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(dataForm);
        formData.append('metric', metric);
        
        if (editingRow) {
            formData.append('action', 'updateEntry');
            formData.append('id', editingRow.dataset.id);
        } else {
            formData.append('action', 'addEntry');
        }

        const response = await fetch('api.php', { method: 'POST', body: formData });
        const result = await response.json();

        if (result.status === 'success') {
            location.reload(); // Reload to see changes
        } else {
            alert('Error: ' + (result.message || 'Could not save entry.'));
        }
    });
});