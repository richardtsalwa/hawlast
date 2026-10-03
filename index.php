<?php
require 'hawlastke.php';
require 'config.php';

/**
 * Escape a URL for output as an HTML attribute.
 *
 * The application is not WordPress, so esc_url() is unavailable here.
 */
function hawlast_e( string $url ): string {
    return htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
}

if(isset($_GET['a'])) {
$cookie_name = "affiliate";
$cookie_value = $_GET['a'];
setcookie($cookie_name, $cookie_value, time() + (86400 * 60), "/");
//Cookie is available on entire website and will expire after 60 days
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="google-site-verification" content="YZPXJE-6W2iSvgrFkQPcGRTh3md86Qv7PnNa0DTcA2g" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="keywords" content="Kenya web hosting, wordpress hosting,  website hosting kenya, kenya hosting, web host, joomla hosting." />
<meta name="description" content="Kenyan business, run on modern software. Website, email, M-Pesa, WhatsApp and ERP, designed well and kept running at 99.95% uptime." />
<title>Hawlast Ventures | Kenyan business, run on modern software</title>

<link rel="icon" href="images/hawlast-mark-32.png?v=2" sizes="32x32" type="image/png">
<link rel="icon" href="images/hawlast-mark.svg?v=2" type="image/svg+xml">
<link rel="apple-touch-icon" href="images/hawlast-mark-192.png?v=2">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<script  type="application/ld+json" id="website-json-ld">
{
    "@context":"http://schema.org",
    "@type":"WebSite",
    "name":"HAWLAST",
    "alternateName":"HAWLAST VENTURES",
    "url":"https://www.hawlast.com"
}
</script>
<script  type="application/ld+json" id="social-json-ld">
{
    "@context":"http://schema.org",
    "@type":"Organization",
    "name":"Hawlast Ventures",
    "url":"https://www.hawlast.com",
    "logo": "https://www.hawlast.com/images/domain-registration.gif",
    "sameAs":[
        "https://www.facebook.com/hawlast",
        "https://twitter.com/hawlast"
    ]
}
</script>

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@hawlast">
<meta name="twitter:title" content="Kenyan business, run on modern software.">
<meta name="twitter:description" content="Website, email, M-Pesa, WhatsApp and ERP, designed well and kept running at 99.95% uptime.">
<meta name="twitter:creator" content="@RichardTsalwa">
<!-- Twitter Summary card images must be at least 120x120px -->
<meta name="twitter:image" content="https://www.hawlast.com/images/hawlast-get-online.jpg">

<!-- Open Graph data -->
<meta property="og:title" content="Kenyan business, run on modern software." />
<meta property="og:type" content="article" />
<meta property="og:url" content="https://www.hawlast.com/" />
<meta property="og:image" content="https://www.hawlast.com/images/hawlast-get-online.jpg" />
<meta property="og:description" content="Website, email, M-Pesa, WhatsApp and ERP, designed well and kept running at 99.95% uptime." />
<meta property="og:site_name" content="Hawlast" />
<meta property="fb:admins" content="1166791883" />
<meta property="fb:app_id" content="202015096126" />

<!-- Google Analytics - preserved verbatim from the previous homepage -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-16346251-1', 'auto');
  ga('send', 'pageview');
</script><style>
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
.btn-fill{background:var(--orange);color:#0B0F14}
.btn-fill:hover{filter:brightness(1.08)}
.btn-line{border-color:var(--ink);color:var(--ink)}
.btn-line:hover{background:var(--ink);color:var(--paper)}
.btn-lg{padding:15px 28px;font-size:17px}

/* hero */
.hero{padding:88px 0 72px}
.hero h1{font-size:clamp(42px,7vw,84px);max-width:13ch;font-weight:700}
.hero p.sub{max-width:52ch;font-size:20px;color:var(--muted);margin:28px 0 36px}
.hero .cta{display:flex;gap:12px;flex-wrap:wrap}
.hero-grid{display:grid;grid-template-columns:1.25fr .75fr;gap:48px;align-items:end}
.ledger{background:var(--card);border:1px solid var(--line);border-radius:20px;padding:26px}
.ledger h3{font-size:18px;margin-bottom:14px}
.ledger ul{list-style:none;margin:0;padding:0}
.ledger li{display:flex;justify-content:space-between;gap:12px;padding:11px 0;border-top:1px solid var(--line);font-size:15px}
.ledger li b{font-weight:600}
.ok{color:var(--orange);font-weight:600}
.note{font-size:13px;color:var(--muted);margin:14px 0 0}

/* proof */
.proof{border-block:1px solid var(--line)}
.proof .wrap{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;padding-block:30px}
.proof strong{display:block;font-family:Inter,system-ui,sans-serif;font-size:32px;font-weight:700;letter-spacing:-.02em}
.proof span{color:var(--muted);font-size:14px}
/* mission */
.mission{background:var(--ink);color:#FFFFFF;padding:96px 0}
.mission .wrap{border-left:6px solid var(--orange);padding-left:32px}
.mission p{font-family:Inter,system-ui,sans-serif;font-size:clamp(26px,3.6vw,44px);line-height:1.2;max-width:28ch;margin:0 0 24px;letter-spacing:-.015em}
.mission p.small{font-family:Inter,system-ui,sans-serif;font-size:18px;max-width:58ch;color:#C9D1E6;line-height:1.6}

/* solutions */
section.pad{padding:96px 0}
.sec-head{max-width:30ch;margin-bottom:44px}
.sec-head h2{font-size:clamp(32px,4.4vw,52px)}
.sol{display:grid;grid-template-columns:1fr 1fr;border-top:1px solid var(--line)}
.sol article{padding:32px 32px 32px 0;border-bottom:1px solid var(--line)}
.sol article:nth-child(even){padding:32px 0 32px 32px;border-left:1px solid var(--line)}
.sol h3{font-size:25px;margin-bottom:8px}
.sol p{margin:0 0 12px;color:var(--muted);max-width:40ch}
.sol .tags{font-size:14px;font-weight:500}

/* golderp */
.erp{background:var(--soft);border-block:1px solid var(--line)}
.erp .wrap{display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center}
.erp h2{font-size:clamp(32px,4.4vw,52px);margin-bottom:18px}
.erp p{color:var(--muted);max-width:46ch;margin:0 0 28px}
.flow{display:grid;gap:10px}
.flow div{display:flex;justify-content:space-between;align-items:center;border:1px solid var(--line);border-radius:14px;padding:14px 18px;background:var(--paper);font-weight:500}
.flow div:last-child{background:var(--orange);color:#0B0F14;border-color:var(--orange);font-weight:600}
.flow small{color:var(--muted);font-weight:400}
.flow div:last-child small{color:#0B0F14}

/* close */
.close{text-align:center;padding:110px 0}
.close h2{font-size:clamp(34px,5vw,60px);max-width:16ch;margin:0 auto 20px}
.close p{color:var(--muted);margin:0 auto 32px;max-width:46ch}
footer{border-top:1px solid var(--line);padding:28px 0 40px;color:var(--muted);font-size:14px}
footer .wrap{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap}
footer a{margin-left:18px}

@media (max-width:860px){
  .links{display:none}
  .hero-grid,.erp .wrap{grid-template-columns:1fr}
  .proof .wrap{grid-template-columns:1fr 1fr}
  .sol{grid-template-columns:1fr}
  .sol article,.sol article:nth-child(even){padding:28px 0;border-left:0}
  .btn-line.hide-s{display:none}
  .logo img{height:34px}
  nav{padding-left:16px}
  .hero{padding-top:56px}
}
@media (prefers-reduced-motion:reduce){html{scroll-behavior:auto}}
</style>
</head>
<body>

<?php require __DIR__ . '/includes/site_nav.php'; ?>
<main id="top">
<section class="hero">
  <div class="wrap hero-grid">
    <div>
      <h1>Kenyan business, run on modern software.</h1>
      <p class="sub">Website, email, M-Pesa, WhatsApp and ERP, designed well and kept running at 99.95% uptime, so you can get on with your business.</p>
      <div class="cta">
        <a class="btn btn-fill btn-lg" href="<?php echo hawlast_e( hawlast_url( 'book' ) ); ?>">Book a 15-minute call</a>
        <a class="btn btn-line btn-lg" href="#solutions">See what we run</a>
      </div>
    </div>
    <aside class="ledger" aria-label="Example week">
      <h3>A typical week, handled</h3>
      <ul>
        <li><span>Email delivery (SPF, DKIM)</span><b class="ok">Passing</b></li>
        <li><span>M-Pesa payments reconciled</span><b class="ok">Matched</b></li>
        <li><span>WhatsApp orders into stock</span><b class="ok">Synced</b></li>
        <li><span>eTIMS invoices submitted</span><b class="ok">Filed</b></li>
        <li><span>Site uptime this month</span><b class="ok">99.97%</b></li>
      </ul>
      <p class="note">Illustrative. Replace with live client data.</p>
    </aside>
  </div>
</section>

<section class="proof" id="work" aria-label="Proof">
  <div class="wrap">
    <div><strong>99.95%</strong><span>Uptime commitment</span></div>
    <div><strong>[00]</strong><span>Businesses on AlbaERP</span></div>
    <div><strong>M-Pesa</strong><span>Daraja integrated</span></div>
    <div><strong>eTIMS</strong><span>KRA-ready invoicing</span></div>
  </div>
</section>

<section class="mission">
  <div class="wrap">
    <p>Kenyan founders lose hours every week to broken email, disconnected tools and vendors who don't pick up.</p>
    <p class="small">That isn't just inefficient. It is time taken from your customers and your family. We run the technology underneath your business, and give that time back.</p>
  </div>
</section>

<section class="pad" id="solutions">
  <div class="wrap">
    <div class="sec-head"><h2>One team for the whole stack.</h2></div>
    <div class="sol">
      <article><h3>Retail and e-commerce</h3><p>Sell in store, online and on WhatsApp from one stock count.</p><div class="tags">Website, M-Pesa, POS, WhatsApp</div></article>
      <article><h3>Service businesses</h3><p>Quotes, invoices and payments that follow the client, not the inbox.</p><div class="tags">Email, invoicing, eTIMS, CRM</div></article>
      <article><h3>Schools and churches</h3><p>Fees, members and messages in one system, built from years of running them.</p><div class="tags">Management system, SMS, M-Pesa</div></article>
      <article><h3>Startups and SMEs</h3><p>A clean setup on day one, with a technical lead you can call.</p><div class="tags">Domain, email, site, automation</div></article>
    </div>
  </div>
</section>
<section class="erp pad" id="golderp">
  <div class="wrap">
    <div>
      <h2>AlbaERP connects sales, stock and money.</h2>
      <p>Built for how Kenyan businesses trade: M-Pesa, WhatsApp orders, multiple branches and KRA compliance in one place.</p>
      <a class="btn btn-fill btn-lg" href="<?php echo hawlast_e( hawlast_url( 'albaerp' ) ); ?>">See the AlbaERP demo</a>
    </div>
    <div class="flow" aria-label="How AlbaERP connects">
      <div>WhatsApp order <small>arrives</small></div>
      <div>Stock <small>reserved</small></div>
      <div>M-Pesa payment <small>matched</small></div>
      <div>Invoice and report <small>done</small></div>
    </div>
  </div>
</section>

<section class="close" id="pricing">
  <div class="wrap">
    <h2>Tell us what is slowing you down.</h2>
    <p>Fifteen minutes. We will tell you what to fix first, whether or not you hire us.</p>
    <a class="btn btn-fill btn-lg" href="<?php echo hawlast_e( hawlast_url( 'book' ) ); ?>">Book a 15-minute call</a>
  </div>
</section>
</main>

<!-- Facebook SDK / pixel - app id and site id preserved from the previous homepage -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.1&appId=202015096126&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
<?php
// index.php already closed <main> above so the pixel scripts sit outside it.
$mainClosed = true;
require __DIR__ . '/includes/site_foot.php';
?>