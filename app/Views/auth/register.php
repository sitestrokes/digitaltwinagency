<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title ?? 'Create Account — TwinProfit HQ') ?></title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{height:100%}
body{font-family:'Plus Jakarta Sans',sans-serif;background:#1b2d4f;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;-webkit-font-smoothing:antialiased}
.auth-card{background:#fff;border-radius:28px;padding:44px 40px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,0.3)}
.auth-logo{display:flex;align-items:center;gap:12px;margin-bottom:28px}
.auth-brand{font-family:'Outfit',sans-serif;font-size:22px;font-weight:800;color:#1b2d4f;letter-spacing:-0.5px;display:flex;align-items:center;gap:8px}
.auth-brand .badge{display:inline-block;padding:2px 9px;background:#d4a01e;color:#1a1a2e;border-radius:7px;font-size:12px;font-weight:800}
.auth-tagline{font-size:11px;color:#8a95a8;font-weight:600;margin-top:1px}
.auth-title{font-family:'Outfit',sans-serif;font-size:24px;font-weight:800;color:#1a1a2e;letter-spacing:-0.5px;margin-bottom:4px}
.auth-subtitle{font-size:14px;color:#6b7a94;font-weight:500;margin-bottom:24px}
.form-group{margin-bottom:14px}
.form-label{display:block;font-size:13px;font-weight:800;color:#1a1a2e;margin-bottom:5px;letter-spacing:0.3px}
.form-input{width:100%;padding:13px 16px;background:#f0f3f8;border:2px solid transparent;border-radius:14px;color:#1a1a2e;font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;font-weight:600;outline:none;transition:all 0.2s}
.form-input:focus{background:#fff;border-color:#d4a01e;box-shadow:0 0 0 4px rgba(212,160,30,0.15)}
.form-input::placeholder{color:#8a95a8;font-weight:500}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.btn-auth{width:100%;padding:15px;background:#d4a01e;border:none;border-radius:100px;color:#1a1a2e;font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:800;cursor:pointer;transition:all 0.3s;margin-top:8px;box-shadow:0 4px 16px rgba(212,160,30,0.3)}
.btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(212,160,30,0.4);background:#e0ad28}
.auth-footer{text-align:center;margin-top:18px;font-size:14px;color:#6b7a94;font-weight:500}
.auth-footer a{color:#1b2d4f;font-weight:700;text-decoration:none}
.auth-footer a:hover{text-decoration:underline}
.alert{padding:12px 16px;border-radius:12px;font-size:14px;font-weight:600;margin-bottom:16px}
.alert-error{background:#ffe0e6;color:#cc2244;border-left:4px solid #cc2244}
.alert-success{background:#d4edda;color:#155724;border-left:4px solid #28a745}
.errors-list{padding:12px 16px;background:#ffe0e6;border-radius:12px;border-left:4px solid #cc2244;margin-bottom:16px}
.errors-list li{font-size:13px;font-weight:600;color:#cc2244;margin-bottom:4px;list-style:none}
.value-props{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:24px}
.value-prop{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:#3d3d5c}
.value-prop-icon{width:20px;height:20px;background:#d4a01e;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0}
</style>
</head>
<body>
<div class="auth-card">
  <div class="auth-logo">
    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" width="40" height="40">
      <path d="M8 12 H44 V26 H32 V90 H20 V26 H8 Z" fill="#1b2d4f"/>
      <path d="M40 28 H76 V42 H64 V90 H52 V42 H40 Z" fill="#d4a01e"/>
      <polygon points="20,12 26,2 32,12" fill="#1b2d4f"/>
    </svg>
    <div>
      <div class="auth-brand">TwinProfit <span class="badge">HQ</span></div>
      <div class="auth-tagline">Digital Twin Agency Command Center</div>
    </div>
  </div>

  <div class="auth-title">Start your free account</div>
  <div class="auth-subtitle">Your complete Digital Twin agency toolkit</div>

  <div class="value-props">
    <div class="value-prop"><div class="value-prop-icon">🎯</div> Opportunity Finder</div>
    <div class="value-prop"><div class="value-prop-icon">📦</div> Service Packager</div>
    <div class="value-prop"><div class="value-prop-icon">📄</div> AI Proposals</div>
    <div class="value-prop"><div class="value-prop-icon">💰</div> UGC Calculator</div>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>
  <?php $errors = session()->getFlashdata('errors'); if (!empty($errors)): ?>
    <ul class="errors-list">
      <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form action="<?= base_url('register') ?>" method="post">
    <?= csrf_field() ?>
    <div class="form-group">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-input" placeholder="Your name" value="<?= esc(old('name')) ?>" required>
    </div>
    <div class="form-group">
      <label class="form-label">Email Address</label>
      <input type="email" name="email" class="form-input" placeholder="you@agency.com" value="<?= esc(old('email')) ?>" required>
    </div>
    <div class="grid-2">
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-input" placeholder="Min 8 chars" required>
      </div>
      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirm" class="form-input" placeholder="Repeat password" required>
      </div>
    </div>
    <button type="submit" class="btn-auth">Create Account →</button>
  </form>

  <div class="auth-footer">
    Already have an account? <a href="<?= base_url('login') ?>">Sign in</a>
  </div>
</div>
</body>
</html>
