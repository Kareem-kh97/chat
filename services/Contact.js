const ContactService = {
  init: function() {
    
    // ContactService.check_token();
    ContactService.list();
    ContactService.container_size_updater();

    
    // validate the edit message form
    $("#add-friend-form").validate({
      submitHandler: function (form) {
        var entity = Object.fromEntries(new FormData(form).entries());
        // console.log(entity);
        ContactService.add_friend(entity);
      },
    });

  },

/**
 * fetch contacts
 * set header
 * generate html for each contact
 * appeand it to contact_list
 */

  list: function(){
    $.ajax({  // fetch a lsit of contacts from Rest endoint
      url: "rest/contacts",  //this resr end point contains contaacts
      type: "GET",
      beforeSend: function(xhr){
        xhr.setRequestHeader('Authorization', localStorage.getItem('token'));
      },
      success: function(data) {
        $("#contact-list-col").html("");
        var html = "";
        for(let i = 0; i < data.length; i++){
          html += `
            <div id="contact-`+data[i].id+`" name="Contact Item" class="card btn text-start mb-1" style="background-color: azure;" onclick="ContactService.on_contact_click(`+data[i].id+`)">
              <div class="card-body py-2 px-0">
                <div class="container">
                  <div class="row">
                    <div class="col">
                      <p class="fs-5 mb-1">`+data[i].first_name+` `+data[i].last_name+`</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `;
        }
        $("#contact-list-col").html(html);
        // console.log(data);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message);
        // ContactService.logout();
      }
    });
  },

  /**
   * by clicking takes the contactID
   * set current_contact to contactId
   * retrieves mesgs using `get_messages`
   */
  on_contact_click: function(contactId){

    if(localStorage.getItem('current_contact') === null){
      // if the page is reloaded or just opened (there is no current contact)

      // set current contact id in localstorage
      localStorage.setItem('current_contact', contactId)

      MessageService.get_messages(contactId);

      $('#type-input-col').css("visibility", "visible");

      //set getMessages interval
        setInterval(function() {
          MessageService.get_messages(parseInt(localStorage.getItem('current_contact')));
        }, 1000);
    }

    else{
      // update current contact id in localstorage
      localStorage.setItem('current_contact', contactId);
      MessageService.get_messages(contactId);
    }


  },

  /**
   * updating the hight of the contactList
   */
  container_size_updater: function(){ 
    window.addEventListener("resize", function(){ // it adds an addEventListener to the "resize" event
      $('#contact-list-col').height(''+window.innerHeight-110) // it sets the height of the container to the hight of the window - 110px
    });
  },

  /**
   * 
   * adding a new contact 
   */
  add_friend: function(entity){
    $.ajax({
      url: "rest/contacts",
      type: "POST",
      data: JSON.stringify(entity), // the entity is the sent data
      contentType: "application/json",
      dataType: "json", // expected response data in the token are of json formate
      beforeSend: function (xhr) {
        xhr.setRequestHeader("Authorization", localStorage.getItem("token"));
      },
      success: function (result) {
        console.log("IT IS SUCCESSFUL");
        console.log(result);
        toastr.success(result['message']);
        ContactService.list();
        $('#add-friend-modal').modal('hide');
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message);
        // console.log(XMLHttpRequest);
        // console.log(textStatus);
        // console.log(errorThrown);
      },
    });

  },

};

// on page load Call the init function
window.addEventListener('load', () => {
  // ContactService.init(); // responsible for initializing the ContactService object
});
