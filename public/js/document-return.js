// public/js/document-return.js

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('search-form');
    const resultsContainer = document.getElementById('results-container');
    const noLpInput = document.getElementById('no_lp');
    const searchButton = document.getElementById('search-accident');

    if (searchButton) {
        searchButton.addEventListener('click', async (e) => {
            e.preventDefault();

            const noLp = noLpInput.value.trim();

            if (noLp.length < 3) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Tidak Valid',
                    text: 'Masukkan minimal 3 karakter untuk pencarian',
                    timer: 2000
                });
                return;
            }

            resultsContainer.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3">Mencari data...</p>
                </div>
            `;

            try {
                const url = `/cms/document-return/search-accident?no_lp=${encodeURIComponent(noLp)}`;

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (!result.success) {
                    resultsContainer.innerHTML = `<p class="text-danger text-center">${result.message || 'Tidak ditemukan data dengan No LP tersebut'}</p>`;
                    return;
                }

                renderAccidents(result.data, result.pagination);

            } catch (error) {
                resultsContainer.innerHTML = '<p class="text-danger text-center">Gagal memuat data. Silakan coba lagi.</p>';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mencari',
                    text: error.message || 'Terjadi kesalahan saat mencari data'
                });
            }
        });
    }

    // Fungsi render card accident
    function renderAccidents(accidents, pagination) {
        resultsContainer.innerHTML = '';
        accidents.forEach(acc => {
            let selraBadgeClass = 'bg-secondary'; // Default jika ID tidak dikenal

            switch (acc.selra_id) {
                case 'S0107': // Dalam Proses
                    selraBadgeClass = 'bg-danger';
                    break;
                case 'S0101': // P21
                    selraBadgeClass = 'bg-success';
                    break;
                case 'S0102': // Sp3
                    selraBadgeClass = 'bg-info';
                    break;
                case 'S0103': // Diversi
                    selraBadgeClass = 'bg-primary';
                    break;
                case 'S0104': // POM/TNI
                    selraBadgeClass = 'bg-dark bg-gradient';
                    break;
                case 'S0108': // SP2LID
                    selraBadgeClass = 'text-bg-warning';
                    break;
            }

            const col = document.createElement('div');
            col.className = 'col-12 mb-2';
            col.innerHTML = `
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-file-earmark-text me-2"></i>${escapeHtml(acc.no_lp)}</h5>
                            <span class="badge rounded-pill ${selraBadgeClass} px-3">${escapeHtml(acc.selra)}</span>
                        </div>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-sm-6">
                                <div class="card h-100 border-0 shadow-sm rounded-3 p-2">
                                    <div class="card-body p-2 text-center text-md-start">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-building me-1"></i>Polda / Polres</small>
                                        <span class="fw-bold small">${escapeHtml(acc.polda_name)} / ${escapeHtml(acc.polres_name)}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card h-100 border-0 shadow-sm rounded-3 p-2">
                                    <div class="card-body p-2 text-center text-md-start">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-calendar-event me-1"></i>Tanggal Kejadian</small>
                                        <span class="fw-bold small">${acc.accident_date || '-'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card h-100 border-0 shadow-sm rounded-3 p-2">
                                    <div class="card-body p-2 text-center text-md-start">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-person-badge me-1"></i>Petugas</small>
                                        <span class="fw-bold small">${escapeHtml(acc.rank_id || '')} ${escapeHtml(acc.officer_name || '-')}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card h-100 border-0 shadow-sm rounded-3 p-2">
                                    <div class="card-body p-2 text-center text-md-start">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-clock-history me-1"></i>Tanggal Aktivitas Terakhir</small>
                                        <span class="fw-bold small">${acc.accident_last_update || '-'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <a href="javascript:void(0)"
                               class="text-primary text-decoration-none fw-bold small"
                               data-bs-toggle="collapse"
                               data-bs-target="#docs-${acc.id}"
                               onclick="loadDocuments('${acc.id}')">
                                LIHAT DAFTAR DOKUMEN <i class="bi bi-chevron-down ms-1"></i>
                            </a>
                        </div>

                        <div class="collapse mt-3" id="docs-${acc.id}">
                            <hr class="my-4 opacity-25">
                            <div class="row g-3" id="doc-list-${acc.id}">
                                <div class="col-12 text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            resultsContainer.appendChild(col);
        });
    }

    window.loadDocuments = async function (accidentId) {
        const listEl = document.getElementById(`doc-list-${accidentId}`);
        if (!listEl || listEl.dataset.loaded === 'true') return;

        try {
            const response = await fetch(`/cms/document-return/accident/${accidentId}/documents`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();

            if (!result.success || result.data.length === 0) {
                listEl.innerHTML = '<div class="col-12 text-center text-muted">Tidak ada dokumen.</div>';
            } else {
                let html = '';
                result.data.forEach(doc => {
                    let returnBtn = '';
                    if (doc.is_eligible_return === true) {
                        // let docType = doc.type.toLowerCase().replace('document', '');
                        let docType = '';
                        const typeMap = {
                            '0101': 'sprinlidik',
                            '0201': 'sprindik',
                            '0706': 'lhgp',
                            '0215': 'tap_tersangka',
                            '0204': 'spdp'
                        };

                        if (typeMap[doc.category_code]) {
                            docType = typeMap[doc.category_code];
                        } else if (doc.category_code === '0702') {
                            if (doc.category_name.includes('Penyidikan') || doc.related_type?.includes('Penyidikan')) {
                                docType = 'springas_sidik';
                            } else if (doc.category_name.includes('Penyelidikan') || doc.related_type?.includes('Penyelidikan')) {
                                docType = 'springas_lidik';
                            } else {
                                docType = 'springas';
                            }
                        } else {
                            // docType = doc.type.toLowerCase().replace('document', '');
                            docType = doc.category_code;
                        }

                        returnBtn = `
                            <button class="btn btn-danger btn-sm w-100 rounded-pill shadow-sm btn-return-doc d-flex align-items-center justify-content-center gap-2 mt-3 fw-bold"
                                    data-accident-id="${accidentId}"
                                    data-doc-id="${doc.id}"
                                    data-doc-type="${docType}"
                                    data-category-name="${escapeHtml(doc.category_name)}">
                                <i class="bi bi-arrow-left-right"></i> Kembalikan
                            </button>
                        `;
                    }

                    html += `
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                            <div class="card-body p-3 d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge rounded-pill ${doc.badge_class} mb-2">${escapeHtml(doc.status_text)}</span>
                                    <h6 class="fw-bold mb-1 text-dark">${escapeHtml(doc.category_name)}</h6>
                                    <p class="text-muted small mb-0">${escapeHtml(doc.title)}</p>
                                </div>
                                <div class="mt-auto pt-2 border-top">
                                    <small class="text-muted" style="font-size:0.75rem"><i class="bi bi-calendar-event me-1"></i> ${escapeHtml(doc.text_date)} : ${escapeHtml(doc.information_date)}</small>
                                    ${returnBtn}
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
                listEl.innerHTML = html;
            }
            listEl.dataset.loaded = 'true';
        } catch (err) {
            listEl.innerHTML = '<div class="col-12 text-danger">Gagal memuat.</div>';
        }
    };

    document.addEventListener('click', async function (e) {
        if (!e.target.classList.contains('btn-return-doc')) return;

        e.preventDefault();
        const btn = e.target;
        const originalContent = '<i class="bi bi-arrow-left-right"></i> Kembalikan';

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';

        const accidentId = btn.dataset.accidentId;
        const docId = btn.dataset.docId;
        const docType = btn.dataset.docType;
        const categoryName = btn.dataset.categoryName;

        try {
            const res = await fetch(`/cms/document-return/cascade/${accidentId}/${docId}?document_type=${encodeURIComponent(docType)}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) {
                const errorText = await res.text();
                throw new Error(`Gagal memuat info dokumen terkait (${res.status})`);
            }

            const data = await res.json();

            if (!data.success) {
                throw new Error(data.message || 'Dokumen tidak dapat dikembalikan');
            }

            let htmlContent = `
            <div class="text-start px-1">
                <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4">
                    <div class="d-flex">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                        <div>
                            <small class="d-block fw-bold text-uppercase">Dokumen Utama:</small>
                            <span class="small">${escapeHtml(data.main)}</span>
                        </div>
                    </div>
                </div>
        `;

            if (data.cascade.length > 0) {
                htmlContent += `
                <div class="mb-3">
                    <label class="fw-bold small text-muted mb-2"><i class="bi bi-layers me-1"></i> DOKUMEN TERKAIT (IKUT DIKEMBALIKAN):</label>
                    <ul class="list-group list-group-flush border rounded-3 overflow-hidden shadow-sm" style="max-height: 150px; overflow-y: auto;">
                        ${data.cascade.map(item => `
                            <li class="list-group-item list-group-item-light small py-2">
                                <i class="bi bi-file-earmark-arrow-left me-2 text-danger"></i>${escapeHtml(item)}
                            </li>
                        `).join('')}
                    </ul>
                </div>
            `;
            }

            htmlContent += `
                <div class="mt-4">
                    <label for="swal-reason" class="form-label fw-bold small text-primary">
                        <i class="bi bi-chat-left-text me-1"></i> ALASAN PENGEMBALIAN <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control border-primary shadow-sm rounded-3"
                              id="swal-reason" rows="3"
                              placeholder="Contoh: Salah input data administrasi..."></textarea>
                    <div class="form-text mt-1 text-muted" style="font-size: 0.75rem;">Minimal 5 karakter wajib diisi.</div>
                </div>
            </div>
        `;

            const { value: reason, isConfirmed } = await Swal.fire({
                title: 'Konfirmasi Pengembalian',
                html: htmlContent,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Ya, Kembalikan',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-primary bg-gradient px-4 py-2 mx-2 rounded-pill shadow-sm',
                    cancelButton: 'btn btn-light px-4 py-2 mx-2 rounded-pill',
                    popup: 'rounded-4 shadow-lg border-0'
                },
                focusConfirm: false,
                preConfirm: () => {
                    const reasonInput = document.getElementById('swal-reason').value.trim();
                    if (!reasonInput || reasonInput.length < 5) {
                        Swal.showValidationMessage('Alasan wajib diisi minimal 5 karakter');
                        return false;
                    }
                    return reasonInput;
                }
            });

            if (!isConfirmed) {
                btn.disabled = false;
                btn.innerHTML = originalContent;
                return;
            }

            // Proses Submit ke Server
            const submitRes = await fetch('/cms/document-return/return', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    accident_id: accidentId,
                    document_id: docId,
                    document_type: docType,
                    reason: reason
                })
            });

            const result = await submitRes.json();

            if (result.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message || 'Dokumen berhasil dikembalikan.',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4' }
                });

                const listEl = document.getElementById(`doc-list-${accidentId}`);
                if (listEl) {
                    listEl.dataset.loaded = 'false';
                    loadDocuments(accidentId);
                }
            } else {
                throw new Error(result.message || 'Gagal melakukan pengembalian');
            }

        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: err.message,
                customClass: {
                    confirmButton: 'btn btn-primary rounded-pill px-4',
                    popup: 'rounded-4'
                },
                buttonsStyling: false
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    });

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
