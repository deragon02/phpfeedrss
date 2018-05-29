
<?php
  
  include("FeedWriter.php");
  include '../dataload.php';

	$servername = strtolower($_SERVER['SERVER_NAME']);
	$servername = (substr($servername,0,4) == 'www.')?substr($servername,4):$servername;
	
	#------
	if(preg_match('#([a-z0-9-]+?)\.ol2\.ir#i', $servername, $blogname)){
		$res=mysql_query("select id,title,username from `feeds` where `username`='{$blogname[1]}'");
		if(mysql_num_rows($res)>0){
			list($id,$title,$username)=mysql_fetch_array($res);
		}
	}else{
		$title='مطالب سایت';
		$username='www';
	}
  //Creating an instance of FeedWriter class. 
  //The constant RSS2 is passed to mention the version
  $TestFeed = new FeedWriter(RSS2);
  
  //Setting the channel elements
  //Use wrapper functions for common channel elements
  $TestFeed->setTitle("$title");
  $TestFeed->setLink("http://$username.ol2.ir/");
  $TestFeed->setDescription("$title");
  
  
  //Use core setChannelElement() function for other optional channels
  $TestFeed->setChannelElement('language', 'fa-ir');
//last poem
$res=mysql_query("select `title`,`text`,`date`,`keywords` from `news_tmp` ".(isset($id)?"where `fid`=$id":'')." order by `date` DESC limit 100");
$row=mysql_fetch_assoc($res);
$TestFeed->setChannelElement('pubDate', date(DATE_RSS, $row[date]));
  
  //Adding a feed. Genarally this portion will be in a loop and add all feeds.
  

mysql_data_seek($res,0);
//$res=mysql_query("SELECT news2.title,news2.id,news2.view,news2.image,news2.date,cat2.title as 'cat',members.name as 'name',news2.text,news2.cat as 'cid',news2.uid FROM `news2`,`cat2`,`members` WHERE news2.publish='y' and cat2.id=news2.cat and members.id=news2.uid group by news2.id order by date DESC,view ASC limit 30");
//$res=mysql_query("SELECT news2.title,news2.id,news2.view,news2.image,news2.date,cat2.title as 'cat',members.name as 'name' FROM (select * from `news2` where  news2.publish='y' and news2.image!='' order by date DESC,view ASC limit 30) as `news2`,`cat2`,`members` WHERE cat2.id=news2.cat and members.id=news2.uid group by news2.id order by news2.date DESC");
if(mysql_num_rows($res)!=0){
	while($row=mysql_fetch_array($res)){  
		  //Create an empty FeedItem
		  $sajjad = $row['id'];
		  $newItem = $TestFeed->createNewItem();
		  $newItem->setTitle($row['title']);
		  $newItem->setLink("http://$username.ol2.ir/page-"$row['id'].html');
		  $newItem->setDate($row['date']);
		  $newItem->setDescription($row['text']);
		  //Now add the feed item
		  $TestFeed->addItem($newItem);
	}
}
  
  //OK. Everything is done. Now genarate the feed.
  $TestFeed->genarateFeed();
?>
