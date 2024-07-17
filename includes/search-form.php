<script type="text/javascript">
    $('document').ready(function(){
       $('#res-search-input').tooltip({'trigger':'manual','title':'Your search query is empty!'});
       $('#res-search-input').click(function(){
           $(this).tooltip('hide');
       });
     $('#res-search-input').keypress(function(e){
       if(e.which===13 && $.trim($('#res-search-input').val()) === ""){
           $('#res-search-input').tooltip('show');
           setTimeout(function(){$('#res-search-input').tooltip('hide');},2000);
          return false;
       }  
     }); 
     $("#res-search-input").keyup(function() {
        if($.trim($('#res-search-input').val()) !== ""){
         $('#res-search-input').tooltip('hide');   
        } 
     });
     $('#res-search').submit(function(){
      if($.trim($('#res-search-input').val()) === ""){
         $('#res-search-input').tooltip('show');
         setTimeout(function(){$('#res-search-input').tooltip('hide');},2000);
         return false;
      }else{
          $('#res-search-input').tooltip('hide');  
          return true;
      }   
     });
    });
</script>    

<hr/>
<form id="res-search" action="/search.php">
  <input id="res-search-input" type="text" name="query" placeholder="Search among <?php echo resourceCount(123); ?> Educational Resources" value="<?php if(isset($queryin)){echo $queryin;} ?>">
  <button type="submit">Search</button>
</form>
<hr/>
 <!--div class="search-container">
         <form class="form-search" action="<?php echo BASE_URL.'/search.php'?>">
         <input id="gen_search" type="text" class="input-medium search-query" placeholder="Type here to search resources" name="query">
         <button type="submit" class="btn searchbtn gen_search">Search</button>
         </form>
         </div-->

