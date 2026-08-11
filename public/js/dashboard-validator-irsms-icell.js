/**
 * Dashboard Validator IRSMS & ICELL
 * Enhanced version with auto-reload and modern UI features
 */

// Global variables for auto reload system
let autoReloadInterval;
let isAutoReloadEnabled = true;
let countdownTimer;
let remainingTime = 300; // 300 seconds = 5 minutes

// Update current time every second
function updateCurrentTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    const currentTimeElement = document.getElementById('currentTime');
    if (currentTimeElement) {
        currentTimeElement.textContent = `${hours}:${minutes}:${seconds}`;
    }
}

// Logo loading management
function handleLogoError(logoElement) {
    console.warn('Logo failed to load:', logoElement.src);
    logoElement.classList.add('logo-error');
    logoElement.alt = 'Logo tidak dapat dimuat';
    logoElement.title = 'Logo tidak dapat dimuat';
}

// Initialize logo error handling
function initLogoHandling() {
    const logos = document.querySelectorAll('.dashboard-logo');
    logos.forEach(logo => {
        logo.addEventListener('error', () => handleLogoError(logo));
        logo.addEventListener('load', () => {
            logo.classList.remove('logo-error');
        });
    });
}

// Dark mode toggle functionality
function initDarkMode() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;
    const icon = darkModeToggle?.querySelector('i');

    if (!darkModeToggle || !icon) return;

    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        icon.classList.remove('bi-moon-stars');
        icon.classList.add('bi-sun');
    }

    darkModeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');

        if (body.classList.contains('dark-mode')) {
            icon.classList.remove('bi-moon-stars');
            icon.classList.add('bi-sun');
            localStorage.setItem('theme', 'dark');
        } else {
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon-stars');
            localStorage.setItem('theme', 'light');
        }
    });
}

// Pulse alert checker for high values
function checkPulseAlerts() {
    try {
        const irsmsPendingElement = document.querySelector('.stats-card.warning:first-of-type .stats-number');
        const icellPendingElement = document.querySelector('.stats-card.warning:last-of-type .stats-number');

        if (irsmsPendingElement) {
            const irsmsValue = parseInt(irsmsPendingElement.textContent.replace(/[^\d]/g, ''));
            const irsmsCard = irsmsPendingElement.closest('.stats-card');

            if (irsmsValue > 100) {
                irsmsCard?.classList.add('pulse-alert');
            } else {
                irsmsCard?.classList.remove('pulse-alert');
            }
        }

        if (icellPendingElement) {
            const icellValue = parseInt(icellPendingElement.textContent.replace(/[^\d]/g, ''));
            const icellCard = icellPendingElement.closest('.stats-card');

            if (icellValue > 50) {
                icellCard?.classList.add('pulse-alert');
            } else {
                icellCard?.classList.remove('pulse-alert');
            }
        }
    } catch (error) {
        console.warn('Error checking pulse alerts:', error);
    }
}

// Progress bar animation
function initProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar-custom');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
}

// Stats card hover effects
function initStatsCardEffects() {
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('mouseenter', function () {
            if (!this.classList.contains('pulse-alert')) {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            }
        });

        card.addEventListener('mouseleave', function () {
            if (!this.classList.contains('pulse-alert')) {
                this.style.transform = 'translateY(0) scale(1)';
            }
        });
    });
}

// Parallax effect for header
function initParallaxEffect() {
    window.addEventListener('scroll', function () {
        const scrolled = window.pageYOffset;
        const header = document.querySelector('.dashboard-header');
        if (header) {
            header.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
}

// Create skeleton loading elements
function createSkeletonElements() {
    return {
        statsCard: `
            <div class="loading-skeleton-content">
                <!-- Icon skeleton (top-right) -->
                <div class="position-absolute top-0 end-0 mt-3 me-3">
                    <div class="skeleton skeleton-icon"></div>
                </div>
                
                <!-- Content skeleton -->
                <div class="d-flex flex-column justify-content-between h-100">
                    <!-- Top text section -->
                    <div class="mb-3">
                        <div class="skeleton skeleton-text medium mb-2"></div>
                        <div class="skeleton skeleton-text short"></div>
                    </div>
                    
                    <!-- Center number -->
                    <div class="text-center my-auto">
                        <div class="skeleton skeleton-number mx-auto"></div>
                    </div>
                    
                    <!-- Bottom progress section -->
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="skeleton skeleton-text short"></div>
                            <div class="skeleton skeleton-badge"></div>
                        </div>
                        <div class="skeleton skeleton-progress"></div>
                    </div>
                </div>
            </div>
        `,
        leaderboardItem: `
            <div class="leader-item leaderboard-skeleton">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3 position-relative">
                            <!-- Rank badge (absolute positioned) -->
                            <div class="skeleton skeleton-rank" style="z-index: 10; position: absolute; top: -8px; left: -8px;"></div>
                            <!-- Avatar -->
                            <div class="skeleton skeleton-avatar"></div>
                        </div>
                        <div class="flex-grow-1">
                            <!-- Name -->
                            <div class="skeleton skeleton-text medium mb-2"></div>
                            <!-- Role and progress -->
                            <div class="d-flex align-items-center">
                                <div class="skeleton skeleton-text short me-2"></div>
                                <div class="skeleton skeleton-progress-small"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Badge count -->
                    <div class="skeleton skeleton-badge"></div>
                </div>
            </div>
        `
    };
}

// Show loading overlay
function showLoadingOverlay() {
    window.location.reload();
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.classList.remove('d-none');
}

// Hide loading overlay
function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.classList.add('d-none');
}

// Show skeleton loading
function showSkeletonLoading() {
    window.location.reload();
    const skeletons = createSkeletonElements();

    // Add skeleton to stats cards
    document.querySelectorAll('.stats-card').forEach(card => {
        if (!card.classList.contains('loading')) {
            card.classList.add('loading');
            card.insertAdjacentHTML('beforeend', skeletons.statsCard);
        }
    });

    // Add skeleton to leaderboards - target the correct content area
    document.querySelectorAll('.leaderboard-card .p-2').forEach(contentArea => {
        const parentCard = contentArea.closest('.leaderboard-card');
        if (parentCard && !parentCard.classList.contains('loading')) {
            parentCard.classList.add('loading');
            contentArea.innerHTML = Array(5).fill(skeletons.leaderboardItem).join('');
        }
    });

    // Update auto-reload indicator
    const indicator = document.getElementById('autoReloadIndicator');
    if (indicator) {
        indicator.classList.add('skeleton-mode');
        const text = indicator.querySelector('.auto-reload-text');
        if (text) {
            text.innerHTML = 'Memuat data terbaru...';
        }
    }
}

// Hide skeleton loading
function hideSkeletonLoading() {
    // Remove skeleton from stats cards
    document.querySelectorAll('.stats-card.loading').forEach(card => {
        card.classList.remove('loading');
        const skeletonContent = card.querySelector('.loading-skeleton-content');
        if (skeletonContent) {
            skeletonContent.remove();
        }
    });

    // Remove skeleton from leaderboards
    document.querySelectorAll('.leaderboard-card.loading').forEach(card => {
        card.classList.remove('loading');
        // Note: Content will be replaced by updateLeaderboardData, so no need to manually clear
    });

    // Update auto-reload indicator
    const indicator = document.getElementById('autoReloadIndicator');
    if (indicator) {
        indicator.classList.remove('skeleton-mode');
    }
}

// Format time display (MM:SS)
function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}
// Create auto reload indicator UI
function createAutoReloadIndicator() {
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
}

// Add CSS styles for auto reload indicator
function addAutoReloadStyles() {
    if (document.getElementById('autoReloadStyles')) return;

    const autoReloadStyles = document.createElement('style');
    autoReloadStyles.id = 'autoReloadStyles';
    autoReloadStyles.textContent = `
        .auto-reload-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 1040;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .auto-reload-indicator:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .auto-reload-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        .auto-reload-dot.paused {
            background: #f59e0b;
            animation: none;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .btn-toggle-auto-reload {
            background: none;
            border: none;
            color: #64748b;
            padding: 0.25rem;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-toggle-auto-reload:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #3b82f6;
        }

        .auto-reload-text {
            color: #374151;
            white-space: nowrap;
        }

        body.dark-mode .auto-reload-indicator {
            background: rgba(30, 41, 59, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .auto-reload-text {
            color: #e2e8f0;
        }

        body.dark-mode .btn-toggle-auto-reload {
            color: #94a3b8;
        }

        body.dark-mode .btn-toggle-auto-reload:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #60a5fa;
        }

        @media (max-width: 768px) {
            .auto-reload-indicator {
                bottom: 10px;
                right: 10px;
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
        }

        .auto-reload-indicator.loading .auto-reload-dot {
            background: #3b82f6;
            animation: spin 1s linear infinite;
        }

        .auto-reload-indicator.loading .auto-reload-text {
            color: #3b82f6;
        }

        .auto-reload-indicator.skeleton-mode .auto-reload-dot {
            background: #8b5cf6;
            animation: pulse-skeleton 1.5s ease-in-out infinite;
        }

        .auto-reload-indicator.skeleton-mode .auto-reload-text {
            color: #8b5cf6;
        }

        body.dark-mode .auto-reload-indicator.loading .auto-reload-text {
            color: #60a5fa;
        }

        body.dark-mode .auto-reload-indicator.skeleton-mode .auto-reload-text {
            color: #a78bfa;
        }

        @keyframes pulse-skeleton {
            0%, 100% { opacity: 0.7; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.3); }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(autoReloadStyles);
}

// Start countdown timer
function startCountdown() {
    if (countdownTimer) {
        clearInterval(countdownTimer);
        countdownTimer = null;
    }

    const countdownElement = document.getElementById('countdown');
    if (!countdownElement) {
        console.warn('Countdown element not found');
        return;
    }

    countdownTimer = setInterval(() => {
        remainingTime--;
        countdownElement.textContent = formatTime(remainingTime);

        if (remainingTime <= 0) {
            clearInterval(countdownTimer);
            countdownTimer = null;
            if (isAutoReloadEnabled) {
                performAutoReload();
            } else {
                resetCountdown();
                startCountdown();
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

// Reset countdown to initial value
function resetCountdown() {
    remainingTime = 300; // 5 minutes
    const countdownElement = document.getElementById('countdown');
    if (countdownElement) {
        countdownElement.textContent = formatTime(remainingTime);
    }
}

// Reset auto reload system
function resetAutoReload() {
    stopCountdown();
    resetCountdown();
    if (isAutoReloadEnabled) {
        startCountdown();
    }
}

// Perform auto reload with skeleton animation
function performAutoReload() {
    console.log('Starting auto reload with skeleton animation');

    // Show skeleton loading
    showSkeletonLoading();

    // Show loading overlay
    // showLoadingOverlay();

    // fetchDashboardData()
    //     .then(data => {
    //         if (data) {
    //             try {
    //                 updateDashboardData(data);
    //                 updateLeaderboardData('irsms', data.topValidatorsIrsms);
    //                 updateLeaderboardData('icell', data.topValidatorsIcell);
    //             } catch (updateError) {
    //                 console.error('Error updating dashboard data:', updateError);
    //                 showUpdateError('Terjadi kesalahan saat memperbarui data. Mencoba refresh halaman...');
    //                 setTimeout(() => {
    //                     window.location.reload();
    //                 }, 2000);
    //             }
    //         } else {
    //             console.warn('No data received from server, performing page reload');
    //             window.location.reload();
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Failed to fetch dashboard data:', error);
    //         showUpdateError('Koneksi bermasalah. Akan refresh halaman...');
    //         setTimeout(() => {
    //             window.location.reload();
    //         }, 2000);
    //     })
    //     .finally(() => {
    //         createAutoReloadIndicator();
    //         resetCountdown();
    //         if (isAutoReloadEnabled) {
    //             startCountdown();
    //         }
    //     });
    // Use AJAX to fetch fresh data instead of full page reload
    fetchDashboardData()
        .then(data => {
            if (data) {
                try {
                    updateDashboardData(data);
                    updateLeaderboardData('irsms', data.topValidatorsIrsms);
                    updateLeaderboardData('icell', data.topValidatorsIcell);
                } catch (updateError) {
                    console.error('Error updating dashboard data:', updateError);
                    // Show user-friendly error message
                    showUpdateError('Terjadi kesalahan saat memperbarui data. Mencoba refresh halaman...');
                    // Fallback to page reload after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            } else {
                console.warn('No data received from server, performing page reload');
                // Fallback to page reload if AJAX fails
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Failed to fetch dashboard data:', error);
            // Show user-friendly error message
            showUpdateError('Koneksi bermasalah. Akan refresh halaman...');
            // Fallback to page reload after short delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .finally(() => {
            // Hide skeleton and reset countdown
            hideSkeletonLoading();
            createAutoReloadIndicator();
            resetCountdown();
            if (isAutoReloadEnabled) {
                startCountdown();
            }
        });
}

// Fetch dashboard data via AJAX
async function fetchDashboardData() {
    try {
        // Add current URL parameters to maintain filters
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('ajax', 'true');
        currentUrl.searchParams.set('include_leaderboard', 'true');
        currentUrl.searchParams.set('_t', Date.now()); // prevent cache

        const response = await fetch(currentUrl.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Validate response structure
        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response format');
        }

        console.log('Dashboard data fetched successfully:', {
            hasIrsmsData: !!data.topValidatorsIrsms,
            hasIcellData: !!data.topValidatorsIcell,
            irsmsCount: data.topValidatorsIrsms ? data.topValidatorsIrsms.length : 0,
            icellCount: data.topValidatorsIcell ? data.topValidatorsIcell.length : 0
        });

        return data;
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
        return null;
    }
}

// Update dashboard data with fresh information
function updateDashboardData(data) {
    if (!data) return;

    try {
        // Update IRSMS stats
        const statsNumbers = document.querySelectorAll('.stats-number');
        if (statsNumbers.length >= 6) {
            statsNumbers[0].textContent = formatNumber(data.pendingValidationTodayIrsms || 0);
            statsNumbers[1].textContent = formatNumber(data.totalValidationIrsms || 0);
            statsNumbers[2].textContent = formatNumber(data.rejectedValidationIrsms || 0);

            // Update ICELL stats
            statsNumbers[3].textContent = formatNumber(data.pendingValidationTodayIcell || 0);
            statsNumbers[4].textContent = formatNumber(data.totalValidationIcell || 0);
            statsNumbers[5].textContent = formatNumber(data.rejectedValidationIcell || 0);
        }

        // Update progress bars and percentages
        const progressBars = document.querySelectorAll('.progress-bar-custom');
        const badges = document.querySelectorAll('.badge-custom');

        if (progressBars.length >= 2 && badges.length >= 2) {
            // IRSMS percentages
            // Ambil IRSMS percentage dari response API percentageValidate
            const irsmsPercentage = typeof data.percentageValidate === 'number'
                ? data.percentageValidate
                : (data.percentageValidateIrsms || 0); // Tambahkan fallback ke percentageValidateIrsms

            if (progressBars[0]) {
                progressBars[0].style.width = irsmsPercentage + '%';
                progressBars[0].setAttribute('aria-valuenow', irsmsPercentage);
            }
            if (badges[0]) {
                badges[0].textContent = irsmsPercentage + '%';
            }

            // ICELL percentages
            const icellPercentage = data.pendingValidationTodayPercentageIcell || 0;
            if (progressBars[1]) {
                progressBars[1].style.width = icellPercentage + '%';
                progressBars[1].setAttribute('aria-valuenow', icellPercentage);
            }
            if (badges[1]) {
                badges[1].textContent = icellPercentage + '%';
            }
        }

        // Update range display
        const rangeDisplay = document.querySelector('.rangeDisplay');
        if (rangeDisplay && data.rangeDisplay) {
            rangeDisplay.textContent = data.rangeDisplay;
        }

        // Update API status
        updateApiStatus(data.irsmsApiStatus);

        // Update leaderboards if available with error handling
        if (data.topValidatorsIrsms && Array.isArray(data.topValidatorsIrsms)) {
            updateLeaderboardData('irsms', data.topValidatorsIrsms);
        } else {
            console.warn('IRSMS validator data not available or invalid format');
            updateLeaderboardData('irsms', []);
        }

        if (data.topValidatorsIcell && Array.isArray(data.topValidatorsIcell)) {
            updateLeaderboardData('icell', data.topValidatorsIcell);
        } else {
            console.warn('ICELL validator data not available or invalid format');
            updateLeaderboardData('icell', []);
        }

        // Re-check pulse alerts after data update
        setTimeout(checkPulseAlerts, 100);

        console.log('Dashboard data updated successfully');
    } catch (error) {
        console.error('Error updating dashboard data:', error);
    }
}

// Update API status indicator
function updateApiStatus(status) {
    let apiStatusBadge = document.querySelector('.api-status-badge');
    const headerElement = document.querySelector('h3.fw-bold.text-blue-dark');

    if (!headerElement) return;

    if (status === 'connected') {
        if (!apiStatusBadge) {
            apiStatusBadge = document.createElement('span');
            apiStatusBadge.className = 'badge bg-success ms-2 align-middle fs-8 d-inline-flex align-items-center api-status-badge';
            headerElement.appendChild(apiStatusBadge);
        }

        apiStatusBadge.className = 'badge bg-success ms-2 align-middle fs-8 d-inline-flex align-items-center api-status-badge';
        apiStatusBadge.innerHTML = '<i class="bi bi-cloud-check me-1"></i><span>API Connected</span>';
    } else {
        if (!apiStatusBadge) {
            apiStatusBadge = document.createElement('span');
            apiStatusBadge.className = 'badge bg-secondary ms-2 align-middle fs-8 d-inline-flex align-items-center api-status-badge';
            headerElement.appendChild(apiStatusBadge);
        }

        apiStatusBadge.className = 'badge bg-secondary ms-2 align-middle fs-8 d-inline-flex align-items-center api-status-badge';
        apiStatusBadge.innerHTML = '<i class="bi bi-database me-1"></i><span>Local Data</span>';
    }
}

// Update leaderboard data
function updateLeaderboardData(system, validators) {
    const targetSelector = system === 'irsms'
        ? '#leaderboardIrsmsContent'
        : '#leaderboardIcellContent';
    const $targetElement = $(targetSelector);

    if (!$targetElement.length) {
        console.warn(`Leaderboard container not found for system: ${system}`);
        return;
    }

    // Handle empty or invalid data
    if (!validators || !Array.isArray(validators) || validators.length === 0) {
        $targetElement.html(`
            <div class="text-center py-5">
                <i class="bi bi-people text-muted opacity-25" style="font-size: 4rem;"></i>
                <p class="mt-3 text-muted fw-medium">Belum ada data validator</p>
                <p class="text-muted small">Data akan muncul setelah ada aktivitas validasi</p>
            </div>
        `);
        return;
    }

    try {
        let html = '';
        const maxValidationCount = validators[0]?.validation_count || 1;

        console.log(`Updating ${system} leaderboard with ${validators.length} validators`);

        // Generate ALL items with correct absolute index
        validators.forEach((validator, absoluteIndex) => {
            const isHidden = absoluteIndex >= 5; // Hide items from index 5 onwards (rank 6+)
            html += generateLeaderItem(validator, absoluteIndex, maxValidationCount, isHidden);
        });

        // Add expand button if more than 5 validators
        if (validators.length > 5) {
            const remainingCount = validators.length - 5;
            html += `
                <div class="text-center mt-3">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-4 btn-expand-leaderboard" 
                            data-target="${system === 'irsms' ? 'leaderboardIrsms' : 'leaderboardIcell'}" 
                            data-expanded="false">
                        <i class="bi bi-chevron-down me-1"></i>
                        <span class="expand-text">Lihat Semua (${remainingCount} lainnya)</span>
                    </button>
                </div>
            `;
        }

        $targetElement.html(html);
        console.log(`Successfully updated ${system} leaderboard with ${validators.length} validators`);

    } catch (error) {
        console.error(`Error updating ${system} leaderboard:`, error);
        $targetElement.html(`
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle text-warning opacity-50" style="font-size: 3rem;"></i>
                <p class="mt-3 text-muted">Gagal memuat data validator</p>
            </div>
        `);
    }
}

// Helper function to generate leader item HTML
function generateLeaderItem(validator, index, maxCount, isHidden = false) {
    const rankClass = index < 3 ? `rank-${index + 1}` : '';
    const badgeClass = index < 3 ? 'badge-success' : 'badge-primary';
    const progressClass = index < 3 ? 'bg-success' : 'bg-primary';
    const progressColor = index < 3 ? 'var(--emerald-500)' : 'var(--primary-500)';

    const validationCount = validator.validation_count || validator.validationCount || 0;
    const percentage = maxCount > 0 ? (validationCount / maxCount * 100) : 0;
    const initial = (validator.name || 'U').charAt(0).toUpperCase();
    const displayRank = index + 1;

    // Avatar HTML - dengan foto atau inisial
    let avatarHtml = '';
    if (validator.image) {
        avatarHtml = `
            <img src="${validator.image}" 
                 alt="${validator.name || 'Unknown'}" 
                 class="rounded-circle" 
                 style="width: 45px; height: 45px; object-fit: cover;"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <span class="fw-semibold" style="display: none;">${initial}</span>
        `;
    } else {
        avatarHtml = `<span class="fw-semibold">${initial}</span>`;
    }

    console.log(`Generating item - index: ${index}, rank: ${displayRank}, name: ${validator.name}, hasImage: ${!!validator.image}, isHidden: ${isHidden}`);

    return `
        <div class="leader-item ${isHidden ? 'leader-item-hidden' : ''}" 
             data-index="${index}" 
             style="${isHidden ? 'display: none;' : ''}">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-3 position-relative">
                        <div class="rank-badge ${rankClass}" style="z-index: 10; position: absolute;">
                            ${displayRank}
                        </div>
                        <div class="avatar">
                            ${avatarHtml}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-semibold">${validator.name || 'Unknown'}</h6>
                        <div class="d-flex align-items-center">
                            <small class="text-muted me-2">${validator.role || 'Validator'}</small>
                            <div class="progress-custom" style="width: 80px; height: 4px;">
                                <div class="progress-bar-custom ${progressClass}" 
                                     style="width: ${percentage}%; background: ${progressColor};">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="badge-custom ${badgeClass}">
                    ${validationCount.toLocaleString()}
                </div>
            </div>
        </div>
    `;
}

// Pastikan fungsi initializeDashboardData ada SEBELUM $(document).ready
function initializeDashboardData() {
    // Check if we have server-side data available
    if (typeof dashboardData !== 'undefined') {
        console.log('Initializing dashboard with server data');

        // Store dashboard data globally for use in filter functions
        window.dashboardData = dashboardData;

        console.log('Dashboard initialized - leaderboards rendered by Blade template');
        console.log('IRSMS validators:', dashboardData.topValidatorsIrsms ? dashboardData.topValidatorsIrsms.length : 0);
        console.log('ICELL validators:', dashboardData.topValidatorsIcell ? dashboardData.topValidatorsIcell.length : 0);
    } else {
        console.warn('Dashboard data not available from server');
    }
}

// Initialize expand/collapse functionality for leaderboards
function initLeaderboardExpand() {
    console.log('Initializing leaderboard expand functionality');

    $(document).on('click', '.btn-expand-leaderboard', function (e) {
        e.preventDefault();

        const $btn = $(this);
        const target = $btn.data('target');
        const isExpanded = $btn.attr('data-expanded') === 'true';
        const $targetContainer = $(`#${target}Content`);
        const $hiddenItems = $targetContainer.find('.leader-item-hidden');
        const $icon = $btn.find('i');
        const $text = $btn.find('.expand-text');

        console.log('Expand button clicked:', {
            target: target,
            isExpanded: isExpanded,
            hiddenItemsCount: $hiddenItems.length
        });

        if (!$hiddenItems.length) {
            console.warn('No hidden items found');
            return;
        }

        if (!isExpanded) {
            // Expand - show all items
            $hiddenItems.slideDown(300, function () {
                $(this).addClass('fade-in-item');
            });

            $icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
            $text.text('Sembunyikan');
            $btn.attr('data-expanded', 'true');

            console.log('Leaderboard expanded');
        } else {
            // Collapse - hide items beyond top 5
            $hiddenItems.slideUp(300, function () {
                $(this).removeClass('fade-in-item');
            });

            const hiddenCount = $hiddenItems.length;
            $icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
            $text.text(`Lihat Semua (${hiddenCount} lainnya)`);
            $btn.attr('data-expanded', 'false');

            console.log('Leaderboard collapsed');

            // Scroll back to top of leaderboard smoothly
            setTimeout(() => {
                $targetContainer.parent()[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }, 100);
        }
    });

    console.log('Leaderboard expand functionality initialized');
}

// Helper function to format numbers with thousands separator
function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// Toggle auto reload on/off
function toggleAutoReload() {
    const indicator = document.getElementById('autoReloadIndicator');
    const dot = indicator?.querySelector('.auto-reload-dot');
    const text = indicator?.querySelector('.auto-reload-text');
    const toggleBtn = document.getElementById('toggleAutoReload');
    const toggleIcon = toggleBtn?.querySelector('i');

    if (!indicator || !dot || !text || !toggleBtn || !toggleIcon) {
        console.error('Auto reload UI elements not found');
        return;
    }

    isAutoReloadEnabled = !isAutoReloadEnabled;

    if (isAutoReloadEnabled) {
        // Enable auto reload
        dot.classList.remove('paused');
        text.innerHTML = `Memperbarui data dalam <span id="countdown">${formatTime(remainingTime)}</span>`;
        toggleIcon.className = 'bi bi-pause-fill';
        toggleBtn.title = 'Pause auto reload';

        if (!countdownTimer) {
            startCountdown();
        }

        console.log('Auto reload enabled');
    } else {
        // Disable auto reload
        dot.classList.add('paused');
        text.textContent = 'Memperbarui data dijeda';
        toggleIcon.className = 'bi bi-play-fill';
        toggleBtn.title = 'Resume auto reload';
        stopCountdown();

        console.log('Auto reload disabled');
    }

    // Save preference to localStorage
    localStorage.setItem('autoReloadEnabled', isAutoReloadEnabled.toString());
}

// Load user preferences from localStorage
function loadPreferences() {
    const savedAutoReload = localStorage.getItem('autoReloadEnabled');
    if (savedAutoReload !== null) {
        isAutoReloadEnabled = savedAutoReload === 'true';
    }
}

// Initialize auto reload system
function initAutoReload() {
    console.log('Initializing auto reload system...');

    loadPreferences();
    addAutoReloadStyles();
    createAutoReloadIndicator();

    // Update UI based on saved preference
    if (!isAutoReloadEnabled) {
        const indicator = document.getElementById('autoReloadIndicator');
        const dot = indicator?.querySelector('.auto-reload-dot');
        const text = indicator?.querySelector('.auto-reload-text');
        const toggleBtn = document.getElementById('toggleAutoReload');
        const toggleIcon = toggleBtn?.querySelector('i');

        if (dot && text && toggleIcon) {
            dot.classList.add('paused');
            text.textContent = 'Auto reload dijeda';
            toggleIcon.className = 'bi bi-play-fill';
            if (toggleBtn) toggleBtn.title = 'Resume auto reload';
        }
    } else {
        startCountdown();
    }

    console.log('Auto reload system initialized successfully');
}

// Enhanced refresh functionality
function initRefreshButton() {
    const refreshButton = document.getElementById('refreshData');
    if (refreshButton) {
        refreshButton.addEventListener('click', function () {
            const loadingOverlay = document.getElementById('loadingOverlay');

            this.classList.add('rotating');
            if (loadingOverlay) {
                loadingOverlay.classList.remove('d-none');
            }

            // Reset auto reload when manually refreshing
            resetAutoReload();

            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    }
}

// Initialize leaderboard filter functionality
function initLeaderboardFilters() {
    // IRSMS Leaderboard Filter dengan Dropdown
    $('.filter-leaderboard-irsms').on('click', function (e) {
        e.preventDefault();

        const $link = $(this);
        const range = $link.data('range');
        const system = $link.data('system');

        // Update active state pada dropdown items
        $('.filter-leaderboard-irsms').removeClass('active');
        $link.addClass('active');

        // Update label dropdown button
        const labelText = $link.text().trim();
        $('#filterLabelIrsms').text(labelText);

        // Load leaderboard data with filter
        loadLeaderboardWithFilter(system, range);
    });

    // ICELL Leaderboard Filter dengan Dropdown
    $('.filter-leaderboard-icell').on('click', function (e) {
        e.preventDefault();

        const $link = $(this);
        const range = $link.data('range');
        const system = $link.data('system');

        // Update active state pada dropdown items
        $('.filter-leaderboard-icell').removeClass('active');
        $link.addClass('active');

        // Update label dropdown button
        const labelText = $link.text().trim();
        $('#filterLabelIcell').text(labelText);

        // Load leaderboard data with filter
        loadLeaderboardWithFilter(system, range);
    });
}

// Load leaderboard data with filter
function loadLeaderboardWithFilter(system, range) {
    const targetSelector = system === 'irsms'
        ? '#leaderboardIrsmsContent'
        : '#leaderboardIcellContent';
    const $targetElement = $(targetSelector);

    if (!$targetElement.length) {
        console.warn(`Leaderboard container not found for system: ${system}`);
        return;
    }

    // Show loading state
    $targetElement.html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat data...</p>
        </div>
    `);

    // Make AJAX request
    $.ajax({
        url: leaderboardUrl,
        method: 'GET',
        data: {
            system: system,
            range: range
        },
        dataType: 'json',
        success: function (response) {
            console.log(`${system.toUpperCase()} leaderboard data loaded successfully:`, response);

            if (response && response.topValidators) {
                updateLeaderboardData(system, response.topValidators);
            } else {
                showLeaderboardError($targetElement, 'Data tidak tersedia');
            }
        },
        error: function (xhr, status, error) {
            console.error(`Failed to load ${system} leaderboard:`, error);
            showLeaderboardError($targetElement, 'Gagal memuat data');
        }
    });
}

// Show leaderboard error message
function showLeaderboardError($element, message) {
    $element.html(`
        <div class="text-center py-5">
            <i class="bi bi-exclamation-triangle text-warning opacity-25" style="font-size: 4rem;"></i>
            <p class="mt-3 text-muted fw-medium">${message}</p>
            <button class="btn btn-sm btn-outline-primary mt-2" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
            </button>
        </div>
    `);
}

// Show update error message to user
function showUpdateError(message) {
    // Remove any existing error message
    const existingError = document.querySelector('.auto-reload-error');
    if (existingError) {
        existingError.remove();
    }

    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'auto-reload-error alert alert-warning alert-dismissible fade show position-fixed';
    errorDiv.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 350px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;

    errorDiv.innerHTML = `
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Auto Reload:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Add to page
    document.body.appendChild(errorDiv);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (errorDiv && errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 5000);
}

// Update $(document).ready - pastikan urutan yang benar
$(document).ready(function () {
    console.log('Dashboard validator script loaded at: ' + new Date().toISOString());

    // Update time immediately and then every second
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);

    // Initialize dashboard features
    initDarkMode();
    initProgressBars();
    initStatsCardEffects();
    initParallaxEffect();
    initRefreshButton();

    // Check pulse alerts initially and set interval
    checkPulseAlerts();
    setInterval(checkPulseAlerts, 30000);

    // Initialize auto reload system after a short delay
    setTimeout(() => {
        initAutoReload();
        console.log('Auto reload system initialized');
    }, 500);

    // Add initial data loading - HARUS SEBELUM filter
    initializeDashboardData();

    // Initialize leaderboard filters
    initLeaderboardFilters();

    // Initialize expand/collapse functionality - HARUS SETELAH DOM ready
    initLeaderboardExpand();

    // Initialize logo handling
    initLogoHandling();

    console.log('All dashboard features initialized');
});