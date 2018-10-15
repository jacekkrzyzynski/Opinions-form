$( document ).ready(function() {
  $('#form').on('submit',function() {
    
    var unhide_erroralert = false;
    var erroralert_messages = [];
    
    $('#firstname').val($.trim($('#firstname').val()));
    $('#lastname').val($.trim($('#lastname').val()));
    $('#email').val($.trim($('#email').val()));
    
    if ($('#firstname').val() == '') {
      unhide_erroralert = true;
      erroralert_messages.push('Imię');
      $('#firstname').css('background', '#fbb');
      }
    else {
      $('#firstname').css('background', '#fff');
      } 
       
    if ($('#lastname').val() == '') {
      unhide_erroralert = true;
      erroralert_messages.push('Nazwisko');
      $('#lastname').css('background', '#fbb');
      }
    else {
      $('#lastname').css('background', '#fff');
      } 
            
    if ($('#email').val() == '') {
      unhide_erroralert = true;
      erroralert_messages.push('Email');
      $('#email').css('background', '#fbb');
      }    
    else if (!isEmail($('#email').val())) {
      unhide_erroralert = true;
      erroralert_messages.push('Email - niepoprawny format');
      $('#email').css('background', '#fbb');
      }
    else {
      $('#email').css('background', '#fff');
      } 

    if ($('#image').val() != '') {
      var ext = $('#image').val().split('.').pop().toLowerCase();
      if($.inArray(ext, ['png','jpg','jpeg']) == -1) {
        unhide_erroralert = true;
        erroralert_messages.push('Avatar - tylko plik jpg/png');
        $('#image').css('background', '#fbb');
        }
      else {
        $('#image').css('background', '#fff');
        }
      }
                      
    if (unhide_erroralert) {
      $('#erroralertmessage').html(erroralert_messages.join(', '));
      $('#erroralert').show();
      
      return false;
      }
    
    }); 

  function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
    }
  
  $('#rate_1').on('click', function() {
    $('#rate_1').css('color', '#f00');
    $('#rate_2').css('color', '#000');
    $('#rate_3').css('color', '#000');
    $('#rate_4').css('color', '#000');
    $('#rate_5').css('color', '#000');
    $('#rating').val('1');
  });
  $('#rate_2').on('click', function() {
    $('#rate_1').css('color', '#f00');
    $('#rate_2').css('color', '#f00');
    $('#rate_3').css('color', '#000');
    $('#rate_4').css('color', '#000');
    $('#rate_5').css('color', '#000');
    $('#currating').val('2');
  });
  $('#rate_3').on('click', function() {
    $('#rate_1').css('color', '#f00');
    $('#rate_2').css('color', '#f00');
    $('#rate_3').css('color', '#f00');
    $('#rate_4').css('color', '#000');
    $('#rate_5').css('color', '#000');
    $('#rating').val('3');
  });   
  $('#rate_4').on('click', function() {
    $('#rate_1').css('color', '#f00');
    $('#rate_2').css('color', '#f00');
    $('#rate_3').css('color', '#f00');
    $('#rate_4').css('color', '#f00');
    $('#rate_5').css('color', '#000');
    $('#rating').val('4');
  });  
  $('#rate_5').on('click', function() {
    $('#rate_1').css('color', '#f00');
    $('#rate_2').css('color', '#f00');
    $('#rate_3').css('color', '#f00');
    $('#rate_4').css('color', '#f00');
    $('#rate_5').css('color', '#f00');
    $('#rating').val('5');
  });   
});