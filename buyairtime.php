<?php
require_once __DIR__ . '/hawlastke.php';

// Started before any output, otherwise PHP cannot send the session cookie.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pageTitle  = 'Buy Telkom, Airtel or Safaricom Airtime with M-PESA | Hawlast Ventures';
$pageDesc   = 'Buy Safaricom, Airtel or Telkom airtime using M-PESA paybill 822490. Enter the phone number, choose your amount, pay from your phone and the airtime arrives on the number you entered.';

require_once __DIR__ . '/includes/site_head.php';

// Token issued with the page and echoed back by the AJAX calls (checked with hash_equals).
$_SESSION['airtime_token'] = bin2hex(random_bytes(16));
?>

<section class="page-head">
  <div class="wrap">
    <h1>Buy airtime with M-PESA.</h1>
    <p>Enter the number that should receive the airtime, choose how much, then pay from your
       phone using paybill <b style="color:var(--orange)">822490</b>. Safaricom, Airtel and Telkom
       are all covered, and you can Fuliza as well.</p>
  </div>
</section>

<section class="pad-y">
  <div class="wrap">
    <div class="steps">

      <!-- STEP 1 : recipient number -->
      <div class="step" id="step1">
        <div class="step-top">
          <span class="step-n">1</span>
          <div>
            <h3>Who is getting the airtime?</h3>
            <p>The phone number that will be topped up.</p>
          </div>
        </div>

        <div class="field">
          <label for="phone">Phone number (10 digits)</label>
          <input type="tel" id="phone" name="phone" inputmode="numeric" autocomplete="tel"
                 placeholder="0720401869" maxlength="13">
          <p class="hint">Safaricom, Airtel or Telkom. Type 0720..., 254720... or +254720...</p>
        </div>

        <p class="msg" id="m1" role="alert"></p>

        <div class="row">
          <button type="button" class="btn btn-fill btn-lg" id="b1">Continue</button>
        </div>
<!-- STEP 2 : confirm the number and set the amount -->
      <div class="step" id="step2" hidden>
        <div class="step-top">
          <span class="step-n">2</span>
          <div>
            <h3>Check the number, set the amount.</h3>
            <p>The airtime goes to this number and nowhere else.</p>
          </div>
        </div>

        <div class="review">
          <span class="num" id="showPhone">0720401869</span>
          <span class="who">Recipient</span>
        </div>

        <div class="field">
          <label for="amount">How much airtime?</label>
          <div class="amount">
            <input type="text" id="amount" name="amount" inputmode="numeric" placeholder="100">
            <span class="cur">KES</span>
          </div>
          <p class="hint">Whole numbers only, from KES 5 to KES 2,000.</p>
        </div>

        <p class="msg" id="m2" role="alert"></p>

        <div class="row">
          <button type="button" class="btn btn-fill btn-lg" id="b2">Show me how to pay</button>
          <button type="button" class="btn btn-line" id="b2back">Change number</button>
        </div>
      </div>

      <!-- STEP 3 : pay on the phone -->
      <div class="step" id="step3" hidden>
        <div class="step-top">
          <span class="step-n">3</span>
          <div>
            <h3>Now pay from your phone.</h3>
            <p>Airtime is sent the moment Safaricom confirms your payment.</p>
          </div>
        </div>

        <div class="kiosk">
          <h4>On your phone go to <b>M-PESA &rarr; Lipa na M-PESA &rarr; Pay Bill</b></h4>
          <ol>
            <li>Enter the <b>paybill number</b> below.</li>
            <li>For the <b>account number</b>, enter the phone number that should receive the airtime. We have filled it in for you.</li>
            <li>Enter the <b>amount</b> below.</li>
            <li>Enter your M-PESA PIN and confirm.</li>
          </ol>
          <div class="ticket">
            <span>
              <span class="lbl">Paybill</span><br>
              <span class="big">822490</span>
            </span>
            <span style="margin-left:auto;text-align:right">
              <span class="lbl">Account no (recipient)</span><br>
              <span class="acct" id="acctNo">0720401869</span>
            </span>
          </div>
          <div class="ticket" style="border-top:0;padding-top:4px;margin-top:4px">
            <span>
              <span class="lbl">Amount to pay</span><br>
              <span class="big" id="amtNo">KES 100</span>
<!-- delivery receipt, filled from the airtime_transactions row -->
    <div class="receipt" id="receipt" aria-live="polite">
      <h3 id="rTitle">Airtime delivered.</h3>
      <p class="when" id="rWhen"></p>
      <dl>
        <dt>Recipient number</dt><dd id="rPhone"></dd>
        <dt>You paid</dt><dd id="rPaid"></dd>
        <dt>Airtime delivered</dt><dd id="rDelivered"></dd>
        <dt>M-PESA charge refunded</dt><dd id="rBonus"></dd>
        <dt>Status</dt><dd><span class="badge" id="rStatus"></span></dd>
        <dt>Request id</dt><dd id="rReq"></dd>
      </dl>
      <p class="hint" style="margin-top:20px">
        A confirmation SMS will also reach the number you topped up.
        Keep the request id for any support conversation.
      </p>
      <div class="row" style="margin-top:18px">
        <a class="btn btn-line" href="<?php echo hawlast_url(); ?>">Back to the homepage</a>
      </div>
    </div>
  </div>
</section>

<script>
jQuery(function ($) {
  'use strict';

  var API    = <?php echo json_encode( hawlast_url( 'airtime-api.php' ) ); ?>;
  var TOKEN  = <?php echo json_encode( $_SESSION['airtime_token'] ); ?>;

  var state = { phone: '', amount: 0, tries: 0, timer: null };

  // Roughly six minutes of polling. Safaricom is normally instant, so this is a ceiling.
  var MAX_TRIES = 90;
  var GAP       = 4000;

  function say(id, text, good) {
    $('#' + id).text(text).toggleClass('ok', !!good);
  }

  function show(step) {
    ['#step1', '#step2', '#step3'].each(function (i) {
      $(['#step1', '#step2', '#step3'][i]).prop('hidden', i !== step);
    });
    $('.step:visible')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function spin(on) {
    $('#wheel').toggleClass('on', !!on);
    $('#b3').prop('disabled', !!on);
  }

  // POST helper. Everything goes through the session token issued with the page.
  function call(data, done) {
    $.ajax({
      url: API,
      type: 'POST',
      dataType: 'json',
      data: $.extend({ token: TOKEN }, data)
    }).done(done).fail(function (xhr) {
      done({ ok: false, error: (xhr.responseJSON && xhr.responseJSON.error) || 'Something went wrong. Please try again.' });
    });
  }

  /* ---- step 1 : the number ---------------------------------------- */
  $('#phone').on('input', function () {
    $(this).val($(this).val().replace(/[^0-9]/g, ''));
    say('m1', '');
  });

  $('#b1').on('click', function () {
    var phone = $.trim($('#phone').val());

    say('m1', 'Checking the number...');
    $('#b1').prop('disabled', true);

    call({ action: 'check_number', phone: phone }, function (res) {
      $('#b1').prop('disabled', false);

      if (!res.ok) { say('m1', res.error); return; }

      state.phone = res.local;
      $('#showPhone').text(res.local);
      say('m1', '', true);
      show(1);
      $('#amount').focus();
    });
  });

  /* ---- step 2 : the amount --------------------------------------- */
  $('#amount').on('input', function () {
    $(this).val($(this).val().replace(/[^0-9]/g, ''));
    say('m2', '');
  });

  $('#b2').on('click', function () {
    var amount = $.trim($('#amount').val());

    say('m2', 'One moment...');
    $('#b2').prop('disabled', true);

    call({ action: 'check_amount', phone: state.phone, amount: amount }, function (res) {
      $('#b2').prop('disabled', false);

      if (!res.ok) { say('m2', res.error); return; }

      state.amount = res.amount;
      state.phone  = res.phone;
      state.tries  = 0;

      $('#acctNo').text(res.phone);
      $('#amtNo').text('KES ' + res.amount.toLocaleString());

      say('m2', '', true);
      show(2);
    });
  });

  $('#b2back').on('click', function () {
    say('m1', ''); say('m2', '');
    show(0);
    $('#phone').focus().select();
  });

  $('#b3back').on('click', function () {
    stopPolling();
    spin(false);
    say('m3', '');
    $('#amount').val('');
    show(0);
  });

  /* ---- step 3 : wait for the airtime_transactions row ------------- */
  function stopPolling() {
    if (state.timer) { clearTimeout(state.timer); state.timer = null; }
  }

  function poll() {
    call({ action: 'status' }, function (res) {
      if (!res.ok) {
        spin(false);
        stopPolling();
        say('m3', res.error);
        return;
      }

      if (!res.delivered) {
        state.tries++;

        if (state.tries >= MAX_TRIES) {
          spin(false);
          stopPolling();
          say('m3', 'Still waiting. If your M-PESA went through, the airtime is on its way. Call 0720 401869 with your M-PESA confirmation code and we will sort it out.');
          return;
        }

        state.timer = setTimeout(poll, GAP);
        return;
      }

      stopPolling();
      spin(false);
      showReceipt(res);
    });
  }

  function showReceipt(res) {
    var r = res.record;
    var failed = !!res.failed;

    // <dl> is built from named fields, so no user string is ever pasted in as markup.
<?php require __DIR__ . '/includes/site_foot.php'; ?>
    $('#rTitle').text(failed ? 'Airtime could not be delivered.' : 'Airtime delivered.');
    $('#rWhen').text(r.date);
    $('#rPhone').text(state.phone);
    $('#rPaid').text('KES ' + r.paid.toLocaleString());
    $('#rDelivered').text('KES ' + r.delivered.toLocaleString());
    $('#rBonus').text('KES ' + Math.max(r.delivered - r.paid, 0).toLocaleString());
    $('#rStatus').text(r.status);
    $('#rReq').text(r.requestid);

    $('#receipt').toggleClass('fail', failed).addClass('on');
    $('#receipt')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  $('#b3').on('click', function () {
    say('m3', '');
    spin(true);
    state.tries = 0;
    poll();
  });
});
</script>
            </span>
            <span style="margin-left:auto;font-size:14px;color:var(--muted);max-width:34ch">
              We top up a little extra to refund your M-PESA charge.
            </span>
          </div>
        </div>

        <p class="hint" style="margin-bottom:16px">
          Paying less than KES 101? No top-up bonus. You get the exact amount you paid.
          For help, WhatsApp or SMS Hawlast Ventures on 0720 401869.
        </p>

        <div class="row">
          <button type="button" class="btn btn-fill btn-lg" id="b3">I have paid, check my airtime</button>
          <button type="button" class="btn btn-line" id="b3back">Start over</button>
        </div>

        <div class="loading" id="wheel">
          <span class="spinner" aria-hidden="true"></span>
          <p><b>Waiting for Safaricom</b>
             Confirm the payment on your phone. We check with the network every few seconds,
             so please keep this page open.</p>
        </div>

        <p class="msg" id="m3" role="alert"></p>
      </div>

    </div>
      </div>