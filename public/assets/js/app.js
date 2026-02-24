// ═══════════════════════════════════════════════════════════
// TWINPROFIT HQ — app.js
// Original client-side logic + SaaS API integration
// ═══════════════════════════════════════════════════════════

// ── Data Constants ──────────────────────────────────────────
const PAIN_POINTS = {
  "Restaurant / Café": ["Menu updates take days & cost hundreds", "Can't showcase ambiance or food quality online", "No virtual tour to drive foot traffic", "Social content is stale — competitors post daily video"],
  "Dental Practice": ["Website feels cold and clinical — no trust builder", "Patients can't visualize procedures before booking", "Zero video testimonials to build confidence", "Staff won't go on camera for social content"],
  "Real Estate Agency": ["Listings look identical to every competitor", "Agents lack personal video branding", "No neighborhood walkthrough content", "Virtual tours are $500+ each — can't scale"],
  "Auto Dealership": ["Inventory walkaround videos cost $200+ each", "Sales staff can't create consistent content", "No personalized follow-up video capability", "Social feeds are stock photos — zero engagement"],
  "Med Spa / Aesthetics": ["Before/after content limited by regulations", "Can't demonstrate procedures safely in marketing", "Staff aren't comfortable on camera", "Need constant fresh social proof content"],
  "Law Firm": ["Attorneys refuse to go on camera", "All content feels stiff and unapproachable", "FAQ videos would generate leads but cost $5K+", "Website has zero personality — just text walls"],
  "Fitness / Gym": ["Trainers don't create content consistently", "No class preview videos to boost enrollment", "Member testimonials are text-only", "Social content takes 10+ hours/week"],
  "HVAC / Plumbing": ["Impossible to showcase work visually", "Technicians won't participate in videos", "No educational content for homeowners", "Every competitor's website looks identical"],
  "Roofing / Construction": ["Project portfolio is just static photos", "No educational content for prospects", "Can't differentiate from 50 other roofers", "Estimates need visual support"],
  "Insurance Agency": ["Hardest product to make visually interesting", "Agents struggle to create any content", "Explainer videos needed but budget is $0", "Zero personal brand presence online"],
  "Chiropractic": ["Can't show patient results easily", "Educational content would drive referrals", "No consistent social posting", "Competitor content is better"],
  "Pet Services": ["Cute content but no strategy", "No virtual facility tours", "Staff videos would build trust", "User-generated content is inconsistent"],
  "Salon / Barbershop": ["Style showcases are just photos", "No video tutorials", "Stylists won't create content", "Social feed needs video badly"],
  "Photography Studio": ["Portfolio is static", "Behind-the-scenes would sell more", "No video marketing at all", "Client testimonials are text-only"],
  "Accounting / Tax Prep": ["Hardest niche to make exciting", "Educational content needed", "Tax tips videos would attract leads", "Zero personal brand online"]
};

const SERVICES = [
  { id:"avatar",       name:"AI Digital Twin / Avatar Creation",    basePrice:500, desc:"Custom AI avatar cloned from client" },
  { id:"social",       name:"AI Social Media Videos (30/mo)",       basePrice:800, desc:"30 short-form videos per month" },
  { id:"ugcAds",       name:"UGC-Style Ad Creatives",               basePrice:600, desc:"Performance ads using AI presenters" },
  { id:"tour",         name:"AI Virtual Tour Narration",            basePrice:400, desc:"AI avatar narrates virtual tours" },
  { id:"training",     name:"Training / Explainer Videos",          basePrice:700, desc:"Customer-facing training content" },
  { id:"testimonials", name:"AI Testimonial Enhancement",           basePrice:350, desc:"Text reviews → video testimonials" },
  { id:"multilingual", name:"Multilingual Video Translation",       basePrice:450, desc:"Translate videos into 29+ languages" },
  { id:"greeter",      name:"Website AI Greeter Widget",            basePrice:300, desc:"AI avatar greeting on website" },
  { id:"emailVideo",   name:"Video Email Campaigns",                basePrice:500, desc:"Personalized AI video in emails" },
  { id:"retainer",     name:"Monthly Retainer & Updates",           basePrice:600, desc:"Ongoing updates & maintenance" }
];

const UGC_PLATFORMS = [
  { name:"Fiverr",              type:"Marketplace",   notes:"List UGC gigs, $150-500/video" },
  { name:"Upwork",              type:"Freelance",     notes:"Bid on $2K-10K UGC contracts" },
  { name:"Billo",               type:"UGC Platform",  notes:"Direct brand connections" },
  { name:"Trend.io",            type:"UGC Platform",  notes:"Premium brands seeking UGC" },
  { name:"Collabstr",           type:"Marketplace",   notes:"Set your own rates" },
  { name:"JoinBrands",          type:"UGC Platform",  notes:"Products + payment for UGC" },
  { name:"Insense",             type:"Agency Tool",   notes:"Manage UGC at scale" },
  { name:"Facebook Groups",     type:"Community",     notes:"'UGC Jobs' daily opportunities" },
  { name:"LinkedIn Outreach",   type:"Direct",        notes:"DM marketing managers" },
  { name:"Cold Email (Apollo)", type:"Outbound",      notes:"Build DTC lists, pitch AI UGC" }
];

// ── Tab Switching ────────────────────────────────────────────
function switchTab(tabId, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById(tabId).classList.add('active');
  btn.classList.add('active');
}

// ── Tab 1: Opportunity Finder ────────────────────────────────
function analyzeProspect() {
  const name = document.getElementById('bizName').value.trim();
  const niche = document.getElementById('bizNiche').value;
  if (!name || !niche) { showToast('Please fill in Business Name and Niche.', 'error'); return; }

  const url         = document.getElementById('bizUrl').value.trim();
  const website     = document.getElementById('bizWebsite').value;
  const video       = document.getElementById('bizVideo').value;
  const social      = document.getElementById('bizSocial').value;
  const budget      = document.getElementById('bizBudget').value;
  const competitors = document.getElementById('bizCompetitors').value;

  let score = 0;
  const highValue = ["Restaurant / Café","Real Estate Agency","Med Spa / Aesthetics","Auto Dealership","Dental Practice"];
  score += highValue.includes(niche) ? 20 : 12;
  score += website === 'outdated' ? 18 : website === 'none' ? 8 : 10;
  score += video === 'none' ? 25 : video === 'minimal' ? 18 : 5;
  score += social === 'none' ? 20 : social === 'basic' ? 15 : 5;
  score += (budget === 'high' || budget === 'premium') ? 15 : budget === 'mid' ? 10 : 5;
  score += competitors === 'many' ? 12 : competitors === 'some' ? 8 : 3;
  score = Math.min(score, 100);

  document.getElementById('scoreCardPlaceholder').style.display = 'none';
  const resultEl = document.getElementById('scoreResult');
  resultEl.style.display = 'block';
  resultEl.style.animation = 'scaleIn 0.5s ease';

  let color, label, badgeClass;
  if (score >= 75) {
    color = '#cc2244'; label = '<i class="ph-fill ph-fire"></i> HOT PROSPECT'; badgeClass = 'readiness-hot';
  } else if (score >= 50) {
    color = '#e49a2a'; label = '<i class="ph-fill ph-lightning"></i> WARM LEAD'; badgeClass = 'readiness-warm';
  } else {
    color = '#6aace0'; label = '<i class="ph-fill ph-snowflake"></i> NEEDS NURTURING'; badgeClass = 'readiness-cold';
  }

  const circle = document.getElementById('scoreCircle');
  circle.style.stroke = color;
  const offset = 540 - (score / 100) * 540;
  setTimeout(() => { circle.style.strokeDashoffset = offset; }, 100);

  document.getElementById('scoreNum').style.color = color;
  document.getElementById('scoreNum').textContent  = score;

  const badge = document.getElementById('readinessBadge');
  badge.className   = 'readiness-badge ' + badgeClass;
  badge.innerHTML   = label;

  const points = PAIN_POINTS[niche] || PAIN_POINTS["Restaurant / Café"];
  document.getElementById('painPointsList').innerHTML = points.map((p, i) => `
    <div class="pain-item" style="animation:slideRight ${0.3 + i * 0.1}s ease forwards;opacity:0;border-left-color:${color}">
      <div class="pain-icon"><i class="ph-bold ph-crosshair"></i></div>
      <div class="pain-text">${p}</div>
    </div>
  `).join('');

  window._prospectData = { name, url, niche, score, color, label, points };
  markPipelineDone('tab1');

  // Show save button
  const saveBtn = document.getElementById('saveProspectBtn');
  if (saveBtn) { saveBtn.style.display = 'inline-flex'; }
}

function showOnePager() {
  const d = window._prospectData;
  if (!d) return;
  document.getElementById('onePagerContent').innerHTML = `
    <button class="one-pager-close" onclick="hideOnePager()">✕</button>
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" width="36" height="36" style="flex-shrink:0">
        <path d="M8 12 H44 V26 H32 V90 H20 V26 H8 Z" fill="#1b2d4f"/>
        <path d="M40 28 H76 V42 H64 V90 H52 V42 H40 Z" fill="#d4a01e"/>
        <polygon points="20,12 26,2 32,12" fill="#1b2d4f"/>
      </svg>
      <div>
        <div style="font-family:var(--font-display);font-size:14px;font-weight:800;color:#0a0a1a">TwinProfit <span style="display:inline-block;padding:1px 7px;background:#1b2d4f;color:#fff;border-radius:5px;font-size:11px;font-weight:800">HQ</span></div>
        <div style="font-size:11px;color:#888">Digital Twin Readiness Assessment</div>
      </div>
    </div>
    <h2 style="font-family:var(--font-display);font-size:26px">${d.name}</h2>
    <p style="color:#888;font-size:14px;margin-bottom:${d.url ? '6px' : '16px'}">${d.niche} &bull; ${new Date().toLocaleDateString()}</p>
    ${d.url ? `<p style="font-size:13px;margin-bottom:16px"><a href="${d.url}" target="_blank" style="color:#1b2d4f;font-weight:700;word-break:break-all">${d.url}</a></p>` : ''}
    <div class="op-score-box">
      <div class="op-score-num" style="color:${d.color}">${d.score}</div>
      <div>
        <div style="font-weight:800;font-size:16px;color:${d.color}">${d.label.replace(/<[^>]+>/g,'')}</div>
        <div style="font-size:13px;color:#888;margin-top:2px">Readiness Score / 100</div>
      </div>
    </div>
    <div class="op-section-title">Key Opportunities</div>
    ${d.points.map(p => `<div class="op-pain-item">${p}</div>`).join('')}
    <div class="op-section-title">Recommended Solution</div>
    <p style="font-size:14px;color:#444;line-height:1.7"><strong>${d.name}</strong> is an ideal candidate for an <strong>AI Digital Twin</strong> — a custom AI avatar that creates unlimited video content without staff on camera. This generates <strong>30+ professional videos/month</strong> at a fraction of traditional costs.</p>
    <div class="op-cta"><h4>Ready to See It in Action?</h4><p>Let us show you a live demo of your AI Digital Twin.</p></div>
    <div style="margin-top:16px;text-align:center">
      <button onclick="window.print()" style="padding:10px 28px;background:#1b2d4f;border:none;border-radius:100px;color:#fff;font-weight:800;cursor:pointer;font-family:var(--font-body)">
        <i class="ph-bold ph-printer"></i> Print One-Pager
      </button>
    </div>
  `;
  document.getElementById('onePagerOverlay').classList.add('visible');
}

function hideOnePager() {
  document.getElementById('onePagerOverlay').classList.remove('visible');
}

// ── Tab 2: Service Packager ──────────────────────────────────
function initServiceGrid() {
  document.getElementById('serviceGrid').innerHTML = SERVICES.map(s => `
    <label class="service-check" id="svc-${s.id}" onclick="toggleService('${s.id}')">
      <input type="checkbox" data-id="${s.id}" data-price="${s.basePrice}">
      <div class="check-box"><i class="ph-bold ph-check"></i></div>
      <div>
        <div class="service-name">${s.name}</div>
        <div class="service-price">$${s.basePrice}/mo</div>
        <div class="service-desc">${s.desc}</div>
      </div>
    </label>
  `).join('');
}

function toggleService(id) {
  const el = document.getElementById('svc-' + id);
  const cb = el.querySelector('input');
  cb.checked = !cb.checked;
  el.classList.toggle('checked', cb.checked);
}

function generatePackages() {
  const checked = document.querySelectorAll('#serviceGrid input:checked');
  if (checked.length < 2) { showToast('Select at least 2 services.', 'error'); return; }

  const selected = Array.from(checked).map(cb => ({
    id:    cb.dataset.id,
    price: parseInt(cb.dataset.price),
    name:  SERVICES.find(s => s.id === cb.dataset.id).name
  })).sort((a, b) => a.price - b.price);

  const sc = Math.max(2, Math.ceil(selected.length * 0.4));
  const gc = Math.max(3, Math.ceil(selected.length * 0.7));
  const starterS  = selected.slice(0, sc);
  const growthS   = selected.slice(0, gc);
  const premiumS  = [...selected];

  const sp = starterS.reduce((s, x) => s + x.price, 0);
  const gp = growthS.reduce((s, x) => s + x.price, 0);
  const pp = premiumS.reduce((s, x) => s + x.price, 0);

  let prices = {
    starter: Math.ceil(sp * 0.85 / 100) * 100 || 997,
    growth:  Math.ceil(gp * 0.9  / 100) * 100 || 1997,
    premium: Math.ceil(pp * 0.95 / 100) * 100 || 3497
  };
  if (prices.growth  <= prices.starter) prices.growth  = prices.starter + 500;
  if (prices.premium <= prices.growth)  prices.premium = prices.growth  + 1000;

  window._lastPackageData = { prices, starterS, growthS, premiumS };

  const tiers = [
    { name:'Starter', cls:'tier-starter', badge:'STARTER', price:prices.starter, services:starterS },
    { name:'Growth',  cls:'tier-growth',  badge:'GROWTH',  price:prices.growth,  services:growthS },
    { name:'Premium', cls:'tier-premium', badge:'PREMIUM', price:prices.premium, services:premiumS }
  ];

  document.getElementById('tierCards').innerHTML = tiers.map((t, i) => {
    const cost   = Math.round(t.price * 0.2);
    const profit = t.price - cost;
    const margin = Math.round((profit / t.price) * 100);
    return `<div class="tier-card ${t.cls}" style="animation:fadeUp ${0.3 + i * 0.15}s ease both">
      <div class="tier-badge">${t.badge}</div>
      <div class="tier-price">$${t.price.toLocaleString()}</div>
      <div class="tier-period">per month</div>
      <ul class="tier-services">${t.services.map(s => `<li>${s.name}</li>`).join('')}</ul>
      <div class="tier-margin">
        <div style="display:flex;justify-content:space-between"><span style="opacity:0.6">Cost</span><span style="font-weight:700">$${cost}</span></div>
        <div style="display:flex;justify-content:space-between;margin-top:8px"><span style="opacity:0.6">Profit</span><span style="font-weight:700;color:${t.cls==='tier-premium'?'#fff':'var(--green-deep)'}">$${profit.toLocaleString()} (${margin}%)</span></div>
      </div>
    </div>`;
  }).join('');

  const gPrice = prices.growth;
  document.getElementById('revTableBody').innerHTML = [3, 5, 10, 20].map(c => {
    const m = c * gPrice;
    const p = Math.round(m * 0.8);
    const a = m * 12;
    return `<tr>
      <td style="font-weight:800">${c} clients</td>
      <td class="rev-amount">$${m.toLocaleString()}/mo</td>
      <td class="rev-amount">$${p.toLocaleString()}/mo</td>
      <td class="rev-annual">$${a.toLocaleString()}/yr</td>
    </tr>`;
  }).join('');

  document.getElementById('tiersOutput').style.display = 'block';
  document.getElementById('tiersOutput').scrollIntoView({ behavior: 'smooth', block: 'start' });
  markPipelineDone('tab2');
}

// ── Tab 3: Proposal Generator ────────────────────────────────
function generateProposalTemplate() {
  const agency   = document.getElementById('propAgency').value.trim()   || 'Your Agency';
  const client   = document.getElementById('propClient').value.trim();
  const contact  = document.getElementById('propContact').value.trim()  || 'Valued Client';
  const niche    = document.getElementById('propNiche').value;
  const tier     = document.getElementById('propTier').value;
  const services = document.getElementById('propServices').value.trim();
  const notes    = document.getElementById('propNotes').value.trim();

  if (!client) { showToast('Please enter client business name.', 'error'); return; }

  const prices   = { starter: 997, growth: 1997, premium: 3497 };
  let price      = prices[tier] || parseInt(document.getElementById('propCustomPrice')?.value) || 1997;
  const tierName = tier === 'custom' ? 'Custom' : tier.charAt(0).toUpperCase() + tier.slice(1);
  const serviceList = services
    ? services.split(',').map(s => s.trim()).filter(Boolean)
    : ['AI Digital Twin Creation', 'Monthly Social Videos', 'Website AI Greeter'];
  const today = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
  const validUntil = new Date(Date.now() + 14 * 864e5).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

  const html = `
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px">
      <div><h2>Digital Twin Service Proposal</h2><div class="prop-meta">Prepared by <strong>${agency}</strong> &bull; ${today}</div></div>
      <div style="text-align:right"><div style="font-family:var(--font-display);font-size:20px">${agency}</div><div style="font-size:12px;color:#888">AI-Powered Digital Solutions</div></div>
    </div>
    <h3>Dear ${contact},</h3>
    <p>Thank you for the opportunity to present this proposal for <strong>${client}</strong>. We recommend our <strong>${tierName} Digital Twin Package</strong> — designed specifically for ${niche || 'your industry'}.</p>
    <h3>The Challenge</h3>
    <p>Traditional video production costs $2,000–$10,000 per video. Most staff won't go on camera. You need fresh video content daily but can't afford the traditional route.</p>
    <h3>Our Solution: AI Digital Twins</h3>
    <p>A hyper-realistic AI avatar that produces unlimited professional video content at a fraction of traditional costs. Fresh, engaging video every day without anyone stepping in front of a camera.</p>
    <h3>${tierName} Package — Included Services</h3>
    <table class="prop-table">
      <thead><tr><th>Service</th><th style="text-align:right">Status</th></tr></thead>
      <tbody>${serviceList.map(s => `<tr><td>${s}</td><td style="text-align:right;color:var(--green-deep);font-weight:700"><i class="ph-bold ph-check-circle"></i> Included</td></tr>`).join('')}</tbody>
    </table>
    <div class="prop-total">Investment: $${price.toLocaleString()}/month</div>
    ${notes ? `<h3>Notes</h3><p>${notes}</p>` : ''}
    <h3>Next Steps</h3>
    <p><strong>1.</strong> 15-min onboarding call &nbsp;&rarr;&nbsp; <strong>2.</strong> Build Digital Twin (3–5 days) &nbsp;&rarr;&nbsp; <strong>3.</strong> Launch &amp; deliver content</p>
    <div class="prop-footer"><p><strong>Valid until ${validUntil}.</strong> Ready to transform ${client}'s digital presence? Let's talk.</p></div>
  `;

  document.getElementById('proposalPreview').innerHTML = html;
  document.getElementById('proposalOutput').style.display = 'block';
  document.getElementById('proposalOutput').style.animation = 'fadeUp 0.5s ease';
  window._lastGenerationMode = 'template';
  markPipelineDone('tab3');
  document.getElementById('proposalOutput').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function copyProposal() {
  navigator.clipboard.writeText(document.getElementById('proposalPreview').innerText).then(() => {
    showToast('Proposal copied to clipboard!', 'success');
  });
}

function printProposal() {
  const w = window.open('', '_blank');
  w.document.write(`<!DOCTYPE html><html><head><title>Proposal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
      body{font-family:'Plus Jakarta Sans',sans-serif;color:#1a1a1a;padding:40px;line-height:1.7;max-width:800px;margin:0 auto}
      h2{font-family:'Outfit',sans-serif;font-size:24px;font-weight:800;letter-spacing:-0.5px}
      h3{font-family:'Outfit',sans-serif;font-size:16px;font-weight:700;margin:20px 0 8px;letter-spacing:-0.3px}
      p{font-size:14px;color:#444;margin-bottom:10px}
      table{width:100%;border-collapse:collapse;margin:14px 0}
      th{background:#f4f6fa;padding:10px 14px;text-align:left;font-size:12px}
      td{padding:10px 14px;border-bottom:1px solid #eee;font-size:14px}
      .prop-total{text-align:right;font-size:24px;font-weight:800;font-family:'Outfit',sans-serif;margin-top:16px;padding-top:16px;border-top:3px solid #1b2d4f;color:#1b2d4f}
      .prop-meta{font-size:13px;color:#888;margin-bottom:20px;padding-bottom:14px;border-bottom:3px solid #d4a01e}
      .prop-footer{margin-top:24px;padding:16px;background:#f0f3f8;border-radius:12px;text-align:center}
      .prop-footer p{font-size:13px;color:#666}
    </style>
  </head><body>${document.getElementById('proposalPreview').innerHTML}</body></html>`);
  w.document.close();
  w.focus();
  setTimeout(() => w.print(), 500);
}

// ── Tab 4: UGC Calculator ────────────────────────────────────
function initPlatforms() {
  document.getElementById('platformList').innerHTML = UGC_PLATFORMS.map((p, i) => `
    <div class="ugc-platform-item">
      <div class="ugc-platform-num">${String(i + 1).padStart(2, '0')}</div>
      <div><div class="ugc-platform-name">${p.name}</div><div class="ugc-platform-type">${p.type}</div></div>
      <div class="ugc-platform-notes">${p.notes}</div>
    </div>
  `).join('');
}

function calcUGC() {
  const price  = parseInt(document.getElementById('ugcPrice').value);
  const volume = parseInt(document.getElementById('ugcVolume').value);
  const cost   = parseInt(document.getElementById('ugcCost').value);

  const wR = price * volume;
  const wC = cost * volume;
  const wP = wR - wC;
  const mR = wR * 4.33;
  const mP = wP * 4.33;
  const aR = mR * 12;
  const aP = mP * 12;
  const margin = Math.round((wP / wR) * 100);

  document.getElementById('aiMarginPct').textContent = margin + '%';
  const bar = document.getElementById('aiMarginBar');
  bar.style.width  = margin + '%';
  bar.textContent  = margin + '%';

  document.getElementById('ugcProjections').innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
      <div style="padding:18px;background:var(--green-pale);border-radius:var(--radius-md);border-left:4px solid var(--green-deep)">
        <div style="font-size:11px;font-weight:800;color:var(--text-secondary);letter-spacing:1px;margin-bottom:4px">WEEKLY REVENUE</div>
        <div style="font-family:var(--font-display);font-size:26px;color:var(--green-deep)">$${Math.round(wR).toLocaleString()}</div>
      </div>
      <div style="padding:18px;background:var(--sky-pale);border-radius:var(--radius-md);border-left:4px solid var(--sky-deep)">
        <div style="font-size:11px;font-weight:800;color:var(--text-secondary);letter-spacing:1px;margin-bottom:4px">WEEKLY PROFIT</div>
        <div style="font-family:var(--font-display);font-size:26px;color:var(--sky-deep)">$${Math.round(wP).toLocaleString()}</div>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
      <div style="padding:18px;background:var(--amber-pale);border-radius:var(--radius-md);border-left:4px solid var(--amber-deep)">
        <div style="font-size:11px;font-weight:800;color:var(--text-secondary);letter-spacing:1px;margin-bottom:4px">MONTHLY REVENUE</div>
        <div style="font-family:var(--font-display);font-size:26px;color:var(--amber-deep)">$${Math.round(mR).toLocaleString()}</div>
      </div>
      <div style="padding:18px;background:var(--coral-pale);border-radius:var(--radius-md);border-left:4px solid var(--coral-deep)">
        <div style="font-size:11px;font-weight:800;color:var(--text-secondary);letter-spacing:1px;margin-bottom:4px">MONTHLY PROFIT</div>
        <div style="font-family:var(--font-display);font-size:26px;color:var(--coral-deep)">$${Math.round(mP).toLocaleString()}</div>
      </div>
    </div>
    <div style="padding:24px;background:var(--green-deep);border-radius:var(--radius-xl);text-align:center;color:#fff;box-shadow:0 8px 30px rgba(27,45,79,0.25)">
      <div style="font-size:11px;font-weight:800;letter-spacing:2px;opacity:0.7;margin-bottom:6px">ANNUAL PROJECTION</div>
      <div style="font-family:var(--font-display);font-size:40px">$${Math.round(aR).toLocaleString()}</div>
      <div style="font-size:14px;opacity:0.75;margin-top:4px">revenue &nbsp;/&nbsp; <span style="color:var(--amber);font-weight:800">$${Math.round(aP).toLocaleString()}</span> profit</div>
    </div>
    <div style="margin-top:16px;padding:16px;background:var(--cream);border-radius:var(--radius-md);display:flex;justify-content:space-around;text-align:center">
      <div><div style="font-size:11px;font-weight:800;color:var(--text-muted)">VIDEOS/MO</div><div style="font-size:20px;font-weight:800">${Math.round(volume * 4.33)}</div></div>
      <div style="width:1px;background:var(--border-light)"></div>
      <div><div style="font-size:11px;font-weight:800;color:var(--text-muted)">COST/VIDEO</div><div style="font-size:20px;font-weight:800">$${cost}</div></div>
      <div style="width:1px;background:var(--border-light)"></div>
      <div><div style="font-size:11px;font-weight:800;color:var(--text-muted)">MARGIN</div><div style="font-size:20px;font-weight:800;color:var(--green-deep)">${margin}%</div></div>
    </div>
  `;
}

// ═══════════════════════════════════════════════════════════
// SaaS API HELPERS
// ═══════════════════════════════════════════════════════════

async function apiPost(endpoint, data) {
  const res = await fetch(TWINPROFIT.baseUrl + endpoint, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      [TWINPROFIT.csrfName]: TWINPROFIT.csrfHash,
    },
    body: JSON.stringify(data),
  });
  const newToken = res.headers.get('X-CSRF-TOKEN');
  if (newToken) TWINPROFIT.csrfHash = newToken;
  const json = await res.json().catch(() => ({ error: 'Invalid server response' }));
  if (!res.ok) throw new Error(json.error || 'Request failed (' + res.status + ')');
  return json.data;
}

async function apiDelete(endpoint) {
  const res = await fetch(TWINPROFIT.baseUrl + endpoint, {
    method: 'DELETE',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      [TWINPROFIT.csrfName]: TWINPROFIT.csrfHash,
    },
  });
  const json = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(json.error || 'Delete failed');
  return json.data;
}

// ═══════════════════════════════════════════════════════════
// TOAST NOTIFICATIONS
// ═══════════════════════════════════════════════════════════

function showToast(message, type = 'success', duration = 3500) {
  const container = document.getElementById('toastContainer');
  if (!container) return;
  const icons = { success: 'ph-check-circle', error: 'ph-warning-circle', info: 'ph-info' };
  const toast = document.createElement('div');
  toast.className = 'toast toast-' + type;
  toast.innerHTML = '<i class="ph-fill ' + (icons[type] || 'ph-info') + '"></i> ' + message;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

// ═══════════════════════════════════════════════════════════
// PIPELINE PROGRESS TRACKER
// ═══════════════════════════════════════════════════════════

const _pipelineDone = { tab1: false, tab2: false, tab3: false, tab4: false, tab5: false };

function markPipelineDone(tab) {
  if (_pipelineDone[tab]) return;
  _pipelineDone[tab] = true;
  const map = { tab1: 'ps1', tab2: 'ps2', tab3: 'ps3', tab4: 'ps4', tab5: 'ps5' };
  const el = document.getElementById(map[tab]);
  if (el) el.classList.add('done');
}

// ═══════════════════════════════════════════════════════════
// TAB 1: SAVE PROSPECT
// ═══════════════════════════════════════════════════════════

async function saveProspect() {
  const d = window._prospectData;
  if (!d) { showToast('Analyze a prospect first.', 'error'); return; }

  const btn = document.getElementById('saveProspectBtn');
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Saving...'; }

  try {
    await apiPost('api/prospects/save', {
      name:            d.name,
      website_url:     d.url || null,
      niche:           d.niche,
      website_status:  document.getElementById('bizWebsite')?.value,
      video_status:    document.getElementById('bizVideo')?.value,
      social_status:   document.getElementById('bizSocial')?.value,
      budget:          document.getElementById('bizBudget')?.value,
      competitors:     document.getElementById('bizCompetitors')?.value,
      score:           d.score,
      readiness_level: d.score >= 75 ? 'hot' : d.score >= 50 ? 'warm' : 'cold',
      pain_points:     d.points || [],
    });
    showToast('Prospect saved!', 'success');
    if (btn) { btn.innerHTML = '<i class="ph-fill ph-check-circle"></i> Saved!'; }
    setTimeout(() => {
      if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ph-bold ph-floppy-disk"></i> Save'; }
    }, 3000);
  } catch (err) {
    showToast(err.message, 'error');
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ph-bold ph-floppy-disk"></i> Save'; }
  }
}

// ═══════════════════════════════════════════════════════════
// TAB 2: SAVE PACKAGE
// ═══════════════════════════════════════════════════════════

async function savePackage() {
  if (!window._lastPackageData) { showToast('Generate packages first.', 'error'); return; }

  const tierCards = document.querySelectorAll('.tier-card');
  if (!tierCards.length) { showToast('Generate packages first.', 'error'); return; }

  const getServices = (card) =>
    Array.from(card.querySelectorAll('.tier-services li')).map(li => li.textContent.trim());
  const getPrice = (card) => {
    const el = card.querySelector('.tier-price');
    return el ? parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0 : 0;
  };

  const name = prompt('Name this package set (e.g. "Restaurant Bundle Q1"):');
  if (!name) return;

  try {
    await apiPost('api/packages/save', {
      name,
      starter_price:    getPrice(tierCards[0]),
      growth_price:     getPrice(tierCards[1]),
      premium_price:    getPrice(tierCards[2]),
      starter_services: getServices(tierCards[0]),
      growth_services:  getServices(tierCards[1]),
      premium_services: getServices(tierCards[2]),
      selected_services: [],
    });
    showToast('Package set "' + name + '" saved!', 'success');
    markPipelineDone('tab2');
  } catch (err) {
    showToast(err.message, 'error');
  }
}

// ═══════════════════════════════════════════════════════════
// TAB 3: AI PROPOSAL GENERATION
// ═══════════════════════════════════════════════════════════

async function generateProposalAI(e) {
  if (e) e.preventDefault();

  const client  = document.getElementById('propClient')?.value.trim();
  const contact = document.getElementById('propContact')?.value.trim();
  const niche   = document.getElementById('propNiche')?.value;
  const tier    = document.getElementById('propTier')?.value;
  const agency  = document.getElementById('propAgency')?.value.trim();
  const notes   = document.getElementById('propNotes')?.value.trim();
  const svcRaw  = document.getElementById('propServices')?.value.trim();

  if (!client) { showToast('Please enter client business name.', 'error'); return; }

  const prices     = { starter: 997, growth: 1997, premium: 3497 };
  const price      = prices[tier] || parseInt(document.getElementById('propCustomPrice')?.value) || 1997;
  const services   = svcRaw ? svcRaw.split(',').map(s => s.trim()).filter(Boolean) : ['AI Digital Twin Creation', 'Monthly Social Videos', 'Website AI Greeter'];
  const painPoints = (window._prospectData?.niche === niche) ? (window._prospectData?.points || []) : [];

  const btn = document.getElementById('generateAiBtn');
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Writing with AI...'; }

  const previewEl = document.getElementById('proposalPreview');
  const outputEl  = document.getElementById('proposalOutput');
  if (previewEl) {
    previewEl.innerHTML = `
      <div class="skeleton skeleton-line" style="width:60%;height:28px;margin-bottom:20px"></div>
      <div class="skeleton skeleton-line"></div>
      <div class="skeleton skeleton-line"></div>
      <div class="skeleton skeleton-line" style="width:80%"></div>
      <div class="skeleton skeleton-line" style="margin-top:20px;height:20px;width:40%"></div>
      <div class="skeleton skeleton-line"></div>
      <div class="skeleton skeleton-line"></div>
    `;
    if (outputEl) outputEl.style.display = 'block';
  }

  try {
    const result = await apiPost('api/proposals/generate', {
      mode:         'ai',
      agency_name:  agency || TWINPROFIT.agencyName || 'Your Agency',
      client_name:  client,
      contact_name: contact,
      niche,
      tier,
      price,
      services,
      pain_points:  painPoints,
      notes,
    });

    if (previewEl) previewEl.innerHTML = result.content;
    if (outputEl)  outputEl.style.display = 'block';

    window._lastGenerationMode = result.generation_mode || 'ai';

    if (result.generation_mode === 'template') {
      showToast('AI unavailable — showing template version.', 'info');
    } else {
      const tokens = result.tokens_used ? ` (~${result.tokens_used} tokens)` : '';
      showToast('AI proposal generated!' + tokens, 'success');
    }

    markPipelineDone('tab3');
    outputEl?.scrollIntoView({ behavior: 'smooth', block: 'start' });

  } catch (err) {
    showToast(err.message, 'error');
    generateProposalTemplate();
  } finally {
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ph-bold ph-robot"></i> Generate with AI ✨'; }
  }
}

// ── Tab 3: Save Proposal ─────────────────────────────────────
async function saveProposal() {
  const previewEl = document.getElementById('proposalPreview');
  const client    = document.getElementById('propClient')?.value.trim();
  if (!previewEl?.innerHTML.trim() || !client) {
    showToast('Generate a proposal first.', 'error'); return;
  }

  const tier   = document.getElementById('propTier')?.value;
  const agency = document.getElementById('propAgency')?.value.trim();
  const contact= document.getElementById('propContact')?.value.trim();
  const niche  = document.getElementById('propNiche')?.value;
  const svcRaw = document.getElementById('propServices')?.value.trim();
  const notes  = document.getElementById('propNotes')?.value.trim();
  const prices = { starter: 997, growth: 1997, premium: 3497 };
  const price  = prices[tier] || parseInt(document.getElementById('propCustomPrice')?.value) || 1997;
  const svcs   = svcRaw ? svcRaw.split(',').map(s => s.trim()).filter(Boolean) : [];

  const saveBtn = document.getElementById('saveProposalBtn');
  if (saveBtn) { saveBtn.disabled = true; saveBtn.innerHTML = '<span class="spinner"></span> Saving...'; }

  try {
    await apiPost('api/proposals/save', {
      agency_name:     agency,
      client_name:     client,
      contact_name:    contact,
      niche,
      tier,
      price,
      services:        svcs,
      content:         previewEl.innerHTML,
      generation_mode: window._lastGenerationMode || 'template',
      notes,
    });
    showToast('Proposal saved!', 'success');
    if (saveBtn) { saveBtn.innerHTML = '<i class="ph-fill ph-check-circle"></i> Saved!'; }
    setTimeout(() => {
      if (saveBtn) { saveBtn.disabled = false; saveBtn.innerHTML = '<i class="ph-bold ph-floppy-disk"></i> Save Proposal'; }
    }, 3000);
  } catch (err) {
    showToast(err.message, 'error');
    if (saveBtn) { saveBtn.disabled = false; saveBtn.innerHTML = '<i class="ph-bold ph-floppy-disk"></i> Save Proposal'; }
  }
}

// ═══════════════════════════════════════════════════════════
// TAB 5: SETTINGS
// ═══════════════════════════════════════════════════════════

function selectModel(modelName, labelEl) {
  document.querySelectorAll('.model-option').forEach(el => el.classList.remove('selected'));
  labelEl.classList.add('selected');
  const radio = labelEl.querySelector('input[type="radio"]');
  if (radio) radio.checked = true;
}

async function saveApiKey() {
  const keyInput = document.getElementById('settingsApiKey');
  const key      = keyInput?.value.trim();
  const modelEl  = document.querySelector('input[name="model"]:checked');
  const model    = modelEl?.value || 'gpt-4.1-nano';

  if (!key || key.includes('•')) {
    showToast('Please enter your OpenAI API key.', 'error'); return;
  }
  if (!key.startsWith('sk-')) {
    showToast('Invalid key format. OpenAI keys start with sk-', 'error'); return;
  }

  try {
    const result = await apiPost('api/settings/save', { openai_api_key: key, openai_model: model });
    if (keyInput && result.masked_key) keyInput.value = result.masked_key;
    TWINPROFIT.hasKey = true;
    showToast('API key saved securely with AES-256!', 'success');
    markPipelineDone('tab5');
    const statusEl = document.querySelector('#tab5 .api-status');
    if (statusEl) {
      statusEl.className = 'api-status api-status-success mb-16';
      statusEl.innerHTML = '<i class="ph-fill ph-check-circle"></i> API key configured — AI proposals enabled';
    }
    const dot = document.querySelector('#settingsTabBtn span[style*="f87171"]');
    if (dot) dot.remove();
    setTimeout(() => location.reload(), 1500);
  } catch (err) {
    showToast(err.message, 'error');
  }
}

async function testApiKey() {
  const btn      = document.getElementById('testKeyBtn');
  const resultEl = document.getElementById('testKeyResult');
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Testing...'; }
  if (resultEl) resultEl.style.display = 'none';

  try {
    const result = await apiPost('api/settings/test-key', {});
    if (result.connected) {
      if (resultEl) {
        resultEl.className = 'api-status api-status-success';
        resultEl.innerHTML = '<i class="ph-fill ph-check-circle"></i> Connected to OpenAI (' + (result.model || '') + ')';
        resultEl.style.display = 'inline-flex';
      }
      showToast('OpenAI connection successful!', 'success');
    } else {
      if (resultEl) {
        resultEl.className = 'api-status api-status-error';
        resultEl.innerHTML = '<i class="ph-fill ph-warning-circle"></i> Connection failed — check your key';
        resultEl.style.display = 'inline-flex';
      }
    }
  } catch (err) {
    if (resultEl) {
      resultEl.className = 'api-status api-status-error';
      resultEl.innerHTML = '<i class="ph-fill ph-warning-circle"></i> ' + err.message;
      resultEl.style.display = 'inline-flex';
    }
    showToast(err.message, 'error');
  } finally {
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ph-bold ph-plug"></i> Test Connection'; }
  }
}

async function saveProfile() {
  const name  = document.getElementById('settingsAgencyName')?.value.trim();
  const email = document.getElementById('settingsAgencyEmail')?.value.trim();
  const phone = document.getElementById('settingsAgencyPhone')?.value.trim();
  try {
    await apiPost('api/settings/save', { agency_name: name, agency_email: email, agency_phone: phone });
    TWINPROFIT.agencyName = name || TWINPROFIT.agencyName;
    showToast('Agency profile saved!', 'success');
    markPipelineDone('tab5');
    const agencyInput = document.getElementById('propAgency');
    if (agencyInput && name) agencyInput.value = name;
  } catch (err) {
    showToast(err.message, 'error');
  }
}

// ═══════════════════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {
  initServiceGrid();
  initPlatforms();
  calcUGC();

  // Auto-fill agency name from server settings
  const agencyInput = document.getElementById('propAgency');
  if (agencyInput && !agencyInput.value && TWINPROFIT.agencyName) {
    agencyInput.value = TWINPROFIT.agencyName;
  }

  // Auto-dismiss server flash messages
  const flashContainer = document.getElementById('flashContainer');
  if (flashContainer) {
    setTimeout(() => {
      flashContainer.style.opacity = '0';
      flashContainer.style.transition = 'opacity 0.5s';
      setTimeout(() => flashContainer.remove(), 500);
    }, 4000);
  }

  // Mark Tab 4 done after 2s viewing
  document.querySelectorAll('.tab-btn')[3]?.addEventListener('click', () => {
    setTimeout(() => markPipelineDone('tab4'), 2000);
  });

  // Mark settings done if key already exists
  if (TWINPROFIT.hasKey) markPipelineDone('tab5');
});
