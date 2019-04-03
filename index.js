
// Programming Project#1
// 	Author: Xiang Lin(xxl180009)
// 	Date: March 01, 2019
// 	Description: Create a Library management application for retrieveing, modify data with mysql database.
//     File Description: JS file performs Ajax call to pass data back and borth, also performs interaction.
 

$(document).ready(function(){

    //globle vars:


    //Search button is clicked.
    $("#search").click(function(event){
        //get the textstring user put
        var inputString = $("#entry").val();

        console.log("input is: " + inputString);

        //handle some exceptions, bad searching
        if(inputString.length ==0){
            $("#message").css("color","red");
            $("#message").fadeIn(500);
            $("#message").text("Enter something to start a search...");
        }else if(inputString == "&"){
            $("#message").css("color","red");
            $("#message").fadeIn(500);
            $("#message").text("Current system doesn't support searching for '&' symbol...");
        }else if(inputString == "#"){
            $("#message").css("color","red");
            $("#message").fadeIn(500);
            $("#message").text("Current system doesn't support searching for '# symbol...");
        }else if(inputString == "%"){
            $("#message").css("color","red");
            $("#message").fadeIn(500);
            $("#message").text("Current system doesn't support searching for '%' symbol...");
        }else if(inputString == "+"){
            $("#message").css("color","red");
            $("#message").fadeIn(500);
            $("#message").text("Current system doesn't support searching for '+' symbol...");
        }
        else{
            //$("#message").hide();
            //perform ajax call to pass string to server side to get output.
            $.ajax({  
                type: 'GET',
                url: 'search.php', 
                data: { 'string': inputString },
                success: function(response) {
                   $("#table-container").html(response);
                    $("#table-container").fadeIn(600);

                     //use ajax to make sure the check out butotn is clickable
                      ///////////////check out implementation//////////
                     $(".availableBtn").click(function(){
                        $(".checkout-container").fadeOut(200);
                        $(".checkout-container").fadeIn(500);
                        //page is set
                        var selectedISBN = $(this).attr('name');
                        var selectedTitle = $(this).parent().prev().prev().prev().html();
                        $("#checkoutTitle").val(selectedTitle);
                        $("#checkoutISBN").val(selectedISBN);
                        $( "#checkoutBid" ).focus();
                    });

                    //use ajax to make sure check in button is ok.
                    $(".notavailableBtn").click(function(){
                        var selectedISBN = $(this).attr('name');
                        $.ajax({
                            type: 'GET',
                            url: 'checkIn.php', 
                            data: {isbn:selectedISBN},
                            success: function(response){
                                alert(response);
                            },error:function(xhr){alert(xhr.responseText)}
                        });
                        window.location.reload();
                    });




                },error:function(xhr){alert(xhr.responseText)}
            });
        }
    });


    ///////////////Borrower page implementation//////////
    //Go to create new borrower sign up page:
    $("#newBorrowerBtn").click(function(){
        location.href = "newBorrower.html";
    });
    //Go back to main page from sign up page:
     $("#BReturnToMain").click(function(){
        location.href = "index.html";
    });
    $("#BSignup").click(function(){
        var Bname = $("#Bname").val();
        var Bssn = $("#Bssn").val();
        var Baddress = $("#Baddress").val();
        var Bphone = $("#Bphone").val();
        //check all the fields:
        var ssnpatt = /^\d{3}-\d{2}-\d{4}$/;
        var phonepatt = /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/;
        if(!Bname || !Bssn || !Baddress || !Bphone){
            //make sure they are not empty
            $("#Bmessage").fadeIn(300);
            $("#Bmessage").text("Please enter all the fields to sign up the borrower.");
        }else if(!Bssn.match(ssnpatt)){
            $("#Bmessage").text("Please enter correct SSN as '123-45-6789' format!");
            $("#Bmessage").fadeIn(300);
            $("#Bmessage").css("color","red")
        }else if(!Bphone.match(phonepatt)){
            $("#Bmessage").text("Please enter correct Phone number as '123 456 7899' format!");
            $("#Bmessage").fadeIn(300);
            $("#Bmessage").css("color","red")
        }
        else{
            $("#Bmessage").hide();
        $("#BorrowerMessage").remove();
        $("#BduplicateError").remove();
        $("#Bsuccess").remove();
            //handle ajax call here,
            //GET those values into server side php, and Insert into database.
            $.ajax({  
                type: 'GET',
                url: 'createBorrower.php', 
                data: { userName: Bname, userSsn: Bssn, userAddress: Baddress,userPhone: Bphone},
                success: function(response) {
                    $("form").append(response);
                    $("#BorrowerMessage").fadeIn(300);
                    $("#BorrowerMessage").css("color","green");
                    $("#BduplicateError").fadeIn(300);
                    $("#BduplicateError").css("color","red");
                    $("#Bsuccess").fadeIn(300);
                    $("#Bsuccess").css("color","green");
                },error:function(xhr){alert(xhr.responseText)}
            });
        }
    });
    
    ///////////////Check out implementation//////////
    //check out php ajax call
    $("#checkOutSignup").click(function(){
        var bookISBN = $("#checkoutISBN").val();
        var borrowerID = $("#checkoutBid").val();

        $.ajax({
            type: 'POST',
            url: 'checkOut.php',
            data: {isbn:bookISBN, id: borrowerID},
            success: function(response){
                alert(response);
                $("#message").html(response);
                //check out book message should show up.
                $("#message").fadeIn(200);
            },error:function(xhr){alert(xhr.response)}
        });

        window.location.reload();
        alert(response);
        });

        //////////////Fine page implementation///////////////
    $("#fineBtn").click(function(){
        location.href = "fine.html";
    });

    ///////////////////////////////////
    //These are for the user experiences:

    //index page:

    //hide message for user-experience.
    $("#entry").click(function(){
        $("#message").hide();
    });
    $("#table-container").click(function(){
        $("#message").hide();
    });

     //Set the enter key to trigger the search button
     $("#entry").on('keydown', function (e) {
        if (e.key === "Enter") {
            // Cancel the default action, if needed
            e.preventDefault();
            $("#search").click();
        }
    });


    //borrower page:
    $("#Bssn").click(function(){
        $("#Bmessage").hide();
        $("#BorrowerMessage").remove();
        $("#BduplicateError").remove();
        $("#Bsuccess").remove();
    });
    $("#Bname").click(function(){
        $("#Bmessage").hide();
        $("#BorrowerMessage").remove();
        $("#BduplicateError").remove();
        $("#Bsuccess").remove();
    });

    $("#Baddress").click(function(){
        $("#Bmessage").hide();
        $("#BorrowerMessage").remove();
        $("#BduplicateError").remove();
        $("#Bsuccess").remove();
    });

    $("#Bphone").click(function(){
        $("#Bmessage").hide();
        $("#BorrowerMessage").remove();
        $("#BduplicateError").remove();
        $("#Bsuccess").remove();
    });

    //checkout stuff:
    $("#checkOutCancel").click(function(){
        $(".checkout-container").fadeOut(200);
        $("#checkmessage").remove();
        $("#checkmessage").remove();
    });

    //extra validation:
    $("#Bssn").blur(function(){
        var ssnpatt = /^([0-6]\d{2}|7[0-6]\d|77[0-2])([ \-]?)(\d{2})\2(\d{4})$/;
        if(!$("#Bssn").val().match(ssnpatt)){
            $("#Bmessage").text("Please enter correct SSN as '123-45-6789' format!");
            $("#Bmessage").fadeIn(300);
            $("#Bmessage").css("color","red")
        }
    });


 



    
}); 