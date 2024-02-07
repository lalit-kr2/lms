$(".heading").on('click', function(){
    let id = $(this).attr('id').split('_')[1];

    $.ajax({
        url: window.location.origin + '/lms/local/payment/gateways.php',
        method: 'post',
        data: {id: id},
        dataType: 'json',
        async: true,
        success: function(data){
            if(data.success){
                console.log("Success");
                window.location.reload();
            }
        }
    })
})