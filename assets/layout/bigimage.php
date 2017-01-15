<div class="post-thumbnail-container <?= ($thumbnail_url) ? '' : 'no-image-big'?>">
  <div class="post-thumbnail-layout-big">
    <div class="post-thumbnail-title"><?= $text ?></div>
    <div class="post-thumbnail-desc">
      <p><?= $desc ?></p>
    </div>
    <img src="<?= $thumbnail_url ?>" alt="">
  </div>
</div>
