document.addEventListener("DOMContentLoaded", function() {
 //hide login and show registration
 let signup = document.querySelector('#signup');
 let signin = document.querySelector('#signin')
 let login_form = document.querySelector('.first');
 let register_form = document.querySelector('.second');
 signup.addEventListener('click', function(event) {
  event.preventDefault();
  register_form.classList.remove('second');
  login_form.classList.add('second');
 })
 signin.addEventListener('click', function(event) {
  event.preventDefault();
  register_form.classList.add('second');
  login_form.classList.remove('second');
 })
})