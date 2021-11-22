<title>Flaticon Picker & Downloader - Flaticon Collection</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="//code.jquery.com/jquery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<style>
	.thumbnail {
    transition: all 0.5s ease;
    background: white;
    padding: 10px;
  }
  .thumbnail:hover {
    background: silver;
    padding: 11px;
  }

  #custom-search-input{
    padding: 3px;
    border: solid 1px #E4E4E4;
    border-radius: 6px;
    background-color: #fff;
  }

  #custom-search-input input{
   border: 0;
   box-shadow: none;
 }

 #custom-search-input button{
   margin: 2px 0 0 0;
   background: none;
   box-shadow: none;
   border: 0;
   color: #666666;
   padding: 0 8px 0 10px;
   border-left: solid 1px #ccc;
 }

 #custom-search-input button:hover{
   border: 0;
   box-shadow: none;
   border-left: solid 1px #ccc;
 }

 #custom-search-input .glyphicon-search{
   font-size: 23px;
 }
  #mydropdown{top:1px}
  .flatIconPanel{ width: 65vw; }
</style>
<div class="container">
  <div class="page-header">
    <h1>Flaticon Picker & Downloader - Flaticon Collection</h1>      
  </div>
  <div class="col-sm-12">
    <div class="form-group">
      <label>Select File Type</label>
        <label><input type="radio" class="flatIconType" name="type" value="true" disabled>SVG<small> (No longer available due to new fee policy)</small></label>
      </div>
      <div class="radio">
        <label><input type="radio" class="flatIconType" name="type" value="false" checked>PNG</label>
      </div>
    </div>
    <div class="btn-group">
      <button type="button" class="btn btn-info disabled flatIconDownload glyphicon glyphicon glyphicon-download-alt"></button>
      <button type="button" class="btn btn-default flatIconVal glyphicon glyphicon-plus">
        <img height="20" width="20" src="" style="display:none">
        <input type="hidden" name="icon">
      </button>
      <button type="button" class="btn btn-default flatIconEmpty glyphicon glyphicon-remove  "style='display:none'></button>
      <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle"  id="mydropdown" data-toggle="dropdown">Select <span class="caret"></span></button>
        <div class="dropdown-menu" role="menu">
          <div class="container flatIconPanel">
            <div class="panel panel-info ">
              <div class="panel-heading">Flaticon Archive List<i class="glyphicon glyphicon-remove pull-right flatIconClose"></i></div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-12">
                    <div id="custom-search-input">
                      <div class="input-group col-md-12">
                        <input type="text" name="keyword" autocomplete="off" class="form-control input-lg flatIconText" placeholder="search icon " />
                        <span class="input-group-btn">
                          <button class="btn btn-info btn-lg" type="button">
                            <i class="glyphicon glyphicon-search"></i>
                          </button>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="text-center">
                  <ul class="pagination flatIconPaging"></ul>
                </div>
                <div class="row flatIconList"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  reqFlatAjax=null;
  $(flatIconGeting("",0,"flatIcon-list"));
  $("body").on("keyup",".flatIconText",function(){
   flatIconGeting($(this).val());
 });
  $("body").on("change",".flatIconType",function(){
    var keyword=$(".flatIconText").val();
    if(keyword){
      flatIconGeting(keyword);
    }else{
      flatIconGeting("",0,"flatIcon-list")
    }
 });
  $("body").on("click",".flatIconClose",function(e){
   $("#mydropdown").parent().removeClass('open');
 });  
  $("body").on("click",".flatIconEmpty",function(e){
    $(this).hide();
    $(".flatIconVal").addClass("glyphicon-plus");
    $(".flatIconVal input").val("");
    $(".flatIconVal img").attr("src","").hide();
    flatIconGeting("",0,"flatIcon-list");
  });
  $('body').on("click", ".dropdown-menu", function (e) {
    $(this).parent().is(".open") && e.stopPropagation();
});
  $("body").on("click",".flatIconSelect",function(){
   var img=$(this).data("src");
   if(img){
    $(".flatIconEmpty").show();
    $(".flatIconVal").removeClass("glyphicon-plus");
    $(".flatIconVal input").val(img);
    $(".flatIconVal img").attr("src",img).show();
   	$("#mydropdown").parent().removeClass('open');
    $(".flatIconDownload").removeClass("btn-success glyphicon-ok btn-danger glyphicon-remove disabled").text("").addClass("btn-info glyphicon-download-alt");
  }
});
  $("body").on("click",".flatIconDownload",function(){
   var img=$(".flatIconVal input").val();
   if(img){
     $.ajax({
      url: "ajax.php",
      type: "POST",
      dataType:"JSON",
      data: {Islem:"flatIcon-save",url:img},
      success: function (Sonuc) {
        if(Sonuc.r=="ok"){
     	     $(".flatIconDownload").removeClass("btn-info disabled glyphicon-download-alt").addClass("btn-success glyphicon-ok").text(" Saved");
             flatIconGeting("",0,"flatIcon-list");
        }else{
             $(".flatIconDownload").removeClass("btn-info glyphicon-download-alt").addClass("btn-danger glyphicon-remove").text(" Error");
        }
      }
    });
  }
});
  function flatIconGeting(word="",page=0,Islem="flatIcon-get"){
    var dataPost={
      keyword:word,
      page:page,
      type:$(".flatIconType:checked").val(),
      Islem:Islem
    };
    var paging="";
    $(".flatIconPaging").html("<li>Loading, please wait...</li>");
    if(Islem=="flatIcon-get"){
      $(".btn-group").addClass("open");
    }
    if (reqFlatAjax != null) reqFlatAjax.abort();
    reqFlatAjax =$.ajax({
      url: "ajax.php",
      type: "POST",
      dataType:"JSON",
      data: dataPost,
      success: function (Sonuc) {
        try{
          if(Sonuc.html){
            $(".flatIconList").html(Sonuc.html);
            if(Islem=="flatIcon-list"){
              $(".flatIconList").prepend('<li class="alert text-info small text-center"> Local images saved list</li>');
            }
            if(Sonuc.cur_page=="1"){
              paging=`<li> <a aria-label="&laquo; Previous" disabled> <span aria-hidden="true">&laquo; Previous</span> </a> </li>`;
            }else{
              paging=`<li><a href='javascript:flatIconGeting("${Sonuc.keyword}",${Sonuc.pre_page},"${Islem}");'> &laquo; Previous </a></li>`;
            }	
            if(Sonuc.cur_page==Sonuc.total_page){
              paging+=`<li> <a aria-label="Next" disabled> <span aria-hidden="true">Next  &raquo;</span> </a> </li>`;
            }else{
              paging+=`<li><a href='javascript:flatIconGeting("${Sonuc.keyword}",${Sonuc.next_page},"${Islem}");'>Next &raquo;</a></li>`;
            }	
            $(".flatIconPaging").html(paging);
            if(Islem=="flatIcon-get"){
              $(".btn-group").addClass("open");
            }
          }else{
            $(".flatIconPaging").html("<li>No results found</li>");
          }
        }catch (e){
          console.log(e+" Hata Olutu..");
        }
      },
      error: function (Sonuc) {
        console.log(Sonuc);
      }
    });

  }
</script>
