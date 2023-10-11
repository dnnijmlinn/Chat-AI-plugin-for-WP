function getCurrentTime() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    return hours + ':' + minutes;
}

jQuery(document).ready(function($) {
    var hiText = $('#chatbot_area').data('hi-text');
    $('#chat_messages').append('<div>' + hiText + ' <span class="message-time">' + getCurrentTime() + '</span></div>');

    $('#chat_send').click(function() {
        let userText = $('#chat_input').val();
        $('#chat_messages').append('<div>' + userText + ' <span class="message-time">' + getCurrentTime() + '</span></div>');

        $.ajax({
            url: "http://127.0.0.1:5000/ask",
            method: "POST",
            data: JSON.stringify({ text: userText }),
            contentType: "application/json",
            success: function(response) {
                console.log('Success response:', response);
                $('#chat_messages').append('<div>' + response.response + ' <span class="message-time">' + getCurrentTime() + '</span></div>');
                
                // $.post(
                //     myAjax.ajaxurl,
                //     {
                //         action: 'save_chat_history',
                //         user_message: userText,
                //         bot_response: response.response
                //     }
                // );
            },
            error: function(error) {
                console.error('Error response:', error);
                console.error(error);  
                $('#chat_messages').append('<div>Sorry, there was an error. <span class="message-time">' + getCurrentTime() + '</span></div>');
                console.log('Response received:', response);

            }
        });

        $('#chat_input').val(''); 
    });

    $('#chat_input').keydown(function(e) {
        if (e.keyCode == 13 && !e.shiftKey) {
            e.preventDefault();
            $('#chat_send').click();
        }
    });
    console.log('My JS is loaded'); 

        $(".file-link").click(function(e) {
        e.preventDefault();
        console.log("File link clicked");

        var filepath = $(this).data("filepath");
        console.log("File path: " + filepath);

        console.log("Ajax URL: " + ajaxurl); 

        $.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                action: "get_file_content",
                filepath: filepath
            },
            success: function(response) {
                console.log("Response received"); 
                console.log(response);
                $("#file-editor").html('<textarea style="width:100%; height:400px;">' + response + '</textarea>');
            },
            error: function() {
                console.error('Failed to fetch file content');
            }
        });
    });
});

document.getElementById('chat_input').addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});


