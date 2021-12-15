<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json; charset=UTF-8");

//session_start();
include("mysql.php");

//session_start();
// +----------------------------------------------------+
// | GET Methods being called with identifier "action" |
// +----------------------------------------------------+
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == "logout") {
        session_unset();
        session_destroy();
        $response['authenticated'] = FALSE;
        echo json_encode($response);
    }
    
}








// +----------------------------------------------------+
// | POST Methods being called with identifier "action" |
// +----------------------------------------------------+
// Function globalID($user_id){

// $GLOBALS['id'] = $user_id;
// return $GLOBALS['id'];
// }
if (isset($_GET['action'])) {
    
    $action = $_GET['action'];





    // LOGIN
    if ($action == "login") {
        $loginObject = json_decode(file_get_contents('php://input'));
        $username = $loginObject->username;
        $password = $loginObject->password;

        // Get the users login information
        $sql = "SELECT * FROM userlogin WHERE username = '$username' LIMIT 1";
        $result = $mySQL->query($sql);

        // Check if the usernam exists
        if ($result->num_rows == 1) {
            $data = $result->fetch_object();
            // Check if it is the right password for that username
            if (password_verify($password, $data->pass)) {
                $sql = "SELECT * FROM userlist WHERE userID = " . $data->id;
                $user = $mySQL->query($sql)->fetch_object();

                






         $sql = "SELECT * FROM userlist WHERE userID = " . $data->id ;
                $row = $mySQL->query($sql)->fetch_object();

       // $row = $result->fetch_object();
        $_SESSION['firstName'] = $row->firstName ;
        $_SESSION['lastName'] = $row->lastName ; 
        $_SESSION['email'] = $row->email;
        $_SESSION['phoneNr'] =$row->phoneNr ;
        $_SESSION['userID'] = $data->id;

        $response['id'] = $_SESSION['userID'];
        
        //$response['error'] = "No error Awesome";
        $response['firstName']=$_SESSION['firstName'];
        $response['lastName']=$_SESSION['lastName'];
        $response['phoneNr']=$_SESSION['phoneNr'];
        $response['email']=$_SESSION['email'];






                $response['authenticated'] = TRUE;
                $response['userData'] = $user;
                echo json_encode($response);
            } else {
                $response['authenticated'] = FALSE;
                $response['error'] = "Wrong password";
                echo json_encode($response);
            }
        } else {
            $response['authenticated'] = FALSE;
            $response['error'] = "User doesn't exist";
            echo json_encode($response);
        }
    }


    // SIGN UP
    if ($action == "signup") {
        $loginObject = json_decode(file_get_contents('php://input'));
        $username = $loginObject->username;
        $_SESSION["username"] = $username;
        $email = $loginObject->email;
        $password = $loginObject->password;
        $passwordCheck = $loginObject->passwordCheck;

        if (!empty($username) && !empty($password)) {
            // Check if passwords are the same
            if ($password == $passwordCheck) {

                 // Check if email is already in usd
                $sql = "SELECT id FROM users WHERE email = '$email'";
                $result = $mySQL->query($sql);

                // If the email does not exist, then continue
                if ($result->num_rows == 0) {

                    // Check if username already exists
                    $sql = "SELECT id FROM userlogin WHERE username = '$username'";
                    $result = $mySQL->query($sql);

                    // If the username does not exist, then create a new user
                    if ($result->num_rows == 0) {
                        $passEncrypt = password_hash($loginObject->password, PASSWORD_DEFAULT);
                        
                        $sql = "CALL CreateUser('$email', '$username', '$passEncrypt')";
                    
                        if ($mySQL->query($sql) === TRUE) {
                            $response['signupSuccess'] = TRUE;
                            $response['username']=$username;
                            $response['email']=$email;
                            echo json_encode($response);
                        } else {
                            $response['signupSuccess'] = FALSE;
                            $response['error'] = "Signup failed. Please try again.";
                            echo json_encode($response);
                        }
                    } else {
                        $response['signupSuccess'] = FALSE;
                        $response['error'] = "Signup failed. Username taken.";
                        echo json_encode($response);
                    }
                }else {
                        $response['signupSuccess'] = FALSE;
                        $response['error'] = "Signup failed. Email taken.";
                        echo json_encode($response);
                    }
                }else {
                $response['signupSuccess'] = FALSE;
                $response['error'] = "Signup failed. Passwords not the same.";
                echo json_encode($response);
            }
        } else {
            $response['signupSuccess'] = FALSE;
            $response['error'] = "Signup failed. Please fill out all fields.";
            echo json_encode($response);
        }
    }


    // SIGN UP Second Step
    if ($action == "signup_2step") {
        $loginObject = json_decode(file_get_contents('php://input'));
        $firstName = $loginObject->firstName;
        $lastName = $loginObject->lastName;
        $phoneNr = $loginObject->phoneNr;
        
        $user_id = $loginObject->userID;

        if (!empty($firstName)) {

            if(!empty($lastName)){ 
                $sql = "CALL CreateProfile( '$user_id','$firstName', '$lastName', '$phoneNr')";

                if ($mySQL->query($sql) === TRUE) {
                    $response['signupSuccess'] = TRUE;
                    $response['firstName']=$firstName;
                    $response['lastName']=$lastName;
                    $response['phoneNr']=$phoneNr;
                    



                $sql = "SELECT * FROM userlist WHERE userID = " . $user_id;
                $user = $mySQL->query($sql)->fetch_object();
                $response['authenticated'] = TRUE;
                $response['userData'] = $user;
                echo json_encode($response);




                    //echo json_encode($response);
                } else {
                    $response['signupSuccess'] = FALSE;
                    $response['error'] = "Signup failed. Please try again.";
                    echo json_encode($response);
                }
            } else{
                $response['signupSuccess'] = FALSE;
                $response['error'] = "Signup failed. Please type your Last name";
                echo json_encode($response);
            }
        }else{
                $response['signupSuccess'] = FALSE;
                $response['error'] = "Signup failed. Please type your First name";
                echo json_encode($response);
            }

    }

    if ($action=="appendPost"){
        $sql = " SELECT * From Posts ORDER BY id DESC;";
        $result = $mySQL->query($sql);
        $i=0;
        while($row = $result->fetch_object()){
            $response[$i]["id"] = $row->id;
            $response[$i]["title"] = $row->title;
            $response[$i]["Item_description"] = $row->Item_description;
            $response[$i]["price"] = $row->price;
            $response[$i]["THEaddress"] = $row->THEaddress;
            $response[$i]["phone_nr"] = $row->phone_nr;

            //$response["id"] = $row->id;
            $i++;
            $response["i"] = $i;

        
        }
        echo json_encode($response);
    }

    if($action=="profile_display"){
        $loginObject = json_decode(file_get_contents('php://input'));
        $user_id = $loginObject->userID;
        if(isset($user_id)) {
            $sql = "SELECT * FROM userlist WHERE userID = " . $user_id  ;
            $row = $mySQL->query($sql)->fetch_object();

            $firstName = $row->firstName ;
            $lastName = $row->lastName ; 
            $email = $row->email;
            $phoneNr =$row->phoneNr ;
            $profile_pic =$row-> profile_pic;
            if($profile_pic <>""){
                $src="http://savva.cc/It_is_what_it_is/php-login-service/".$profile_pic;
            }else{
                $src="";
            }
            $response['id'] = $user_id ;
            
            //$response['error'] = "No error Awesome";
            $response['firstName']=$firstName;
            $response['lastName']=$lastName;
            $response['phoneNr']=$phoneNr;
            $response['email']=$email;
            $response['src']=$src;

            echo json_encode($response);
        } else {
            echo json_encode(array("status" => "error"));
        }
        

    }

     // Edit/update Profile
    if ($action == "edit_profile") {
        $loginObject = json_decode(file_get_contents('php://input'));
        $userID = $loginObject->userID;
        $firstName = $loginObject->firstName;
        $lastName = $loginObject->lastName;
        $phoneNr = $loginObject->phoneNr;
        $email = $loginObject->email;

                    $user_id = $userID;
                    


                        $sql = "CALL EditUser( '$user_id','$firstName', '$lastName', '$phoneNr','$email')";

                         if ($mySQL->query($sql) === TRUE) {
                            $response['signupSuccess'] = TRUE;
                            $response['error'] = "No error Awesome";
                            $response['whatever']="aaaas";
                            echo json_encode($response);
                        } else {
                            $response['signupSuccess'] = FALSE;
                            $response['error'] = "Edit failed. Please try again.";
                            echo json_encode($response);
                        }




    }

   

// Update Password
    if ($action == "update_pwd") {
        $loginObject = json_decode(file_get_contents('php://input'));
        $init_password = $loginObject->init_password ;
        $user_id = $loginObject->userID;
        $password = $loginObject->password;
        $passwordCheck = $loginObject->passwordCheck;

        $sql = "SELECT * FROM userlogin WHERE id = " . $user_id;
        $row = $mySQL->query($sql)->fetch_object();
        if (password_verify($init_password, $row->pass))
        {
            if($password!="" And $passwordCheck!==""){
            // Check if passwords are the same
                if ($password == $passwordCheck) {

                    $passEncrypt = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "CALL UpdatePassword('$user_id','$passEncrypt')";

                    if ($mySQL->query($sql) === TRUE) {
                            $response['signupSuccess'] = TRUE;
                            $response['error'] = "No error Awesome";
                            echo json_encode($response);
                        } else {
                            $response['signupSuccess'] = FALSE;
                            $response['error'] = "Update password failed. Please try again.";
                            echo json_encode($response);
                        }
                
                }else{
                    $response['signupSuccess'] = FALSE;
                    $response['error'] = "Password dosen't match  ";
                    echo json_encode($response);
            }
            }else{
                $response['signupSuccess'] = FALSE;
                $response['error'] = "Password can't be blank. Please fill it in  ";
                echo json_encode($response);
            }

        }else{
            $response['signupSuccess'] = FALSE;
            $response['error'] = "Wrong current Password";
            echo json_encode($response);
        }
    }
 




// Delete profile

    if ($action == "deleteProfile") {
        $loginObject = json_decode(file_get_contents('php://input'));

        $user_id = $loginObject->userID;

        $sql = "CALL DeleteUser('$user_id')";

        if ($mySQL->query($sql) === TRUE) {
            $response['signupSuccess'] = TRUE;
            $response['error'] = "No error Awesome";     
           echo json_encode($response);
        } else {
            $response['signupSuccess'] = FALSE;
            $response['error'] = "Delete Account went wrong";
            echo json_encode($response);
        }   
    }

// Create a post
    if ($action == "CreatePost") {
        $loginObject = json_decode(file_get_contents('php://input'));
        $title = $loginObject->title;
        $description = $loginObject->description;
        $price = $loginObject->price;
        $address = $loginObject->address;
        $phone_nr = $loginObject->phone_nr;

        $selected_categories_object = $loginObject->selected_categories_object;

        if (!empty($title) ) {
           
            if (!empty($description)) {
                if (!empty($phone_nr)) {

            
                    // $sql = "SELECT id FROM users WHERE email = '$email'";
                    // $result = $mySQL->query($sql);
                    if($price==""){
                        $price="null";
                    }

                    if($address==""){
                        $address="null";
                    }
                    $response['title'] = $title;
                    $response['description'] = $description;
                    $response['price'] = $price;
                    $response['address'] = $address;

                    $text ="";


                    foreach($selected_categories_object as $value){
                                //$text = $text + $value + " " ;
                            }

                    $sql = "CALL CreatePost('$title', '$description', '$price','$address','$phone_nr')";
                        
                            if ($mySQL->query($sql) == TRUE) {
                                $response['acctionSuccess'] = TRUE;
                                //$response['text'] = $selected_categories_object['vegan'];
                                echo json_encode($response);

                            } else {
                                $response['acctionSuccess'] = FALSE;
                                $response['error'] = "Post failed. Please try again.";
                                echo json_encode($response);
                            }           

                            foreach($selected_categories_object as $value){

                            }
                    }else {
                        $response['acctionSuccess'] = FALSE;
                        $response['error'] = "Posting failed. Please fill out the Phone Number";
                        echo json_encode($response);
                    }

                }else {
                    $response['acctionSuccess'] = FALSE;
                    $response['error'] = "Posting failed. Please fill out the Description";
                    echo json_encode($response);
            }
        } else {
            $response['acctionSuccess'] = FALSE;
            $response['error'] = "Posting failed. Please fill out the Title";
            echo json_encode($response);
        }
    }

//Upload Profile Picure
if($action == "upload"){

    if (isset($_FILES["fileToUpload"])) {
        $userID = $_GET['userID'];
        $username = $_GET['username'];

        class FileUpload {
            
            public function UploadFile(){

            // Used for controlling the logic in this script
            $file = $_FILES["fileToUpload"];
            $x= $_FILES['fileToUpload']['tmp_name'];
            // Used to create a JSON response
            $output = [];
            $fileData = [];

            $userID = $_GET['userID'];
            $username = $_GET['username'];

            $newName =$this->RenameFile($username) ;
            $size = $this->GetFileSize();
            $target_dir = "original/";
            $target_file = $this->FilePath($username);

           
            move_uploaded_file($file["tmp_name"], $target_file);
      
            $output['userID'] = $userID;
            $output['username'] = $username;
            $output["rename"] = $newName;
            $output['target_dir'] = $target_dir;
            $output['$file["tmp_name"]'] = $x;
            $output['size'] = $size;

            if($this->ImageCheck()){
                $output['error']=false;
            }else{
                $output['status']="Error , Wrong file type";
                $output['error']=True;
            }
            if($this->Accepted_File_Size()){
                $output['error']=false;
            }else{
                $output['status']="Error , File size too big";
                $output['error']=True;
            }

            if($this->is_tmp_name_empty()){
                $output['status']="Something is wrong with the file you try to uploade.    Please try another file";
                $output['error']=True;
            }
            echo json_encode($output);
            
            }
            
            public function GetFileType(){
                $name = $_FILES["fileToUpload"]["name"];
                $ext = substr(strrchr($name,'.'),1); 
            
                return $ext;
            }
            // Create a function that will rename the image into $Username+_Profile.Pic+filetype    ====> This way each user will have an unic and logic name for their profile pic
            public function RenameFile($username){
                $name = $_FILES["fileToUpload"]["name"];
                $ext = $this->GetFileType(); 
            
                $newName = $username."_ProfilePic." .$ext;
    
                return $newName;
            }

            public function GetFileSize(){
                $size=$_FILES['fileToUpload']['size'];

                return $size;
            
            }

            public function ImageCheck(){
                $allowedImageTypes = ["jpg", "jpeg", "png", "gif"];
                $fileType = strtolower($this->GetFileType());
                //chech if uploaded image extensions matches any $allowedImageTypes
                foreach($allowedImageTypes as $ext) {
                    if( $fileType == $ext){
                        $isAccepted =true;
                        break;
                        
                    }else{
                        $isAccepted =false; 
                    }
                }
                if($isAccepted){
                    return true;
                }
                return false;
            }
            public function FilePath($username){
                $newName =$this->RenameFile($username) ;
                
                $target_dir = "original/";
                $target_file = $target_dir . basename($newName);

                return $target_file;
            }
            public function Accepted_File_Size(){
                $allowedMaxFileSize = 1024 * 1024 * 5;
                if($this->GetFileSize()>$allowedMaxFileSize){
                    return false;
                }
                return true;
            }

            public function is_tmp_name_empty(){
                $file = $_FILES["fileToUpload"];
                if($file["tmp_name"]==""){
                    return true;
                } return false;

            }
        }

        $upload = new FileUpload();
        if($upload->ImageCheck()){
            $upload->UploadFile();
        }

        $filePath = $upload->FilePath($username);

    //there is a bug . In some cases tmp_name is empty and because of it the file is not uploaded thats why we chane file location in DB only if tmp_name <>"" and it's uploaded in the file
    if(!$upload->is_tmp_name_empty()){

            $sql = "UPDATE users
            SET profile_pic = '$filePath'
            WHERE id='$userID'";
            $result = $mySQL->query($sql);
    }

}


