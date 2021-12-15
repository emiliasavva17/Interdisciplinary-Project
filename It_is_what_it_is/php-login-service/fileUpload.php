<html>
<head>

   <!-- Link your php/css file -->
<head>

<?php


    // Create a class to handle file uploads
    //  - There must be an option to return JSON after a successful upload,
    //    with some information about the file that was uploaded

    

    class FileUpload {
        public function UploadFile(){


        
            $file = $_FILES["fileToUpload"];
            //echo var_dump($file) ;
            $file['name'] =$this->RenameFile() ;
            //echo $rename ."<br><br><br>";
            // $this->RenameFile();
            $target_dir = "original/";
            $target_file = $target_dir . basename($file["name"]);
            //echo var_dump($target_file);
            move_uploaded_file($file["tmp_name"], $target_file);
            $response = $this->RenameFile();
            //$this->ImageCheck();
           // $this->GetFileName();
         //
        }
       
        public function GetFileName(){
            $filename = $this->RenameFile();
            echo "<br>The file name is " . "<div id = 'data' >" . $filename . "</div><br></p><br>";

        }

        public function GetFileType(){
            $name = $_FILES["fileToUpload"]["name"];
            $ext = substr(strrchr($name,'.'),1); 
           // echo "The file type is" . "<div id = 'data' >."  . $ext . "</div><br></p>"; 
            return $ext;
        }

        public function GetFileSize(){
            $size=$_FILES['fileToUpload']['size'];
            echo "The file size is " . "<div id = 'data' >"  . $size . " bytes </div><br></p><br>";
        }

        public function RenameFile(){
             $name = $_FILES["fileToUpload"]["name"];
            $ext = substr(strrchr($name,'.'),1); 
            $username = "emsa";
            $name = $username."." .$ext;

            //echo "the rename file " . $name ."<br>";
            return $name;
        }

         public function ImageCheck(){
            $allowedImageTypes = ["jpg", "jpeg", "png", "gif"];
            $fileType = $this->GetFileType();

            foreach($allowedImageTypes as $ext) {

                if( $fileType == $ext){
                    $isAccepted =true;
                    break;
                    
                }else{
                    $isAccepted =false; 
                }
            }
            if($isAccepted){
                echo "Image Accepted <br>";
                    return true;
            }
            echo "Wrong file type. Please uplead an image with .jpg; .jpeg; .png; .gif extentions";
            return false;
        }

        public function displayImg(){
            $file['name'] =$this->RenameFile() ;
            $target_dir = "original/";
            $path = $target_dir + $file['name'];
            echo $path;

        }

    }
    $upload = new FileUpload();
    if($upload->ImageCheck()){
        $upload->displayImg();
    }
    

?>

</html>