<?php
/**
 * Shared closing chrome: </main>, the footer and the page scripts.
 *
 * Set $pageJquery = true before including this file on pages that need jQuery
 * (buyairtime.php does), so pages that do not use it are not made to download it.
 *
 * Set $mainClosed = true if the page has already emitted its own </main> with
 * tracking/pixel markup after it (index.php does), so the tag is not doubled.
 */
?>
<?php if (empty($mainClosed)): ?>
</main>
<?php endif; ?>

<footer>
  <div class="wrap">
    <span>Hawlast Ventures, Nairobi. &copy; <?php echo date('Y'); ?>.</span>
    <span><a href="<?php echo hawlast_url( 'domain-registration' ); ?>">Domains and hosting</a><a href="<?php echo hawlast_url( 'blog' ); ?>">Blog</a><a href="<?php echo hawlast_url( 'buyairtime.php' ); ?>">Buy airtime</a><a href="<?php echo hawlast_url( 'contactus.php' ); ?>">Contact us</a><a href="<?php echo hawlast_url( 'terms-of-service.php' ); ?>">Terms</a><a href="<?php echo hawlast_url( 'privacy-policy.php' ); ?>">Privacy</a></span>
  </div>
</footer>
<?php if (!empty($pageJquery)): ?>
<script src="<?php echo hawlast_url( 'jquery.min.js' ); ?>"></script>
<?php endif; ?>
</body></html>