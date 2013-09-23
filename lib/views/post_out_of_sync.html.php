<div class="error error-sync-<?php echo $id ;?>">
<p>"<strong><?php echo $title; ?></strong>" is out of sync with news@me.  Please save it again.
  <? if (WpNewsAtMe::DEBUG) { ?>
  (<?php echo $signature; ?> <?php echo $string; ?>)</p>
  <? } ?>
</div>
