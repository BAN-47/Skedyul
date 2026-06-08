<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SKEDYUL – <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard'; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
</head>
<style>
.topbar {
  position: relative;
  z-index: 10;
}

.main {
  display: flex;
  flex-direction: column;
}
</style>
<body>

<!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-title">Dashboard</div>
      <div class="notif-wrap">
        <button class="btn-notif" onclick="toggleNotif()">
          Notifications <span class="notif-badge" id="notif-count">3</span>
        </button>
        <div class="notif-dropdown" id="notif-dropdown">
          <div class="p-3 fw-bold" style="font-size:14px;border-bottom:1px solid var(--border);">Notifications</div>
          <div id="notif-list"></div>
          <div class="p-2 text-center" style="border-top:1px solid var(--border);">
            <button onclick="markAllRead()" style="font-size:12px;color:var(--blue);font-weight:600;background:none;border:none;cursor:pointer;font-family:var(--font);">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>
<!-- END TOPBAR -->