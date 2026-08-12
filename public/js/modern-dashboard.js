// Global variables for dashboard functionality
let autoReloadInterval;
let isAutoReloadEnabled = true;
let countdownTimer;
const AUTO_RELOAD_TIME = 600; // 600 seconds = 10 minutes (change to lower value for testing)
let remainingTime = AUTO_RELOAD_TIME;

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

    // Enhance charts with modern settings
    enhanceCharts();

    // Add event listeners
    setupEventListeners();

    // Initialize auto reload system
    initializeAutoReload();

    // Add loading overlay
    setupLoadingOverlay();

    // Initialize dashboard specific functions if they exist
    setTimeout(() => {
        if (typeof loadDashBarChart === 'function') {
            loadDashBarChart();
        }
        if (typeof loadPieChart === 'function') {
            loadPieChart();
        }
        if (typeof updateContent === 'function') {
            updateContent();
        }
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
        // Stop countdown while loading
        stopCountdown();
    });

    // Hide loading on AJAX stop
    $(document).ajaxStop(function () {
        loadingOverlay.classList.add('d-none');
        console.log('Loading overlay hidden - restarting countdown');
        // Restart countdown after loading is complete
        if (isAutoReloadEnabled) {
            resetCountdown();
            startCountdown();
        }
    });

    // Also watch for manual changes to loading overlay visibility
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const target = mutation.target;
                if (target.classList.contains('d-none')) {
                    // Loading overlay is hidden
                    console.log('MutationObserver: Loading overlay hidden');
                    if (isAutoReloadEnabled && !countdownTimer) {
                        setTimeout(() => {
                            console.log('Restarting countdown after overlay hidden');
                            resetCountdown();
                            startCountdown();
                        }, 500); // Small delay to ensure everything is loaded
                    }
                } else {
                    // Loading overlay is shown
                    console.log('MutationObserver: Loading overlay shown - stopping countdown');
                    stopCountdown();
                }
            }
        });
    });

    // Start observing
    observer.observe(loadingOverlay, {
        attributes: true,
        attributeFilter: ['class']
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
            // Option 1: Page reload (current behavior)
            window.location.reload();
            
            // Option 2: Manual refresh without page reload (uncomment to use)
            // resetAndRestartCountdown();
        });
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function (e) {
        // Ctrl/Cmd + R to refresh data
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            window.location.reload();
        }

        // Space to toggle auto reload
        if (e.key === ' ' && e.target === document.body) {
            e.preventDefault();
            toggleAutoReload();
        }

        // F5 to manual refresh and restart countdown (without page reload)
        if (e.key === 'F5') {
            e.preventDefault();
            resetAndRestartCountdown();
        }
    });
}

// Reset and restart countdown (for manual refresh without page reload)
function resetAndRestartCountdown() {
    if (!isAutoReloadEnabled) return;
    
    console.log('Manual refresh: resetting and restarting countdown');
    resetCountdown();
    startCountdown();
    
    // Optional: trigger a visual feedback
    const indicator = document.getElementById('autoReloadIndicator');
    if (indicator) {
        indicator.style.transform = 'scale(1.05)';
        setTimeout(() => {
            indicator.style.transform = 'scale(1)';
        }, 200);
    }
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
    // Ensure seconds is not negative
    const positiveSeconds = Math.max(0, seconds);
    const mins = Math.floor(positiveSeconds / 60);
    const secs = positiveSeconds % 60;
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
            <div class="d-flex align-items-center gap-2 object-fit-scale">
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
            z-index: 1;
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
        
        .auto-reload-text.updating {
            color: #f59e0b !important;
            font-weight: 600 !important;
        }

        @keyframes updatePulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.02);
            }
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

    if (!isAutoReloadEnabled) return; // Don't start if auto reload is disabled

    countdownTimer = setInterval(() => {
        if (!isAutoReloadEnabled || remainingTime <= 0) {
            stopCountdown(); // Stop the timer if disabled or time is up
            if (isAutoReloadEnabled && remainingTime <= 0) {
                // Update text and visual indicators to show "Memperbarui data..."
                const textElement = document.querySelector('.auto-reload-text');
                const dot = document.querySelector('.auto-reload-dot');

                if (textElement) {
                    textElement.innerHTML = 'Sedang memperbarui data...';
                    textElement.classList.add('updating');
                }
                // Change dot color to indicate loading
                if (dot) {
                    dot.style.backgroundColor = '#f59e0b'; // amber color
                    dot.style.animation = 'pulse 1s infinite'; // faster pulse
                }
                //btn-toggle-auto-reload hide
                const btnToggleAutoReload = document.querySelector('.btn-toggle-auto-reload');
                if (btnToggleAutoReload) {
                    btnToggleAutoReload.style.display = 'none';
                }

                performAutoReload();
            }
            return;
        }

        remainingTime--;

        const countdownElement = document.getElementById('countdown');
        const textElement = document.querySelector('.auto-reload-text');

        if (remainingTime <= 0) {
            // Update text and visual indicators to show "Memperbarui data..."
            const dot = document.querySelector('.auto-reload-dot');

            if (textElement) {
                textElement.innerHTML = 'Sedang memperbarui data...';
                textElement.classList.add('updating');
            }

            // Change dot color to indicate loading
            if (dot) {
                dot.style.backgroundColor = '#f59e0b'; // amber color
                dot.style.animation = 'pulse 1s infinite'; // faster pulse
            }

            stopCountdown(); // Stop the timer before performing reload
            performAutoReload();
        } else {
        // Normal countdown display
            if (countdownElement) {
                countdownElement.textContent = formatTime(remainingTime);
            }
            // Ensure text shows countdown format
            if (textElement) {
                textElement.innerHTML = `Memperbarui data dalam <span id="countdown">${formatTime(remainingTime)}</span>`;
            }
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

    setTimeout(() => {
        remainingTime = AUTO_RELOAD_TIME; // Reset to configured time
    },1000);

    const countdownElement = document.getElementById('countdown');
    const textElement = document.querySelector('.auto-reload-text');
    const dot = document.querySelector('.auto-reload-dot');

    if (countdownElement) {
        countdownElement.textContent = formatTime(remainingTime);
    }

    // Reset text to normal countdown format
    if (textElement) {
        textElement.innerHTML = `Memperbarui data dalam <span id="countdown">${formatTime(remainingTime)}</span>`;
        textElement.classList.remove('updating');
    }

    // Reset dot color and animation
    if (dot) {
        dot.style.backgroundColor = '#10b981'; // green color
        dot.style.animation = 'pulse 2s infinite'; // normal pulse
    }
}

// Toggle auto reload functionality
function toggleAutoReload() {
    isAutoReloadEnabled = !isAutoReloadEnabled;
    const toggleBtn = document.getElementById('toggleAutoReload');
    const dot = document.querySelector('.auto-reload-dot');
    const textElement = document.querySelector('.auto-reload-text');

    if (isAutoReloadEnabled) {
        toggleBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
        toggleBtn.title = 'Pause auto reload';
        if (dot) {
            dot.style.backgroundColor = '#10b981'; // green color
            dot.style.animation = 'pulse 2s infinite'; // normal pulse
        }

        // Reset text to countdown format when enabling auto reload
        if (textElement) {
            textElement.innerHTML = `Memperbarui data dalam <span id="countdown">${formatTime(remainingTime)}</span>`;
            textElement.classList.remove('updating');
        }

        startCountdown();
    } else {
        toggleBtn.innerHTML = '<i class="bi bi-play-fill"></i>';
        toggleBtn.title = 'Resume auto reload';
        if (dot) {
            dot.style.backgroundColor = '#ef4444'; // red color when paused
            dot.style.animation = 'pulse 2s infinite';
        }
        stopCountdown();
    }
}

// Perform auto reload
function performAutoReload() {
    if (!isAutoReloadEnabled) return;

    // Simple page reload
    window.location.reload();
}
