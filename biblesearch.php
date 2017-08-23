<?php

//TODO: refine error strings to your context format
//TODO: create the myseql connection that connects to the bibles database

$text = "Matt 3:16"; //any freetext string to confirm against.
//$result = isVerse($text) returns true or some form of error string

function isVerse($text){

	if($text=="")return false;

	$text=strtolower($text);
	
	$sign="";
	
	$parts = getparts($text);
	
	if(is_numeric($parts[0]))$book= $parts[0]." " . ucwords($parts[1]);
	else $book = ucwords($parts[0]);
	$book=trim($book);
	
	if(is_numeric($parts[0]))$chapter= $parts[2];
	else $chapter = $parts[1];
	
	if(is_numeric($parts[0]))$num=3;
	else $num=2;
	$verses = array();
	while($num< count($parts)){
		$num++;
		if(is_numeric($parts[$num]))$verses[] = $parts[$num];
		elseif(trim($parts[$num])!="") $sign = $parts[$num];
	}
	
	if(trim($sign)=="-"){
		$start=$verses[0];
		$end=$verses[1];
		$verses=array();
		while($start<=$end){
			$verses[$start]=$start;
			$start++;
		}
		krsort($verses);
	}
	
    $bookno="";
	
	if($book==""){ echo "alert('Invalid book');"; return false;}
	
	$query = "select * from bible_books where book like \"$book%\"";
	$result = mysql_query($query);
	$bookinfo =  mysql_fetch_array($result, MYSQL_ASSOC);

    $bookno = $bookinfo['number'];
	 			 
    if(!is_numeric($bookno)){ echo "alert('Invalid bible book: $book');"; return false;}
    if($chapter!=""){
		if(!is_numeric($chapter)){ echo "alert('Invalid book chapter');"; return false;}
		if($chapter > $bookinfo['chapters'] || $chapter<1){ echo "alert('Invalid book chapter: ".$bookinfo['book']." $chapter. Should be between ".$bookinfo['book']." 1 and ".$bookinfo['book']." ".$bookinfo['chapters']."');"; return false;}
		$query = "select * from bible where book = \"$bookno\" and chapter=\"$chapter\"";
		$numverses = mysql_query($query);
		while($numversesb=mysql_fetch_array($numverses)){
			$numversesa[] = $numversesb;
		}
		$numverses = count($numversesa);
		foreach($verses as $k=>$verse){
			if($verse > $numverses || $verse<1){ echo "alert('Invalid verse number: $verse. Should be between 1 and $numverses');"; return false;}
			
		}
	}
	return true;
}


function getparts($verse){

  $verse = trim(strtolower($verse));
  $parts=array();
	
  $versearr = array();

  while($verse!=""){

    if(substr($verse,0,1)!=" ")array_unshift($versearr,substr($verse,0,1));
    $verse = substr($verse,1);

  }

  $num="";
  $word="";
  $keeper=count($versearr);
  
  while($keeper>0){
    $bit=$versearr[$keeper-1];
    
  	if(is_numeric($bit)){
		if($word!=""){
                  $parts[]=$word;
                  $word="";
                }
		$num="$num$bit";
	}
	else{
		if($num!=""){
                  $parts[]=$num;
                  $num="";
                }
		$word="$word$bit";
	}
  
        $keeper--;
  }
  if($word!="")$parts[]=$word;
  if($num!="")$parts[]=$num;
	
  return($parts);

}
