<?php
function getContentDetail($conn, $entityID, $entityType){


	$sql = "SELECT
		`contentDetailID`,
		`entityType`,
		`entityID`,
		`shortName`,
		`longDesc`,
		`content`,
		`url`,
		`fileFormat`,
		`updatedBy`,
		`updatedOn`,
		`createdBy`,
		`createdOn`
		FROM `content_detail`
		WHERE 1 = 1
		AND entityType = '$entityType'
		AND entityID = $entityID";
	return mysqli_query($conn, $sql);;
}

function getComment($conn, $entityID){
	$contentData = getContentDetail($conn, $entityID, "NOTE");
	$commentList = array();
	if(mysqli_num_rows($contentData) == 0){
		return $commentList;
	}
	while($row=mysqli_fetch_array($contentData)){
		$header = $row['shortName'];
		$longDesc = $row['longDesc'];
		if(count($commentList) == 0){
			array_push($commentList, newComment($header, $longDesc));
		}else{
			$comData = checkComment($commentList, $header);
			if($comData != null){
				array_push($comData->noteList, $longDesc);
			}else{
				array_push($commentList, newComment($header, $longDesc));
			}
		}
	}
	return $commentList;
}

function checkComment($commentList, $header){
	foreach ($commentList as $comment) {
		if($comment->header == $header){
			return $comment;
		}
	}
	return null;
}
function newComment($header, $longDesc){
	$commentData->header = $header;
	$commentData->noteList = array();
	array_push($commentData->noteList, $longDesc);
	return $commentData;
}

?>