// Modern Dashboard JavaScript

// Global variables for dashboard functionality
let autoReloadInterval;
let isAutoReloadEnabled = true;
let countdownTimer;
let remainingTime = 600; // 600 seconds = 10 minutes

// Define modern color palette
const modernColors = [
    '#2563eb', // blue
    '#10b981', // green
    '#f59e0b', // amber
    '#ef4444', // red
    '#8b5cf6', // violet
    '#ec4899', // pink
    '#14b8a6', // teal
    '#f97316', // orange
    '#6366f1', // indigo
    '#84cc16'  // lime
];

// Initialize chart objects (will be set by dashboard)
var dashBarChart;
var dashPieChart;
var dashPie2Chart;

// Set theme based on localStorage or default to light
document.addEventListener('DOMContentLoaded', function () {
    // Initialize dark mode
    initializeDarkMode();

    // Initialize time display
    updateTime();
    setInterval(updateTime, 1000);

    // Add loading overlay
    setupLoadingOverlay();

    // Enhance charts with modern settings
    enhanceCharts();

    // Add event listeners
    setupEventListeners();

    // Initialize auto reload system
    initializeAutoReload();

    // Initialize dashboard specific functions if they exist
    setTimeout(() => {
        console.log('Modern Dashboard: Initializing dashboard functions...');
        if (typeof loadDashBarChart === 'function') {
            console.log('Modern Dashboard: Loading bar chart...');
            loadDashBarChart();
        }
        if (typeof loadPieChart === 'function') {
            console.log('Modern Dashboard: Loading pie charts...');
            loadPieChart();
        }
        if (typeof updateContent === 'function') {
            console.log('Modern Dashboard: Updating content...');
            updateContent();
        }
        console.log('Modern Dashboard: Initialization complete!');
    }, 100);
});

// Dark mode functionality
function initializeDarkMode() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (!darkModeToggle) return;

    const isDarkMode = localStorage.getItem('darkMode') === 'true';

    if (isDarkMode) {
        document.body.classList.add('dark-mode');
        darkModeToggle.innerHTML = '<i class="bi bi-sun"></i>';
    }

    darkModeToggle.addEventListener('click', function () {
        document.body.classList.toggle('dark-mode');
        const isNowDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', isNowDark);

        darkModeToggle.innerHTML = isNowDark ?
            '<i class="bi bi-sun"></i>' :
            '<i class="bi bi-moon-stars"></i>';

        // Update charts with new theme
        refreshCharts();
    });
}

// Update time display
function updateTime() {
    const timeElement = document.getElementById('currentTime');
    if (!timeElement) return;

    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    timeElement.textContent = `${hours}:${minutes}:${seconds}`;
}

// Setup loading overlay
function setupLoadingOverlay() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (!loadingOverlay) return;

    // Show loading on AJAX start
    $(document).ajaxStart(function () {
        loadingOverlay.classList.remove('d-none');
    });

    // Hide loading on AJAX stop
    $(document).ajaxStop(function () {
        loadingOverlay.classList.add('d-none');
    });
}

// Global settings for enhancing chart appearance
function enhanceCharts() {
    if (typeof Highcharts === 'undefined') return;

    // Add custom chart styling first
    addChartStyling();

    // Set global options for all charts
    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'Inter, sans-serif'
            },
            backgroundColor: 'transparent'
        },
        colors: modernColors,
        title: {
            style: {
                fontWeight: '600',
                fontSize: '16px'
            }
        },
        subtitle: {
            style: {
                fontSize: '13px',
                opacity: 0.8
            }
        },
        xAxis: {
            labels: {
                style: {
                    fontSize: '12px'
                }
            },
            lineColor: 'rgba(0, 0, 0, 0.1)',
            gridLineColor: 'rgba(0, 0, 0, 0.05)'
        },
        yAxis: {
            labels: {
                style: {
                    fontSize: '12px'
                }
            },
            gridLineColor: 'rgba(0, 0, 0, 0.1)'
        },
        legend: {
            itemStyle: {
                fontWeight: '500',
                fontSize: '13px'
            }
        },
        plotOptions: {
            series: {
                borderRadius: 4,
                animation: {
                    duration: 1000
                }
            },
            column: {
                borderRadius: 4
            },
            pie: {
                dataLabels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            borderWidth: 0,
            borderRadius: 8,
            shadow: true,
            style: {
                fontSize: '12px'
            },
            headerFormat: '<span style="font-size: 12px; font-weight: 600;">{point.key}</span><br/>'
        },
        credits: {
            enabled: false
        }
    });
}

// Setup event listeners
function setupEventListeners() {
    const refreshBtn = document.getElementById('refreshData');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            // Reload data and refresh charts
            if (typeof loadDashBarChart === 'function') loadDashBarChart();
            if (typeof loadPieChart === 'function') loadPieChart();
            if (typeof updateContent === 'function') updateContent();
        });
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function (e) {
        // Ctrl/Cmd + R to refresh data
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            if (typeof loadDashBarChart === 'function') loadDashBarChart();
            if (typeof loadPieChart === 'function') loadPieChart();
            if (typeof updateContent === 'function') updateContent();
        }

        // Space to toggle auto reload
        if (e.key === ' ' && e.target === document.body) {
            e.preventDefault();
            toggleAutoReload();
        }
    });
}

// Refresh charts with new theme
function refreshCharts() {
    if (typeof dashBarChart !== 'undefined' && dashBarChart) {
        dashBarChart.update({
            chart: {
                backgroundColor: 'transparent'
            }
        });
    }

    if (typeof dashPieChart !== 'undefined' && dashPieChart) {
        dashPieChart.update({
            chart: {
                backgroundColor: 'transparent'
            }
        });
    }

    if (typeof dashPie2Chart !== 'undefined' && dashPie2Chart) {
        dashPie2Chart.update({
            chart: {
                backgroundColor: 'transparent'
            }
        });
    }
}

// Dark mode styles
document.addEventListener('DOMContentLoaded', function () {
    const style = document.createElement('style');
    style.textContent = `
        .dark-mode {
            --body-bg: #0f172a;
            --card-bg: #1e293b;
            --text-color: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: #334155;
        }
        
        .dark-mode .card {
            background-color: var(--card-bg);
            color: var(--text-color);
        }
        
        .dark-mode .table {
            color: var(--text-color);
        }
        
        .dark-mode .table th {
            background-color: #334155;
            border-color: #475569;
        }
        
        .dark-mode .table td {
            border-color: #475569;
        }
        
        .dark-mode .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .dark-mode .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .dark-mode .text-muted {
            color: #94a3b8 !important;
        }
        
        .dark-mode .progress-custom {
            background-color: #334155;
        }
        
        .dark-mode .card-dash {
            border-left-color: #3b82f6;
        }
        
        .dark-mode .card-dash .icons-dashboard {
            color: #3b82f6;
        }
        
        .dark-mode .card-dash .fw-7 {
            color: #cbd5e1;
        }
        
        .dark-mode .card-dash h2 {
            color: #3b82f6 !important;
        }
    `;
    document.head.appendChild(style);
});

// ==============================================
// UTILITY FUNCTIONS
// ==============================================

/**
 * Format number with thousands separator (dot)
 * @param {number} num - Number to format
 * @returns {string} Formatted number string
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

/**
 * Parse formatted number string back to integer
 * @param {string} str - Formatted number string
 * @returns {number} Parsed integer
 */
function parseFormattedNumber(str) {
    return parseInt(str.toString().replace(/\./g, '')) || 0;
}

/**
 * Animate counter from start to end value
 * @param {string} id - Element ID to update
 * @param {number} start - Starting value
 * @param {number} end - Ending value
 */
function animateCounter(id, start, end) {
    let current = start;
    const increment = end > start ? 1 : -1;
    const step = Math.abs(end - start) / 50;
    const timer = setInterval(function () {
        current += increment * step;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            clearInterval(timer);
            current = end;
        }
        $('#' + id).text(formatNumber(Math.round(current)));
    }, 10);
}

/**
 * Format seconds to minutes:seconds format
 * @param {number} seconds - Seconds to format
 * @returns {string} Formatted time string
 */
function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

// ==============================================
// CHART STYLING FUNCTIONS
// ==============================================

// Add custom chart styling
function addChartStyling() {
    if (document.getElementById('customChartStyles')) return;

    const chartStyles = document.createElement('style');
    chartStyles.id = 'customChartStyles';
    chartStyles.textContent = `
        .highcharts-container {
            border-radius: 12px !important;
            overflow: hidden;
        }
        
        .highcharts-background {
            rx: 12 !important;
            ry: 12 !important;
        }
        
        .highcharts-tooltip {
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.1)) !important;
        }
        
        .highcharts-data-label text {
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1)) !important;
        }
        
        .highcharts-series-hover .highcharts-point {
            filter: brightness(1.1) !important;
        }
        
        #dashBar, #dashPie, #dashPie2 {
            border-radius: 12px !important;
        }
        
        .highcharts-credits {
            display: none !important;
        }
        
        .highcharts-legend-item text {
            fill: #64748b !important;
            font-family: 'Inter', sans-serif !important;
        }
        
        .highcharts-axis-labels text {
            fill: #64748b !important;
            font-family: 'Inter', sans-serif !important;
        }
    `;
    document.head.appendChild(chartStyles);
}

// ==============================================
// AUTO RELOAD SYSTEM
// ==============================================

// Initialize auto reload system
function initializeAutoReload() {
    addAutoReloadStyles();
    createAutoReloadIndicator();
    startCountdown();
}

// Create auto reload indicator UI
function createAutoReloadIndicator() {
    try {
        // Remove existing indicator if any
        const existingIndicator = document.getElementById('autoReloadIndicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }

        const indicator = document.createElement('div');
        indicator.id = 'autoReloadIndicator';
        indicator.className = 'auto-reload-indicator';
        indicator.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <div class="auto-reload-dot"></div>
                <span class="auto-reload-text">Memperbarui data dalam <span id="countdown">${formatTime(remainingTime)}</span></span>
                <button class="btn-toggle-auto-reload" id="toggleAutoReload" title="Pause auto reload">
                    <i class="bi bi-pause-fill"></i>
                </button>
            </div>
        `;
        document.body.appendChild(indicator);

        // Add event listener to toggle button
        const toggleBtn = document.getElementById('toggleAutoReload');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleAutoReload);
        }
    } catch (error) {
        console.error('Error creating auto reload indicator:', error);
    }
}

// Add CSS styles for auto reload indicator
function addAutoReloadStyles() {
    if (document.getElementById('autoReloadStyles')) return;

    const autoReloadStyles = document.createElement('style');
    autoReloadStyles.id = 'autoReloadStyles';
    autoReloadStyles.textContent = `
        .auto-reload-indicator {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 1050;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            transition: all 0.3s ease;
        }
        
        .auto-reload-indicator:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
        }
        
        .auto-reload-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .btn-toggle-auto-reload {
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .btn-toggle-auto-reload:hover {
            background: rgba(100, 116, 139, 0.1);
            color: #475569;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    `;
    document.head.appendChild(autoReloadStyles);
}

// Start countdown timer
function startCountdown() {
    stopCountdown(); // Clear any existing timer

    countdownTimer = setInterval(() => {
        remainingTime--;

        const countdownElement = document.getElementById('countdown');
        if (countdownElement) {
            countdownElement.textContent = formatTime(remainingTime);
        }

        if (remainingTime <= 0) {
            performAutoReload();
        }
    }, 1000);
}

// Stop countdown timer
function stopCountdown() {
    if (countdownTimer) {
        clearInterval(countdownTimer);
        countdownTimer = null;
    }
}

// Reset countdown timer
function resetCountdown() {
    stopCountdown();
    remainingTime = 600; // Reset to 10 minutes
    const countdownElement = document.getElementById('countdown');
    if (countdownElement) {
        countdownElement.textContent = formatTime(remainingTime);
    }
}

// Toggle auto reload functionality
function toggleAutoReload() {
    isAutoReloadEnabled = !isAutoReloadEnabled;
    const toggleBtn = document.getElementById('toggleAutoReload');
    const dot = document.querySelector('.auto-reload-dot');

    if (isAutoReloadEnabled) {
        toggleBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
        toggleBtn.title = 'Pause auto reload';
        if (dot) dot.style.backgroundColor = '#10b981';
        startCountdown();
    } else {
        toggleBtn.innerHTML = '<i class="bi bi-play-fill"></i>';
        toggleBtn.title = 'Resume auto reload';
        if (dot) dot.style.backgroundColor = '#ef4444';
        stopCountdown();
    }
}

// Perform auto reload
function performAutoReload() {
    if (!isAutoReloadEnabled) return;

    console.log('Performing auto reload...');
    resetCountdown();

    // Call dashboard specific functions (these should be defined in the view)
    if (typeof loadDashBarChart === 'function') loadDashBarChart();
    if (typeof loadPieChart === 'function') loadPieChart();
    if (typeof updateContent === 'function') updateContent();

    if (isAutoReloadEnabled) {
        startCountdown();
    }
}
