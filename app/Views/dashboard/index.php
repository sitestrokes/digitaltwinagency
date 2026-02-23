<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="app-wrapper">

  <!-- FLASH MESSAGES -->
  <?php if (session()->getFlashdata('success')): ?>
  <div class="toast-container" id="flashContainer">
    <div class="toast toast-success"><i class="ph-bold ph-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?></div>
  </div>
  <?php endif; ?>

  <!-- PIPELINE PROGRESS BAR -->
  <div class="pipeline-bar" id="pipelineBar">
    <div class="pipeline-step" id="ps1" title="Opportunity Finder"></div>
    <div class="pipeline-step" id="ps2" title="Service Packager"></div>
    <div class="pipeline-step" id="ps3" title="Proposal Generator"></div>
    <div class="pipeline-step" id="ps4" title="UGC Calculator"></div>
    <div class="pipeline-step" id="ps5" title="Settings"></div>
  </div>

  <!-- HEADER -->
  <header class="header">
    <div class="header-brand">
      <div class="header-logo">
        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
          <path d="M8 12 H44 V26 H32 V90 H20 V26 H8 Z" fill="#ffffff"/>
          <path d="M40 28 H76 V42 H64 V90 H52 V42 H40 Z" fill="#d4a01e"/>
          <rect x="76" y="52" width="16" height="38" rx="2" fill="#ffffff" opacity="0.15"/>
          <polygon points="20,12 26,2 32,12" fill="#ffffff"/>
        </svg>
      </div>
      <div>
        <div class="header-title">
          TwinProfit <span class="hq-badge">HQ</span>
        </div>
        <div class="header-subtitle">Digital Twin Agency Command Center</div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:16px">
      <div class="header-badge"><i class="ph-fill ph-star-four"></i> $497 VALUE</div>
      <div style="display:flex;align-items:center;gap:10px">
        <span style="font-size:13px;color:rgba(255,255,255,0.7);font-weight:600"><?= esc(session()->get('user_name') ?? '') ?></span>
        <a href="<?= base_url('logout') ?>" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:100px;font-size:13px;font-weight:700;color:rgba(255,255,255,0.8);text-decoration:none;transition:all 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
          <i class="ph-bold ph-sign-out"></i> Logout
        </a>
      </div>
    </div>
  </header>

  <!-- TAB NAV -->
  <nav class="tab-nav" id="tabNav">
    <button class="tab-btn active" onclick="switchTab('tab1', this)">
      <span class="tab-step">01</span>
      <span class="tab-icon"><i class="ph-bold ph-binoculars"></i></span>
      <span class="tab-label">Opportunity Finder</span>
    </button>
    <button class="tab-btn" onclick="switchTab('tab2', this)">
      <span class="tab-step">02</span>
      <span class="tab-icon"><i class="ph-bold ph-package"></i></span>
      <span class="tab-label">Service Packager</span>
    </button>
    <button class="tab-btn" onclick="switchTab('tab3', this)">
      <span class="tab-step">03</span>
      <span class="tab-icon"><i class="ph-bold ph-file-text"></i></span>
      <span class="tab-label">Proposal Generator</span>
    </button>
    <button class="tab-btn" onclick="switchTab('tab4', this)">
      <span class="tab-step">04</span>
      <span class="tab-icon"><i class="ph-bold ph-currency-dollar"></i></span>
      <span class="tab-label">UGC Revenue Calc</span>
    </button>
    <button class="tab-btn" id="settingsTabBtn" onclick="switchTab('tab5', this)">
      <span class="tab-step">05</span>
      <span class="tab-icon"><i class="ph-bold ph-gear"></i></span>
      <span class="tab-label">Settings</span>
      <?php if (!($hasKey ?? false)): ?>
      <span style="width:8px;height:8px;background:#f87171;border-radius:50%;flex-shrink:0;animation:sparkle 2s ease-in-out infinite"></span>
      <?php endif; ?>
    </button>
  </nav>

  <!-- TAB 1: OPPORTUNITY FINDER -->
  <div class="tab-content active" id="tab1">
    <div class="section-header">
      <div class="section-tag"><i class="ph-bold ph-lightning"></i> Step 1 — Prospect Intelligence</div>
      <h2 class="section-title">Opportunity Finder</h2>
      <p class="section-desc">Input any local business's details and get an instant Digital Twin Readiness Score. Find the hottest prospects and know exactly which pain point to lead with.</p>
    </div>

    <div class="bento-grid bento-main">
      <!-- LEFT: Form Card -->
      <div class="card">
        <h3 style="font-family:var(--font-display);font-size:18px;margin-bottom:20px;color:var(--text-dark)">
          Business Details
        </h3>
        <div class="form-group">
          <label class="form-label">Business Name</label>
          <input type="text" class="form-input" id="bizName" placeholder="e.g. Mario's Italian Kitchen">
        </div>
        <div class="form-group">
          <label class="form-label">Business Niche</label>
          <select class="form-select" id="bizNiche">
            <option value="">Select niche...</option>
            <option>Restaurant / Café</option><option>Dental Practice</option>
            <option>Real Estate Agency</option><option>Auto Dealership</option>
            <option>Med Spa / Aesthetics</option><option>Law Firm</option>
            <option>Fitness / Gym</option><option>HVAC / Plumbing</option>
            <option>Roofing / Construction</option><option>Insurance Agency</option>
            <option>Chiropractic</option><option>Pet Services</option>
            <option>Salon / Barbershop</option><option>Photography Studio</option>
            <option>Accounting / Tax Prep</option>
          </select>
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Website Status</label>
            <select class="form-select" id="bizWebsite">
              <option value="modern">Modern & updated</option>
              <option value="outdated">Outdated website</option>
              <option value="none">No website</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Video Content</label>
            <select class="form-select" id="bizVideo">
              <option value="none">No video content</option>
              <option value="minimal">A few old videos</option>
              <option value="active">Regular video</option>
            </select>
          </div>
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Social Presence</label>
            <select class="form-select" id="bizSocial">
              <option value="none">No social media</option>
              <option value="basic">Posts occasionally</option>
              <option value="active">Posts regularly</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Marketing Budget</label>
            <select class="form-select" id="bizBudget">
              <option value="low">Under $1K/mo</option>
              <option value="mid" selected>$1K - $3K/mo</option>
              <option value="high">$3K - $5K/mo</option>
              <option value="premium">$5K+/mo</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Competitors Using Video</label>
          <select class="form-select" id="bizCompetitors">
            <option value="none">None</option>
            <option value="some">A few</option>
            <option value="many">Many</option>
          </select>
        </div>
        <button class="btn btn-primary btn-block mt-8" onclick="analyzeProspect()">
          <i class="ph-bold ph-lightning"></i> Analyze Prospect
        </button>
      </div>

      <!-- RIGHT: Score + Sidebar -->
      <div class="bento-sidebar">
        <div class="card" id="scoreCardPlaceholder" style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;min-height:260px;padding:24px 28px;background:var(--cream);overflow:hidden;position:relative">
          <img src="<?= base_url('assets/images/opportunity-finder.png') ?>" alt="Find profitable prospects" style="width:200px;height:auto;margin-bottom:16px;animation:float 4s ease-in-out infinite;filter:drop-shadow(0 8px 20px rgba(0,0,0,0.08))">
          <div style="font-family:var(--font-display);font-size:20px;font-weight:800;color:var(--text-dark);letter-spacing:-0.3px">Find Your Next Client</div>
          <div style="font-size:13px;color:var(--text-secondary);font-weight:600;margin-top:6px;line-height:1.5">Fill in the form and hit <strong style="color:var(--green-deep)">Analyze</strong><br>to get the readiness score</div>
        </div>
        <div class="card" id="scoreResult" style="display:none;flex:1">
          <div class="text-center">
            <div class="score-ring-wrap" id="scoreRing">
              <svg viewBox="0 0 200 200">
                <circle class="score-bg-circle" cx="100" cy="100" r="86"/>
                <circle class="score-fill-circle" id="scoreCircle" cx="100" cy="100" r="86"
                  stroke-dasharray="540" stroke-dashoffset="540"/>
              </svg>
              <div style="text-align:center">
                <div class="score-value" id="scoreNum">0</div>
                <div class="score-sublabel">READINESS</div>
              </div>
            </div>
            <div id="readinessBadge" class="readiness-badge readiness-hot mb-16">🔥 HOT PROSPECT</div>
          </div>
          <div style="max-height:200px;overflow-y:auto;margin-bottom:16px" id="painPointsList"></div>
          <div class="flex gap-8">
            <button class="btn btn-secondary btn-sm" onclick="showOnePager()" style="flex:1"><i class="ph-bold ph-file-text"></i> One-Pager</button>
            <button class="btn btn-outline btn-sm" onclick="window.print()" style="flex:1"><i class="ph-bold ph-printer"></i> Print</button>
            <button class="btn btn-save-item btn-sm" onclick="saveProspect()" id="saveProspectBtn"><i class="ph-bold ph-floppy-disk"></i> Save</button>
          </div>
        </div>
        <div class="card card-green" style="padding:22px 24px">
          <div style="font-family:var(--font-display);font-size:16px;margin-bottom:4px"><i class="ph-bold ph-crosshair"></i> Close this prospect?</div>
          <div style="font-size:13px;opacity:0.75;margin-bottom:12px">You need iMimic to create their Digital Twin.</div>
          <button class="imimic-cta-btn" style="width:100%;font-size:13px;padding:11px 20px">Get iMimic → Close This Deal</button>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB 2: SERVICE PACKAGER & PRICER -->
  <div class="tab-content" id="tab2">
    <div class="section-header">
      <div class="section-tag"><i class="ph-bold ph-package"></i> Step 2 — Package & Price</div>
      <h2 class="section-title">Service Packager & Pricer</h2>
      <p class="section-desc">Select the AI Digital Twin services you want to offer. We'll auto-generate 3 pricing tiers with profit margins and revenue projections.</p>
    </div>

    <div class="card mb-24">
      <h3 style="font-family:var(--font-display);font-size:18px;margin-bottom:16px;color:var(--text-dark)">Select Services to Offer</h3>
      <div class="grid-2" id="serviceGrid"></div>
      <button class="btn btn-primary btn-block mt-20" onclick="generatePackages()"><i class="ph-bold ph-package"></i> Generate 3-Tier Packages</button>
    </div>

    <div id="tiersOutput" style="display:none">
      <h3 style="font-family:var(--font-display);font-size:22px;margin-bottom:20px;color:var(--text-dark)">Your Package Tiers</h3>
      <div class="grid-3" id="tierCards"></div>

      <div class="card mt-24">
        <h3 style="font-family:var(--font-display);font-size:18px;margin-bottom:4px;color:var(--text-dark)"><i class="ph-bold ph-chart-line-up"></i> Revenue Projections</h3>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:12px;font-weight:600">Based on your Growth tier pricing</p>
        <table class="rev-table">
          <thead><tr><th>Clients</th><th>Monthly Revenue</th><th>Monthly Profit (80%)</th><th>Annual Revenue</th></tr></thead>
          <tbody id="revTableBody"></tbody>
        </table>
      </div>

      <button class="btn btn-secondary mt-16" onclick="savePackage()"><i class="ph-bold ph-floppy-disk"></i> Save This Package Set</button>

      <div class="imimic-cta">
        <div class="imimic-cta-text">
          <h4><i class="ph-bold ph-package"></i> Packages built — now you need the engine</h4>
          <p>Every service above is powered by iMimic's AI Digital Twin technology.</p>
        </div>
        <button class="imimic-cta-btn">Get iMimic → Power Your Packages</button>
      </div>
    </div>
  </div>

  <!-- TAB 3: PROPOSAL GENERATOR -->
  <div class="tab-content" id="tab3">
    <div class="section-header">
      <div class="section-tag"><i class="ph-bold ph-file-text"></i> Step 3 — Close The Deal</div>
      <h2 class="section-title">Proposal Generator</h2>
      <p class="section-desc">Fill in client details, click generate, and get a professional proposal ready to send.</p>
    </div>

    <div class="grid-2">
      <div class="card">
        <h3 style="font-family:var(--font-display);font-size:18px;margin-bottom:20px;color:var(--text-dark)">Client & Project Details</h3>
        <div class="form-group">
          <label class="form-label">Your Agency Name</label>
          <input type="text" class="form-input" id="propAgency" placeholder="e.g. TwinMedia Agency">
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Client Business Name</label>
            <input type="text" class="form-input" id="propClient" placeholder="e.g. Bella's Bistro">
          </div>
          <div class="form-group">
            <label class="form-label">Client Contact Name</label>
            <input type="text" class="form-input" id="propContact" placeholder="e.g. Maria Rossi">
          </div>
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Client Niche</label>
            <select class="form-select" id="propNiche">
              <option value="">Select niche...</option>
              <option>Restaurant / Café</option><option>Dental Practice</option>
              <option>Real Estate Agency</option><option>Auto Dealership</option>
              <option>Med Spa / Aesthetics</option><option>Law Firm</option>
              <option>Fitness / Gym</option><option>HVAC / Plumbing</option>
              <option>Other</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Package Tier</label>
            <select class="form-select" id="propTier" onchange="document.getElementById('propCustomPriceGroup').style.display=this.value==='custom'?'block':'none'">
              <option value="starter">Starter — $997/mo</option>
              <option value="growth" selected>Growth — $1,997/mo</option>
              <option value="premium">Premium — $3,497/mo</option>
              <option value="custom">Custom Package</option>
            </select>
          </div>
        </div>
        <div class="form-group" id="propCustomPriceGroup" style="display:none">
          <label class="form-label">Custom Monthly Price</label>
          <input type="number" class="form-input" id="propCustomPrice" placeholder="e.g. 2500">
        </div>
        <div class="form-group">
          <label class="form-label">Key Services (comma separated)</label>
          <input type="text" class="form-input" id="propServices" placeholder="e.g. AI Avatar, 30 Social Videos/mo, Website Greeter">
        </div>
        <div class="form-group">
          <label class="form-label">Special Notes (optional)</label>
          <textarea class="form-input" id="propNotes" rows="3" placeholder="Any special terms or notes..."></textarea>
        </div>
        <?php if ($hasKey ?? false): ?>
        <button class="btn btn-primary btn-block mt-8" id="generateAiBtn" onclick="generateProposalAI(event)">
          <i class="ph-bold ph-robot"></i> Generate with AI ✨
        </button>
        <button class="btn btn-secondary btn-block mt-8" onclick="generateProposalTemplate()">
          <i class="ph-bold ph-file-text"></i> Use Template
        </button>
        <?php else: ?>
        <button class="btn btn-primary btn-block mt-8" onclick="generateProposalTemplate()">
          <i class="ph-bold ph-file-text"></i> Generate Proposal
        </button>
        <div style="font-size:12px;color:var(--text-muted);text-align:center;margin-top:8px;font-weight:600">
          <i class="ph-bold ph-info"></i> Add OpenAI key in <a href="#" onclick="switchTab('tab5',document.querySelectorAll('.tab-btn')[4]);return false;" style="color:var(--amber-deep)">Settings</a> for AI generation
        </div>
        <?php endif; ?>
      </div>

      <div id="proposalOutput" style="display:none">
        <div class="flex justify-between items-center mb-16">
          <h3 style="font-family:var(--font-display);font-size:18px;color:var(--text-dark)">Preview</h3>
          <div class="flex gap-8">
            <button class="btn btn-secondary btn-sm" onclick="copyProposal()"><i class="ph-bold ph-clipboard-text"></i> Copy</button>
            <button class="btn btn-outline btn-sm" onclick="printProposal()"><i class="ph-bold ph-printer"></i> Print PDF</button>
          </div>
        </div>
        <div class="proposal-preview" id="proposalPreview"></div>
        <div class="imimic-cta mt-24" style="flex-direction:column;text-align:center;gap:12px">
          <div class="imimic-cta-text" style="text-align:center">
            <h4><i class="ph-bold ph-file-text"></i> Proposal ready — now deliver the magic</h4>
            <p>When they sign, you need iMimic to build their Digital Twin.</p>
          </div>
          <button class="imimic-cta-btn">Get iMimic → Deliver On Your Proposal</button>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB 4: UGC REVENUE CALCULATOR -->
  <div class="tab-content" id="tab4">
    <div class="section-header">
      <div class="section-tag"><i class="ph-bold ph-currency-dollar"></i> Step 4 — Add Revenue Stream</div>
      <h2 class="section-title">UGC Revenue Calculator</h2>
      <p class="section-desc">See how much you can earn selling AI-generated UGC videos. 95%+ profit margins vs. human creators.</p>
    </div>

    <!-- Top Stats -->
    <div class="grid-4 mb-24">
      <div class="ugc-stat-card"><div class="ugc-stat-value">95%+</div><div class="ugc-stat-label">Profit Margin</div></div>
      <div class="ugc-stat-card"><div class="ugc-stat-value">$150-500</div><div class="ugc-stat-label">Per Video (AI)</div></div>
      <div class="ugc-stat-card"><div class="ugc-stat-value">$500-2.5K</div><div class="ugc-stat-label">Human Creator Cost</div></div>
      <div class="ugc-stat-card"><div class="ugc-stat-value">10x</div><div class="ugc-stat-label">Output Speed</div></div>
    </div>

    <div class="grid-2">
      <div>
        <div class="card mb-20">
          <h3 style="font-family:var(--font-display);font-size:18px;margin-bottom:16px;color:var(--text-dark)"><i class="ph-bold ph-chart-bar"></i> Scaling Scenarios</h3>
          <div class="form-group">
            <label class="form-label">Average Price Per UGC Video</label>
            <select class="form-select" id="ugcPrice" onchange="calcUGC()">
              <option value="150">$150 (Entry Level)</option>
              <option value="250" selected>$250 (Mid Range)</option>
              <option value="400">$400 (Premium)</option>
              <option value="500">$500 (Agency Rate)</option>
            </select>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Videos Per Week</label>
              <select class="form-select" id="ugcVolume" onchange="calcUGC()">
                <option value="5">5/week</option>
                <option value="10" selected>10/week</option>
                <option value="20">20/week</option>
                <option value="40">40/week (scaled)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">AI Cost Per Video</label>
              <select class="form-select" id="ugcCost" onchange="calcUGC()">
                <option value="5">~$5 (standard)</option>
                <option value="10" selected>~$10 (with extras)</option>
                <option value="15">~$15 (premium)</option>
              </select>
            </div>
          </div>
        </div>

        <div class="card" id="ugcResults">
          <h3 style="font-family:var(--font-display);font-size:18px;margin-bottom:16px;color:var(--text-dark)"><i class="ph-bold ph-coins"></i> Revenue Projections</h3>
          <div id="ugcProjections"></div>
          <div class="mt-24">
            <div style="font-size:12px;font-weight:800;color:var(--text-secondary);letter-spacing:1px;margin-bottom:14px;text-transform:uppercase">Profit Margin Comparison</div>
            <div class="comparison-bar-wrap">
              <div class="comparison-label">
                <span>AI UGC (iMimic)</span>
                <span class="font-mono fw-800 text-green" id="aiMarginPct">96%</span>
              </div>
              <div class="comparison-bar">
                <div class="comparison-fill bar-green" id="aiMarginBar" style="width:96%">96%</div>
              </div>
            </div>
            <div class="comparison-bar-wrap">
              <div class="comparison-label">
                <span>Human UGC Creator</span>
                <span class="font-mono fw-800 text-coral">30-40%</span>
              </div>
              <div class="comparison-bar">
                <div class="comparison-fill bar-coral" style="width:35%">35%</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div>
        <div class="card mb-20">
          <h3 style="font-family:var(--font-display);font-size:18px;margin-bottom:14px;color:var(--text-dark)"><i class="ph-bold ph-tag"></i> UGC Pricing by Niche</h3>
          <div id="nichePricing">
            <div class="ugc-niche-row"><span class="ugc-niche-name">E-commerce / DTC</span><span class="ugc-niche-price">$150-300</span></div>
            <div class="ugc-niche-row"><span class="ugc-niche-name">SaaS / Tech Apps</span><span class="ugc-niche-price">$200-400</span></div>
            <div class="ugc-niche-row"><span class="ugc-niche-name">Health & Wellness</span><span class="ugc-niche-price">$175-350</span></div>
            <div class="ugc-niche-row"><span class="ugc-niche-name">Beauty & Skincare</span><span class="ugc-niche-price">$200-500</span></div>
            <div class="ugc-niche-row"><span class="ugc-niche-name">Food & Beverage</span><span class="ugc-niche-price">$150-300</span></div>
            <div class="ugc-niche-row"><span class="ugc-niche-name">Fitness / Supplements</span><span class="ugc-niche-price">$175-400</span></div>
            <div class="ugc-niche-row"><span class="ugc-niche-name">Real Estate</span><span class="ugc-niche-price">$250-500</span></div>
            <div class="ugc-niche-row"><span class="ugc-niche-name">Financial Services</span><span class="ugc-niche-price">$300-600</span></div>
            <div class="ugc-niche-row"><span class="ugc-niche-name">Local Restaurants</span><span class="ugc-niche-price">$100-250</span></div>
            <div class="ugc-niche-row" style="border:none"><span class="ugc-niche-name">Coaches / Courses</span><span class="ugc-niche-price">$200-450</span></div>
          </div>
        </div>
        <div class="card">
          <h3 style="font-family:var(--font-display);font-size:18px;margin-bottom:14px;color:var(--text-dark)"><i class="ph-bold ph-globe-simple"></i> 10 Platforms to Find UGC Clients</h3>
          <div id="platformList"></div>
        </div>
      </div>
    </div>

    <div class="imimic-cta">
      <div class="imimic-cta-text">
        <h4><i class="ph-bold ph-currency-dollar"></i> UGC is pure profit — but only with the right tool</h4>
        <p>iMimic creates broadcast-quality AI UGC videos in minutes. No actors, no shoots. Just profit.</p>
      </div>
      <button class="imimic-cta-btn">Get iMimic → Start Selling UGC</button>
    </div>
  </div>

  <!-- TAB 5: SETTINGS -->
  <div class="tab-content" id="tab5">
    <div class="section-header">
      <div class="section-tag"><i class="ph-bold ph-gear"></i> Step 5 — Configure Your Tools</div>
      <h2 class="section-title">Settings</h2>
      <p class="section-desc">Add your OpenAI API key to enable AI-powered proposal generation. Your key is encrypted with AES-256 and never stored in plain text.</p>
    </div>

    <div class="grid-2">
      <!-- OpenAI API Key Card -->
      <div class="card">
        <h3 class="settings-section-title"><i class="ph-bold ph-robot"></i> OpenAI API Key</h3>

        <?php if ($hasKey ?? false): ?>
        <div class="api-status api-status-success mb-16">
          <i class="ph-fill ph-check-circle"></i> API key configured — AI proposals enabled
        </div>
        <?php else: ?>
        <div class="api-status api-status-error mb-16">
          <i class="ph-fill ph-warning-circle"></i> No API key — using template proposals only
        </div>
        <?php endif; ?>

        <div class="form-group">
          <label class="form-label">OpenAI API Key</label>
          <div class="api-key-input-wrap">
            <input type="password" class="form-input" id="settingsApiKey"
              placeholder="sk-..."
              value="<?= esc($maskedKey ?? '') ?>"
              autocomplete="off">
          </div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:6px;font-weight:600">
            Get your key at <a href="https://platform.openai.com/api-keys" target="_blank" style="color:var(--amber-deep)">platform.openai.com/api-keys</a>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">AI Model</label>
          <div class="model-selector" id="modelSelector">
            <label class="model-option <?= (($settings['openai_model'] ?? 'gpt-4.1-nano') === 'gpt-4.1-nano') ? 'selected' : '' ?>" onclick="selectModel('gpt-4.1-nano', this)">
              <input type="radio" name="model" value="gpt-4.1-nano" <?= (($settings['openai_model'] ?? 'gpt-4.1-nano') === 'gpt-4.1-nano') ? 'checked' : '' ?>>
              <i class="ph-bold ph-lightning"></i> gpt-4.1-nano <span style="font-size:11px;opacity:0.6;font-weight:600">(Fast · Cheap)</span>
            </label>
            <label class="model-option <?= (($settings['openai_model'] ?? '') === 'gpt-4o-mini') ? 'selected' : '' ?>" onclick="selectModel('gpt-4o-mini', this)">
              <input type="radio" name="model" value="gpt-4o-mini" <?= (($settings['openai_model'] ?? '') === 'gpt-4o-mini') ? 'checked' : '' ?>>
              <i class="ph-bold ph-brain"></i> gpt-4o-mini <span style="font-size:11px;opacity:0.6;font-weight:600">(Balanced)</span>
            </label>
            <label class="model-option <?= (($settings['openai_model'] ?? '') === 'gpt-4o') ? 'selected' : '' ?>" onclick="selectModel('gpt-4o', this)">
              <input type="radio" name="model" value="gpt-4o" <?= (($settings['openai_model'] ?? '') === 'gpt-4o') ? 'checked' : '' ?>>
              <i class="ph-bold ph-star"></i> gpt-4o <span style="font-size:11px;opacity:0.6;font-weight:600">(Best quality)</span>
            </label>
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:20px">
          <button class="btn btn-primary" onclick="saveApiKey()" style="flex:1">
            <i class="ph-bold ph-floppy-disk"></i> Save Key
          </button>
          <button class="btn btn-secondary" onclick="testApiKey()" id="testKeyBtn">
            <i class="ph-bold ph-plug"></i> Test Connection
          </button>
        </div>

        <div id="testKeyResult" style="margin-top:12px;display:none"></div>
      </div>

      <!-- Agency Profile + Account -->
      <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
          <h3 class="settings-section-title"><i class="ph-bold ph-buildings"></i> Agency Profile</h3>
          <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;font-weight:600">This auto-fills your proposals and one-pagers.</p>
          <div class="form-group">
            <label class="form-label">Agency Name</label>
            <input type="text" class="form-input" id="settingsAgencyName"
              placeholder="e.g. TwinMedia Agency"
              value="<?= esc($settings['agency_name'] ?? '') ?>">
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" class="form-input" id="settingsAgencyEmail"
                placeholder="hello@agency.com"
                value="<?= esc($settings['agency_email'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Phone</label>
              <input type="text" class="form-input" id="settingsAgencyPhone"
                placeholder="+1 (555) 000-0000"
                value="<?= esc($settings['agency_phone'] ?? '') ?>">
            </div>
          </div>
          <button class="btn btn-secondary btn-block mt-8" onclick="saveProfile()">
            <i class="ph-bold ph-floppy-disk"></i> Save Profile
          </button>
        </div>

        <div class="card card-green">
          <h3 style="font-family:var(--font-display);font-size:16px;margin-bottom:12px;color:#fff">
            <i class="ph-bold ph-user-circle"></i> Your Account
          </h3>
          <div class="settings-account-chip" style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.85)">
            <i class="ph-bold ph-user"></i> <?= esc(session()->get('user_name') ?? '') ?>
          </div>
          <div class="settings-account-chip" style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.7)">
            <i class="ph-bold ph-envelope"></i> <?= esc(session()->get('user_email') ?? '') ?>
          </div>
          <div style="margin-top:16px">
            <a href="<?= base_url('logout') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:100px;color:rgba(255,255,255,0.8);font-size:13px;font-weight:700;text-decoration:none;transition:all 0.2s">
              <i class="ph-bold ph-sign-out"></i> Sign Out
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

</div><!-- /app-wrapper -->

<!-- ONE-PAGER OVERLAY -->
<div class="one-pager-overlay" id="onePagerOverlay">
  <div class="one-pager" id="onePagerContent"></div>
</div>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="toastContainer"></div>

<?= $this->endSection() ?>
