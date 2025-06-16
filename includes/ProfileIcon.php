<?php
function renderProfileIcon($userName, $userRole) {
    $onlineStatus = isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] < 300) ? 'online' : 'offline';
    $statusColor = $onlineStatus === 'online' ? 'bg-green-500' : 'bg-gray-400';
?>
    <div class="flex items-center gap-3">
        <div class="relative">
            <div class="w-10 h-10 rounded-full bg-sky-500 flex items-center justify-center text-white font-semibold">
                <?= strtoupper(substr($userName, 0, 1)) ?>
            </div>
            <span class="absolute bottom-0 right-0 w-3 h-3 <?= $statusColor ?> rounded-full border-2 border-white dark:border-slate-800"></span>
        </div>
        <div class="flex flex-col">
            <span class="font-semibold text-slate-900 dark:text-slate-100"><?= htmlspecialchars($userName) ?></span>
            <span class="text-sm text-slate-500 dark:text-slate-400"><?= ucfirst($userRole) ?></span>
        </div>
    </div>
<?php
}
?> 