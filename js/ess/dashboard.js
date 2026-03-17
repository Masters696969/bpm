const announcementList = document.getElementById("announcementList");
const btnRefreshAnnouncements = document.getElementById("btnRefreshAnnouncements");
const todayLabel = document.getElementById("todayLabel");

document.addEventListener("DOMContentLoaded", () => {
    setTodayLabel();
    bindEvents();
    loadAnnouncements();
});

function bindEvents() {
    if (btnRefreshAnnouncements) {
        btnRefreshAnnouncements.addEventListener("click", loadAnnouncements);
    }
}

function setTodayLabel() {
    const now = new Date();
    const formatted = now.toLocaleDateString("en-US", {
        month: "long",
        day: "2-digit",
        year: "numeric"
    });

    if (todayLabel) {
        todayLabel.innerHTML = `
            <i data-lucide="calendar-days"></i>
            ${escapeHtml(formatted)}
        `;
        refreshIcons();
    }
}

async function loadAnnouncements() {
    try {
        announcementList.innerHTML = `
            <div class="loading-state">
                <i data-lucide="loader-circle"></i>
                <span>Loading announcements...</span>
            </div>
        `;
        refreshIcons();

        const data = await fetchJson("includes/dashboard_data.php?action=get_announcements");

        if (!data || data.success === false) {
            throw new Error(data?.message || "Failed to load announcements.");
        }

        const rows = Array.isArray(data.announcements) ? data.announcements : [];
        renderAnnouncements(rows);
    } catch (error) {
        console.error(error);
        announcementList.innerHTML = `
            <div class="empty-state">
                <i data-lucide="triangle-alert"></i>
                <h4>Unable to load announcements</h4>
                <p>Please try again.</p>
            </div>
        `;
        refreshIcons();

        if (window.Swal) {
            Swal.fire("Error", error.message || "Failed to load announcements.", "error");
        }
    }
}

function renderAnnouncements(rows) {
    if (!rows.length) {
        announcementList.innerHTML = `
            <div class="empty-state">
                <i data-lucide="megaphone-off"></i>
                <h4>No announcements yet</h4>
                <p>There are no posted updates at the moment.</p>
            </div>
        `;
        refreshIcons();
        return;
    }

    announcementList.innerHTML = rows.map(item => `
        <article class="announcement-card">
            <div class="announcement-top">
                <div class="announcement-title-wrap">
                    <h4 class="announcement-title">${escapeHtml(item.title || "Untitled Announcement")}</h4>
                    <span class="priority-badge ${getPriorityClass(item.priority)}">
                        ${escapeHtml(formatPriority(item.priority))}
                    </span>
                </div>
            </div>

            <p class="announcement-message">${escapeHtml(item.message || "")}</p>

            <div class="announcement-meta">
                <span class="meta-item">
                    <i data-lucide="badge-info"></i>
                    ${escapeHtml(item.posted_by || "System")}
                </span>
                <span class="meta-item">
                    <i data-lucide="clock-3"></i>
                    ${escapeHtml(item.posted_at_label || "-")}
                </span>
            </div>
        </article>
    `).join("");

    refreshIcons();
}

async function fetchJson(url) {
    const res = await fetch(url, {
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        }
    });

    const text = await res.text();

    try {
        return JSON.parse(text);
    } catch (err) {
        console.error("Invalid JSON:", text);
        throw new Error("Invalid server response.");
    }
}

function getPriorityClass(priority) {
    const value = String(priority || "").toLowerCase();

    if (value === "high") return "priority-high";
    if (value === "medium") return "priority-medium";
    return "priority-low";
}

function formatPriority(priority) {
    const value = String(priority || "low").toLowerCase();

    if (value === "high") return "High Priority";
    if (value === "medium") return "Medium Priority";
    return "General";
}

function refreshIcons() {
    if (window.lucide) {
        lucide.createIcons();
    }
}

function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, s => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;"
    })[s]);
}