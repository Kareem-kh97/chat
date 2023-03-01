const UserService = {
  init: function() {
    
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
      submitHandler:function(form) {
        var entity = Object.fromEntries((new FormData(form)).entries());
        $('#signup-modal-spinner').show();
        UserService.signup(entity);
      }
    });
  
  },
  signup: function(entity) {
    $.ajax({
      url: 'rest/signup',
      type: 'POST',
      data: JSON.stringify(entity),
      contentType: "application/json",
      dataType: "json",
      beforeSend: function(xhr) {
        xhr.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem("token"));
      },
      success: function(result) {
        console.log(result);
        localStorage.setItem("token", result.token);
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
        toastr.error(XMLHttpRequest.responseJSON.message);
        // hide the modal spinner
        $('#login-modal-spinner').hide();
        
      }
    });
  },

  logout: function(){
    localStorage.clear();
    location.reload();
  },

};

// Call the init function when the page loads
window.addEventListener('load', () => {
  UserService.init();
});
