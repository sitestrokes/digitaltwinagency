<?php

namespace App\Controllers;

use App\Models\UserSettingModel;
use App\Models\ProspectModel;
use App\Models\ProposalModel;
use App\Services\AuthService;
use App\Services\EncryptionService;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $userId    = session()->get('user_id');
        $settingsModel  = new UserSettingModel();
        $encryptionSvc  = new EncryptionService();

        $settings   = $settingsModel->getByUserId($userId);
        $hasKey     = !empty($settings['openai_api_key']);
        $maskedKey  = '';
        $agencyName = $settings['agency_name'] ?? '';

        if ($hasKey) {
            $plain     = $encryptionSvc->decryptKey($settings['openai_api_key']);
            $maskedKey = $encryptionSvc->maskKey($plain);
        }

        $prospectModel = new ProspectModel();
        $proposalModel = new ProposalModel();

        return view('dashboard/index', [
            'title'       => 'TwinProfit HQ — Agency Command Center',
            'hasKey'      => $hasKey,
            'maskedKey'   => $maskedKey,
            'agencyName'  => $agencyName,
            'settings'    => $settings ?? [],
            'recentProspects' => $prospectModel->getRecentByUser($userId, 5),
            'recentProposals' => $proposalModel->getRecentByUser($userId, 5),
        ]);
    }
}
