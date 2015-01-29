<div class="wrap">
  <?php  

   do_action('theader');

   ?>


<h2 class="nav-tab-wrapper">
<?php   foreach ($this->settings as $tab => $section){ 

       $tabname = strtolower(str_replace(" ", "_", $tab));
  ?>
       
            <a href="#<?php  echo $tabname;  ?>" id="<?php echo $tabname; ?>-tab" class="nav-tab"><?php  echo $tab;  ?></a>


       
 <?php  } ?>
</h2>


<br>

<?php   foreach ($this->settings as $tab => $section){ 

       $tabname = strtolower(str_replace(" ", "_", $tab));
?>
  
        <div id="<?php echo $tabname; ?>" class="group">
  <div class="ui  segment">


       <form method="post" action="options.php">   
          <?php
            settings_fields($tabname); 
            //do_settings_fields( $tabname, $tabname );
            do_settings_sections( $tabname );
            submit_button();
         ?>
</form>
        </div>

        </div>


<?php  }  ?>










<br>

<?php  /*  ?>

<h2>Semantic</h2>




<div class="ui grid">

	<div class="left floated four wide column">

       <div class="ui vertical pointing demo menu">


      <?php   foreach ($this->settings as $tab => $section): ?>

      		<a class="item" data-tab="<?php  echo strtolower(str_replace(" ", "_", $tab)); ?>"> <?php echo $tab;  ?></a>			

      <?php   endforeach;  ?>


      </div>





</div>

  <div class="right floated twelve wide  column">
      <?php  foreach ($this->settings as $tab => $section): ?>
      	<div class="ui bottom attached segment tab" data-tab="<?php echo strtolower(str_replace(" ", "_", $tab));    ?>"> 
      <form method="post" action="options.php">

<div class="ui">




      		<?php  
        $tabname = strtolower(str_replace(" ", "_", $tab));
        settings_fields($tabname); 
        do_settings_sections( $tabname );
     		 ?> 


      	
        <?php  submit_button();  ?>

        </div>
      </form>
      </div>

      <?php  endforeach;  ?>

  </div>


</div>







<div id="tabs" class="tabs">
    <nav>
        <ul>

<?php   foreach ($this->settings as $tab => $section){ 

       $tabname = strtolower(str_replace(" ", "_", $tab));
  ?>
       
           <!--  <a href="#<?php  echo $tabname;  ?>" id="<?php echo $tabname; ?>-tab" class="nav-tab"><?php  echo $tab;  ?></a> -->
            <li><a href="#<?php  echo $tabname;  ?>"><i class="fa fa-thumb-tack"></i> <span><?php  echo $tab;  ?></span></a></li>

       
 <?php  } ?>


<!--             <li><a href="#section-1"><i class="fa fa-thumb-tack"></i> <span>Shop</span></a></li>
            <li><a href="#section-2"><i class="fa fa-thumb-tack"></i> <span>Drinks</span></a></li>
            <li><a href="#section-3"><i class="fa fa-thumb-tack"></i> <span>Food</span></a></li>
            <li><a href="#section-4"><i class="fa fa-thumb-tack"></i> <span>Lab</span></a></li>
            <li><a href="#section-5"><i class="fa fa-thumb-tack"></i> <span>Order</span></a></li> -->
        </ul>
    </nav>
    <div class="content">


<?php   foreach ($this->settings as $tab => $section){ 

       $tabname = strtolower(str_replace(" ", "_", $tab));
?>


        <section id="<?php echo $tabname; ?>">
            <?php  echo $tab;  ?>

        </section>

<?php  }  ?>  
      
    </div><!-- /content -->
</div><!-- /tabs -->



<script src="js/cbpFWTabs.js"></script>
<script>
    new CBPFWTabs( document.getElementById( 'tabs' ) );
</script>


<?php  */  ?>