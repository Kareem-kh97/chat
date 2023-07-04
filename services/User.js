const UserService = {
  // `UserService` class handles the authentication, registration and token management

  // initilizes the UserService module by performing the other functions.
  init: function() {
    // to check whther the token exist on localstorage, no token then show login modal
    UserService.check_token();

    // validate the login form inside the login modal
    $('#login-form').validate({
      submitHandler: function(form) {
        var entity = Object.fromEntries((new FormData(form)).entries());
        $('#login-modal-spinner').show()
        UserService.login(entity);
      }
    });
    $('#signup-form').validate({
      submitHandler:function(form) { // handle form submission
        var entity = Object.fromEntries((new FormData(form)).entries());
        $('#signup-modal-spinner').show();
        UserService.signup(entity);
      }
    });
  
  },
  // a function that send AJAX POST request to the server to register a new user
  // -sends user registration data (`entity`) as json data in the request payload
  // -request is made to the `rest/signup` URL
  // -the `Authorization` header is set with JWT retrieved from the localstorage.
  // on success: it saves the tooken that returned by the server in localstorage + hides signup modal + shows a success msg and reloads the page.
  // on error: shows a toaster msg and hide the spinner.
  signup: function(entity) {
    $.ajax({
      url: 'rest/signup',
      type: 'POST',
      data: JSON.stringify(entity),
      contentType: "application/json",
      dataType: "json", //indicating that response should be in json format
      beforeSend: function(xhr) { // retrieving token from localstorage and sets it as the value of the `Aythorization`
        xhr.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem("token"));
      },
      // result response data afrom the server
      // it is called if the signup rerquest is successful
      success: function(result) {
        console.log(result);
        localStorage.setItem("token", result.token); //store the recieved token
        $('#signup-modal').modal('hide');
        // hide the modal spinner
        $('#signup-modal-spinner').hide();
        
        // show success message
        toastr.success("Sign up successful!");
        // setup contacts and account info
        
        window.location.reload();
        
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message);
        // hide the modal spinner
        $('#signup-modal-spinner').hide();
        
      }
    });
  },
  
  check_token: function(){
    var token = localStorage.getItem("token");
    if (!token){
      // show login modal when page loads if there is no token
      $('#login-modal').modal('show');      
    }
    else{
      ContactService.init();
    }
  },

  login: function(entity){    
    $.ajax({
      url: 'rest/login',
      type: 'POST',
      data: JSON.stringify(entity),
      contentType: "application/json",
      dataType: "json",
      
      success: function(result) {
        console.log(result);
        localStorage.setItem("token", result.token);
        
        // hide the modal
        $('#login-modal').modal('hide');
        // hide the modal spinner
        
        
        $('#login-modal-spinner').hide();
        
        // setup contacts and account info
        
        window.location.reload();
        
      },
      
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.log(XMLHttpRequest.responseText);
        toastr.error('An error occurred.');
        // hide the modal spinner
        $('#login-modal-spinner').hide();
    }
    
    });
  },
// clears the localstorage and reloads the page
  logout: function(){
    localStorage.clear();
    location.reload();
  },

};

// addEventListener: ataches an even-listener to the `load` event of the window
// when the `load` event is triggered the UserService.init is executed
// pirpose: is to ensure that all the necessary resources are loaded (web page is fullt loaded)
window.addEventListener('load', () => {
  UserService.init();
});
