<?php

namespace App\Services;

class ProposalService
{
    /**
     * Generate a static template-based proposal HTML
     */
    public function generateTemplate(array $p): string
    {
        $agency   = esc($p['agency_name'] ?? 'Your Agency');
        $client   = esc($p['client_name'] ?? 'Valued Client');
        $contact  = esc($p['contact_name'] ?? 'Team');
        $niche    = esc($p['niche'] ?? 'your industry');
        $tier     = ucfirst($p['tier'] ?? 'growth');
        $price    = number_format((int)($p['price'] ?? 1997));
        $notes    = esc($p['notes'] ?? '');
        $today    = date('F j, Y');
        $validUntil = date('F j, Y', strtotime('+14 days'));

        $services = is_array($p['services'] ?? null) ? $p['services'] : ['AI Digital Twin Creation', 'Monthly Social Videos', 'Website AI Greeter'];
        $serviceRows = '';
        foreach ($services as $svc) {
            $serviceRows .= '<tr><td>' . esc($svc) . '</td><td style="color:#1b2d4f;font-weight:700">✓ Included</td></tr>';
        }

        return <<<HTML
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px">
  <div>
    <h2>Digital Twin Service Proposal</h2>
    <div class="prop-meta">Prepared by <strong>{$agency}</strong> &bull; {$today}</div>
  </div>
  <div style="text-align:right">
    <div style="font-family:var(--font-display);font-size:20px">{$agency}</div>
    <div style="font-size:12px;color:#888">AI-Powered Digital Solutions</div>
  </div>
</div>
<h3>Dear {$contact},</h3>
<p>Thank you for the opportunity to present this proposal for <strong>{$client}</strong>. We recommend our <strong>{$tier} Digital Twin Package</strong> — designed specifically for {$niche} businesses like yours.</p>
<h3>The Challenge</h3>
<p>Traditional video production costs \$2,000–\$10,000 per video. Most staff won't go on camera. You need fresh, professional video content to stay competitive but can't afford the traditional route. Your competitors are already using video — every day without it is market share lost.</p>
<h3>Our Solution: AI Digital Twins</h3>
<p>A hyper-realistic AI avatar trained on your brand voice creates unlimited professional video content at a fraction of traditional costs. Fresh, engaging video every day — without anyone stepping in front of a camera.</p>
<h3>{$tier} Package — What's Included</h3>
<table class="prop-table">
  <thead><tr><th>Service</th><th>Status</th></tr></thead>
  <tbody>{$serviceRows}</tbody>
</table>
<div class="prop-total">Monthly Investment: \${$price}/mo</div>
HTML . ($notes ? "<h3>Notes</h3><p>{$notes}</p>" : '') . <<<HTML

<h3>Next Steps</h3>
<p><strong>1.</strong> 15-min onboarding call &nbsp;&rarr;&nbsp; <strong>2.</strong> Build your Digital Twin (3–5 days) &nbsp;&rarr;&nbsp; <strong>3.</strong> Launch &amp; deliver content</p>
<div class="prop-footer">
  <p><strong>Valid until {$validUntil}.</strong> Ready to transform {$client}'s digital presence?</p>
  <p style="margin-top:8px">— <strong>{$agency}</strong></p>
</div>
HTML;
    }
}
