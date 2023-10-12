function signupToggle() {
  var signupForm = document.querySelector(".signup-form");
  signupForm.classList.toggle("active");
  var wrapper = document.querySelector(".wrapper");
  wrapper.classList.toggle("active");
}

function loginToggle() {
  var loginForm = document.querySelector(".login-form");
  loginForm.classList.toggle("active");
  var wrapper = document.querySelector(".wrapper");
  wrapper.classList.toggle("active");
}
