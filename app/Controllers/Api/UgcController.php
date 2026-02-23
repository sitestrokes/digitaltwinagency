<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class UgcController extends BaseController
{
    public function calculate(): ResponseInterface
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getJSON(true) ?? [];

        $price  = (int) ($data['price'] ?? 250);
        $volume = (int) ($data['volume'] ?? 10);
        $cost   = (int) ($data['cost'] ?? 10);

        if ($price <= 0 || $volume <= 0 || $cost < 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'data' => null, 'error' => 'Invalid calculation inputs.',
            ]);
        }

        $weeklyRevenue = $price * $volume;
        $weeklyProfit  = ($price - $cost) * $volume;
        $monthlyRevenue = round($weeklyRevenue * 4.33);
        $monthlyProfit  = round($weeklyProfit * 4.33);
        $annualRevenue  = $monthlyRevenue * 12;
        $annualProfit   = $monthlyProfit * 12;
        $margin         = round(($weeklyProfit / $weeklyRevenue) * 100);

        return $this->response->setJSON([
            'data' => [
                'weekly_revenue'  => $weeklyRevenue,
                'weekly_profit'   => $weeklyProfit,
                'monthly_revenue' => $monthlyRevenue,
                'monthly_profit'  => $monthlyProfit,
                'annual_revenue'  => $annualRevenue,
                'annual_profit'   => $annualProfit,
                'margin_pct'      => $margin,
            ],
            'error' => null,
        ]);
    }
}
