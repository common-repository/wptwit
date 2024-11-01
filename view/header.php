<div class="wrap wpTwit">
  <h2>wpTwit Twitter Feed</h2>

  <?php
  $url_parts = $_GET;
  unset($url_parts['subpage'], $url_parts['success'], $url_parts['id']);

  $subpage_url = $_SERVER['SCRIPT_NAME'];
  if ($url_parts) {
    $subpage_url .= '?';
    foreach ($url_parts as $k => $v) {
      $subpage_url .= $k . '=' . $v . '&';
    }
    $subpage_url = substr($subpage_url, 0, strlen($subpage_url) - 1);
  }
  ?>

  <?php if ($errorArray && is_array($errorArray)) { ?>
    <ul>
      <li><?php echo implode($errorArray, '</li><li>'); ?></li>
    </ul>
  <?php } ?>