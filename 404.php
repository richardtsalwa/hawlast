<?php
require __DIR__ . '/hawlastke.php';

http_response_code(404);

$pageTitle  = '404 - Tumehamishwa! | Hawlast Ventures';
$pageDesc   = '404 - Samahani, tumehamishwa na ukurasa huu haupo. Nunua airtime, soma blog yetu, registers domain au rudi nyumbani.';
$pageRobots = 'noindex,follow';

require __DIR__ . '/includes/site_head.php';

// The slug the visitor actually asked for, kept short so a long URL cannot break the layout.
$slug = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
$slug = htmlspecialchars(mb_substr($slug, 0, 120), ENT_QUOTES, 'UTF-8');
?>
<section class="page-head">
  <div class="wrap">
    <p class="big-404">404</p>
    <p class="slug"><em>Tumehamishwa.</em> Samahani sana, huu ukurasa haujaonekana hapa.</p>
    <p class="why">
      <span>Tuna kubahatisha kuwa ukurasa huu umeenda kwenye <b>zombie mode</b> - bado anafanya kazi, lakini hana mtu anayemwona.</span>
      <span style="margin-top:10px">Huenda ulitafuta kwa mkosa, au ulikuwa unaotafuta <b>kiwango</b> kilichokosewa. Either way, hatujaiwezi.</span>
    </p>
    <div class="row">
      <a class="btn btn-fill btn-lg" href="<?php echo hawlast_url(); ?>">Rudi nyumbani</a>
      <a class="btn btn-line btn-lg" href="<?php echo hawlast_url( 'contactus.php' ); ?>">Tuambie ulikuwa unatafuta nini</a>
    </div>
  </div>
</section>

<section class="pad-y">
  <div class="wrap">
    <p class="hint" style="margin-bottom:22px">Ulikuwa ukitafuta: <code><?php echo $slug !== '' ? $slug : '/'; ?></code></p>
    <p style="font-size:22px;max-width:40ch;margin:0 0 34px;color:var(--ink);font-weight:600">Lakini badala ya kukataa, basi chukua kitu kimoja hapa chini. Tumeandaa vitu vingi.</p>

    <div class="cards">
      <a class="card" href="<?php echo hawlast_url( 'buyairtime.php' ); ?>">
        <span class="ico" aria-hidden="true">&#128176;</span>
        <span>
          <h3>Nunua Airtime</h3>
          <p>Safaricom, Airtel au Telkom - lipa kwa M-PESA, pokea papo hapo. Hookuma hata ya kama tango moja tu.</p>
        </span>
      </a>

      <a class="card" href="<?php echo hawlast_url( 'blog' ); ?>">
        <span class="ico" aria-hidden="true">&#128214;</span>
        <span>
          <h3>Soma Blog Yetu</h3>
          <p>Blog yetu ina makala marefu kuhusu kupanga tovuti. Maana yake unapaswa kuisoma kwa macho yako yote mwenyewe - tayari?</p>
        </span>
      </a>

      <a class="card" href="<?php echo hawlast_url( 'domain-registration' ); ?>">
        <span class="ico" aria-hidden="true">&#127968;</span>
        <span>
          <h3>Registers Domain</h3>
          <p>Pata jina la tovuti yako - .co.ke, .ke, .com, .net na mengine. Usajili wa mwaka mmoja tu, miaka mingi iko po.</p>
        </span>
      </a>

      <a class="card" href="<?php echo hawlast_url(); ?>">
        <span class="ico" aria-hidden="true">&#8962;</span>
        <span>
          <h3>Rudi Nyumbani</h3>
          <p>Ukurasa wa mwanzo. Unaona? Hakuna kosa. Hebu tuendelee pale ulipofika.</p>
        </span>
      </a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/site_foot.php'; ?>