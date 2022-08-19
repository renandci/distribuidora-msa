<div style="background-color: #f5f5f5;" class="banner">
  <div class="banner-index">
    <?php foreach ($CONFIG['banners'] as $banner) { ?>
      <?php if (empty($banner['produto'])) { ?>
        <div class="clearfix">
          <img data-src="<?php echo Imgs::src($banner['banner'], 'banners'); ?>" class="lazyOwl" width="100%" />
        </div>
      <?php } else { ?>
        <div class="clearfix">
          <a href="<?php echo $banner['produto'] ?>" class="show">
            <img data-src="<?php echo Imgs::src($banner['banner'], 'banners'); ?>" class="lazyOwl" width="100%" />
          </a>
        </div>
      <?php } ?>
    <?php } ?>
  </div>
</div>
