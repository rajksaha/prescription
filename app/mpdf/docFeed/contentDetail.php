<?php
function getContentDetail($conn, $entityID, $entityType){


	$sql = "SELECT
		contentDetailID,
		entityType,
		entityID,
		shortName,
		longDesc,
		content,
		url,
		fileFormat,
		updatedBy,
		updatedOn,
		createdBy,
		createdOn
		FROM content_detail
		WHERE 1 = 1
		AND entityType = '$entityType'
		AND entityID = '$entityID'";
	$sth = $conn->prepare($sql);
	$sth->execute();
	return $sth;
}

function getComment($conn, $entityID){
	$contentData = getContentDetail($conn, $entityID, "NOTE");
	$commentList = array();
	if($contentData->rowCount() == 0){
        return $commentList;
	}
	while($row = $contentData->fetch(PDO::FETCH_ASSOC)){
        $header = $row['shortname'];
		$longDesc = $row['longdesc'];
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
    $commentData = new class {};
    $commentData->header = $header;
	$commentData->noteList = array();
	array_push($commentData->noteList, $longDesc);
	return $commentData;
}

?>