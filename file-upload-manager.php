<?php
	
	require_once __DIR__.'/imports.php'; 
	
	# upload our file to s3 
	
	try{ 
	
		$file = $_FILES["fileToUpload"];
		
		$name = $file['name'];
	
		# file to upload 
		$file_path = $_FILES["fileToUpload"]["tmp_name"]; # this is the sample file that we are going to upload 

		$file_name = pathinfo($name)['basename']; 
	 
		# actual uploading 
		$request_status = $s3->putObject([ 
			'Bucket' => $config['s3-access']['bucket'], 
			'Key' => 'from_php_script/'.$file_name, # 'from_php_script' will be our folder on s3 (this would be automatically created) 
			'Body' => fopen($file_path, 'rb'), # reading the file in the 'binary' mode 
			'ContentType' => $_FILES["fileToUpload"]["type"],
			'ACL' => $config['s3-access']['acl'] 
		]); 
		
		//CONVERSION TO .TXT AND EXTRACTING EMAIL AND PHONE
		exec('python3 pdf2txt.py -o output.txt '.$file_path);
	
		$email = shell_exec('python3 extractEmail.py');
		
		$phone = shell_exec('python3 extractPhone.py');
		
		//MySQL CONNECTION AND UPLOAD
		$connection = mysqli_connect(CONNECTION INFORMATION HERE);
		
		$file_URL = $request_status["ObjectURL"];
		
		$query = "INSERT INTO RESUMES VALUES (NULL, '$file_URL', '$email', '$phone');";
		$result = mysqli_query($connection, $query);
		
		if ($result) {
			echo "Successful write.";
		}
		else {
			echo "Unsuccessful write.";
		}
		
		# printing result 
		echo "Upload Successful";
		?>
		<a href="uploadTest.php" class="four">Back</a>
		<?php
	}catch(Exception $ex){ 
		echo "Error Occurred\n", $ex->getMessage(); 
	} 

	
?>