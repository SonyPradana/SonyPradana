<?php
    $header_login   = $portal['auth']['login'] ??  false;
    $active_menu    = $portal['header']['active_menu'] ?? $active_menu ?? MENU_MEDREC;
    $list_menu      = $portal['header']['header_menu'] ?? $menu_link ?? "none";
    $display_name   = $portal['auth']['display_name'] ?? "null";
    $small_dp       = $portal['auth']['display_picture_small'] ?? "null";

?>
<?php if( $header_login ): ?>
<div class="header navbar">
    <div class="margin-left"></div>
    <div class="navbar-box">
        <a href="/rekam-medis/view?active_menu">Rekam Medis</a>
        <a href="/kia-anak/view/biodata?active_menu">Kia Anak</a>
        <a href="/kia-anak/view/posyandu?active_menu">Posyandu</a>
    </div>
    <div class="margin-right"></div>
</div>
<?php endif; ?>
<div class="header title">
    <p>Welcome To Simpus Lerep</p>
</div>
<div class="header menu">
    <div class="margin-left"></div>
    <div class="logo">
        <?php if( $header_login ): ?>
        <div class="burger-menu">
            <div class="bm-1"></div>
            <div class="bm-2"></div>
            <div class="bm-3"></div>
        </div>
        <?php endif; ?>
        <a href="/">Simpus</a>
    </div>
    <div class="nav">
    <?php if( $header_login ): ?>
        <?php foreach( $list_menu as $ml ) : ?>
        <a href="<?= $ml[1] ?>" <?= $active_menu == $ml[0] ? 'class="active"' : ''?>><?= $ml[0] ?></a>
        <?php endforeach ; ?>
    <?php endif; ?>
    </div>
    <div class="account">
        <?php if( $header_login ): ?>
        <div class="boxs-account" onclick="open_modal()">
            <div class="box-account left">
                <img class="pic-box" src="<?= $small_dp ?>" alt="@<?= $display_name ?>)">
            </div>
            <div class="box-account right">
                <p><?= $display_name?></p>
            </div>
        </div>
        <?php else: ?>
            <a class="btn outline blue light rounded" href="/login?url=<?= $_SERVER['REQUEST_URI'] ?>">login</a>
        <?php endif; ?>
    </div>
    <div class="margin-right"></div>
</div>
