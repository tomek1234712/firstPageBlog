<?php
if( !defined( 'ADMIN_PAGE' ) )
  exit;
?>
  <footer id="foot">
    <nav>
      <ul>
        <li class="back"><a href="javascript:history.back();">&laquo; <?php echo $lang['back']; ?></a></li>
        <li class="home"><a href="./" target="_blank"><?php echo $lang['homepage']; ?></a></li>
      </ul>
    </nav>
  </footer>
</div>
<?php
if( isset( $_COOKIE['bLicense'.str_replace( '.', '', $config['version'] )] ) && !isset( $_COOKIE['bNoticesDisplayed'] ) && isset( $_SESSION['iMessagesNoticesNumber'] ) && $_SESSION['iMessagesNoticesNumber'] > 0 ){ ?>
  <script>
  $(function(){
    $( '#messages .notices > a:first-child' ).trigger( 'click' );
    createCookie( 'bNoticesDisplayed', 1 );
  });
  </script><?php
} ?>
</body>
</html>