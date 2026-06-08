<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Faculty Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/faculty_dashboard.css') }}">
</head>
<body>

<div class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-text">SKED<span>YUL</span></div>
  </div>

  <div class="sidebar-user">
    <div class="sidebar-avatar" id="sb-avatar">A</div>
    <div class="sidebar-user-info">
      <div class="sidebar-user-name" id="sb-name">Tech Admin</div>
      <div class="sidebar-user-role" id="sb-role">Technical Administrator</div>
    </div>
  </div>

  <div class="sidebar-nav" id="sidebar-nav"></div>

  <div class="sidebar-bottom">
    <button class="btn-logout" onclick="logout()">⬅ Sign Out</button>
  </div>
</div>

<!-- MAIN -->
<div class="main">
  <div class="topbar">
    <div class="topbar-title" id="topbar-title">Dashboard</div>

    <div id="topbar-notif-bell" style="position:relative;">
      <button onclick="toggleNotifDropdown()" style="padding:8px 14px;border-radius:8px;background:var(--grey2);border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;display:flex;align-items:center;gap:6px;">
        Notifications
        <span id="notif-count" style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">3</span>
      </button>

      <div id="notif-dropdown" style="display:none;position:absolute;top:44px;right:0;width:340px;background:var(--white);border:1px solid var(--border);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;overflow:hidden;">
        <div style="padding:14px 16px;border-bottom:1px solid var(--border);font-size:14px;font-weight:700;color:var(--text);">
          Notifications
        </div>

        <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>

        <div style="padding:10px 16px;border-top:1px solid var(--border);text-align:center;">
          <button onclick="markAllRead()" style="font-size:12px;color:var(--blue);font-weight:600;background:none;border:none;cursor:pointer;font-family:var(--font);">
            Mark all as read
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>