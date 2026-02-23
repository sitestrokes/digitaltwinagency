<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title ?? 'TwinProfit HQ') ?></title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/regular/style.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/bold/style.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/fill/style.css" />
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
<?= $this->renderSection('head') ?>
</head>
<body data-has-key="<?= esc($hasKey ?? 'false') ?>">

<?= $this->renderSection('content') ?>

<script>
window.TWINPROFIT = {
    baseUrl: '<?= base_url() ?>',
    csrfName: '<?= csrf_token() ?>',
    csrfHash: '<?= csrf_hash() ?>',
    userName: '<?= esc(session()->get('user_name') ?? '') ?>',
    hasKey: <?= ($hasKey ?? false) ? 'true' : 'false' ?>,
    agencyName: '<?= esc($agencyName ?? '') ?>',
};
</script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
