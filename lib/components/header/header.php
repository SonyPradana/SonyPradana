
<div class="header title">
    <p>Welcome To Simpus Lerep</p>
</div>
<div class="header menu">
    <div class="margin-left"></div>
    <div class="logo">
        <?php if( $auth->TrushClient()): ?>
        <div class="burger-menu">
            <div class="bm-1"></div>
            <div class="bm-2"></div>
            <div class="bm-3"></div>
        </div>
        <?php endif; ?>
        <a href="/">Simpus</a>
    </div>
    <div class="nav">                
    <?php if( $auth->TrushClient()): ?>
        <?php foreach( $menu_link as $ml ) : ?>
        <a href="<?= $ml[1] ?>" <?= $active_menu == $ml ? 'class="active"' : ''?>><?= $ml[0] ?></a>
        <?php endforeach ; ?>
    <?php endif; ?>
    </div>
    <div class="account">
        <?php if( $auth->TrushClient()): ?>
        <div class="boxs-account" onclick="open_modal()">
            <div class="box-account left">
                <img class="pic-box" src="<?= $user->getSmallDisplayPicture() ?>" alt="@<?= $user->getDisplayName() ?>)">
            </div>
            <div class="box-account right">
                <p><?= $user->getDisplayName()?></p>
            </div>
        </div>                
        <?php else: ?>
            <a class="btn outline blue light rounded" href="/login/?url=<?= $_SERVER['REQUEST_URI'] ?>">login</a>
        <?php endif; ?>
    </div>
    <div class="margin-right"></div>
</div>
