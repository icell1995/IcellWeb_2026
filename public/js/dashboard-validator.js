// Update current time every second
function updateCurrentTime() {
    const now = new Date();
    let hours = now.getHours();
    let minutes = now.getMinutes();
    let seconds = now.getSeconds();

    // Add leading zeros
    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;

    document.getElementById('currentTime').textContent = hours + ':' + minutes + ':' + seconds;
}

// Document ready function
$(document).ready(function () {
    console.log('Dashboard validator script loaded at: ' + new Date().toISOString());

    // Verify Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded. Charts will not be displayed.');
        // Display error message on chart containers
        displayChartError('validationTrendChart', 'Chart.js tidak dimuat dengan benar');
        displayChartError('documentTypeChart', 'Chart.js tidak dimuat dengan benar');
    } else {
        console.log('Chart.js is loaded correctly. Version:', Chart.version);
    }

    // Update time immediately and then every second
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);

    // Refresh button with animation
    $('#refreshData').on('click', function () {
        // Add spinning animation
        const $icon = $(this).find('i');
        $icon.addClass('fa-spin');

        // Create overlay for loading effect
        const $overlay = $(
            '<div class="loading-overlay d-flex flex-column align-items-center justify-content-center"><div class="spinner-border text-primary mb-3" role="status"></div><div>Memperbarui data...</div></div>'
        );
        $('body').append($overlay);
        $overlay.fadeIn(200);

        // AJAX request instead of full page reload
        $.ajax({
            url: window.location.href,
            type: 'GET',
            data: {
                ajax: true,
                range: $('a.filter-btn.active').data('range') || 'week'
            },
            dataType: 'json',
            success: function (response) {
                // Update dashboard data
                updateDashboardData(response);

                // Remove animation and overlay
                setTimeout(function () {
                    $icon.removeClass('fa-spin');
                    $overlay.fadeOut(200, function () {
                        $overlay.remove();
                    });
                }, 500);
            },
            error: function () {
                // Fallback to page reload on error
                setTimeout(function () {
                    location.reload();
                }, 800);
            }
        });
    });

    // Function to update dashboard data with AJAX response
    function updateDashboardData(data) {
        // This would be implemented if backend returns JSON data
        // For now, we'll leave it as a page reload
        location.reload();
    }

    // Custom date range validation
    $('#customDateModal form').on('submit', function (e) {
        const startDate = new Date($('#startDate').val());
        const endDate = new Date($('#endDate').val());

        if (endDate < startDate) {
            e.preventDefault();
            alert('Tanggal akhir tidak boleh sebelum tanggal mulai');
            return false;
        }

        // Show loading overlay
        const $overlay = $(
            '<div class="loading-overlay d-flex flex-column align-items-center justify-content-center"><div class="spinner-border text-primary mb-3" role="status"></div><div>Memuat data...</div></div>'
        );
        $('body').append($overlay);
        $overlay.fadeIn(200);
    });

    // Filter leaderboard
    $('.filter-leaderboard').on('click', function (e) {
        e.preventDefault();
        const range = $(this).data('range');

        // Update active state
        $('.filter-leaderboard').removeClass('active');
        $(this).addClass('active');

        // Load leaderboard data
        loadLeaderboard(range);
    });

    function loadLeaderboard(range) {
        $('#validatorLeaderboard').html(
            '<li class="list-group-item text-center py-4"><div class="spinner-border text-primary" role="status"></div></li>'
        );

        $.ajax({
            url: leaderboardUrl,
            type: "GET",
            data: {
                range: range
            },
            success: function (response) {
                if (response.topValidators.length === 0) {
                    $('#validatorLeaderboard').html(`
                        <li class="list-group-item text-center py-4">
                            <i class="bi bi-emoji-neutral fs-3 d-block mb-2 text-muted"></i>
                            <p class="mb-0 text-muted">Belum ada data validator</p>
                        </li>
                    `);
                    return;
                }

                let html = '';
                response.topValidators.forEach((validator, index) => {
                    html += `
                        <li class="list-group-item leader-card ${index < 3 ? 'rank-' + (index + 1) : ''}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 position-relative">
                                        ${index < 3 ?
                            `<span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-${index == 0 ? 'warning' : (index == 1 ? 'secondary' : 'danger')}">
                                                ${index + 1}
                                            </span>` :
                            `<span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-light text-dark">
                                                ${index + 1}
                                            </span>`
                        }
                                        <div class="rounded-circle bg-light d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                            <span class="fw-bold text-secondary">${validator.name.substring(0, 2).toUpperCase()}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">${validator.name}</h6>
                                        <small class="text-muted">${validator.role || ''}</small>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill">${validator.validation_count}</span>
                            </div>
                        </li>
                    `;
                });

                $('#validatorLeaderboard').html(html);
            },
            error: function () {
                $('#validatorLeaderboard').html(`
                    <li class="list-group-item text-center py-4">
                        <i class="bi bi-exclamation-triangle fs-3 d-block mb-2 text-warning"></i>
                        <p class="mb-0 text-muted">Gagal memuat data</p>
                    </li>
                `);
            }
        });
    }

    // Initialize validation trend chart
    try {
        if (typeof Chart !== 'undefined') {
            initValidationTrendChart();
        }
    } catch (error) {
        console.error('Error initializing validation trend chart:', error);
        // Menampilkan pesan error pada canvas chart
        displayChartError('validationTrendChart', 'Gagal memuat chart: ' + error.message);
    }

    // Initialize document type chart
    try {
        if (typeof Chart !== 'undefined') {
            initDocumentTypeChart();
        }
    } catch (error) {
        console.error('Error initializing document type chart:', error);
        // Menampilkan pesan error pada canvas chart
        displayChartError('documentTypeChart', 'Gagal memuat chart: ' + error.message);
    }

    // Set default values for date inputs
    const today = new Date();
    const weekAgo = new Date();
    weekAgo.setDate(today.getDate() - 7);

    // Format dates as YYYY-MM-DD
    const formatDate = (date) => {
        const d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
        return [year, month.padStart(2, '0'), day.padStart(2, '0')].join('-');
    };

    $('#startDate').val(formatDate(weekAgo));
    $('#endDate').val(formatDate(today));
});

// Helper function to display error messages on chart canvases
function displayChartError(canvasId, errorMessage) {
    const chartElement = document.getElementById(canvasId);
    if (!chartElement) {
        console.error('Cannot display error: Chart element ' + canvasId + ' not found');
        return;
    }

    try {
        const ctx = chartElement.getContext('2d');
        ctx.clearRect(0, 0, chartElement.width, chartElement.height);
        ctx.font = '14px Arial';
        ctx.fillStyle = '#dc3545';
        ctx.textAlign = 'center';
        ctx.fillText(errorMessage, chartElement.width / 2, chartElement.height / 2);
    } catch (err) {
        console.error('Error displaying chart error message:', err);
    }
}

// Initialize validation trend chart
function initValidationTrendChart() {
    // Verify that Chart is defined
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded. Cannot create validation trend chart.');
        return null;
    }

    const chartElement = document.getElementById('validationTrendChart');
    if (!chartElement) {
        console.error('Chart element validationTrendChart not found');
        return null;
    }

    const trendCtx = chartElement.getContext('2d');

    // Check if we have valid data
    if (!validationTrendData || !validationTrendData.labels || !validationTrendData.validated || !validationTrendData.pending) {
        console.error('Invalid validation trend data structure:', validationTrendData);
        displayChartError('validationTrendChart', 'Data tidak valid untuk menampilkan chart');
        return null;
    }

    // Clear any existing chart instance
    if (window.validationTrendChart) {
        try {
            if (typeof window.validationTrendChart.destroy === 'function') {
                window.validationTrendChart.destroy();
                console.log('Previous validation trend chart destroyed successfully');
            } else {
                console.warn('Chart instance has no destroy method');
            }
        } catch (error) {
            console.error('Error destroying previous chart:', error);
        }

        // Make sure we remove the reference regardless
        window.validationTrendChart = null;
    }

    // Create a new chart instance
    try {
        window.validationTrendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: validationTrendData.labels,
                datasets: [{
                    label: 'Dokumen Divalidasi',
                    data: validationTrendData.validated,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0d6efd',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBorderWidth: 2,
                    fill: true
                },
                {
                    label: 'Menunggu Validasi',
                    data: validationTrendData.pending,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#ffc107',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBorderWidth: 2,
                    fill: true
                }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#333',
                        bodyColor: '#666',
                        borderColor: '#ddd',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        cornerRadius: 8,
                        titleFont: {
                            weight: 'bold',
                        },
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    intersect: false,
                    axis: 'x'
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            lineWidth: 0.5
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#6c757d'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            lineWidth: 0.5
                        }
                    }
                },
                elements: {
                    line: {
                        borderWidth: 2
                    }
                }
            }
        });

        console.log('Validation trend chart created successfully');
        return window.validationTrendChart;
    } catch (error) {
        console.error('Failed to create validation trend chart:', error);
        displayChartError('validationTrendChart', 'Gagal membuat chart: ' + error.message);
        return null;
    }
}

// Initialize document type chart
function initDocumentTypeChart() {
    // Verify that Chart is defined
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded. Cannot create document type chart.');
        return null;
    }

    const chartElement = document.getElementById('documentTypeChart');
    if (!chartElement) {
        console.error('Chart element documentTypeChart not found');
        return null;
    }

    const docTypeCtx = chartElement.getContext('2d');

    // Check if we have valid data
    if (!documentTypeData || !Array.isArray(documentTypeData) || documentTypeData.length === 0) {
        console.error('Invalid document type data structure:', documentTypeData);
        displayChartError('documentTypeChart', 'Data tidak valid untuk menampilkan chart');
        return null;
    }

    // Clear any existing chart instance
    if (window.documentTypeChart) {
        try {
            if (typeof window.documentTypeChart.destroy === 'function') {
                window.documentTypeChart.destroy();
                console.log('Previous document type chart destroyed successfully');
            } else {
                console.warn('Chart instance has no destroy method');
            }
        } catch (error) {
            console.error('Error destroying previous chart:', error);
        }

        // Make sure we remove the reference regardless
        window.documentTypeChart = null;
    }

    // Prepare data
    const labels = documentTypeData.map(item => item.document_category_name || 'Tidak Ada Kategori');
    const values = documentTypeData.map(item => parseInt(item.count) || 0);

    // Create a new chart instance
    try {
        window.documentTypeChart = new Chart(docTypeCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        '#0d6efd', '#6f42c1', '#d63384', '#dc3545',
                        '#fd7e14', '#ffc107', '#198754', '#20c997',
                        '#0dcaf0', '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverBorderColor: '#ffffff',
                    hoverBorderWidth: 3,
                    hoverOffset: 10,
                    spacing: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                radius: '85%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#333',
                        bodyColor: '#666',
                        borderColor: '#ddd',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        boxPadding: 3,
                        titleFont: {
                            weight: 'bold'
                        },
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' dokumen';
                                }
                                return label;
                            }
                        }
                    }
                },
                layout: {
                    padding: 20
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        console.log('Document type chart created successfully');
        return window.documentTypeChart;
    } catch (error) {
        console.error('Failed to create document type chart:', error);
        displayChartError('documentTypeChart', 'Gagal membuat chart: ' + error.message);
        return null;
    }
}