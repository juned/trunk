<? extend('master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>
<script type="text/javascript">
    $(document).ready(function() {
//         $(".invoice_day").hide();
//         $('label[for="Billing Day"]').hide()
       
document.getElementsByName("currency_id")[0].selectedIndex = <?=$currency_id-1?>;
document.getElementsByName("timezone_id")[0].selectedIndex = <?=$timezone_id-1?>;
document.getElementsByName("country_id")[0].selectedIndex = <?=$country_id-1?>;
document.getElementsByName("sweep_id")[0].selectedIndex = <?=2?>;

	 $(".sweep_id").change(function(e){
            if(this.value != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url()?>/accounts/customer_invoice_option",
                    data:"sweepid="+this.value, 
                    success: function(response) {
                        $(".invoice_day").html(response);
                        $('.invoice_day').show();
                        $('label[for="Billing Day"]').show()
                    }
                });
            }else{
                $('label[for="Billing Day"]').hide()
                $('.invoice_day').css('display','none');                
            }
        });
        $(".sweep_id").change();
        });
</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<br/>
<?php endblock() ?>
<?php startblock('content') ?>

<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">      
                         <div style="color:red;margin-left: 60px;">
                                <?php
                                $data_errrors = json_decode($validation_errors);
                                foreach ($data_errrors as $key => $value) {
                                    echo $value . "<br/>";
                                }
                                ?> 
                          </div>
                        <?php echo $form; ?>
                </div>  
            </div>
        </div>
    </div>
</section>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
