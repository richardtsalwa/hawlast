<?php
/**
 * Shared <head> + navigation for the redesigned Hawlast pages:
 * 404.php, buyairtime.php, terms-of-service.php, privacy-policy.php.
 *
 * index.php keeps its own copy of this chrome so the homepage is untouched.
 * Set $pageTitle / $pageDesc / $pageRobots before including this file.
 */
$pageTitle  = $pageTitle  ?? 'Hawlast Ventures';
$pageDesc   = $pageDesc   ?? 'Kenyan business, run on modern software. Website, email, M-Pesa, WhatsApp and ERP.';
$pageRobots = $pageRobots ?? 'index,follow';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="description" content="<?php echo htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="robots" content="<?php echo htmlspecialchars($pageRobots, ENT_QUOTES, 'UTF-8'); ?>">
<title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>

<link rel="icon" href="<?php echo hawlast_url( 'images/hawlast-mark-32.png' ) . '?v=2'; ?>" sizes="32x32" type="image/png">
<link rel="icon" href="<?php echo hawlast_url( 'images/hawlast-mark.svg' ) . '?v=2'; ?>" type="image/svg+xml">
<link rel="apple-touch-icon" href="<?php echo hawlast_url( 'images/hawlast-mark-192.png' ) . '?v=2'; ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --ink:#10204A; --paper:#FFFFFF; --card:#FFFFFF; --soft:#FBF7F1; --muted:#5B6470; --line:#E6E1D8; --orange:#E8590C;
  box-sizing:border-box;
  padding-top:env(safe-area-inset-top,0px); padding-bottom:env(safe-area-inset-bottom,0px);
}
html{scroll-padding-top:calc(env(safe-area-inset-top,0px) + 90px);scroll-behavior:smooth}
*{box-sizing:border-box}
body{margin:0;background:var(--paper);color:var(--ink);font-family:Inter,system-ui,sans-serif;font-size:17px;line-height:1.6}
h1,h2,h3{font-weight:700;font-family:Inter,system-ui,sans-serif;line-height:1.08;margin:0;letter-spacing:-.03em}
a{color:inherit}
.wrap{max-width:1120px;margin:0 auto;padding:0 24px}
:focus-visible{outline:3px solid var(--orange);outline-offset:3px}

/* nav */
header{position:sticky;top:env(safe-area-inset-top,0px);z-index:10;padding:14px 16px 0}
nav{max-width:1120px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:16px;background:var(--card);border:1px solid var(--line);border-radius:999px;padding:10px 12px 10px 22px;box-shadow:0 6px 24px rgba(11,15,20,.08)}
.logo{display:flex;align-items:center;text-decoration:none}
.logo img{height:40px;width:auto;display:block}
.links{display:flex;gap:30px}
.links a{text-decoration:none;font-weight:500;font-size:15px;color:var(--muted)}
.links a:hover{color:var(--ink)}
.actions{display:flex;gap:8px;align-items:center}
.btn{display:inline-block;text-decoration:none;font-weight:600;font-size:15px;padding:10px 20px;border-radius:999px;border:1.5px solid transparent;white-space:nowrap}
.btn-fill{background:var(--orange);color:#0B0F14;border-color:var(--orange)}
.btn-fill:hover{filter:brightness(1.08)}
.btn-line{border-color:var(--ink);color:var(--ink)}
.btn-line:hover{background:var(--ink);color:var(--paper)}
.btn-lg{padding:15px 28px;font-size:17px}
/* page heading */
.page-head{padding:70px 0 44px;border-bottom:1px solid var(--line);background:var(--soft)}
.page-head h1{font-size:clamp(36px,5.4vw,62px);max-width:18ch}
.page-head p{color:var(--muted);font-size:19px;max-width:58ch;margin:20px 0 0}
.pad-y{padding:72px 0}

/* playful 404 */
.big-404{font-size:clamp(88px,20vw,190px);line-height:.86;color:var(--orange);letter-spacing:-.05em;margin:0 0 8px}
.slug{font-size:clamp(20px,2.6vw,27px);color:var(--ink);max-width:44ch;margin:0 0 18px}
.slug em{font-style:normal;color:var(--orange)}
.why{color:var(--muted);max-width:56ch;margin:0 0 34px}
.why span{display:block}
.why b{color:var(--ink);font-weight:600}

/* link cards */
.cards{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
.card{display:flex;gap:18px;align-items:flex-start;border:1px solid var(--line);border-radius:18px;padding:24px;background:var(--card);text-decoration:none;transition:transform .15s ease,box-shadow .15s ease}
.card:hover{transform:translateY(-3px);box-shadow:0 12px 26px rgba(11,15,20,.10)}
.card .ico{flex:0 0 52px;width:52px;height:52px;border-radius:14px;background:var(--orange);color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px}
.card:nth-child(2) .ico{background:var(--ink)}
.card:nth-child(3) .ico{background:var(--orange);filter:brightness(.86)}
.card:nth-child(4) .ico{background:var(--soft);color:var(--orange);border:1px solid var(--line)}
.card h3{font-size:20px;margin:0 0 6px}
.card p{margin:0;color:var(--muted);font-size:15px}
.card:hover h3{color:var(--orange)}
/* 3 step form */
.steps{display:grid;gap:18px;max-width:760px}
.step{border:1px solid var(--line);border-radius:20px;padding:28px;background:var(--card);box-shadow:0 8px 22px rgba(11,15,20,.06)}
.step[hidden]{display:none}
.step-top{display:flex;align-items:center;gap:14px;margin-bottom:18px}
.step-n{flex:0 0 40px;width:40px;height:40px;border-radius:50%;background:var(--orange);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:17px}
.step h3{font-size:22px}
.step-top p{margin:3px 0 0;color:var(--muted);font-size:14.5px}
.field{display:flex;flex-direction:column;gap:8px;margin-bottom:16px}
.field label{font-size:14px;font-weight:600;color:var(--muted)}
.field input{border:1.5px solid var(--line);border-radius:12px;padding:15px 17px;font:inherit;font-size:18px;background:var(--paper);color:var(--ink);width:100%}
.field input:focus{border-color:var(--orange)}
.amount{display:flex;align-items:center;gap:14px;border:1.5px solid var(--line);border-radius:12px;padding:6px 17px}
.amount input{border:0;padding:9px 0;font-size:22px;font-weight:700;width:100%}
.amount .cur{font-weight:700;font-size:15px;color:var(--muted);letter-spacing:.08em}
.hint{font-size:13.5px;color:var(--muted);margin:0}
.msg{min-height:22px;font-size:14px;margin:0 0 8px;color:#B3261E}
.msg.ok{color:#1E7A3C}
.review{display:flex;align-items:center;gap:14px;border:1px solid var(--line);border-radius:12px;padding:14px 17px;background:var(--soft);margin-bottom:18px}
.review .num{font-size:22px;font-weight:700;letter-spacing:.04em}
.review .who{font-size:13.5px;color:var(--muted);margin-left:auto}
.row{display:flex;gap:12px;flex-wrap:wrap;align-items:center}

/* mpesa prompt */
.kiosk{margin:0 0 18px;border:2px dashed var(--orange);border-radius:18px;padding:24px;background:#FFF6F0}
.kiosk h4{margin:0 0 14px;font-size:17px}
.kiosk ol{margin:0;padding-left:22px}
.kiosk li{margin-bottom:7px}
.kiosk b{color:var(--orange)}
.ticket{display:flex;align-items:baseline;gap:12px;border-top:1px dashed rgba(232,89,12,.5);margin-top:16px;padding-top:16px}
.ticket .big{font-size:30px;font-weight:800;letter-spacing:.06em;color:var(--orange)}
.ticket .lbl{font-size:13px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}
.ticket .acct{margin-left:auto;font-size:24px;font-weight:800;letter-spacing:.06em}

/* loading wheel */
.loading{display:none;align-items:center;gap:16px;border:1px solid var(--line);border-radius:16px;padding:20px;background:var(--soft)}
.loading.on{display:flex}
.spinner{flex:0 0 42px;width:42px;height:42px;border-radius:50%;border:4px solid rgba(232,89,12,.18);border-top-color:var(--orange);animation:spin .9s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.loading p{margin:0;font-size:15px;color:var(--muted)}
.loading b{color:var(--ink);display:block;font-size:16px}

/* delivery receipt */
.receipt{display:none;border:1px solid var(--line);border-top:5px solid var(--orange);border-radius:18px;padding:26px;background:var(--card);box-shadow:0 10px 26px rgba(11,15,20,.07);max-width:760px}
.receipt.on{display:block}
.receipt h3{font-size:24px;margin-bottom:4px}
.receipt .when{color:var(--muted);font-size:14.5px;margin:0 0 20px}
.receipt dl{display:grid;grid-template-columns:auto 1fr;gap:12px 22px;margin:0}
.receipt dt{font-size:14px;color:var(--muted)}
.receipt dd{margin:0;font-weight:600;font-size:16px;word-break:break-word}
.receipt .badge{display:inline-block;padding:5px 14px;border-radius:999px;font-size:13px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;background:#E7F4EC;color:#1E7A3C}
.receipt.fail .badge{background:#FBE9E7;color:#B3261E}
.receipt.fail{border-top-color:#B3261E}

/* legal / terms */
.legal{max-width:74ch}
.legal h2{font-size:27px;margin:38px 0 12px}
.legal h3{font-size:20px;margin:28px 0 10px}
.legal p{color:var(--muted);margin:0 0 14px}
.legal i{color:var(--muted)}
.legal a{color:var(--orange);text-decoration:underline}
.meta{color:var(--muted);font-size:14px;margin:34px 0 0;border-top:1px solid var(--line);padding-top:18px}

footer{border-top:1px solid var(--line);padding:28px 0 40px;color:var(--muted);font-size:14px}
footer .wrap{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap}
footer a{margin-left:18px}

@media (max-width:860px){
  .links{display:none}
  .cards{grid-template-columns:1fr}
  .btn-line.hide-s{display:none}
  .logo img{height:34px}
  nav{padding-left:16px}
  .page-head{padding-top:52px}
  .receipt dl{grid-template-columns:1fr;gap:4px}
  .receipt dd{margin-bottom:12px}
}
@media (prefers-reduced-motion:reduce){
  html{scroll-behavior:auto}
  .spinner{animation-duration:2.4s}
}
</style>
</head>
<body>

<?php require __DIR__ . '/site_nav.php'; ?>
<main>