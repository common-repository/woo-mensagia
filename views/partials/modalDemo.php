<!-- Modal -->
<div class="modal fade" id="modalDemo" tabindex="-1" role="dialog" aria-labelledby="modalDemo">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="icon-user"></i> <?php echo __("Thanks for trying the Mensagia WooCoomerce Plugin", 'woo-mensagia');?></h4>
            </div>
            <div class="modal-body">
                <p><?php echo __("If you want to try this plugin, you can download it for free from:", 'woo-mensagia');?> </p>
                <p>
                    <ul>
                        <li><a target='_blank' href="https://mensagia.com/plugin-sms">https://mensagia.com/plugin-sms</a></li>
                    </ul>
                </p>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" onclick="jQuery('#modalDemo').modal('hide');"><?php echo __("Continue", 'woo-mensagia');?></button>
            </div>
        </div>
    </div>
</div>