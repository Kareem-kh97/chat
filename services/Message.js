const MessageService = {
  currentMessages: [],

  init: function () {
    // set event listener for window size change to update the height of the messages container (row)
    MessageService.container_size_updater();

    // MessageService.get_messages();
    MessageService.expandTextarea("input-textarea");

    // validate the login form inside the login modal
    
    $("#send-message-form").validate({
      submitHandler: function (form) {
        var entity = Object.fromEntries(new FormData(form).entries());
        $("#login-modal-spinner").show();
        MessageService.send_message(entity);
      },
    });

    // validate the edit message form
    $("#edit-message-form").validate({
      submitHandler: function (form) {
        var entity = Object.fromEntries(new FormData(form).entries());
        // console.log(entity);
        MessageService.update_text(entity);
      },
    });
  },

  /**
   * 
   * retrieving messages for specific contactID by AJAX call
   * by sending an ajax call to the endpoint 
   * inside ajax `beforesend` method is used to set the authorization header with a token retrieved from local storage
   * in case of success then the messages/data is being sent to the list_messages
   */
  get_messages: function (contactId) {
    $.ajax({
      url: "rest/messages/" + contactId,
      type: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("Authorization", localStorage.getItem("token"));
      },
      success: function (data) {
        if (MessageService.currentMessages === []) {
          // no messages in currentMessages array (empty array)
          // set the currentMessages array with response data
          MessageService.currentMessages = data;
          console.log(MessageService.currentMessages);

          // update html
          MessageService.list_messages(
            MessageService.currentMessages,
            contactId
          );
        } else {
          // if there is messages in currentMessages array
          if (
            JSON.stringify(MessageService.currentMessages) ===
            JSON.stringify(data)
          ) {
            // if there is no changes in database for messages
            // console.log("There is no update in messages");
          } else {
            // update the currentMessages array with response data
            MessageService.currentMessages = data;

            // update html
            MessageService.list_messages(
              MessageService.currentMessages,
              contactId
            );
          }
        }
      
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message);
        // ContactService.logout();
      },
    });
  },

  list_messages: function (data, contactId) {
    $("#messages-list").html("");
    var html = "";
    for (let i = 0; i < data.length; i++) {
      

      /**
       * we are figuring out who is the sender and who is the reiever
       * if contactID matchs the recieverId ==> `senderType` ==> `sender`
       * if contactID does not matches the recieverId ==> `senderType` = `contact` 
       * 
       * `data` is an associative array that contains the meg  index with the contactId
       */
      const senderType = contactId === data[i].receiver_id ? "user" : "contact";
      const bg_color =
        contactId === data[i].receiver_id ? "primary" : "secondary";
      const alignment = contactId === data[i].receiver_id ? "end" : "start";
      const edit_mode = contactId === data[i].receiver_id ? "block" : "none";
      html +=
        `
    <div id="msg-` +
        data[i].id +
        `" name="Message item" class="col-12 p-0">
      <div class="container text-end">
        <div class="row justify-content-` +
        alignment +
        ` msg-item">        
        <div class="col-auto mt-2 px-1">
          <button type="button" data-bs-toggle="dropdown" aria-expanded="false" class="text-decoration-none msg-options-button" style="
          padding-bottom: 9px;
          padding-top: 9px;            
          border: none;
          background: none;
          padding-left: 4px;
          padding-right: 4px;
            ">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
              <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
            </svg>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" type=button onclick="MessageService.softdelete(` +
        data[i].id +
        `)">Delete</a></li>
            <li><a class="dropdown-item" type=button onclick="MessageService.toggle_edit_message(` +
        data[i].id +
        `, '` +
        data[i].text +
        `')" style="display: ` + edit_mode + `">Edit Message</a></li>
          </ul>
        </div>
          <div class="col-auto py-2 px-3 my-1 ` + senderType + `-message bg-` +
        bg_color +
        ` text-end"
            style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
            <p class="mb-0 text-white" style="max-width: 500px; text-align: start;  ">` +
        data[i].text +
        `</p>
          </div>
        </div>
        
      </div>
    </div>
    `;
    }
    $("#messages-list").html(html);

    // Scroll to bottom automatically when the messages (html get updated)
    var objDiv = document.getElementById("messages-row");
    objDiv.scrollTop = objDiv.scrollHeight;
  },

  // it being called when usersubmits the send message form
  // recieve entity
  send_message: function (entity) {
    // retrieve the contact from the browser
    const currentContact = localStorage.getItem("current_contact");
    // set the rID property``of the entity to the currentContact
    // by now we have the message and the rID
    entity["receiver_id"] = currentContact;
    //clear
    document.getElementById("input-textarea").value = "";
    // make a server call
    $.ajax({
      url: "rest/sendmessage",
      type: "POST",
      //data to be sent to the entity
      data: JSON.stringify(entity), 
      //to say data are in json
      contentType: "application/json",
      dataType: "json", // expected data response in json formate
      // setting the header to `Authorized` by doing so the server can recognize/authorize the user
      beforeSend: function (xhr) {
        xhr.setRequestHeader("Authorization", localStorage.getItem("token"));
      },
      success: function (result) {
        console.log("IT IS SUCCESSFUL");
        console.log(result);
        //update the message list by getting last messages from contact
        MessageService.get_messages(currentContact); 
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        // toastr.error(XMLHttpRequest.responseJSON.message);
        // console.log(XMLHttpRequest);
        // console.log(textStatus);
        // console.log(errorThrown);
      },
    });
  },
  update_text: function (entity) {
    // entity["receiver_id"] = currentContact;

    $.ajax({
      url: "rest/updatetext",
      type: "PUT",
      data: JSON.stringify(entity), // the entity is the sent data
      contentType: "application/json",
      dataType: "json", // expected data in the token are of json formate
      beforeSend: function (xhr) {
        xhr.setRequestHeader("Authorization", localStorage.getItem("token"));
      },
      success: function (result) {
        console.log("IT IS SUCCESSFUL");
        toastr.success(result.message);
        $("#edit-message-modal").modal("hide");
        MessageService.get_messages(parseInt(localStorage.getItem('current_contact')));
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        // toastr.error(XMLHttpRequest.responseJSON.message);
        toastr.error(errorThrown);
        console.log(errorThrown);
      },
    });
  },

  expandTextarea: function (id) {
    document.getElementById(id).addEventListener(
      "keyup",
      function () {
        if (this.scrollHeight > 200) {
          this.style.overflow = "auto";
          this.style.height = 200;
        } else {
          this.style.overflow = "hidden";
          this.style.height = 0;
          this.style.height = this.scrollHeight + "px";
        }
      },
      false
    );
  },

  container_size_updater: function () {
    // responsible for updating the height of the messages row container whenever the window is resized
    $("#messages-row").height("" + window.innerHeight - 80);
    window.addEventListener("resize", function () {
      // it adds an addEventListener to the "resize" event
      $("#messages-row").height("" + window.innerHeight - 80); // it sets the height of the container to the hight of the window - 110px
    });
  },

  softdelete: function (id) {
    console.log(id);
    $.ajax({
      url: "rest/softdelete/" + id,
      type: "DELETE",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("Authorization", localStorage.getItem("token"));
      },
      success: function () {
        // console.log("deleting " + id);
        // console.log("Message with ID " + id + " has been soft-deleted.");
        MessageService.get_messages(
          parseInt(localStorage.getItem("current_contact"))
        );
        toastr.success("Message has been deleted.");
        //   $("#message-row").html('');
        //   var html = "";
        //   html += `<div
        //   id="messages-row"
        //   name="Messages row"
        //   class="row px-1 py-1 text-center"
        //   style="overflow-y: scroll"
        //   onclick="MessageService.echo( `+ id +`)"
        // ></div>`;
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message);
      },
    });
  },

  toggle_edit_message: function (id, text) {
    $("#message-edit-id").val(id);
    $("#message-edit-text").val(text);
    $("#edit-message-modal").modal("show");
  },

  // toggle_edit_message: function (entity) {
  // update message with new text ajax call
  // },
};

window.addEventListener("load", () => {
  // call it on page load so that will sets eventListener
  //and form validation for send-message form
  MessageService.init();
});
