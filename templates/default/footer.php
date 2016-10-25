<?php
if( !defined( 'CUSTOMER_PAGE' ) )
  exit;
?>
<!-- footer -->
<section id="footer">
  <div class="wrapper">
  <div class="zawartosc">
<div id="footer_lewo">
</div>

<div id="footer_prawo">
 <?php if( isset( $config['enabled_widgets'] ) ) echo $oWidget->listWidgets( Array( 'iType' => 4, 'bDontDisplayErrors' => true ) ); ?>
</div>


  </div>
  </div>
</section>

<!-- powered -->
<section id="powered">
  <div class="wrapper">
  <div class="zawartosc">

      <div id="mailer">
         <?php if( isset( $config['enabled_widgets'] ) ) echo $oWidget->listWidgets( Array( 'iType' => 3, 'bDontDisplayErrors' => true ) ); ?>

      </div>

      <div id="stopki">
      <div id="copyright">
      <?php echo $config['copyright']; ?> </div> <div id="realizacja">
      <?php echo $config['realizacja']; ?> </div> <div id="cms">
      <?php echo $config['cms']; ?> 
      </div>
      </div>


  </div>
  </div>
</section>


</div><!-- #container -->

</body>
</html>