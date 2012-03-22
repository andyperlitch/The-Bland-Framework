function initGallery(){
  $("#gallery-scroll").yoxview();
}

$(document).ready(function(){
  initGallery();
  initHsTabs();
  $("#load-more-gallery").click(function(e){
    e.preventDefault();
    var $this = $(this),
      text = $this.html();
    $this.html('loading gallery images...');
    
    // get ids of all the images currently there
    var idString = '';
    $("#gallery-scroll a").each(function(index,elem){
      idString += $(elem).attr('id') + '+';
    });
    idString.replace('gal-img-','');
    $.ajax({
      type:'GET',
      url:syspath+'gallery/',
      data:'exclude='+idString,  
      dataType:'json',
      success:function(res){
        $.each(res,function(key,valueObj){
          $this.prev().append(ich.gallery_img_tmpl(valueObj)).yoxview();
        });
      },
      complete:function(){
        $this.html(text);
      }
    });
  });
});
