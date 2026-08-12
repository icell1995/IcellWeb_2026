document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchPolda');
    const poldaItems = document.querySelectorAll('.polda-item');
    const noResults = document.getElementById('no-results');
    const poldaCards = document.querySelectorAll('.polda-card');

    // === LIVE SEARCH ===
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let hasResult = false;

        poldaItems.forEach(item => {
            const poldaName = item.getAttribute('data-polda-name').toLowerCase();
            if (searchTerm === '' || poldaName.includes(searchTerm)) {
                item.classList.remove('d-none');
                hasResult = true;
            } else {
                item.classList.add('d-none');
            }
        });

        noResults.classList.toggle('d-none', hasResult || searchTerm === '');
    }

    searchInput.addEventListener('input', performSearch);

    // === LOGIC KLIK CARD ===
    poldaCards.forEach(card => {
        // Kita tangkap klik pada card-body yang berfungsi sebagai trigger
        // const trigger = card.querySelector('.card-body');

        card.addEventListener('click', function () {
            const dropdownEl = card.querySelector('.polres-dropdown');
            const poldaId = this.getAttribute('data-polda-id');
            const listContainer = card.querySelector('.polres-list');
            const icon = this.querySelector('.bi-chevron-down');

            let bsCollapse = bootstrap.Collapse.getInstance(dropdownEl);
            if (!bsCollapse) {
                bsCollapse = new bootstrap.Collapse(dropdownEl, { toggle: false });
            }

            if (dropdownEl.classList.contains('show')) {
                bsCollapse.hide();
            } else {
                // Close others
                document.querySelectorAll('.polres-dropdown.show').forEach(el => {
                    bootstrap.Collapse.getInstance(el).hide();
                });

                // Fetch data jika list masih kosong (hanya tersisa template/kosong)
                if (listContainer.children.length === 0) {
                    listContainer.innerHTML = `
                        <li class="list-group-item bg-transparent text-center py-3 border-0">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2 text-muted">Mengambil data...</span>
                        </li>`;

                    fetch(`/anggota/polres?polda_id=${poldaId}`)
                        .then(res => res.json())
                        .then(polres => {
                            listContainer.innerHTML = '';
                            if (polres.length === 0) {
                                listContainer.innerHTML = `
                                    <div class="text-center py-4">
                                        <i class="bi bi-info-circle text-muted mb-2 fs-4"></i>
                                        <p class="text-muted small mb-0">Tidak ada data Polres tersedia.</p>
                                    </div>`;
                            } else {
                                polres.forEach(pol => {
                                    const count = pol.anggota_count ?? pol.users_count ?? 0;
                                    listContainer.innerHTML += `
                                        <li class="list-group-item bg-transparent border-0 d-flex justify-content-between align-items-center px-4 py-3 border-bottom-dashed">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary bg-opacity-10 p-2 rounded-2 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-building text-secondary" style="font-size: 0.85rem;"></i>
                                                </div>
                                                <span class="fw-medium text-dark" style="font-size: 0.9rem;">${pol.name}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.75rem;">
                                                    ${count} <span class="text-muted fw-normal ms-1">Pers</span>
                                                </span>
                                            </div>
                                        </li>`;
                                });
                            }
                        })
                        .catch(() => {
                            listContainer.innerHTML = `
                                <div class="text-center py-4 text-danger">
                                    <i class="bi bi-exclamation-triangle mb-2 fs-4"></i>
                                    <p class="small mb-0">Gagal memuat data. Silakan coba lagi.</p>
                                </div>`;
                        });
                }
                bsCollapse.show();
            }
        });
    });
});
