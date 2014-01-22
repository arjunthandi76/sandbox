<div<?php print $attributes; ?>>
  <div<?php print $content_attributes; ?>>
    <?php if ($linked_logo_img || $site_name || $site_slogan): ?>
    <div class="branding-data clearfix">
      <?php if ($linked_logo_img): ?>
      <div class="logo-img">
        <?php print $linked_logo_img; ?>
      </div>
      <?php endif; ?>
      <?php if ($site_name || $site_slogan): ?>
      <?php $class = $site_name_hidden && $site_slogan_hidden ? ' element-invisible' : ''; ?>

      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php print $content; ?>
  </div>
</div>