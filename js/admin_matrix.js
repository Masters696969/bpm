document.addEventListener("DOMContentLoaded", () => {
    // Basic navigation/sidebar setup (Assuming shared UI scripts handle standard things)
    const lucide = window.lucide;
    if (lucide) lucide.createIcons();

    // Render Matrix Table UI
    const meritMatrixTbody = document.getElementById("meritMatrixTbody");

    function renderMatrixTables() {
        if (!window.compConfig || !window.compConfig.meritMatrix) return;

        const matrixData = window.compConfig.meritMatrix;
        let htmlView = "";

        // Standard 5 to 1 Rating layout
        const ratings = ["5.0", "4.0", "3.0", "2.0", "1.0"];
        const ranges = ["Low", "Mid", "High"];

        ratings.forEach(rating => {
            htmlView += `<tr><td style="padding:16px; border-bottom:1px solid var(--border-color); border-right:1px solid var(--border-color);"><div style="display:flex; align-items:center; gap:12px;"><div style="width:36px; height:36px; border-radius:8px; background:var(--surface-hover); display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--text-primary); border:1px solid var(--border-color);">${rating}</div><div><div style="font-weight:700; color:var(--text-primary); font-size:14px; margin-bottom:2px;">Rating ${rating}</div><div style="font-size:12px; color:var(--text-secondary);">Performance</div></div></div></td>`;

            ranges.forEach(range => {
                const node = (matrixData[rating] && matrixData[rating][range]) ? matrixData[rating][range] : { min_increase_pct: 0, max_increase_pct: 0 };

                // View HTML
                htmlView += `
                <td style="padding:16px; border-bottom:1px solid var(--border-color); ${range !== 'High' ? 'border-right:1px solid var(--border-color);' : ''}">
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; background:var(--surface); padding:8px 12px; border-radius:6px; border:1px solid var(--border-color);">
                            <span style="font-size:11px; text-transform:uppercase; color:var(--text-tertiary); font-weight:600;">Min</span>
                            <span style="font-weight:700; color:var(--text-primary); font-family:monospace; font-size:13px;">${parseFloat(node.min_increase_pct).toFixed(1)}%</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(44,160,120,0.05); padding:8px 12px; border-radius:6px; border:1px solid rgba(44,160,120,0.2);">
                            <span style="font-size:11px; text-transform:uppercase; color:var(--brand-green); font-weight:800;">Max</span>
                            <span style="font-weight:800; color:var(--brand-green); font-family:monospace; font-size:13px;">${parseFloat(node.max_increase_pct).toFixed(1)}%</span>
                        </div>
                    </div>
                </td>`;
            });
            htmlView += `</tr>`;
        });

        if (meritMatrixTbody) meritMatrixTbody.innerHTML = htmlView;
    }

    // Initial Render
    renderMatrixTables();
});
