<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

?>
var page_def_key = '<?php echo substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(5 / strlen($x)))), 1, 5); ?>';
   function setdelekey (delkey){   // Function to make delekey, the delkey should be string
                            $.ajax({
                            method: 'POST',
                            url: 'remotajax.php',
                            data: {
                                'setdelekey':page_def_key,
                                'delkey':delkey
                            },
                            beforeSend: function(){
                                 if($('#delekey').length>0){$('#delekey').remove();}
                            }
                            }).done(function (response) {
                                if(response){
                                res=JSON.parse(response);
                                res = res['deltok'];
                             $(".adm_container").append('<input id="delekey" type="text" value='+ res +' style="display: none" />');
                               }
                            }); 
                         }
    function remvdelekey(delkey){    // function to remove delekey
                                $.ajax({
                                    method: 'POST',
                                    url: 'remotajax.php',
                                    data: {
                                    'remvdelekey':page_def_key,
                                    'delkey':delkey
                                    }
                            });
    }
  
  function delekey_reset(delkey){
        if($('#delekey').length>0){
                remvdelekey(delkey);
                $('#delekey').remove();
                }
                        }
