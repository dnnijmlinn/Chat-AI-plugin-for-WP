console.log('My JS is loaded'); 

jQuery(document).ready(function($) {
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
