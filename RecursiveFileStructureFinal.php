<?php

$depth = 0; //Depth of the directory
$depthFolder = array();
$currentItem = "";
$output = array();
$parentFolder = 0;
$folderId = -1;

//Could add a potential file ideology(what are the allowed files).	

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "c:\\";

class FilePath {


public $folder_id;
public $parent_folder;
public $full_file_path;
public $file_name;

function __construct($full_file_path, $folder_id, $parent_folder,  $file_name) {
	$this->full_file_path = $full_file_path;
    $this->folder_id = $folder_id;
    $this->parent_folder = $parent_folder;
    $this->file_name = $file_name;

  }

function getFilepath(){
return   $this->full_file_path;
}

function getFolderID(){
return   $this->folder_id;
}

function getParentFolderID(){
return   $this->parent_folder;
}

function getFileName(){
return 	 $this->file_name;
}

public function FolderDepth($depthFolder){

	$filepath = "";

	foreach ($depthFolder as $key) {

		$filepath .=  $key . "\\\\";
								
	}

	return $filepath;
}


}
							
if (isset($_POST['submitSearch'])) {

	$connection = new mysqli($servername, $username, $password, $dbname);

	$search = $_POST['search'] ;

	$query = "SELECT filepath FROM main_directory WHERE filepath LIKE '%$search%'";

	$result = $connection->query($query);

	$connection->close();
	
} else {



}

if (isset($_POST['submit'])) {


	

	$directory = "uploads/";


	$target_file = $directory . basename($_FILES["fileToUpload"]["name"]);


	if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){

		
		echo "You have successfully uploaded the text file";

		echo "<br>";

		$myfile = fopen("uploads/".$_FILES["fileToUpload"]["name"], "r");


		while(!feof($myfile)) {							

			$currentCharacter = fgetc($myfile);
 			
			switch ($currentCharacter) {

  				case "{":

  				    $currentItem = trim($currentItem);
					$folderId = $folderId + 1;				
  					array_push($depthFolder, $currentItem);
  					$filepath = FilePath::FolderDepth($depthFolder);
					$folder = new FilePath($filepath,  $folderId, ($folderId - 1), "");
					$objects = array($folder->getFilepath(), $folder->getFolderID(), $folder->getParentFolderID(), $folder->getFileName());
  					array_push($output, $objects);  					
    				$depth = $depth + 1;
    				$currentItem = "";

    				break;

  				case "}":		
    									
					array_pop($depthFolder);
    				$depth = $depth - 1;
    				$currentItem = "";				 			
					break;

				case ";":	

					$filepath = FilePath::FolderDepth($depthFolder);
  				    $currentItem = trim($currentItem);
					$filepath .=  $currentItem;
  					$temp_array = array($filepath, NULL, $folderId, $currentItem);
  					$folder = new FilePath($filepath, NULL, $folderId, $currentItem);
					$objects = array($folder->getFilepath(), $folder->getFolderID(), $folder->getParentFolderID(), $folder->getFileName());
  					array_push($output, $objects);  

					break;	

  				default:

  				$currentItem = $currentItem . $currentCharacter;
    				
			}

 	    }

 		fclose($myfile);
 		echo "<br>";
 		

 		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "c:\\";

 		$connection = new mysqli($servername, $username, $password, $dbname);

 		

		foreach ($output as $key) {

			// echo "<br>";
			// echo $key[0] . ". folder ID:" . $key[1] . ". Parent ID; " . $key[2] . ". File name is;" . $key[3];
					 			
			$query = "INSERT INTO main_directory (filepath, folder_id, parent_folder, file_name) VALUES ('$key[0]', '$key[1]', '$key[2]', '$key[3]')";
			if ($connection->query($query) === TRUE) {

			} else {

				echo "Error: " . $query . "<br>" . $connection->error;

			} 				

			
		}

		$connection->close();

	} else {

		echo "Didn't work sorry";

	}

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
<p></p>
<div class="form">
<form action="" method="post" enctype="multipart/form-data">
  Upload text file:
  <input type="file" name="fileToUpload" id="fileToUpload">

  <input type="submit" value="Upload Image" name="submit">
</form>
</div>
<p></p>
<div class="form">
<form action="" method="post">
  
  <input type="search" name="search" id="search" placeholder="Search...">
  <input type="submit" value="Search Now" name="submitSearch">
  
</form>
</div>
<?php 

if ($result->num_rows > 0) {

	echo "Your results are:" . "<br>";
  
	while($row = $result->fetch_assoc()) {

    	echo $row['filepath'] . "<br>";
  }
}

?>	
</body>
</html>