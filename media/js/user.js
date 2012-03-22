function toggleOtherProducts(index,elem)
{
  $(".model-group-"+index).toggle();
  $this = $(elem);
  if ( $(".blue-marker-up",$this).length ) $this.html('<span class="blue-marker-down"></span>hide other products');
  else $this.html('<span class="blue-marker-up"></span>show other products');
}

$(document).ready(function(){
  
  
  
  // catalog and cart
  if( $('.cat-quant').length ) $(".cat-quant").numericEntry();
  
  // faq
  if ( $(".toggle-dd-dt").length ) {
    $(".toggle-dd-dt").bind('click',function(event){
      event.preventDefault();
      $dd = $(this).parent().next();
      $dd.toggle('fast');
    });
  }
  
});
