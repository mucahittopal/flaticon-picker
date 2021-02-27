<?php 
require "PHPHtmlParser/vendor/autoload.php"; 
use PHPHtmlParser\Dom; 
$imageDir='images/';
$imageDirSource=dirname(__FILE__).'/'.$imageDir;
    if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $islem=$_POST["Islem"];
      switch($islem){
        case"flatIcon-save":
      		$url  = $_POST["url"];
          $str  = parse_url($url); 
          $path   = explode('/', $str['path']); 

          if (!file_exists($imageDirSource)) { 
              mkdir($imageDirSource, 0777, true); 
          }
	
	  if (!file_exists($imageDirSource.end($path))) { 
	    $fileExt=pathinfo($url,PATHINFO_EXTENSION);
	    if(in_array($fileExt,["svg","svgz","png","jpg"]) && $str["host"]=="image.flaticon.com"){
		copy($url, $imageDirSource.end($path));
	    }
	  }	      
          if (file_exists($imageDirSource.end($path))) { 
            $req="ok";
          }else{
            $req="error";
          }
          echo json_encode(["r"=>$req]);
          
        break;
          
        case"flatIcon-list":
          $perpage=50;
          $counter=0;
          $currentPage = isset($_POST['page'])? (int) $_POST['page']:1; 
          $dir = new DirectoryIterator($imageDirSource);
          foreach($dir as $file ){ $counter += ($file->isFile()) ? 1 : 0;}
          $pagesCount   = ceil($counter/$perpage); 
          $paginated = new LimitIterator($dir, $currentPage * $perpage, $perpage);
	  if (!$paginated->valid()) {
          	$paginated = $dir;
	  }
          $nextPage   = ($currentPage+1 > $pagesCount)?$pagesCount:$currentPage+1; 
          $previousPage = ($currentPage-1 == 0)?1:$currentPage-1; 

          $html="";
          foreach ($paginated as $fileinfo){
              if (!$fileinfo->isDot()){
              $html.='<div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                <div class="thumbnail">
                  <a class="flatIconSelect" href="javascript:;" data-src="'.$imageDir.$fileinfo->getFilename().'">
                    <img src="'.$imageDir.$fileinfo->getFilename().'">
                  </a>
                </div>
              </div>';
              }                
           } 
          echo json_encode(["html"=>$html,"keyword"=>"","cur_page"=>$currentPage,"pre_page"=>$previousPage,"next_page"=>$nextPage,"total_page"=>$pagesCount]);
        break;

        case"flatIcon-get":

         	$svg=$_POST['type']=="true"?true:false;
         	$loader=false;
          	$currentPage = isset($_POST['page'])? (int) $_POST['page']:1; 
      		$keyword 	 = $_POST['keyword']; 
      		$dom 		 = new Dom;
      		$dom->load('https://www.flaticon.com/search/'.$currentPage.'?word='.$keyword); 
      		$contents 	= $dom->find('li.icon--item'); 
        
      		$pagesCount 	= $dom->find('span#pagination-total')[0]; 
      		$pagesCount 	= (int)$pagesCount->text;
      		$nextPage		= ($currentPage+1 > $pagesCount)?$pagesCount:$currentPage+1; 
      		$previousPage	= ($currentPage-1 == 0)?1:$currentPage-1; 
        	$html="";
		  foreach ($contents as $content){
			$innerDom = new Dom;  
			$innerDom->load($content->outerHtml); 
			$img = $innerDom->getElementsbyTag('img');
		      if(!current($img)){ continue; } 
			if($svg){
				if(current($innerDom->find(".flaticon-premium")) || current($innerDom->find('[data-type="premium"]'))){ continue; }
				$toSvg=str_replace(["png","/128"],["svg",""],$img->getAttribute('data-src'));
				$imgSrc=$toSvg;
		      }else{
			$imgSrc=$img->getAttribute('data-src');
		      }
		      if($loader){ 
			$imgStr=$img->outerHtml;
		      }else{
			 $imgStr="<img src='{$imgSrc}'>";
		      } 
			$html.='<div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
				<div class="thumbnail">
				    <a class="flatIconSelect" href="javascript:;" data-src="'.$imgSrc.'">
					'.$imgStr.'
				    </a>
				</div>
			     </div>'; 
		  }
        	echo json_encode(["html"=>$html,"keyword"=>$keyword,"cur_page"=>$currentPage,"pre_page"=>$previousPage,"next_page"=>$nextPage,"total_page"=>$pagesCount]);
        break;
    }
  }
