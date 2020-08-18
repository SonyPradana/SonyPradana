<?php if( isset( $views['modals'] ) ): ?>

<div class="modal dialog" id="modal-dialog">
    <div class="modal-box">
        <span class="close" id="modalClose">&times;</span>            
        <div class="boxs-content">
        <?php 

            if( $views['modals']['type'] == 'search-by-name-adress' ){
                require_once BASEURL . '/lib/components/modals/dialog.modal.php';
            }

        ?>
        </div>
    </div>
</div>

<?php endif; ?>
