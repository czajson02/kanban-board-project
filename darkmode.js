// Logout redirect function
function logout(){
    window.location="logout.php";
}

// Dark theme function
function darkmode(){
    b = document.getElementsByTagName("body")[0];
    b.style.background= "url(./pics/w3.jpg)";
    b.style.backgroundSize= "cover";
    b.style.color="white";
    b.style.transition="0.4s";
    uid.style.color="orange";
    uid.style.transition="0.6s";
}

// Light theme function
function lightmode(){
    b = document.getElementsByTagName("body")[0];
    b.style.background= "url(./pics/w1.jpg)";
    b.style.backgroundSize= "cover";
    b.style.color="black";
    b.style.transition="0.4s";
    uid.style.color="darkblue";
    uid.style.transition="0.6s";
}

// Dark/light mode switch via input state change
dark.addEventListener('change', function(){
    if(this.checked){
        lightmode();
    }
    else{
        darkmode();
    }
})