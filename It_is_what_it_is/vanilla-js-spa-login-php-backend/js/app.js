import {
	navigateTo
} from "./router.js";
console.log("app.js is running!");

const host = "http://savva.cc/It_is_what_it_is/php-login-service/";
// created this function to be able to store and get data from fisrt page sign up
// I needed the username that you type in the first form page {I didn't figure out how to work with sessions within php and JS so my sick brai come up with this solution😅; (it seems to work😅)}
async function login1(username, password) {

	const loginObject = {
		username: username,
		password: password
	};
	//console.log(loginObject);
	const response = await fetch(`${host}?action=login`, {
		method: "POST",
		body: JSON.stringify(loginObject)
	});

	const data = await response.json();
	console.log(data);
	if (data.authenticated) {
		localStorage.setItem("userIsAuthenticated", true);
		localStorage.setItem("authUser", JSON.stringify(data.userData));
		resetMessage();

	} else {
		document.querySelector(".login-message").innerHTML = data.error;
	}
}



async function login() {
	const username = document.querySelector("#login-username").value;
	const password = document.querySelector("#login-password").value;
	const loginObject = {
		username: username,
		password: password
	};
	console.log(loginObject);
	const response = await fetch(`${host}?action=login`, {
		method: "POST",
		body: JSON.stringify(loginObject)
	});

	const data = await response.json();
	console.log(data);
	if (data.authenticated) {
		localStorage.setItem("userIsAuthenticated", true);
		localStorage.setItem("authUser", JSON.stringify(data.userData));
		resetMessage();
		navigateTo("#/");
	} else {
		document.querySelector(".login-message").innerHTML = data.error;
	}
}


async function logout() {
	//reset localStorage
	localStorage.removeItem("userIsAuthenticated");
	localStorage.removeItem("authUser");
	const response = await fetch(`${host}?action=logout`, {
		method: "POST",
		body: JSON.stringify()
	});

	//navigate to login
	navigateTo("#/login");
}

async function signup() {
	const username = document.querySelector("#signup-username").value;
	const email = document.querySelector("#signup-email").value;
	const password = document.querySelector("#signup-password").value;
	const passwordCheck = document.querySelector("#signup-password-check").value;

	const user = {
		username,
		email,
		password,
		passwordCheck
	};
	console.log(user);

	const response = await fetch(`${host}?action=signup`, {
		method: "POST",
		body: JSON.stringify(user)
	});

	const data = await response.json();
	console.log(data);
	if (data.signupSuccess) {
		login1(user['username'], user['password']);

		resetMessage();
		navigateTo("#/signup_2step");
	} else {
		document.querySelector(".signup-message").innerHTML = data.error;
	}
}

async function signup_2step() {
	const x = JSON.parse(localStorage.getItem("authUser")); // get from localstorage and parse json
	console.log(x['username']);

	const firstName = document.querySelector("#signup_2step-firstname").value;
	const lastName = document.querySelector("#signup_2step-lastname").value;

	const phoneNr = document.querySelector("#signup_2step-phoneNr").value;
	const userID = x['userID'];

	const user = {
		firstName,
		lastName,
		phoneNr,
		userID
	};
	console.log("updated", user);


	const response = await fetch(`${host}?action=signup_2step`, {
		method: "POST",
		body: JSON.stringify(user)
	});

	const data = await response.json();
	console.log(data);
	if (data.signupSuccess) {
		console.log(user);

		localStorage.setItem("userIsAuthenticated", true);
		localStorage.setItem("authUser", JSON.stringify(data.userData));


		resetMessage();
		navigateTo("#/");
	} else {
		document.querySelector(".signup-message").innerHTML = data.error;
	}
}

async function appendPost() {
	const response = await fetch(`${host}?action=appendPost`, {
		method: "POST",
		body: JSON.stringify()
	});

	const data = await response.json();
	console.log(data);

	let displayPosts = "";

	for (let i = 0; i < data.i; i++) {
		displayPosts += /*html*/ `
			<article class="post">
				
				<div class="postImg">
					<img src="img/postImg.png" alt="food image">
				</div>
				
					<h3>  ${data[i].title} </h3>
				

				
					<p>${data[i].Item_description} </p>
				
			<!--	<div class="profile_info"> 
					<p>Email :  ${data.email} </p>
				</div>
				<div class="profile_info"> 
					<p>Phone Number :  ${data.phoneNr} </p>
				</div> -->
					
				
			
		`;
		if ((data[i].price != null) && (data[i].price != "null")) {
			displayPosts += /*html*/ `
			<p style="text-align:left;"> Price : ${data[i].price} DKK</p>
			`;
		}


		if ((data[i].THEaddress != null) && (data[i].THEaddress != "null")) {
			displayPosts += /*html*/ `
			<p style="text-align:left;"> Address : ${data[i].THEaddress}</p>
			`;

		}
		if ((data[i].phone_nr != null) && (data[i].phone_nr != "null")) {
			displayPosts += /*html*/ `
			<p style="text-align:left;"> Phone number : ${data[i].phone_nr}</p>
			`;

		}

		displayPosts += /*html*/ `</article> `;

	}
	document.querySelector("#home-grid").innerHTML = displayPosts;

}
await appendPost();

function showProfile() {
	const userIsAuthenticated = localStorage.getItem("userIsAuthenticated"); // get from localstorage

	if (userIsAuthenticated) {
		profile_display()

	}
}
async function profile_display() {

	const x = JSON.parse(localStorage.getItem("authUser")); // get from localstorage and parse json

	const userID = x['userID'];
	console.log(userID);
	const user = {
		userID
	};



	const response = await fetch(`${host}?action=profile_display`, {
		method: "POST",
		body: JSON.stringify(user)
	});

	const data = await response.json();
	console.log(data);
	//resetMessage();
	//document.querySelector(".profile-message").innerHTML = "Refresh the page to see updated info";
	if (data.src == "") {
		data.src = "img/icon.png";
	}

	document.querySelector("#user-data").innerHTML = /*html*/ `
			<article class="profile_page">
				<div class="profile_info" >

					<img src="${data.src}" style="height:auto; width:100px; 	margin-bottom: 50px;">
				</div>

				<div class="profile_info"> 
					<p>First Name :  ${data.firstName} </p>
				</div>

				<div class="profile_info"> 
					<p>Last Name :  ${data.lastName} </p>
				</div>
				<div class="profile_info"> 
					<p>Email :  ${data.email} </p>
				</div>
				<div class="profile_info"> 
					<p>Phone Number :  ${data.phoneNr} </p>
				</div>
					
				
			</article>
		`;


}

async function editProfile() {
	const userfromDB = JSON.parse(localStorage.getItem("authUser")); // get from localstorage and parse json


	const userID = userfromDB['userID'];
	const username = userfromDB['username'];
	const firstName = document.querySelector("#edit_profile-firstname").value;
	const lastName = document.querySelector("#edit_profile-lastname").value;
	const phoneNr = document.querySelector("#edit_profile-phoneNr").value;
	const email = document.querySelector("#edit_profile-email").value;

	const user = {
		userID,
		username,
		firstName,
		lastName,
		phoneNr,
		email,

	}
	console.log(user);

	const response = await fetch(`${host}?action=edit_profile`, {
		method: "POST",
		body: JSON.stringify(user)
	});

	const data = await response.json();
	console.log("this is data", data);
	if (data.signupSuccess) {
		resetMessage();
		document.querySelector(".edit-profile-message").innerHTML = data.error;
		navigateTo("#/profile");
	} else {
		document.querySelector(".edit-profile-message").innerHTML = data.error;
	}

}

async function update_pwd() {
	const init_password = document.querySelector("#update-pwd-init-password").value;
	const password = document.querySelector("#update-pwd-password").value;
	const passwordCheck = document.querySelector("#update-pwd-password-check").value;
	const x = JSON.parse(localStorage.getItem("authUser")); // get from localstorage and parse json
	console.log(x['username']);

	const userID = x['userID'];
	const user = {
		userID,
		init_password,
		password,
		passwordCheck
	};

	console.log(user);



	const response = await fetch(`${host}?action=update_pwd`, {
		method: "POST",
		body: JSON.stringify(user)
	});

	const data = await response.json();
	console.log(data);
	if (data.signupSuccess) {
		alert('Succesful update of password');
		resetMessage();
		navigateTo("#/profile");
	} else {
		document.querySelector(".update-pwd-message").innerHTML = data.error;
	}
}



async function deleteProfile() {


	const userfromDB = JSON.parse(localStorage.getItem("authUser"));

	const userID = userfromDB['userID'];
	const user = {
		userID
	}

	const response = await fetch(`${host}?action=deleteProfile`, {
		method: "POST",
		body: JSON.stringify(user)
	});

	const data = await response.json();
	console.log(data);
	if (data.signupSuccess) {
		alert('user Deleted');
		resetMessage();
		logout();
		//navigateTo("#/profile");
	} else {
		document.querySelector(".update-pwd-message").innerHTML = data.error;
	}

}

function pick_filter() {
	console.log("its working");
	// Add active class to the current button  
	var header = document.getElementById("Category");
	var btns = header.getElementsByClassName("filter_category");

	for (const link of btns) {
		link.addEventListener("click", function () {

			//Getting the last name of the class
			//if the class is active the last 7 characters will be "active"
			let activeClass = link.classList[2];
			let x = link.classList[2];
			console.log("x", x);

			if (activeClass === "active") {
				link.classList.replace("active", "unactive");
			} else {
				link.classList.replace("unactive", "active");
			}
			console.log("link", link.classList);
		});
	}
}

async function CreatePost() {
	// create an object that holds the information about selected categories when creating the post as 0 and 1 (false or true) depends what was selected
	var header = document.getElementById("Category");
	var btns = header.getElementsByClassName("filter_category");
	let selected_categories_object = new Object();
	for (const link of btns) {
		selected_categories_object[link.classList[0]] = 0;
		console.log(link.classList[0]);
		if (link.classList[2] == "active") {
			//console.log(link.classList[0]);
			selected_categories_object[link.classList[0]] = 1;
		}

	}
	let o = new Object;
	for (let i = 0; i < btns.length; i++) {
		o[i] = btns[i].classList[0];
	}
	console.log("OO", o);
	const title = document.querySelector("#add-title").value;
	const description = document.querySelector("#add-description").value;
	const price = document.querySelector("#add-price").value;
	const address = document.querySelector("#add-address").value;
	const phone_nr = document.querySelector("#add-phoneNr").value;

	//const imageFile = document.querySelector("#PostfileToUploadUpdate").files[0];

	const user = {
		title,
		description,
		price,
		address,
		phone_nr,
		selected_categories_object
	};
	console.log(user);

	const response = await fetch(`${host}?action=CreatePost`, {
		method: "POST",
		body: JSON.stringify(user)
	});

	const data = await response.json();
	console.log("Post Data", data);
	if (data.acctionSuccess) {
		alert("Item post succesfuly");
		resetMessage();
		navigateTo("#/");
	} else {
		document.querySelector(".add-message").innerHTML = data.error;
	}
}


function resetMessage() {
	document.querySelector(".signup-message").innerHTML = "";
	document.querySelector(".login-message").innerHTML = "";
	document.querySelector(".signup_2step-message").innerHTML = "";
	//document.querySelector(".profile-message").innerHTML = "";
	document.querySelector(".edit-profile-message").innerHTML = "";
	document.querySelector(".update-pwd-message").innerHTML = "";
	document.querySelector(".add-message").innerHTML = "";
}





// function test() {
// 	var header = document.getElementById("Category");
// 	var btns = header.getElementsByClassName("filter_category");
// 	let selected_categories_object = new Object();
// 	for (const link of btns) {
// 		selected_categories_object[link.classList[0]] = 0;
// 		if (link.classList[2] == "active") {
// 			console.log(link.classList[0]);
// 			selected_categories_object[link.classList[0]] = 1;
// 		}
// 	}
// 	console.log(selected_categories_object);
// }



// event listeners
document.querySelector("#btn-login").onclick = () => login();
document.querySelector("#btn-logout").onclick = () => logout();
document.querySelector("#btn-signup").onclick = () => signup();
document.querySelector("#btn-signup_2step").onclick = () => signup_2step();
document.querySelector("#btn-edit-profile").onclick = () => editProfile();
//document.querySelector("#profile-page").onclick = () => profile_display();


document.getElementById("profile-page").addEventListener("load", showProfile());


//document.querySelector("#btn-update-profile-pic").onclick = () => update_profile_pic();
document.querySelector("#btn-update-pwd").onclick = () => update_pwd();
document.querySelector("#drop-profile").onclick = () => deleteProfile();
document.querySelector("#btn-add-item").onclick = () => CreatePost();


//document.querySelector(".filter_category").onclick = () => myFunction();



document.getElementById("Category").addEventListener("click", pick_filter());




document.querySelector("#update-pic").onclick = () => updateProfilePic();
// /**
//  * Preview Image Helper function
//  */
// function previewImage(file, previewId) {
// 	if (file) {
// 		let reader = new FileReader();
// 		reader.onload = event => {
// 			document.querySelector('#' + previewId).setAttribute('src', event.target.result);
// 		};
// 		reader.readAsDataURL(file);
// 	}
// }

/**
 * Upload image to php backend
 */
async function uploadImage(imageFile) {
	const x = JSON.parse(localStorage.getItem("authUser")); // get from localstorage and parse json

	const userID = x['userID'];
	const username = x['username'];
	console.log(userID);


	let formData = new FormData();

	formData.append("fileToUpload", imageFile);

	console.log("Image file", imageFile);



	const response = await fetch(`${host}?action=upload&userID=${userID}&username=${username}`, {
		method: "POST",
		headers: {
			"Access-Control-Allow-Headers": "Content-Type"
		},
		body: formData
		//body: JSON.stringify(user)

	});
	// waiting for the result
	const data = await response.json();
	console.log(data);

	if (!data.error) {
		alert('File seccesfuly uploaded');
		resetMessage();
		navigateTo("#/profile");
	} else {
		document.querySelector(".update-pic-message").innerHTML = data.status;
	}



	return data;
}
async function updateProfilePic() {
	const imageFile = document.querySelector("#fileToUploadUpdate").files[0];
	console.log("update pro imgfile", imageFile);
	//console.log("imageFile ", imageFile);
	//let imageFileName = document.querySelector("#imagePreviewUpdate").src.split("/").pop();
	//console.log("imageFileName ", imageFileName, "0");
	if (imageFile) {
		const imageResult = await uploadImage(imageFile);
		// if (imageResult.status === "success") {
		// 	imageFileName = imageResult.data.fileName;
		// }
	}
	//updateUser(firstname, lastname, age, haircolor, countryName, gender, lookingFor, imageFileName);

}